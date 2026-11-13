<?php

namespace App\Traits;

use App\Enum\MailingEnum;
use App\Mail\VerifyAccountMail;
use App\Models\Verify;
use App\Notifications\ResetPassword;
use Exception;

trait ShouldVerify
{
    private function generateVerifier(): Verify
    {
        return Verify::create([
            'user_id' => $this->id,
            'token' => generate_user_verification_code(),
            'email' => $this->email,
            'expires_at' => now()->addMinutes(10),
        ]);
    }

    public function sendVerificationEmail()
    {
        $verifyUser = $this->generateVerifier();
        $user = $this;

        $type = MailingEnum::EMAIL_VERIFICATION->value;
        $subject = 'Email Verification Request.';
        $mail_class = VerifyAccountMail::class;

        $payload = [
            'user' => [
                'first_name' => $this->first_name,
                'token' => $verifyUser->token,
                'expires_at' => $verifyUser->expires_at->toISOString(),
            ],
        ];
        mail_send($type, $user, $subject, $mail_class, $payload);

        return $verifyUser;
    }

    public function sendPasswordResetEmail()
    {
        $verifyUser = $this->generateVerifier();
        $user = $this;
        try {
            $type = MailingEnum::RESET_OTP->value;
            $subject = 'Password Reset Request';
            $mail_class = ResetPassword::class;
            $data = [
                'user' => $verifyUser,
            ];
            mail_send($type, $user, $subject, $mail_class, $data);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $verifyUser;
    }

    public function verify(Verify $verifier, string $password): void
    {
        $user = $verifier->user;
        $user->password = bcrypt($password);
        ! $user->email_verified_at ? $user->email_verified_at = now() : null;
        $user->save();
        $verifier->delete();
    }
}
