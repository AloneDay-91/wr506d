<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\ApiKeyGenerator;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/me/api-key')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class ApiKeyController extends AbstractController
{
    public function __construct(
        private readonly ApiKeyGenerator $apiKeyGenerator,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Generate a new API key for the authenticated user
     * WARNING: The full key is only shown once and cannot be retrieved later
     */
    #[Route('', name: 'api_key_generate', methods: ['POST'])]
    public function generate(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        // Generate new API key
        $keyData = $this->apiKeyGenerator->generate();

        // Store hash and prefix in database
        $user->setApiKeyHash($keyData['hash']);
        $user->setApiKeyPrefix($keyData['prefix']);
        $user->setApiKeyEnabled(true);
        $user->setApiKeyCreatedAt(new DateTimeImmutable());
        $user->setApiKeyLastUsedAt(null);

        $this->entityManager->flush();

        return $this->json([
            'message' =>
                'API key generated successfully. This is the only time you will see the full key.
                Please store it securely.',
            'apiKey' => $keyData['fullKey'],
            'prefix' => $keyData['prefix'],
            'enabled' => true,
            'createdAt' => $user->getApiKeyCreatedAt()?->format('c'),
        ], Response::HTTP_CREATED);
    }

    /**
     * Get the status of the user's API key
     * NOTE: The full key is NEVER returned, only the prefix and status
     */
    #[Route('', name: 'api_key_status', methods: ['GET'])]
    public function status(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if (null === $user->getApiKeyHash()) {
            return $this->json([
                'message' => 'No API key configured',
                'hasApiKey' => false,
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'prefix' => $user->getApiKeyPrefix(),
            'enabled' => $user->isApiKeyEnabled(),
            'createdAt' => $user->getApiKeyCreatedAt()?->format('c'),
            'lastUsedAt' => $user->getApiKeyLastUsedAt()?->format('c'),
        ]);
    }

    /**
     * Toggle the API key enabled status
     */
    #[Route('/toggle', name: 'api_key_toggle', methods: ['PATCH'])]
    public function toggle(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if (null === $user->getApiKeyHash()) {
            return $this->json([
                'message' => 'No API key configured. Please generate one first.',
            ], Response::HTTP_NOT_FOUND);
        }

        // Toggle the enabled status
        $newStatus = !$user->isApiKeyEnabled();
        $user->setApiKeyEnabled($newStatus);

        $this->entityManager->flush();

        return $this->json([
            'message' => sprintf('API key %s', $newStatus ? 'enabled' : 'disabled'),
            'enabled' => $newStatus,
            'prefix' => $user->getApiKeyPrefix(),
        ]);
    }

    /**
     * Revoke (delete) the user's API key
     */
    #[Route('', name: 'api_key_revoke', methods: ['DELETE'])]
    public function revoke(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if (null === $user->getApiKeyHash()) {
            return $this->json([
                'message' => 'No API key configured',
            ], Response::HTTP_NOT_FOUND);
        }

        // Clear all API key fields
        $user->setApiKeyHash(null);
        $user->setApiKeyPrefix(null);
        $user->setApiKeyEnabled(false);
        $user->setApiKeyCreatedAt(null);
        $user->setApiKeyLastUsedAt(null);

        $this->entityManager->flush();

        return $this->json([
            'message' => 'API key revoked successfully',
        ]);
    }
}
