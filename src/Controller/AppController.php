<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Repository\ProductsRepository;
use App\Services\Manipulations;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
     * @Route("/categories", name="categories", methods={"GET"})
     */
    public function categories()
    {
        return $this->render('app/categories.html.twig', [
            'indexes' => $this->cr->getCategories(),
        ]);
    }

    /**
     * @Route("/c/{category}", name="category", methods={"GET"})
     */
    public function category(string $category, PaginatorInterface $paginator, Request $request)
    {
        return $this->render('app/category.html.twig', [
            'products' => $paginator->paginate($this->cr->findOneBy(['name' => $category])->getProducts(), $request->query->getInt("page", 1), 15),
        ]);
    }

    /**
     * @Route("/search", name="search", methods={"GET"})
     */
    public function search(Request $request, PaginatorInterface $paginator)
    {
        $query = $request->query->get("query");
        return $this->render('app/queried.html.twig', [
            'results' => ['categories' => $this->cr->getQueried($query), 'products' => $paginator->paginate($this->pr->getQueried($query), $request->query->getInt("page", 1), 15)],
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
        $bag = $this->session->get('bag');
        $index = Manipulations::findIndex($bag, $id);
        if ($index !== null) {
            unset($bag[$index]);
            $this->session->set('bag', $bag);
            return new Response("Removed");
        }

        return new Response("Item not found");
    }

    /**
     * @Route("/remove-batch", methods={"POST"})
     */
    public function removeBatchElements(Request $request)
    {

        // TODO
        $ids = $request->request->get("items");

        return new Response();
    }
}
