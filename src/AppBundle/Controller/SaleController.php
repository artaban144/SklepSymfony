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
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SaleController extends Controller
{
    /**
     * @Route("/realization", name="realization")
     */

    public function realizationAction(Request $request, CartPrice $cartPriceGetter){

        $defaultData = [
            'name' => 'Piotr',
            'lastName' => 'Polok',
            'email' => 'p@p.pl',
            'phoneNumber' => '123123123',
            'country' => 'Polska',
            'city' => 'Wisła',
            'postalCode' => '43-460',
            'address' => 'Kopydło 14'
        ];

        $form = $this->createFormBuilder($defaultData)
            ->add('name', TextType::class)
            ->add('lastName', TextType::class)
            ->add('email', EmailType::class)
            ->add('phoneNumber', TextType::class)
            ->add('country', TextType::class)            
            ->add('city', TextType::class)            
            ->add('postalCode', TextType::class)            
            ->add('address', TextType::class)            
            ->add('submit', SubmitType::class)
            ->getForm();

            $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $session = $this->get('session');
            $cart = $session->get('items');

            $cartPrice = $cartPriceGetter->getCartPrice($cart);
            $data = $form->getData();

            $dateTime = new \DateTime();

            $realization = new Realization();
            $realization->setUserId(1);
            $realization->setDate($dateTime);
            $realization->setName($data['name']);
            $realization->setLastName($data['lastName']);
            $realization->setEmail($data['email']);
            $realization->setPhoneNumber($data['phoneNumber']);
            $realization->setCountry($data['country']);
            $realization->setCity($data['city']);
            $realization->setPostalCode($data['postalCode']);
            $realization->setAddress($data['address']);
            $realization->setShipmentId(1);
            $realization->setCart($cart);
            $realization->setPrice($cartPrice);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($realization);
            $entityManager->flush();

        return new Response("<h1>Pomyślnie zrealizowano zamówienie</h1>");

            
        }
        else{
        }


    return $this->render('/sale/realization.html.twig', [
        'form' => $form->createView(),
    ]);

    }
}
