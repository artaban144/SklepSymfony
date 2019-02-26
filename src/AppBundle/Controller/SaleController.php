<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Realization;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Service\CartPrice;
use AppBundle\Form\RealizationType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SaleController extends Controller
{
    /**
     * @Route("/realization", name="realization")
     */

    public function realizationAction(Request $request, CartPrice $cartPriceGetter){
        $realization = new Realization();
        $dateTime = new \DateTime();
        $realization->setDate($dateTime->format('Y-m-d H:i:s'));
        $realization->setUserId(1);
        $realization->setPrice($cartPriceGetter->getCartPrice($this->get('session')->get('items')));

        $form = $this->createForm("RealizationType::class", $realization);

        if ($form->isSubmitted() && $form->isValid()) {
            $realization = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($realization);
            $entityManager->flush();
        }
        else{
            die("cosi");
        }


    return $this->render('sale/realization.html.twig', [
        'form' => $form->createView(),
    ]);


        // return new Response($realization->getPrice());
    }
}
