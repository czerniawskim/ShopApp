<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    private $pr;
    private $cr;
    private $em;
    private $session;

    public function __construct(ProductsRepository $pr, CategoriesRepository $cr, EntityManagerInterface $em, SessionInterface $session)
    {
        $this->pr = $pr;
        $this->cr = $cr;
        $this->em = $em;
        $this->session = $session;
    }

    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function home()
    {
        return $this->render('app/home.html.twig', [
            'data' => $this->pr->getHomeData(),
        ]);
    }

    /**
     * @Route("/product/{id}", name="product", methods={"GET"})
     */
    public function product(int $id)
    {
        return $this->render('app/product.html.twig', [
            'product' => $this->pr->findOneBy(['id' => $id]),
        ]);
    }

    /**
     * @Route("/cart", name="cart", methods={"GET"})
     */
    public function cart()
    {
        return $this->render('app/cart.html.twig', [
            'bag' => $this->session->get('bag'),
        ]);
    }

    /**
     * @Route("/update-cart", methods={"POST"})
     */
    public function updateCart(Request $request)
    {
        // Get new added elements
        $new = json_decode($request->request->get('bag'), true);

        // Define session if not existing and bind data to variable
        if (!$this->session->get('bag')) {
            $this->session->set('bag', []);
        }

        $bag = $this->session->get('bag');

        foreach ($bag as &$elem) {
            if ($elem['product']->getId() == $new['id']) {
                $elem['amount'] = $elem['amount'] + $new['amount'];
                $this->session->set('bag', $bag);

                return new Response("Updated");
            }
        }
        $bag[] = [
            'product' => $this->pr->findOneBy(['id' => $new['id']]),
            'amount' => (int) $new['amount'],
        ];
        $this->session->set('bag', $bag);
        return new Response("Added");
    }

    /**
     * @Route("/remove-bag-element", methods={"POST"})
     */
    public function removeBagElement(Request $request)
    {
        (int) $id = $request->request->get("item");
        return new Response("Id: $id");
        /* $bag = $this->session->get('bag');
    $index = $this->findIndex($bag, $id);
    if ($index !== null) {
    unset($bag[$index]);
    return new Response("Removed");
    }

    return new Response("Item not found"); */
    }

    /**
     * @Route("/remove-batch", methods={"POST"})
     */
    public function removeBatchElements(Request $request)
    {
        $ids = $request->request->get("items");

        return new Response();
    }

    private function findIndex(array $bag, int $id)
    {
        foreach ($bag as $index => $b) {
            if ($b->getId() === $id) {
                return $index;
            }
        }

        return null;
    }
}
