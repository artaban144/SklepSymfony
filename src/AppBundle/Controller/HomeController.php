<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Service\CategoryTree;
use AppBundle\Service\CategoryChildrens;
use Symfony\Component\HttpFoundation\JsonResponse;

class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request, CategoryTree $categoryTreeMaker)
    {
        $products = $this->getDoctrine()
        ->getRepository(Product::class)
        ->findAll();

        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 12)
        );

        $categories = $categoryTreeMaker->getCategoryTree();

        return $this->render('home/index.html.twig', array(
            'products' => $result,
            'cart' => $this->get('session')->get('items'),
            'categories' => $categories,
        ));
    }

    /**
     * @Route("/product/{id}", name="product_show")
     */
    public function showAction($id)
    {
        $product = $this->getDoctrine()
        ->getRepository(Product::class)
        ->find($id);

        $properties = explode("#", $product->getProperties());

        return $this->render('product/product.show.html.twig', array(
            'product' => $product,
            'properties' => $properties,
        ));
    }

    /**
     * @Route("/category/{id}", name="category_list")
     */
    public function listAction($id, Request $request, CategoryTree $categoryTreeMaker, CategoryChildrens $categoryChildrensProducts)
    {
      print($id."<br>");
        if ($request->request->get('id')) {

            $products = $categoryChildrensProducts->getCategoryChildrens($id);
            var_dump($products);

            // var_dump($request->request->get('id'));
            var_dump("resr");
            // $id = $request->request->get('id');
            //
            // $products = $this->getDoctrine()
            // ->getRepository(Product::class)
            // ->findByCategoryId($id);

            //make something curious, get some unbelieveable data
            $arrData = ['output' => $products];
            return new JsonResponse($arrData);
        }
        var_dump("resr");

        $products = $this->getDoctrine()
        ->getRepository(Product::class)
        ->findByCategoryId($id);

        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 12)
        );

        $categories = $categoryTreeMaker->getCategoryTree();

        return $this->render('home/index.html.twig', array(
            'products' => $result,
            'cart' => $this->get('session')->get('items'),
            'categories' => $categories,
        ));
    }
}
