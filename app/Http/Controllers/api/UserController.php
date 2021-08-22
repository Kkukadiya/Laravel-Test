<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Mail\InviteUser;
use App\Mail\VerifyUser;
use App\Models\User;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Image;

class UserController extends Controller
{
    use ApiResponse;

    public function invite(Request $request, $email)
    {
        if ($request->user()->role_id != 1) {
            return $this->failedResponse('You are not authorized to perfome this operation.',[],401);
        }
        $verificationToken = Str::random(40);

        $user = new User();
        $user->fill(['email' => $email, 'verification_token' => $verificationToken]);
        $user->save();

        $mailData = [
            'email' => $email,
            'verification_link' => route('signup', $verificationToken)
        ];
        Mail::to($email)->send(new InviteUser($mailData));

        return $this->successResponse('Invitation link has has been sent successfully.', []);
    }

    public function userSignup(Request $request, $token)
    {
        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            return $this->failedResponse('Invalid verification token.');
        }

        $validator = Validator::make($request->all(), [
            'user_name' => 'required|min:4|max:20|unique:users',
            'password' => 'required',
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationErrors($validator);
        }

        $user->fill($request->only('user_name', 'password', 'name'));
        $user->verification_code = random_int(100000, 999999);
        $user->save();
        $mailData = [
            'name' => $user->name,
            'verification_code' => $user->verification_code
        ];
        Mail::to($user->email)->send(new VerifyUser($mailData));

        return $this->successResponse('A 6 digit verification code sent to your email successfully.', []);
    }

    public function userVerify(Request $request, $code)
    {
        $user = User::where('verification_code', $code)->first();
        if (!$user) {
            return $this->failedResponse('Invalid verification code.');
        }

        $user->verification_code = null;
        $user->verification_token = null;
        $user->email_verified_at = Carbon::now();
        $user->save();

        return $this->successResponse('Profile created successfully.', ['user' => $user]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|min:4|max:20',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationErrors($validator);
        }

        $data = [];
        $user = User::where('user_name', $request->get('username'))->first();
        if (!$user) {
            return $this->failedResponse('Invalid username.',$data,401);
        }

        if (!Hash::check($request->get('password'), $user->password)) {
            return $this->failedResponse('Invalid password.',$data,401);
        }
        $token = $user->createToken('test-token', ['server:update']);

        $data = [
            'user' => $user,
            'token' => $token->plainTextToken
        ];
        return $this->successResponse('Login successful.',$data);
    }

    public function updateProfile(Request $request)
    {
        if ($request->hasFile('avatar')) {
            $image = $request->file('avatar');
            $filename  = time() . '.' . $image->getClientOriginalExtension();
            $path = public_path('avatar'.DIRECTORY_SEPARATOR.$filename);
            Image::make($image->getRealPath())->resize(256, 256)->save($path);
        }
        $user = $request->user();
        if ($user->tokenCan('server:update')) {
            User::where('id', $user->id)
                ->update([
                    'name' => !empty($request->input('name')) ? $request->input('name') : $user->name,
                    'user_name' => !empty($request->input('user_name')) ? $request->input('user_name') : $user->user_name,
                    'email' => !empty($request->input('email ')) ? $request->input('email') : $user->email,
                    'avatar' => !empty($filename) ? $filename : $user->avatar,
                ]);

            $updatedUser = User::find($user->id);

            return $this->successResponse('Profile updated successfully.', ['user' => $updatedUser]);
        }
    }

    public function logout(Request $request) {
        // Revoke the token that was used to authenticate the current request...
        $request->user()->currentAccessToken()->delete();

        $data = [];

        return $this->successResponse('Logged out successfully from device.',$data);
    }
}
