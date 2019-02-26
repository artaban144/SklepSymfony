<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;

use AppBundle\Entity\Product;

class CartPrice{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getCartPrice($cart){

        if($cart){
             $cartArray = json_decode($cart, true);
             $cartPrice = 0;

             foreach($cartArray as $index => $cartItem){
                $product = $this->em
                ->getRepository(Product::class)
                ->find($cartItem['id']);
                $productAmount = $cartItem['count'];
                $productPrice = $product->getPrice();
                $cartPrice += $productPrice * $productAmount;
                
             }
             return($cartPrice);
            }
        else{
            return 0;
         }
    }
}