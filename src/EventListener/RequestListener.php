<?php

namespace App\EventListener;

use App\Entity\User;
use App\Service\Auth\AuthService;
use App\Service\Auth\EncryptionService;
use App\Service\Auth\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Class RequestListener
 * @package App\EventListener
 */
class RequestListener
{

    private AuthService $authService;

    public function __construct(EntityManagerInterface $entityManager, AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $pathInfo = $event->getRequest()->getPathInfo();
        $whiteList = ['/auth/login', '/auth/register'];
        if (in_array($pathInfo, $whiteList)) {
            return;
        }

        $headers = $event->getRequest()->headers;
        if ($headers->get('authorization') === null || !str_contains($headers->get('authorization'), 'Bearer')) {
            throw new BadRequestException("Authorization failure");
        }

        // @TODO user'a erişmek istediğimde hem burası hem controller user authorize etmiş olacak. Buna bir çözüm..
        $this->authService->authorize($headers->get('authorization'));
    }

}