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

    /**
     * @Route("/branches", name="branches")
     */
    public function branches()
    {
        $branches = [
            'Warsaw'=>[
                'Jagiellońska 35',
                'Marszałkowska 75'
            ],
            'Cracow'=>[
                'Królewska 2',
                'Aleja Pokoju 234'
            ],
            'New York'=>[
                '342 Rogers Ave',
                '3453 Ralph Ave'
            ]
        ];

        $openH=[
            'Monday'=>[
                '9:00',
                '21:00'
            ],
            'Tuesday'=>[
                '9:00',
                '21:00'
            ],
            'Wednesday'=>[
                '9:00',
                '21:00'
            ],
            'Thursday'=>[
                '9:00',
                '21:00'
            ],
            'Friday'=>[
                '9:00',
                '21:00'
            ],
            'Saturday'=>[
                '10:00',
                '17:00'
            ],
            'Sunday'=>[
                'Closed'
            ]
        ];

        return $this->render('app/branches.html.twig', [
            'branches'=>$branches,
            'hours'=>$openH
        ]);
    }
}
