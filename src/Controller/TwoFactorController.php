<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\TwoFactorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/2fa')]
class TwoFactorController extends AbstractController
{
    public function __construct(
        private readonly TwoFactorService $twoFactorService,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/setup', name: 'app_2fa_setup', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function setup(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['error' => 'User not found'], 401);
        }

        $secret = $this->twoFactorService->generateSecret();
        $user->setTwoFactorSecret($secret);
        // ON FORCE A FALSE !
        $user->setTwoFactorEnabled(false);

        $this->entityManager->flush();

        // QR code
        $qrCodeDataUri = $this->twoFactorService->getQrCode($user);
        $provisioningUri = $this->twoFactorService->getProvisioningUri($user);

        return $this->json([
            'secret' => $secret,
            'qr_code' => $qrCodeDataUri,
            'provisioning_uri' => $provisioningUri,
            'message' => 'Scan this QR code with your authenticator app...',
        ]);
    }

    #[Route('/enable', name: 'app_2fa_enable', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function enable(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['error' => 'User not found'], 401);
        }

        // Vérifier que le setup a été fait
        if (null === $user->getTwoFactorSecret()) {
            return $this->json(['error' => '2FA not configured. Call POST /api/2fa/setup first.'], 400);
        }

        $data = json_decode($request->getContent(), true);
        $code = $data['code'] ?? '';

        if (empty($code)) {
            return $this->json(['error' => 'Code is required'], 400);
        }

        // Verify the code
        if (!$this->twoFactorService->verifyCode($user, $code)) {
            return $this->json(['error' => 'Invalid code'], 400);
        }

        // Generate backup codes
        $backupCodes = $this->twoFactorService->generateBackupCodes();
        $hashedBackupCodes = $this->twoFactorService->hashBackupCodes($backupCodes);

        // Enable 2FA
        $user->setTwoFactorEnabled(true);
        $user->setTwoFactorBackupCodes($hashedBackupCodes);

        $this->entityManager->flush();

        return $this->json([
            'message' => '2FA enabled successfully',
            'backup_codes' => $backupCodes,
            'warning' => 'Save these backup codes in a safe place. They will not be shown again!',
        ]);
    }
}
