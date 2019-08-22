<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\SearchType;
use App\Repository\ProductsRepository;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage(Request $request)
    {
        $search = $this->createForm(SearchType::class);

        $search->handleRequest($request);
        if($search->isSubmitted() && $search->isValid())
        {
            $data=$search->getData()['Query'];
            return $this->redirectToRoute('search', ['query'=>strtolower($data)]);
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
    public function search($query, ProductsRepository $pR, Request $request)
    {
        $search = $this->createForm(SearchType::class);

        $search->handleRequest($request);
        if($search->isSubmitted() && $search->isValid())
        {
            $data=$search->getData()['Query'];
            return $this->redirectToRoute('search', ['query'=>strtolower($data)]);
        }

        $results=$pR->findBy(['Name'=>$query]);
        
        return $this->render('app/search.html.twig', [
            'results'=>$results,
            'search'=>$search->createView(),
            'query'=>$query
        ]);
    }

    /**
     * @Route("/cart", name="cart")
     */
    public function cart()
    {
        return $this->render('app/cart.html.twig', []);
    }
}
