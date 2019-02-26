<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Category;

class CategoryTree{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getCategoryTree(){
        
        $categoriesNw = $this->em
        ->getRepository(Category::class)
        ->findAll();

        $categories = array();

        foreach ($categoriesNw as $i => $value) {
            $id = $value->getId();
            $parentId = $value->getParentId();
            $name = $value->getName();
            array_push($categories, array("id" => $id,"parentId" => $parentId, "name" => $name));

        }
        return $categories;
    }
}