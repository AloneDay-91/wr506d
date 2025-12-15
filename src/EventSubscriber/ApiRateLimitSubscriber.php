<?php

namespace App\EventSubscriber;

use App\Entity\User;
use DateTimeImmutable;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class ApiRateLimitSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly RateLimiterFactory $anonymousApiLimiter,
        private readonly RateLimiterFactory $authenticatedApiLimiter,
        private readonly TokenStorageInterface $tokenStorage
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Run after security firewall (priority 8) to ensure JWT authentication is processed
            KernelEvents::REQUEST => ['onKernelRequest', 5],
            KernelEvents::RESPONSE => ['onKernelResponse', -10],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$this->shouldApplyRateLimiting($request)) {
            return;
        }

        $user = $this->getAuthenticatedUser();
        $isAuthenticated = $user instanceof UserInterface;
        $identifier = $this->getIdentifier($user, $request);

        if ($this->hasCustomRateLimit($user)) {
            /** @var User $user */
            $this->applyCustomRateLimit($event, $request, $user, $identifier);
            return;
        }

        $this->applyStandardRateLimit($event, $request, $isAuthenticated, $identifier);
    }

    private function shouldApplyRateLimiting(Request $request): bool
    {
        $path = $request->getPathInfo();

        if (!str_starts_with($path, '/api/')) {
            return false;
        }

        // Don't rate limit documentation endpoints
        return !str_starts_with($path, '/api/docs')
            && !str_starts_with($path, '/api/graphql/graphiql');
    }

    private function getAuthenticatedUser(): ?UserInterface
    {
        $token = $this->tokenStorage->getToken();
        return $token?->getUser();
    }

    private function getIdentifier(?UserInterface $user, Request $request): string
    {
        if ($user instanceof UserInterface) {
            return $user->getUserIdentifier();
        }

        return $request->getClientIp() ?? 'unknown';
    }

    private function hasCustomRateLimit(?UserInterface $user): bool
    {
        return $user instanceof User && $user->getRateLimit() !== null;
    }

    private function applyCustomRateLimit(
        RequestEvent $event,
        Request $request,
        User $user,
        string $identifier
    ): void {
        $userRateLimit = $user->getRateLimit();
        $limiter = $this->authenticatedApiLimiter->create($identifier);
        $limit = $limiter->consume(1);

        $request->attributes->set('_rate_limit', [
            'limit' => $userRateLimit,
            'remaining' => max(0, $userRateLimit - (100 - $limit->getRemainingTokens())),
            'reset' => $limit->getRetryAfter()->getTimestamp(),
            'is_custom' => true,
        ]);

        $consumed = 100 - $limit->getRemainingTokens();
        if ($consumed > $userRateLimit) {
            $this->sendRateLimitExceededResponse($event, $userRateLimit, $limit->getRetryAfter());
        }
    }

    private function applyStandardRateLimit(
        RequestEvent $event,
        Request $request,
        bool $isAuthenticated,
        string $identifier
    ): void {
        $limiter = $isAuthenticated
            ? $this->authenticatedApiLimiter->create($identifier)
            : $this->anonymousApiLimiter->create($identifier);

        $limit = $limiter->consume();

        $request->attributes->set('_rate_limit', [
            'limit' => $limit->getLimit(),
            'remaining' => $limit->getRemainingTokens(),
            'reset' => $limit->getRetryAfter()->getTimestamp(),
            'is_custom' => false,
        ]);

        if (!$limit->isAccepted()) {
            $this->sendRateLimitExceededResponse($event, $limit->getLimit(), $limit->getRetryAfter());
        }
    }

    private function sendRateLimitExceededResponse(
        RequestEvent $event,
        int $limit,
        DateTimeImmutable $retryAfter
    ): void {
        $response = new JsonResponse(
            [
                'error' => 'Too Many Requests',
                'message' => 'Rate limit exceeded. Please try again later.',
                'retry_after' => $retryAfter->getTimestamp(),
            ],
            429
        );

        $response->headers->set('Retry-After', (string) $retryAfter->getTimestamp());
        $response->headers->set('X-RateLimit-Limit', (string) $limit);
        $response->headers->set('X-RateLimit-Remaining', '0');
        $response->headers->set('X-RateLimit-Reset', (string) $retryAfter->getTimestamp());

        $event->setResponse($response);
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        // Only add headers if we have rate limit info
        $rateLimitInfo = $request->attributes->get('_rate_limit');
        if (!$rateLimitInfo) {
            return;
        }

        // Add rate limit headers to the response
        $response->headers->set('X-RateLimit-Limit', (string) $rateLimitInfo['limit']);
        $response->headers->set('X-RateLimit-Remaining', (string) $rateLimitInfo['remaining']);
        $response->headers->set('X-RateLimit-Reset', (string) $rateLimitInfo['reset']);
    }
}
