<?php

namespace App\Security;

use App\Entity\User;
use App\Service\TwoFactorService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserAuthenticatorService
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly TwoFactorService $twoFactorService,
        private readonly JWTTokenManagerInterface $jwtManager,
    ) {
    }

    public function verifyCredentials(User $user, string $password, ?string $totpCode): bool
    {
        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            return false;
        }

        if ($user->isTwoFactorEnabled() && $user->getTwoFactorSecret() !== null) {
            if ($totpCode === null || $totpCode === '') {
                return false;
            }

            if (!$this->twoFactorService->verifyCode($user, $totpCode)) {
                return false;
            }
        }

        return true;
    }

    public function createJwt(User $user): string
    {
        return $this->jwtManager->create($user);
    }
}
