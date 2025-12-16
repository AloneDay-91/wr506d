<?php

namespace App\Service;

use OTPHP\TOTP;
use OTPHP\TOTPInterface;

class TotpFactory
{
    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function create(): TOTPInterface
    {
        $totp = TOTP::generate();
        $totp->setRfc3548Compliant(true);
        return $totp;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function createFromSecret(string $secret): TOTPInterface
    {
        return TOTP::createFromSecret($secret);
    }
}
