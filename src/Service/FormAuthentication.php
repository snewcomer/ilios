<?php

declare(strict_types=1);

namespace App\Service;

use App\Classes\SessionUserInterface;
use App\Entity\Manager\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\AuthenticationInterface as AuthenticationEntityInterface;
use App\Entity\Manager\AuthenticationManager;
use App\Traits\AuthenticationService;

class FormAuthentication implements AuthenticationInterface
{
    use AuthenticationService;

    /**
     * @var AuthenticationManager
     */
    protected $authManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    protected $encoder;
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var JsonWebTokenManager
     */
    protected $jwtManager;

    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var SessionUserProvider
     */
    protected $sessionUserProvider;

    /**
     * Constructor
     * @param AuthenticationManager $authManager
     * @param UserManager                    $userManager
     * @param UserPasswordEncoderInterface   $encoder
     * @param TokenStorageInterface          $tokenStorage
     * @param JsonWebTokenManager            $jwtManager
     * @param SessionUserProvider            $sessionUserProvider
     */
    public function __construct(
        AuthenticationManager $authManager,
        UserManager $userManager,
        UserPasswordEncoderInterface $encoder,
        TokenStorageInterface $tokenStorage,
        JsonWebTokenManager $jwtManager,
        SessionUserProvider $sessionUserProvider
    ) {
        $this->authManager = $authManager;
        $this->encoder = $encoder;
        $this->tokenStorage = $tokenStorage;
        $this->jwtManager = $jwtManager;
        $this->userManager = $userManager;
        $this->sessionUserProvider = $sessionUserProvider;
    }

    /**
     * Login a user using a username and password
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $username = null;
        $password = null;
        $content = $request->getContent();
        if (!empty($content)) {
            $arr = json_decode($content, true);
            if (array_key_exists('username', $arr)) {
                $username = $arr['username'];
            }
            if (array_key_exists('password', $arr)) {
                $password = $arr['password'];
            }
        }
        $code = JsonResponse::HTTP_OK;
        $errors = [];
        if (!$username) {
            $errors[] = 'missingUsername';
            $code = JsonResponse::HTTP_BAD_REQUEST;
        }
        if (!$password) {
            $errors[] = 'missingPassword';
            $code = JsonResponse::HTTP_BAD_REQUEST;
        }

        if ($username && $password) {
            $authEntity = $this->authManager->findAuthenticationByUsername($username);
            if ($authEntity) {
                $sessionUser = $this->sessionUserProvider->createSessionUserFromUser($authEntity->getUser());
                if ($sessionUser->isEnabled()) {
                    $passwordValid = $this->encoder->isPasswordValid($sessionUser, $password);
                    if ($passwordValid) {
                        $this->updatePassword($authEntity, $sessionUser, $password);
                        $jwt = $this->jwtManager->createJwtFromSessionUser($sessionUser);

                        return $this->createSuccessResponseFromJWT($jwt);
                    }
                }
            }
            $errors[] = 'badCredentials';
            $code = JsonResponse::HTTP_UNAUTHORIZED;
        }

        return new JsonResponse([
            'status' => 'error',
            'errors' => $errors,
            'jwt' => null,
        ], $code);
    }

    /**
     * Logout a user
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        return new JsonResponse([
            'status' => 'success'
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Update users to the new password encoding when they login
     * @param AuthenticationEntityInterface $authEntity
     * @param SessionUserInterface $sessionUser
     * @param string $password
     */
    protected function updatePassword(
        AuthenticationEntityInterface $authEntity,
        SessionUserInterface $sessionUser,
        $password
    ) {
        if ($this->encoder->needsRehash($sessionUser)) {
            $newPassword = $this->encoder->encodePassword($sessionUser, $password);
            $authEntity->setPasswordHash($newPassword);
            $this->authManager->update($authEntity);
        }
    }

    /**
     * @inheritdoc
     */
    public function getPublicConfigurationInformation(Request $request)
    {
        $configuration = [];
        $configuration['type'] = 'form';

        return $configuration;
    }

    /**
     * @inheritdoc
     */
    public function createAuthenticationResponse(Request $request): Response
    {
        return new Response();
    }
}
