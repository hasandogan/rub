<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Auth\AuthService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;

class BaseController extends AbstractController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'auth_service' => '?'.AuthService::class,
            'doctrine' => '?'.ManagerRegistry::class,
        ]);
    }

    /**
     * @return User
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getCurrentUser(): User {
        /** @var RequestStack $requestStack */
        $requestStack = $this->container->get('request_stack');
        $authService = $this->container->get('auth_service');

        return $authService->authorize($requestStack->getCurrentRequest()->headers->get('authorization'));
    }

}