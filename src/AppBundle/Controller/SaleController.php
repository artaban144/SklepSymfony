<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Realization;
use AppBundle\Entity\Shipment;
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
use AppBundle\Service\ShipmentList;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class SaleController extends Controller
{
    /**
     * @Route("/realization-get-data", name="realization_get_data")
     */

    public function realizationGetDataAction(Request $request, CartPrice $cartPriceGetter, ShipmentList $getShipmentList)
    {
        $shipmentList = $getShipmentList->getShipmentList();
        $realization = new Realization();

        $form = $this->createForm(RealizationType::class, $realization, [
            'data' => $shipmentList,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $realization = $form->getData();

            unset($realization['0'], $realization['1'], $realization['2'], $realization['3']); //?????????????????????

            $session = $this->get('session');

            $encoders = [new JsonEncoder()];
            $normalizers = [new ObjectNormalizer()];
            $serializer = new Serializer($normalizers, $encoders);

            $jsonRealization = $serializer->serialize($realization, 'json');
            $session->set('realization', $jsonRealization);

            return $this->redirectToRoute('realization_summary');
        }

        return $this->render('/sale/realization.getdata.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/realization_summary", name="realization_summary")
     */
    public function realizationSummaryAction()
    {
        $session = $this->get('session');
        $realization = json_decode($session->get('realization'), true);

        $shipment = $this->getDoctrine()
      ->getRepository(Shipment::class)
      ->find($realization['shipmentId']);


        return $this->render('/sale/realization.summary.html.twig', [
            'formData' => $realization,
            'shipmentLabel' =>$shipment->getName(),
        ]);
    }

    /**
     * @Route("/realization", name="realization")
     */
    public function realizationAction(Request $request, CartPrice $cartPriceGetter)
    {
        $session = $this->get('session');

        $cart = $session->get('items');
        $cartPrice = $cartPriceGetter->getCartPrice($cart);

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $jsonRealization = $session->get('realization');
        $realization = $serializer->deserialize($jsonRealization, Realization::class, 'json');

        $realization->setUserId("1");
        $realization->setDate(new \DateTime("now"));
        $realization->setCart($cart);

        $realization->setPrice($cartPrice);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($realization);
        $entityManager->flush();

        $session->set('items', "");

        return $this->render('/sale/realization.success.html.twig');
    }
}
