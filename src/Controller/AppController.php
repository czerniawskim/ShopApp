<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage()
    {
        return $this->render('app/homepage.html.twig', []);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact()
    {

        return $this->render('app/contact.html.twig', []);
    }
}
