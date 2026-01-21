<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\ApiKeyGenerator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly ApiKeyGenerator $apiKeyGenerator
    ) {
    }

    public function supports(Request $request): ?bool
    {
        // Check if the request has an API key in the header
        return $request->headers->has('X-API-KEY');
    }

    public function authenticate(Request $request): Passport
    {
        $apiKey = $request->headers->get('X-API-KEY');

        if (null === $apiKey || '' === $apiKey) {
            throw new CustomUserMessageAuthenticationException('No API key provided');
        }

        // Validate API key format
        if (!$this->apiKeyGenerator->validateFormat($apiKey)) {
            throw new CustomUserMessageAuthenticationException('Invalid API key format');
        }

        // Step 1: Extract the prefix from the provided API key (first 16 characters)
        $prefix = $this->apiKeyGenerator->extractPrefix($apiKey);

        // Step 2: Find user by prefix (indexed search, fast)
        $user = $this->userRepository->findOneBy(['apiKeyPrefix' => $prefix]);

        if (null === $user) {
            throw new CustomUserMessageAuthenticationException('Invalid API key');
        }

        // Step 3: Verify the hash of the complete API key
        $apiKeyHash = $this->apiKeyGenerator->hashKey($apiKey);

        if ($user->getApiKeyHash() !== $apiKeyHash) {
            throw new CustomUserMessageAuthenticationException('Invalid API key');
        }

        // Step 4: Check if API key is enabled
        if (!$user->isApiKeyEnabled()) {
            throw new CustomUserMessageAuthenticationException('API key is disabled');
        }

        return new SelfValidatingPassport(
            new UserBadge($user->getUserIdentifier())
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Update last used timestamp
        $user = $token->getUser();
        if ($user instanceof User) {
            $this->userRepository->updateApiKeyLastUsedAt($user);
        }

        // On success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ], Response::HTTP_UNAUTHORIZED);
    }
}
