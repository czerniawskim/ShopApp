<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UsersRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserController extends AbstractController
{
    /**
     * @Route("/u/login", name="login")
     */
    public function login(SessionInterface $session, Request $request, UsersRepository $uR)
    {
        $logged=$session->get('user');
        if($logged)
        {
            return $this->redirectToRoute('homepage', []);
        }

        $login=$this->createFormBuilder()
        ->add('Username', TextType::class, [
            'attr'=>[
                'placeholder'=>'Username'
            ]
        ])
        ->add('Password', PasswordType::class, [
            'attr'=>[
                'placeholder'=>'Password'
            ]
        ])
        ->add('Login', SubmitType::class)
        ->getForm();

        $login->handleRequest($request);

        if($login->isSubmitted() && $login->isValid())
        {
            $data=$login->getData();

            $exist=$uR->findBy(['Username'=>$data['Username']])[0];

            if($exist && $exist->getPassword() === $data['Password'])
            {
                $session->set('user', $exist);

                return $this->redirectToRoute('homepage', []);
            }
            else
            {
                $this->addFlash('danger', 'There is no such user or password is incorrect');
            }
        }

        return $this->render('user/login.html.twig', [
            'login'=>$login->createView()
        ]);
    }

    /**
     * @Route("/u/register", name="register")
     */
    public function register(SessionInterface $session)
    {
        $logged=$session->get('user');
        if($logged)
        {
            return $this->redirectToRoute('homepage', []);
        }

        return $this->render('user/register.html.twig', []);
    }

    /**
     * @Route("/u/logout", name="logout")
     */
    public function logout(SessionInterface $session)
    {
        $logged=$session->get('user');
        if(!$logged)
        {
            return $this->redirectToRoute('login', []);
        }

        $session->remove('user');

        $this->addFlash('success', 'You were successfully logged out');

        return $this->redirectToRoute('homepage', []);
    }
}
