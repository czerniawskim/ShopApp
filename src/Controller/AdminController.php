<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CategoriesRepository;
use App\Repository\ProductsRepository;
use App\Repository\TagsRepository;
use App\Repository\DealsRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Tags;
use App\Entity\Products;
use App\Entity\Categories;

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
     * @Route("/admin/category/new", name="newCat")
     */
    public function newCat(EntityManagerInterface $em, Request $request)
    {
        $new = $this->createFormBuilder()
        ->add('Name', TextType::class, ['attr'=>['placeholder'=>'Category name']])
        ->add('Tags', EntityType::class, [
            'class'=>Tags::class,
            'choice_label'=>'Name',
            'expanded'=>true,
            'multiple'=>true
        ])
        ->add('Proceed', SubmitType::class)
        ->getForm();

        $new->handleRequest($request);

        if($new->isSubmitted() && $new->isValid())
        {
            $data=$new->getData();

            $category = new Categories();
            $category->setName(strtolower($data['Name']));
            foreach($data['Tags'] as $tag)
            {
                $category->addTag($tag);
            }

            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Category has been created');

            return $this->redirectToRoute('categories', []);
        }

        return $this->render('admin/new/cat.html.twig', [
            'new'=>$new->createView()
        ]);
    }

    /**
     * @Route("/admin/category/remove/{id}", name="remCat")
     */
    public function remCat($id, EntityManagerInterface $em, CategoriesRepository $cR, ProductsRepository $pR)
    {
        $cat=$cR->findBy(['id'=>$id])[0];
        // Remove products that belonged to this category
        $prods = $pR->findBy(['Category'=>$cat]);
        foreach ($prods as $prod) {
            $em->remove($prod);
            $em->flush();
        }

        $em->remove($cat);
        $em->flush();

        $this->addFlash('success', 'Category has been removed');

        return $this->redirectToRoute('categories', []);
    }

    /**
     * @Route("/admin/category/edit/{id}", name="editCat")
     */
    public function editCat($id, CategoriesRepository $cR, EntityManagerInterface $em, Request $request)
    {
        $cat=$cR->findBy(['id'=>$id])[0];

        $edit=$this->createFormBuilder()
        ->add('Name',TextType::class, [
            'attr'=>['placeholder'=>'Category name', 'value'=>$cat->getName()]
        ])
        ->add('Tags',EntityType::class, [
            'class'=>Tags::class,
            'choice_label'=>'Name',
            'expanded'=>true,
            'multiple'=>true
        ])
        ->add('Submit',SubmitType::class)
        ->getForm();

        $edit->handleRequest($request);
        if($edit->isSubmitted() && $edit->isValid())
        {
            $data=$edit->getData();
            $cat->setName($data['Name']);
            $cat->clearTags();
            foreach($data['Tags'] as $tag)
            {
                $cat->addTag($tag);
            }
            $em->flush();
            $this->addFlash('success', 'Category has been edited');
            return $this->redirectToRoute('categories', []);
        }

        return $this->render('admin/editCat.html.twig', [
            'edit'=>$edit->createView(),
            'cat'=>$cat
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
     * @Route("/admin/product/new", name="newProd")
     */
    public function newProd(EntityManagerInterface $em, Request $request)
    {
        $new = $this->createFormBuilder()
        ->add('Name', TextType::class, ['attr'=>['placeholder'=>'Product name']])
        ->add('Price', NumberType::class, ['attr'=>['placeholder'=>'0']])
        ->add('Description', TextareaType::class, ['attr'=>['placeholder'=>'Product description', 'title'=>'Markdown supported'], 'required'=>false])
        ->add('Link', TextType::class, ['attr'=>['placeholder'=>'Product gallery'], 'required'=>false])
        ->add('Category', EntityType::class,[
            'class'=>Categories::class,
            'choice_label'=>'Name'
        ])
        ->add('Img', FileType::class, [
            'constraints' => [
                new File([
                    'maxSize' => '5120k',
                    'mimeTypes' => [
                        'image/jpg',
                        'image/jpeg',
                        'image/png',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid image file',
                ])
            ],
        ])
        ->add('Proceed', SubmitType::class)
        ->getForm();

        $new->handleRequest($request);

        if($new->isSubmitted() && $new->isValid())
        {
            $data=$new->getData();

            $product = new Products();

            if($data['Img'])
            {
                $info = getimagesize($data['Img']);
                $x = $info[0];
                $y = $info[1];
                
                if ($x === $y) {
                    $image = md5(uniqid()).$data['Img']->guessExtension();
            
                    try{
                        $data['Img']->move(
                            'prodsImgs',
                            $image
                        );
                    }
                    catch(FileException $e){
                        $this->addFlash('danger', 'Something went wrong during sending image. Try again');
                        return $this->redirectToRoute('newProd', []);
                    }
                } else {
                    $this->addFlash('danger', 'Image width and height must be equal');

                    return $this->redirectToRoute('newProd', []);
                }
                
            }

            $product->setName(strtolower($data['Name']));
            $product->setPrice($data['Price']);
            $product->setDescription($data['Description']);
            $product->setGalleryLink($data['Link']);
            $product->setImage($image);
            $product->setCategory($data['Category']);

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Product has been successfully added');

            return $this->redirectToRoute('products', []);
        }

        return $this->render('admin/new/prod.html.twig', [
            'new'=>$new->createView()
        ]);
    }

    /**
     * @Route("/admin/product/remove/{id}", name="remProd")
     */
    public function remProd($id, EntityManagerInterface $em, ProductsRepository $pR)
    {
        $prod=$pR->findBy(['id'=>$id])[0];
        $em->remove($prod);
        $em->flush();

        $this->addFlash('success', 'Product has been removed');

        return $this->redirectToRoute('products', []);
    }

    /**
     * @Route("/admin/product/edit/{id}", name="editProd")
     */
    public function editProd($id, ProductsRepository $pR, EntityManagerInterface $em, Request $request)
    {
        $prod=$pR->findBy(['id'=>$id])[0];

        $edit=$this->createFormBuilder()
        ->add('Name',TextType::class, [
            'attr'=>['placeholder'=>'Category name', 'value'=>$prod->getName()]
        ])
        ->add('Price', NumberType::class, [
            'attr'=>['placeholder'=>'Product price', 'value'=>$prod->getPrice()]
        ])
        ->add('Gallery', TextType::class, [
            'attr'=>['placeholder'=>'Product gallery', 'value'=>$prod->getGalleryLink()],
            'required'=>false
        ])
        ->add('Category', EntityType::class, [
            'class'=>Categories::class,
            'choice_label'=>'Name',
            'data'=>$prod->getCategory()
        ])
        ->add('Desc', TextareaType::class, [
            'attr'=>['placeholder'=>'Product description', 'value'=>$prod->getDescription()],
            'required'=>false
        ])
        ->add('Submit',SubmitType::class)
        ->getForm();

        $edit->handleRequest($request);
        if($edit->isSubmitted() && $edit->isValid())
        {
            $data=$edit->getData();
            $prod->setName($data['Name']);
            $prod->setPrice($data['Price']);
            $prod->setGalleryLink($data['Gallery']);
            $prod->setCategory($data['Category']);
            $prod->setDescription($data['Desc']);

            $em->flush();

            $this->addFlash('success', 'Product has been edited');
            return $this->redirectToRoute('products', []);
        }

        return $this->render('admin/editProd.html.twig', [
            'edit'=>$edit->createView(),
            'prod'=>$prod
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
     * @Route("/admin/tag/new", name="newTag")
     */
    public function newTag(EntityManagerInterface $em, Request $request)
    {
        $new = $this->createFormBuilder()
        ->add('Name', TextType::class, ['attr'=>['placeholder'=>'Tag name']])
        ->add('Proceed', SubmitType::class)
        ->getForm();

        $new->handleRequest($request);

        if($new->isSubmitted() && $new->isValid())
        {
            $name=$new->getData()['Name'];

            $tag = new Tags();
            $tag->setName(strtolower($name));

            $em->persist($tag);
            $em->flush();

            $this->addFlash('success', 'Tag has been successfully added');
            return $this->redirectToRoute('tags', []);
        }

        return $this->render('admin/new/tag.html.twig', [
            'new'=>$new->createView()
        ]);
    }

    /**
     * @Route("/admin/tag/remove/{id}", name="remTag")
     */
    public function remTag($id, EntityManagerInterface $em, TagsRepository $tR)
    {
        $tag=$tR->findBy(['id'=>$id])[0];
        $em->remove($tag);
        $em->flush();

        $this->addFlash('success', 'Tag has been removed');

        return $this->redirectToRoute('tags', []);
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
