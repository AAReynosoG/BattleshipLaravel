<?php

namespace App\Http\Controllers;

use App\Mail\CodeVerification;
use App\Models\EmailVerificationToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EmailVerificationController extends Controller
{
    public function verify_email(Request $request, $user_id){
        if (!$request->hasValidSignature()) {
            return view('emails.EmailVerificationErrorView');
        }

        $user = User::find($user_id);

        if (!$user){
            return view('emails.FindUserErrorView');
        }

        $userToken = EmailVerificationToken::where('user_id', $user_id)->first();

        if (!$userToken || !Hash::check($request->token, $userToken->token)) {
            return view('emails.EmailVerificationErrorView');
        }

        $userToken->delete();

            $user->is_verified = true;
            $user->email_verified_at = now();
            $user->save();

            return view('emails.EmailVerificationSuccessView');

        }
}
