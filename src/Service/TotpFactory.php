<?php

namespace App\Service;

use OTPHP\TOTP;
use OTPHP\TOTPInterface;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class TotpFactory
{
    public function create(): TOTPInterface
    {
        return TOTP::generate();
    }

    public function createFromSecret(string $secret): TOTPInterface
    {
        return TOTP::createFromSecret($secret);
    }
}
