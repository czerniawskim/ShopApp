<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use App\Form\SearchType;
use App\Repository\ProductsRepository;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage()
    {
        $search = $this->createForm(SearchType::class);

        if($search->isSubmitted() && $search->isValid())
        {
            return $this->redirectToRoute('search', ['query'=>$data]);
        }

        return $this->render('app/homepage.html.twig', [
            'search'=>$search->createView()
        ]);
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

    /**
     * @Route("/search/{query}", name="search")
     */
    public function search($query, ProductsRepository $pR)
    {
        $search = $this->createForm(SearchType::class);

        if($search->isSubmitted() && $search->isValid())
        {
            return $this->redirectToRoute('search', ['query'=>$data]);
        }

        $results=$pR->findBy(['Name'=>$query]);
        
        return $this->render('app/search.html.twig', [
            'results'=>$results,
            'search'=>$search->createView()
        ]);
    }
}
