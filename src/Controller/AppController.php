<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\SearchType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ProductsRepository;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage(Request $request, ProductsRepository $pR)
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
     * @Route("/product/{id}", name="product")
     */
    public function product($id, ProductsRepository $pR, Request $request, SessionInterface $session)
    {
        $search = $this->createForm(SearchType::class);

        $search->handleRequest($request);
        if($search->isSubmitted() && $search->isValid())
        {
            $data=$search->getData()['Query'];
            return $this->redirectToRoute('search', ['query'=>strtolower($data)]);
        }

        $prod = $pR->findBy(['id'=>$id])[0];

        $add = $this->createFormBuilder()
        ->add('Quantity', NumberType::class,['data'=>1, 'attr'=>['min'=>1,'class'=>'quantity']])
        ->add('Add', SubmitType::class,['attr'=>['class'=>'cart-add']])
        ->getForm();

        $add->handleRequest($request);

        if($add->isSubmitted() && $add->isValid())
        {
            $quant=$add->getData()['Quantity'];

            if ($quant > 0) {
                $sum=$prod->getPrice() * $quant;

                $elem=array("prod"=>$prod->getName(), "quant"=>$quant, "sum"=>$sum);

                $cart = $session->get('cart');
                if($cart != null)
                {
                    foreach($cart as $c)
                    {
                        if ($c['prod'] == $prod->getName()) {
                            $c['quant'] = $c['quant'] + $elem['quant'];
                            $c['sum'] = $c['sum'] + $elem['sum'];
                            
                            //TODO: Updating session object
                        } else {
                            $cart[] = $elem;
                            $session->set('cart', $cart);

                            return $this->redirectToRoute('cart', []);
                        }
                        
                    }
                }
                else
                {
                    $cart[] = $elem;
                    $session->set('cart', $cart);

                    return $this->redirectToRoute('cart', []);
                }
            } else {
                $this->addFlash('danger', 'Wrong quantity');
                return $this->redirectToRoute('product', ['id'=>$id]);
            }
            
        }
        return $this->render('app/product.html.twig', [
            'prod'=>$prod,
            'search'=>$search->createView(),
            'add'=>$add->createView()
        ]);
    }

    /**
     * @Route("/cart", name="cart")
     */
    public function cart(SessionInterface $session, Request $request)
    {
        $search = $this->createForm(SearchType::class);

        $search->handleRequest($request);
        if($search->isSubmitted() && $search->isValid())
        {
            $data=$search->getData()['Query'];
            return $this->redirectToRoute('search', ['query'=>strtolower($data)]);
        }

        $cart = $session->get('cart');
        dump($cart);
        return $this->render('app/cart.html.twig', [
            'search'=>$search->createView()
        ]);
    }
}
