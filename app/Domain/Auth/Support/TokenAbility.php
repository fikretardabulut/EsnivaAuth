<?php

namespace App\Domain\Auth\Support;

final class TokenAbility
{
    public const AUTH_READ = 'auth:read';
    public const AUTH_WRITE = 'auth:write';
    public const TOKENS_READ = 'tokens:read';
    public const TOKENS_WRITE = 'tokens:write';

    public static function default(): array
    {
        return [
            self::AUTH_READ,
            self::AUTH_WRITE,
            self::TOKENS_READ,
            self::TOKENS_WRITE,
        ];
    }
}