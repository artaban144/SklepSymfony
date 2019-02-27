<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\Realization;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class CartController extends Controller
{
    /**
     * @Route("/cart", name="cart")
     */
    public function indexAction(Request $request)
    {

        if(! $this->get('session')){
            $session = new Session();
        }
        else{
            $session = $this->get('session');
        }

             $cart = $session->get('items');
             $cartArray = json_decode($cart, true);

             foreach($cartArray as $index => $cartItem){
                $product = $this->getDoctrine()
                ->getRepository(Product::class)
                ->find($cartItem['id']);
                $cartArray[$index]['name'] = $product->getName();
                $cartArray[$index]['price'] = $product->getPrice();
             }


        return $this->render('cart/cart.html.twig', array(
            'cart' => $cartArray,
        ));
    }
    /**
    * @Route("/cart/add-to-cart/{id}", name="add_to_cart")
     *
    */
public function addAction($id, Request $request){

    if(! $this->get('session')){
        $session = new Session();
    }
    else{
        $session = $this->get('session');
    }

    // $session->set('items', "");

    $cart = $session->get('items');

    if($cart == null) {
        $cartArray = array(array('id' => $id, 'count' => 1));
    }
    else {
        $cartArray = json_decode($cart, true);
        $isDuplicated = false;
        foreach($cartArray as $index => $cartItem){
            if($cartItem['id'] == $id) {
                $cartArray[$index]['count']++;
                $isDuplicated = true;
            }
        }
        if(!$isDuplicated) {
            array_push($cartArray, array('id' => $id, 'count' => 1));
        }
    }
    $session->set('items', json_encode($cartArray));

    $referer = $request->headers->get('referer');
    if($referer) return $this->redirect($referer);
    else return $this->redirectToRoute("homepage");
}

    /**
    * @Route("/cart/delete-from-cart/{id}", name="delete_from_cart")
     *
    */
    public function deleteAction($id, Request $request){

        if(! $this->get('session')){
            $session = new Session();
        }
        else{
            $session = $this->get('session');
        }
    
        $cart = $session->get('items');
    
        if($cart) {
            $cartArray = json_decode($cart, true);
            foreach($cartArray as $index => $cartItem){
                if($cartItem['id'] == $id) {
                    unset($cartArray[$index]);
                }
        }
        $session->set('items', json_encode($cartArray));
        }
        $referer = $request->headers->get('referer');
        if($referer) return $this->redirect($referer);
        else return $this->redirectToRoute("homepage");
    
    }

}
