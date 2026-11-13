<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Crypt;

trait HasBlindIndex
{
    /**
     * Generate a deterministic HMAC hash for blind indexing.
     */
    public static function generateBlindIndex(?string $value): ?string
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        $normalized = strtolower(trim($value));
        $key = config('security.blind_index_key');

        return hash_hmac('sha256', $normalized, $key);
    }

    /**
     * Helper to encrypt a field and set its corresponding blind index attribute simultaneously.
     *
     * @param  string  $attribute  The base attribute name (e.g. 'email')
     * @param  string|null  $value  The plain-text value to encrypt
     * @param  string|null  $indexAttribute  Custom index column name (defaults to 'attribute_bindex')
     */
    protected function encryptAndIndex(string $attribute, ?string $value, ?string $indexAttribute = null): void
    {
        $indexKey = $indexAttribute ?? "{$attribute}_bindex";

        if (! empty($value)) {
            $normalized = strtolower(trim($value));
            $this->attributes[$attribute] = Crypt::encryptString($normalized);
            $this->attributes[$indexKey] = static::generateBlindIndex($normalized);
        } else {
            $this->attributes[$attribute] = null;
            $this->attributes[$indexKey] = null;
        }
    }

    /**
     * Helper to safely decrypt a field.
     */
    protected function decryptAttribute(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            // Gracefully return plain text if record was unencrypted legacy data
            return $value;
        }
    }

    /**
     * Eloquent Query Scope to search blind index columns seamlessly.
     *
     * @param  string  $attribute  The base attribute name (e.g., 'account_number' or 'email')
     * @param  string|null  $value  The plain-text value to search for
     * @param  string|null  $indexAttribute  Custom index column name if different from '{attribute}_bindex'
     */
    public function scopeWhereBlindIndex(Builder $query, string $attribute, ?string $value, ?string $indexAttribute = null): Builder
    {
        $indexKey = $indexAttribute ?? "{$attribute}_bindex";
        $blindIndex = static::generateBlindIndex($value);

        return $query->where($indexKey, $blindIndex);
    }
}
