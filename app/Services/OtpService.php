<?php

namespace App\Services;

use App\Enums\OtpType;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Random\RandomException;

class OtpService
{
    /**
     * Default throttle duration between OTP generation requests in seconds.
     */
    protected const THROTTLE_SECONDS = 60;

    /**
     * Maximum allowed verification attempts before the OTP is invalidated.
     */
    protected const MAXIMUM_VERIFICATION_ATTEMPTS = 5;

    /**
     * Generate and store a new 6-digit OTP for the given email and type.
     *
     *
     * @throws RandomException
     */
    public function generate(string $email, OtpType $type, int $expiresInMinutes = 10): string
    {
        $normalizedEmail = strtolower(trim($email));
        $code = sprintf('%06d', random_int(100000, 999999));
        $cacheKey = $this->buildCacheKey($normalizedEmail, $type);
        $throttleKey = $this->buildThrottleKey($normalizedEmail, $type);

        Cache::put($cacheKey, [
            'code' => Hash::make($code),
            'attempts' => 0,
        ], now()->addMinutes($expiresInMinutes));

        Cache::put($throttleKey, true, now()->addSeconds(self::THROTTLE_SECONDS));

        return $code;
    }

    /**
     * Check if the provided OTP code matches without consuming it.
     */
    public function check(string $email, OtpType $type, string $code): bool
    {
        $normalizedEmail = strtolower(trim($email));
        $cacheKey = $this->buildCacheKey($normalizedEmail, $type);

        /** @var array{code: string, attempts: int}|null $record */
        $record = Cache::get($cacheKey);

        if ($record === null) {
            return false;
        }

        if ($record['attempts'] >= self::MAXIMUM_VERIFICATION_ATTEMPTS) {
            $this->invalidate($normalizedEmail, $type);

            return false;
        }

        if (! Hash::check($code, $record['code'])) {
            $record['attempts']++;
            Cache::put($cacheKey, $record, now()->addMinutes(10));

            return false;
        }

        return true;
    }

    /**
     * Verify whether the provided OTP code is valid and consume it upon success.
     */
    public function verify(string $email, OtpType $type, string $code): bool
    {
        $normalizedEmail = strtolower(trim($email));
        $cacheKey = $this->buildCacheKey($normalizedEmail, $type);

        /** @var array{code: string, attempts: int}|null $record */
        $record = Cache::get($cacheKey);

        if ($record === null) {
            return false;
        }

        if ($record['attempts'] >= self::MAXIMUM_VERIFICATION_ATTEMPTS) {
            $this->invalidate($normalizedEmail, $type);

            return false;
        }

        if (! Hash::check($code, $record['code'])) {
            $record['attempts']++;
            Cache::put($cacheKey, $record, now()->addMinutes(10));

            return false;
        }

        $this->invalidate($normalizedEmail, $type);

        return true;
    }

    /**
     * Invalidate any active OTP for the given email and type.
     */
    public function invalidate(string $email, OtpType $type): void
    {
        $normalizedEmail = strtolower(trim($email));
        Cache::forget($this->buildCacheKey($normalizedEmail, $type));
    }

    /**
     * Check if a valid OTP currently exists for the given email and type.
     */
    public function hasActiveOtp(string $email, OtpType $type): bool
    {
        $normalizedEmail = strtolower(trim($email));

        return Cache::has($this->buildCacheKey($normalizedEmail, $type));
    }

    /**
     * Determine whether generating an OTP for the given email and type is throttled.
     */
    public function isThrottled(string $email, OtpType $type): bool
    {
        $normalizedEmail = strtolower(trim($email));

        return Cache::has($this->buildThrottleKey($normalizedEmail, $type));
    }

    /**
     * Build the primary cache key for OTP record.
     */
    protected function buildCacheKey(string $email, OtpType $type): string
    {
        return 'otp:'.$type->value.':'.$email;
    }

    /**
     * Build the cache key for throttling OTP generation.
     */
    protected function buildThrottleKey(string $email, OtpType $type): string
    {
        return 'otp_throttle:'.$type->value.':'.$email;
    }
}
