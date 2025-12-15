<?php

namespace App\Service;

class ApiKeyGenerator
{
    /**
     * Generate a new API key with prefix and secret part
     *
     * Process:
     * 1. Generate 32 random bytes with random_bytes(32)
     * 2. Convert to hexadecimal with bin2hex() -> 64 chars
     * 3. Hash this hex part with SHA-256
     * 4. Create full key: 'sk_' + hex part -> 67 chars total
     * 5. Extract prefix: first 16 chars of full key
     *
     * @return array{fullKey: string, prefix: string, hash: string}
     */
    public function generate(): array
    {
        // Step 1: Generate 32 random bytes
        $randomBytes = random_bytes(32);

        // Step 2: Convert to hexadecimal (64 chars)
        $hexPart = bin2hex($randomBytes);

        // Step 3: Hash the hex part with SHA-256
        $hash = hash('sha256', $hexPart);

        // Step 4: Create full key with 'sk_' prefix (67 chars total)
        $fullKey = 'sk_' . $hexPart;

        // Step 5: Extract prefix (first 16 chars: 'sk_' + 12 hex chars)
        $prefix = substr($fullKey, 0, 16);

        return [
            'fullKey' => $fullKey,
            'prefix' => $prefix,
            'hash' => $hash,
        ];
    }

    /**
     * Extract the prefix from an API key (first 16 characters)
     */
    public function extractPrefix(string $apiKey): string
    {
        return substr($apiKey, 0, 16);
    }

    /**
     * Hash an API key using SHA-256
     * Only hash the hex part (without 'sk_' prefix)
     */
    public function hashKey(string $apiKey): string
    {
        // Extract hex part (remove 'sk_' prefix)
        $hexPart = substr($apiKey, 3);

        return hash('sha256', $hexPart);
    }

    /**
     * Validate the format of an API key
     * Expected format: sk_{64hex} (67 chars total)
     */
    public function validateFormat(string $apiKey): bool
    {
        // Pattern: sk_ + 64 hex chars (67 chars total)
        $pattern = '/^sk_[a-f0-9]{64}$/i';

        return preg_match($pattern, $apiKey) === 1;
    }
}
