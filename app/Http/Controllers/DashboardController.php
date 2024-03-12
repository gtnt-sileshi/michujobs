<?php

namespace App\Http\Controllers;

//use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {   
        $this->middleware('auth');
    }
    public function index()
    {
        return view('dashboard');
    }

    public function verify()
    {
        return view('user.verify');
    }

    public function resend (Request $request)
    {
        $user = Auth::user();
        if($user->hasVerifiedEmail()){
            return redirect('home')->with('success', 'Your Email was verified');
        }

        $user->sendEmailVerificationNotification();
        return back()->with('success', 'verification link sent successfully');
    }
}
