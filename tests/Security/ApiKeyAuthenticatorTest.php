<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\ApiKeyAuthenticator;
use App\Service\ApiKeyGenerator;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class ApiKeyAuthenticatorTest extends TestCase
{
    private ApiKeyAuthenticator $authenticator;
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;
    private ApiKeyGenerator $apiKeyGenerator;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->apiKeyGenerator = new ApiKeyGenerator();

        $this->authenticator = new ApiKeyAuthenticator(
            $this->userRepository,
            $this->entityManager,
            $this->apiKeyGenerator
        );
    }

    public function testSupportsReturnsTrueWhenApiKeyHeaderIsPresent(): void
    {
        $request = new Request();
        $request->headers->set('X-API-KEY', 'some-key');

        $this->assertTrue($this->authenticator->supports($request));
    }

    public function testSupportsReturnsFalseWhenApiKeyHeaderIsMissing(): void
    {
        $request = new Request();

        $this->assertFalse($this->authenticator->supports($request));
    }

    public function testAuthenticateThrowsExceptionWhenNoApiKeyProvided(): void
    {
        $request = new Request();

        $this->expectException(CustomUserMessageAuthenticationException::class);
        $this->expectExceptionMessage('No API key provided');

        $this->authenticator->authenticate($request);
    }

    public function testAuthenticateThrowsExceptionWhenApiKeyFormatIsInvalid(): void
    {
        $request = new Request();
        $request->headers->set('X-API-KEY', 'invalid-format');

        $this->expectException(CustomUserMessageAuthenticationException::class);
        $this->expectExceptionMessage('Invalid API key format');

        $this->authenticator->authenticate($request);
    }

    public function testAuthenticateThrowsExceptionWhenUserNotFound(): void
    {
        $keyData = $this->apiKeyGenerator->generate();

        $request = new Request();
        $request->headers->set('X-API-KEY', $keyData['fullKey']);

        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['apiKeyPrefix' => $keyData['prefix']])
            ->willReturn(null);

        $this->expectException(CustomUserMessageAuthenticationException::class);
        $this->expectExceptionMessage('Invalid API key');

        $this->authenticator->authenticate($request);
    }

    public function testAuthenticateThrowsExceptionWhenHashDoesNotMatch(): void
    {
        $keyData = $this->apiKeyGenerator->generate();
        $wrongKeyData = $this->apiKeyGenerator->generate();

        $request = new Request();
        $request->headers->set('X-API-KEY', $keyData['fullKey']);

        $user = $this->createMock(User::class);
        $user->method('getApiKeyHash')->willReturn($wrongKeyData['hash']); // Different hash

        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['apiKeyPrefix' => $keyData['prefix']])
            ->willReturn($user);

        $this->expectException(CustomUserMessageAuthenticationException::class);
        $this->expectExceptionMessage('Invalid API key');

        $this->authenticator->authenticate($request);
    }

    public function testAuthenticateThrowsExceptionWhenApiKeyIsDisabled(): void
    {
        $keyData = $this->apiKeyGenerator->generate();

        $request = new Request();
        $request->headers->set('X-API-KEY', $keyData['fullKey']);

        $user = $this->createMock(User::class);
        $user->method('getApiKeyHash')->willReturn($keyData['hash']);
        $user->method('isApiKeyEnabled')->willReturn(false); // Disabled
        $user->method('getUserIdentifier')->willReturn('test@example.com');

        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['apiKeyPrefix' => $keyData['prefix']])
            ->willReturn($user);

        $this->expectException(CustomUserMessageAuthenticationException::class);
        $this->expectExceptionMessage('API key is disabled');

        $this->authenticator->authenticate($request);
    }

    public function testAuthenticateSucceedsWithValidApiKey(): void
    {
        $keyData = $this->apiKeyGenerator->generate();

        $request = new Request();
        $request->headers->set('X-API-KEY', $keyData['fullKey']);

        $user = $this->createMock(User::class);
        $user->method('getApiKeyHash')->willReturn($keyData['hash']);
        $user->method('isApiKeyEnabled')->willReturn(true);
        $user->method('getUserIdentifier')->willReturn('test@example.com');

        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['apiKeyPrefix' => $keyData['prefix']])
            ->willReturn($user);

        $passport = $this->authenticator->authenticate($request);

        $this->assertInstanceOf(\Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport::class, $passport);
    }
}