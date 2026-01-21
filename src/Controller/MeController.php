<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

class MeController
{
    public function __construct(
        private readonly StorageInterface $storage
    ) {
    }

    #[Route('/api/me', name: 'get_current_user', methods: ["GET"])]
    public function getCurrentUser(UserInterface $user): JsonResponse
    {

        if (!$user instanceof \App\Entity\User) {
            throw new AccessDeniedHttpException();
        }

        $photo = $user->getPhoto();
        $photoData = null;

        if ($photo !== null) {
            $contentUrl = null;
            try {
                $contentUrl = $this->storage->resolveUri($photo, 'file');
            } catch (\Exception $e) {
                // Si le fichier n'existe pas, on laisse contentUrl à null
            }

            $photoData = [
                'id' => $photo->getId(),
                'contentUrl' => $contentUrl,
            ];
        }

        $userData = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'photo' => $photoData,
            'twoFactorEnabled' => $user->isTwoFactorEnabled(),
        ];

        return new JsonResponse($userData);
    }
}
