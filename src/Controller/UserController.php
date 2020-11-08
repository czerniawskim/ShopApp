<?php

namespace App\Controller;

use App\Form\RegisterType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\LogoutException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/u/login", name="login", methods={"GET", "POST"})
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        return $this->render('user/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'username' => $authenticationUtils->getLastUsername(),
        ]);
    }

    /**
     * @Route("/u/register", name="register", methods={"GET", "POST"})
     */
    public function register(Request $request, string $error = "", UserRepository $ur, UserPasswordEncoderInterface $encoder)
    {
        $register = $this->createForm(RegisterType::class);
        $register->handleRequest($request);

        if ($register->isSubmitted() && $register->isValid()) {
            $user = $register->getData();

            if (filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
                if (!$ur->checkRegister($user->getUsername(), $user->getEmail())) {
                    $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
                    $user->setRoles(["User"]);

                    $this->em->persist($user);
                    $this->em->flush();

                    return $this->redirectToRoute('login', []);
                } else {
                    $error = "Username or e-mail is taken";
                }
            } else {
                $error = "E-mail is not valid";
            }
        }

        return $this->render('user/register.html.twig', [
            'register' => $register->createView(),
            'error' => $error,
        ]);
    }

    /**
     * @Route("/u/logout", name="logout", methods={"GET"})
     */
    public function logout()
    {
        if ($this->isGranted("USER")) {
            return $this->redirectToRoute('home', []);
        }
        throw new LogoutException("You are not logged in");
    }
}
