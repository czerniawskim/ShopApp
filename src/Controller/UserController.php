<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    /**
     * @Route("/u/login", name="login", methods={"GET", "POST"})
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        return $this->render('user/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'username' => $authenticationUtils->getLastUsername()
        ]);
    }

    /**
     * @Route("/u/logout", name="logout", methods={"GET"})
     */
    public function logout()
    {
        return $this->redirectToRoute('home', []);
    }
}
