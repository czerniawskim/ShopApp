<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoriesRepository;
use App\Repository\ProductsRepository;
use App\Repository\TagsRepository;
use App\Repository\DealsRepository;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/dash", name="dash")
     */
    public function dash()
    {
        return $this->render('admin/dash.html.twig', []);
    }

    /**
     * @Route("/admin/categories", name="categories")
     */
    public function categories(CategoriesRepository $cR)
    {
        $categories = $cR->findBy(array(), array('Name'=>'ASC'));

        return $this->render('admin/categories.html.twig', [
            'categories'=>$categories
        ]);
    }

    /**
     * @Route("/admin/products", name="products")
     */
    public function products(ProductsRepository $pR)
    {
        $products = $pR->findBy(array(), array('Name'=>'ASC'));

        return $this->render('admin/products.html.twig', [
            'products'=>$products
        ]);
    }

    /**
     * @Route("/admin/tags", name="tags")
     */
    public function tags(TagsRepository $tR)
    {
        $tags = $tR->findBy(array(), array('Name'=>'ASC'));

        return $this->render('admin/tags.html.twig', [
            'tags'=>$tags
        ]);
    }

    /**
     * @Route("/admin/deals", name="deals")
     */
    public function deals(DealsRepository $dR)
    {
        $deals = $dR->findBy(array(), array('doneAt'=>'DESC'));
        dump($deals);

        return $this->render('admin/deals.html.twig', [
            'deals'=>$deals
        ]);
    }
}
