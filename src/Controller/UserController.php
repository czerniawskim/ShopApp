<?php

namespace App\Controller;

use App\Form\RegisterType;
use App\Repository\UserRepository;
use App\Services\Manipulations;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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
        if ($this->getUser()) {
            return $this->redirectToRoute('home', []);
        }

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
        if ($this->getUser()) {
            return $this->redirectToRoute('home', []);
        }

        $register = $this->createForm(RegisterType::class);
        $register->handleRequest($request);

        if ($register->isSubmitted() && $register->isValid()) {
            $user = $register->getData();

            if (filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
                if (!$ur->checkRegister($user->getUsername(), $user->getEmail())) {
                    $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
                    $user->setRoles(["ROLE_USER"]);
                    $user->setAddress(Manipulations::fixAddress($user->getAddress()));

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
        return $this->redirectToRoute('home', []);
    }

    /**
     * @Route("/profile/{username}", name="profile", methods={"GET"})
     */
    public function profile(string $username)
    {
        if ($username !== $this->getUser()->getUsername()) {
            return $this->redirectToRoute('profile', ['username' => $this->getUser()->getUsername()]);
        }

        return $this->render('user/profile.html.twig', []);
    }

    /**
     * @Route("/add-to-favs", methods={"POST"})
     */
    public function addToFavs(Request $request)
    {
        return new Response();
    }

    /**
     * @Route("/remove-from-favs", methods={"POST"})
     */
    public function removeFromFavs(Request $request)
    {
        return new Response();
    }
}
