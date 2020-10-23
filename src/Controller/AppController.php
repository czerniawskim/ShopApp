<?php

namespace App\Controller;

use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    private $pr;
    private $em;

    public function __construct(ProductsRepository $pr, EntityManagerInterface $em)
    {
        $this->pr = $pr;
        $this->em = $em;
    }

    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function home()
    {
        return $this->render('app/home.html.twig', [
            'data' => $this->pr->getHomeData()
        ]);
    }
}
