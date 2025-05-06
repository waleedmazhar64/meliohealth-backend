<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Notifications\ForgetPassword;
use App\Notifications\OtpCode;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $otp = rand(100000, 999999);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ? 'admin' : 'patient',
            'otp' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
            'is_verified' => false,
        ]);

        //$user->notify(new OtpCode($otp));

        return response()->json(['message' => 'OTP sent to your email for verification.']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6'
        ]);

        $user = User::where('email', $request->email)
                    ->where('otp', $request->otp)
                    ->first();

        if (!$user || Carbon::now()->gt($user->otp_expires_at)) {
            return response()->json(['message' => 'Invalid or expired OTP'], 400);
        }

        $user->update([
            'is_verified' => true,
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        $token = $user->createToken('MelioHealthApp')->accessToken;

        return response()->json(['message' => 'OTP verified!', 'token' => $token, 'user' => $user]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        
        if (auth()->attempt($credentials)) {
            if (!auth()->user()->is_verified) {
                return response()->json(['message' => 'Please verify your account.'], 403);
            }
            $token = auth()->user()->createToken('MelioHealthApp')->accessToken;
            return response()->json(['token' => $token, 'user' => auth()->user()], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function forgotPassword(Request $request)
    {
        $user = User::where('email', '=', request()->input('email'))->first();
        
        if (!$user) {
            return response()->json(['message' => 'User Not Exist'], 200);
        }

        $input = $request->all();
        $token = Hash::make(mt_rand());
        $token = str_replace("/", "", $token);
        DB::table('password_reset_tokens')->insert([
          'email' => $input['email'],
          'token' => $token,
          'created_at' => Carbon::now()
        ]);
        
        $link ="https://meliohealth.ai/recover-password/" . $token;
        
        $user->notify(new ForgetPassword($user->name, $link));
        
        return response()->json(['message' => 'Recover link is sent to your Email!'], 200);
    }

    public function resetPassword(Request $request)
    {
        $input = $request->all();
        $res = DB::table('password_reset_tokens')
          ->where('token', $input['token'])
          ->orderby('created_at','desc')->get();
        if(sizeof($res) > 0){
          User::where('email', $res[0]->email)
            ->update([
              "password" => Hash::make($request['password'])
            ]);
            return response()->json(['message' => 'password changed'], 200);
        }
        else{
            return response()->json(['message' => 'Reset Password Token Not Found'], 200);
        }
    }

    public function resendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $otp = rand(100000, 999999);

        $user->update([
            'otp' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        //$user->notify(new OtpCode($otp));

        return response()->json(['message' => 'OTP resent successfully.']);
    }

}
