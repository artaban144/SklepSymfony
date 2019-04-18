<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;

use AppBundle\Entity\Shipment;

class ShipmentList
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getShipmentList()
    {
        $shipments = $this->em
        ->getRepository(Shipment::class)
        ->findAll();

        return $shipments;
    }
}
