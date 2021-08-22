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
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use ApiResponse;

    public function invite(Request $request, $email)
    {
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

        return $this->successResponse('Profile created successfully.', [$user]);
    }
}
