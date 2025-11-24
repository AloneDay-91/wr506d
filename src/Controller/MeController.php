<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class MeController
{
    #[Route('/api/me', name: 'get_current_user', methods: ["GET"])]
    public function getCurrentUser(UserInterface $user): JsonResponse
    {

        if (!$user instanceof \App\Entity\User) {
            throw new AccessDeniedHttpException();
        }

        $userData = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
        ];

        return new JsonResponse($userData);
    }
}
