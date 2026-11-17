@extends('layouts.mail')

@section('badge', 'Reset Password')
@section('heading', "Password Reset!")

@section('content')
    <p style="color: #8ca5c7; font-size: 14px; line-height: 1.5; margin: 0 0 20px 0;">
        Hi <strong>{{ $user->name }}</strong>, You have requested to reset your password.
        <br><br>
        Your password reset verification code is:
    </p>

    <div style="background-color: #1b3257; border: 1px dashed rgba(255,255,255,0.2); border-radius: 12px; padding: 16px; text-align: center; margin-bottom: 24px;">        
        <strong style="color: #ffffff; font-size: 20px; letter-spacing: 2px;">{{ $user->verification_code }}</strong>
    </div>

    <p style="color: #8ca5c7; font-size: 14px; line-height: 1.5; margin: 0 0 20px 0;">
        If you did not request a password reset, please ignore this email.
    </p>
@endsection