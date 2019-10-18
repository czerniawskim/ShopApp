<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\SearchType;
use App\Form\RatingType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ProductsRepository;
use App\Entity\Opinions;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Faker\Factory;
use App\Repository\CategoriesRepository;
use App\Entity\Products;

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
        // Get recommended items by
        // Generating pseudo-random number
        // Check if the product is already taken
        // If yes pass, if no insert product into array
        $size = $pR->getSize();
        if(!$size)
        {
            $recom = null;
        }
        else{
            $recom = array();
            $used = array();
            $last = $pR->getLast();
            $last = $last[0]['id'];
            if ($size > 15) {
                while(sizeof($recom) != 15) { 
                    $rand = random_int(1, $last);
                    if(!in_array($rand, $used))
                    {
                        $prod = $pR->findBy(['id'=>$rand]);
                        if($prod)
                        {
                            $recom[] = $prod[0];
                        }
                    }
                    $used[] = $rand;
                }
            } else {
                while(sizeof($recom) != $size) { 
                    $rand = random_int(1, $last);
                    if(!in_array($rand, $used))
                    {
                        $prod = $pR->findBy(['id'=>$rand]);
                        if($prod)
                        {
                            $recom[] = $prod[0];
                        }
                    }
                    $used[] = $rand;
                }
            }
            
        }

        return $this->render('app/homepage.html.twig', [
            'search'=>$search->createView(),
            'recoms'=>$recom
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
    public function search($query, ProductsRepository $pR, Request $request, PaginatorInterface $paginator)
    {
        $search = $this->createForm(SearchType::class);

        $search->handleRequest($request);
        if($search->isSubmitted() && $search->isValid())
        {
            $data=$search->getData()['Query'];
            return $this->redirectToRoute('search', ['query'=>strtolower($data)]);
        }
        
        $results=$pR->findProducts($query);

        foreach($results as &$res)
        {
            $res['image'] = stream_get_contents($res['image']);
        }
        
        return $this->render('app/search.html.twig', [
            'search'=>$search->createView(),
            'query'=>$query,
            'pagination'=>$paginator->paginate($results, $request->query->getInt('page', 1), 6)
        ]);
    }

    /**
     * @Route("/product/{id}", name="product")
     */
    public function product($id, ProductsRepository $pR, EntityManagerInterface $em, Request $request, SessionInterface $session)
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
        ->add('Quantity', NumberType::class,['data'=>1, 'attr'=>['min'=>1,'class'=>'quantity', 'autocomplete'=>"off"]])
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
                    foreach($cart as $key =>$value)
                    {
                        if ($value['prod'] == $prod->getName()) {
                            $cart[$key]['quant'] = $value['quant'] + $elem['quant'];
                            $cart[$key]['sum'] = $value['sum'] + $elem['sum'];
                            
                            // Not sure why but its finally works
                            $session->set('cart',$cart);
                            return $this->redirectToRoute('cart', []);
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

        $rating = $this->createForm(RatingType::class);

        $rating->handleRequest($request);
        if($rating->isSubmitted() && $rating->isValid())
        {
            $data=$rating->getData();
            $user=$session->get('user');

            $opinion = new Opinions();
            $opinion->setRate($data->getRate());
            $opinion->setDescription($data->getDescription());
            $opinion->setProducts($prod);
            $opinion->setUser($user);

            $em->merge($opinion);
            $em->flush();

            return $this->redirectToRoute('product', ['id'=>$id]);
        }

        return $this->render('app/product.html.twig', [
            'prod'=>$prod,
            'search'=>$search->createView(),
            'add'=>$add->createView(),
            'rating'=>$rating->createView()
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
            'search'=>$search->createView(),
            'cart'=>$cart
        ]);
    }

    /**
     * @Route("/cart/remove/{id}", name="remCart")
     */
    public function remCart($id, SessionInterface $session)
    {
        $cart = $session->get('cart');
        unset($cart[$id]);
        $session->remove('cart');
        $session->set('cart',$cart);
        return $this->redirectToRoute('cart', []);
    }

    /**
     * @Route("/cart/clear", name="clearCart")
     */
    public function clearCart(SessionInterface $session)
    {
        $session->remove('cart');

        return $this->redirectToRoute('cart', []);
    }
}
