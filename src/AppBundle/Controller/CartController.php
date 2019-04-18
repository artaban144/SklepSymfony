<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\Realization;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Service\CartPrice;
use Symfony\Component\HttpFoundation\JsonResponse;

class CartController extends Controller
{
    /**
     * @Route("/cart", name="cart")
     */
    public function indexAction(Request $request, CartPrice $cartPriceGetter)
    {
        $session = $this->get('session');


        $cart = $session->get('items');
        $cartArray = json_decode($cart, true);

        if ($cartArray) {
            $cartPrice = $cartPriceGetter->getCartPrice($cart);
            foreach ($cartArray as $index => $cartItem) {
                $product = $this->getDoctrine()
                  ->getRepository(Product::class)
                  ->find($cartItem['id']);
                $cartArray[$index]['name'] = $product->getName();
                $cartArray[$index]['price'] = $product->getPrice();
            }
        } else {
            $cartPrice = 0;
        }

        return $this->render('cart/cart.html.twig', array(
            'cart' => $cartArray,
            'cartPrice' => $cartPrice,
        ));
    }
    /**
    * @Route("/cart/add-to-cart/{id}", name="add_to_cart")
     *
    */
    public function addAction($id, Request $request)
    {
        $session = $this->get('session');

        // $session->set('items', "");

        $cart = $session->get('items');

        if ($cart == null) {
            $cartArray = array(array('id' => $id, 'count' => 1));
        } else {
            $cartArray = json_decode($cart, true);
            $isDuplicated = false;
            foreach ($cartArray as $index => $cartItem) {
                if ($cartItem['id'] == $id) {
                    $cartArray[$index]['count']++;
                    $isDuplicated = true;
                }
            }
            if (!$isDuplicated) {
                array_push($cartArray, array('id' => $id, 'count' => 1));
            }
        }
        $session->set('items', json_encode($cartArray));

        $referer = $request->headers->get('referer');
        if ($referer) {
            return $this->redirect($referer);
        } else {
            return $this->redirectToRoute("homepage");
        }
    }

    /**
    * @Route("/cart/delete-from-cart/{id}", name="delete_from_cart")
     *
    */
    public function deleteAction($id, Request $request)
    {
        if (! $this->get('session')) {
            $session = new Session();
        } else {
            $session = $this->get('session');
        }

        $cart = $session->get('items');

        if ($cart) {
            $cartArray = json_decode($cart, true);
            foreach ($cartArray as $index => $cartItem) {
                if ($cartItem['id'] == $id) {
                    unset($cartArray[$index]);
                }
            }
            $session->set('items', json_encode($cartArray));
        }
        $referer = $request->headers->get('referer');
        if ($referer) {
            return $this->redirect($referer);
        } else {
            return $this->redirectToRoute("homepage");
        }
    }

    /**
    * @Route("/cart/amount/{id}", name="change_product_amount")
     *
    */
    public function changeAmountAction($id, Request $request, CartPrice $cartPriceGetter)
    {
        if (! $this->get('session')) {
            $session = new Session();
        } else {
            $session = $this->get('session');
        }

        $cart = $session->get('items');
        $cartArray = json_decode($cart, true);

        if ($request->request->get('id')) {
            $changeAmountType = $request->request->get('type');

            if ($changeAmountType == 'inc') {
                foreach ($cartArray as $index => $cartItem) {
                    if ($cartArray[$index]['id'] == $id) {
                        $cartArray[$index]['count']++;
                    }
                }
            } elseif ($changeAmountType == 'dec') {
                foreach ($cartArray as $index => $cartItem) {
                    if ($cartArray[$index]['id'] == $id) {
                        if ($cartArray[$index]['count'] > 1) {
                            $cartArray[$index]['count']--;
                        } else {
                            unset($cartArray[$index]);
                        }
                    }
                }
            } elseif ($changeAmountType == 'val') {
                $changeAmountValue = $request->request->get('value');

                foreach ($cartArray as $index => $cartItem) {
                    if ($cartArray[$index]['id'] == $id) {
                        if ($changeAmountValue < 1) {
                            $cartArray[$index]['count'] = 1;
                        } else {
                            $cartArray[$index]['count'] = $changeAmountValue;
                        }
                    }
                }
            }

            $session->set('items', json_encode($cartArray));
            $cart = $session->get('items');

            $cartPrice = $cartPriceGetter->getCartPrice($cart);
            $arrData = ['output' => $changeAmountType, 'cartPrice' => $cartPrice];
            return new JsonResponse($arrData);
        }



        if ($cartArray) {
            $cartPrice = $cartPriceGetter->getCartPrice($cart);
            foreach ($cartArray as $index => $cartItem) {
                $product = $this->getDoctrine()
                    ->getRepository(Product::class)
                    ->find($cartItem['id']);
                $cartArray[$index]['name'] = $product->getName();
                $cartArray[$index]['price'] = $product->getPrice();
            }
        }

        return $this->render('cart/cart.html.twig', array(
              'cart' => $cartArray,
              'cartPrice' => $cartPrice,
          ));
    }
}
