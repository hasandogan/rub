<?php
namespace App\Controller;

use App\Service\Auth\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{

    #[Route('/auth/register', name: 'auth_register', methods: ['POST'])]
    public function register(Request $request, AuthService $authService): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $user = $authService->register($content);
        return new JsonResponse([
            'success'=>true,
            'accessToken'=>$user->getAccessToken(),
            'refreshToken'=>$user->getRefreshToken()
        ]);
    }

    #[Route('/auth/login', name: 'auth_login', methods: ['POST', 'HEAD'])]
    public function login(Request $request, AuthService $authService): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $user =  $authService->login($content);
        return new JsonResponse([
            'success'=>true,
            'accessToken'=>$user->getAccessToken(),
            'refreshToken'=>$user->getRefreshToken()
        ]);
    }
}