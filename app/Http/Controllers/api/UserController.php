<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Mail\InviteUser;
use App\Mail\VerifyUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function invite(Request $request, $email)
    {
        $verificationToken = Str::random(40);
        $user = new User();
        $user->fill(['email' => $email, 'verification_token' => $verificationToken]);
        $user->save();

        $mailData = ['verification_link' => route('signup', $verificationToken)];
        Mail::to($email)->send(new InviteUser($mailData));
    }

    public function signup(Request $request, $token)
    {
        $user = User::where('verification_token', $token)->first();
        if (!$user) {
            return response(['message' => 'Invalid verification token'], 403);
        }
        $user->fill($request->only('user_name', 'password', 'name'));
        $user->verification_code = random_int(100000, 999999);
        $user->save();
        Mail::to($user->email)->send(new VerifyUser(['verification_code' => $user->verification_code]));
        return response(['message' => 'A 6 digit verification code sent to your email']);
    }

    public function verify(Request $request, $code)
    {
        $user = User::where('verification_code', $code)->first();
        if (!$user) {
            return response(['message' => 'Invalid verification code'], 403);
        }
        $user->verification_code = null;
        $user->verification_token = null;
        $user->email_verified_at = Carbon::now();
        $user->save();
        return response(['message' => 'Profile created successfully', 'data' => $user]);
    }

    public function login(Request $request)
    {
        $user = User::where('user_name', $request->get('user_name'))->first();
        if (!$user) {
            return response(['message' => 'Invalid user name'], 401);
        }

        if (!Hash::check($request->get('password'), $user->password)) {
            return response(['message' => 'Invalid password'], 401);
        }
        $token = $user->createToken('test-token', ['server:update']);
        return response(['message' => 'Login successful', 'data' => ['user' => $user, 'token' => $token->plainTextToken]]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        if ($user->tokenCan('server:update')) {
            $user->fill($request->only('name', 'password'));
            $user->save();
            return response(['message' => 'Profile updated successfully', 'data' => ['user' => $user]]);
        }
    }

    public function logout(Request $request) {
        // Revoke the token that was used to authenticate the current request...
        $request->user()->currentAccessToken()->delete();
        return response(['message' => 'Logged out successfully from device', 'data' => []]);
    }
}
