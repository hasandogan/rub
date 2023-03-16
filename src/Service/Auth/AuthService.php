<?php

namespace App\Service\Auth;

use App\Entity\User;
use App\Service\Auth\Constraint\AuthConstraint;
use App\Service\Validation\ValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class AuthService
{
    private EncryptionService $encryptionService;
    private TokenService $tokenService;
    private ValidationService $validationService;
    private EntityManagerInterface $entityManager;

    /**
     * AuthService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, ValidationService $validationService, TokenService $tokenService, EncryptionService $encryptionService)
    {
        $this->entityManager = $entityManager;
        $this->validationService = $validationService;
        $this->tokenService = $tokenService;
        $this->encryptionService = $encryptionService;
    }

    /**
     * @param $userData
     * @return User
     */
    public function register($userData): User
    {
        try {
            $this->validationService->validate($userData, AuthConstraint::registerRules());

            $user = new User();
            $user->setUsername($userData['username']);
            $user->setEmail($userData['email']);
            $user->setPassword($userData['password']);
            $user->setFirstName($userData['firstname'] ?? null);
            $user->setLastName($userData['lastname']);
            return $this->upsert($user);
        } catch (\Exception $exception) {
            dd($exception);
        }

    }

    /**
     * @param $authorizationToken
     * @return User
     */
    public function authorize($authorizationToken): User
    {

        $token = $this->tokenService->getJwtOnAuthorizationHeader($authorizationToken);
        $jwtToken = $this->encryptionService->decrypt($token);

        if(!$jwtToken) {
            throw new BadRequestException("Token not valid.");
        }

        $this->tokenService->validate($jwtToken);
        $publicKey = $this->tokenService->getJwtPayload($jwtToken, 'publicKey');
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(["accessToken"=>$token, 'publicKey' => $publicKey]);

        if ($user == null) {
            throw new BadRequestException("User not found.");
        }

        return $user;
    }

    /**
     * @param $content
     * @return User
     */
    public function login($content): User
    {
        $this->validationService->validate($content, AuthConstraint::loginRules());
        $user = $this->getUser($content['email'], $content['password']);
        return $this->upsert($user);
    }

    /**
     * @param $user
     * @return mixed
     */
    public function upsert($user) {
        $publicKey = $this->encryptionService->createPublicKey();
        $accessToken = $this->createToken($user, $publicKey);
        $refreshToken = $this->createToken($user, $publicKey);

        // Update User Key
        $user->setPublicKey($publicKey);
        $user->setAccessToken($accessToken);
        $user->setRefreshToken($refreshToken);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @param $user
     * @param $publicKey
     * @return false|string
     */
    private function createToken($user, $publicKey): bool|string
    {
        $token = $this->tokenService->generate([
            'username' => $user->getUsername(),
            'publicKey' => $publicKey
        ]);

        return $this->encryptionService->encrypt($token);
    }

    /**
     * @param $email
     * @param $password
     * @return User
     */
    public function getUser($email, $password): User
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email, 'password' => $password]);
        if ($user === null) {
            throw new BadRequestException("User not found");
        }

        return $user;
    }
}