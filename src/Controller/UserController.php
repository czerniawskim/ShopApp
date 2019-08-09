<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UsersRepository;
use App\Entity\Users;
use App\Entity\UserDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserController extends AbstractController
{
    function remove_emoji($string)
    {
        // Match Emoticons
        $regex_emoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clear_string = preg_replace($regex_emoticons, '', $string);
    
        // Match Miscellaneous Symbols and Pictographs
        $regex_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clear_string = preg_replace($regex_symbols, '', $clear_string);
    
        // Match Transport And Map Symbols
        $regex_transport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clear_string = preg_replace($regex_transport, '', $clear_string);
    
        // Match Miscellaneous Symbols
        $regex_misc = '/[\x{2600}-\x{26FF}]/u';
        $clear_string = preg_replace($regex_misc, '', $clear_string);
    
        // Match Dingbats
        $regex_dingbats = '/[\x{2700}-\x{27BF}]/u';
        $clear_string = preg_replace($regex_dingbats, '', $clear_string);
    
        return $clear_string;
    }


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

            $exist=$uR->findBy(['Username'=>$data['Username']]);

            if($exist && $exist[0]->getPassword() === $data['Password'])
            {
                $session->set('user', $exist[0]);

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
    public function register(SessionInterface $session, Request $request, EntityManagerInterface $em, UsersRepository $uR)
    {
        $logged=$session->get('user');
        if($logged)
        {
            return $this->redirectToRoute('homepage', []);
        }

        $register=$this->createFormBuilder()
        ->add('Username', TextType::class, [
            'attr'=>[
                'placeholder'=>'Username'
            ]
        ])
        ->add('Password', RepeatedType::class,[
            'type'=>PasswordType::class,
            'first_options'  => ['attr' => ['placeholder' => 'Password']],
            'second_options' => ['attr' => ['placeholder' => 'Repeat password']]
        ])
        ->add('Email', TextType::class, [
            'attr'=>[
                'placeholder'=>'E-mail'
            ]
        ])
        ->add('Register', SubmitType::class)
        ->getForm();

        $register->handleRequest($request);
        if($register->isSubmitted() && $register->isValid())
        {
            $data=$register->getData();

            $usrname=$this->remove_emoji($data['Username']);
            $mail=$this->remove_emoji($data['Email']);
            
            $exist=$uR->findBy(['Username'=>$usrname]);
            $taken=$uR->findBy(['Username'=>$mail]);

            if(!$exist && !$taken)
            {
                $details=new UserDetails();
                $details->setJoinDate(new \DateTime());

                $em->persist($details);
                $em->flush();

                $user=new Users();
                $user->setUsername($data['Username']);
                $user->setPassword($data['Password']);
                $user->setEmail($data['Email']);
                $user->setResetPass(md5(uniqid()));
                $user->setDetails($details);

                $em->persist($user);
                $em->flush();

                $this->addFlash('success','Your account has been created');

                return $this->redirectToRoute('login', []);
            }
            else
            {
                $this->addFlash('danger', 'This e-mail or username is already taken');
            }
        }
        else if($register->isSubmitted() && !$register->isValid())
        {
            $this->addFlash('danger','Passwords are not matching');
        }


        return $this->render('user/register.html.twig', [
            'register'=>$register->createView()
        ]);
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
