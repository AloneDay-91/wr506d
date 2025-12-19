<?php

namespace App\Security;

use App\Entity\User;
use App\Service\TwoFactorService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserAuthenticatorService
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly TwoFactorService $twoFactorService,
        private readonly JWTTokenManagerInterface $jwtManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function verifyCredentials(User $user, string $password, ?string $totpCode): bool
    {
        $this->logger->info('=== VERIFY CREDENTIALS DEBUG ===');
        $this->logger->info('User email: ' . $user->getEmail());
        $this->logger->info('2FA enabled: ' . ($user->isTwoFactorEnabled() ? 'true' : 'false'));
        $this->logger->info('2FA secret is null: ' . ($user->getTwoFactorSecret() === null ? 'true' : 'false'));
        $this->logger->info('TOTP code: ' . ($totpCode ?? 'NULL'));

        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            $this->logger->info('Password invalid');
            return false;
        }

        $this->logger->info('Password valid');

        if ($user->isTwoFactorEnabled() && $user->getTwoFactorSecret() !== null) {
            $this->logger->info('2FA check required');
            if ($totpCode === null || $totpCode === '') {
                $this->logger->info('TOTP code missing - returning false');
                return false;
            }

            $this->logger->info('Verifying TOTP code');
            if (!$this->twoFactorService->verifyCode($user, $totpCode)) {
                $this->logger->info('TOTP code invalid');
                return false;
            }
            $this->logger->info('TOTP code valid');
        } else {
            $this->logger->info('2FA not required - bypassing');
        }

        $this->logger->info('Credentials verified successfully');
        return true;
    }

    public function createJwt(User $user): string
    {
        return $this->jwtManager->create($user);
    }
}
