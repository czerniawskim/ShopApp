<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class LoginAuthenticator extends AbstractFormLoginAuthenticator
{
    private $ur;
    private $router;
    private $csrf;
    private $encoder;

    public function __construct(UserRepository $ur, RouterInterface $router, CsrfTokenManagerInterface $csrf, UserPasswordEncoderInterface $encoder)
    {
        $this->ur = $ur;
        $this->router = $router;
        $this->csrf = $csrf;
        $this->encoder = $encoder;
    }

    public function supports(Request $request)
    {
        return $request->attributes->get('_route') == "login" && $request->isMethod("POST");
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'username' => $request->get('username'),
            'password' => $request->get('password'),
            'csrf' => $request->get('csrf')
        ];

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['username']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf']);

        if (!$this->csrf->isTokenValid($token)) throw new InvalidCsrfTokenException();

        return $this->ur->findOneBy(['username' => $credentials['username']]);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->encoder->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return new RedirectResponse($this->router->generate("home"));
    }

    public function getLoginUrl()
    {
        return $this->router->generate('login');
    }
}
