<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Category;
use AppBundle\Entity\Product;

class CategoryChildrens
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    private function getCategoryChildrensHelp($id)
    {
        $categorieChildsArray = array();

        $categorieChilds = $this->em
        ->getRepository(Category::class)
        ->findByParentId($id);

        foreach ($categorieChilds as $categoryChild) {
            $categoryId = $categoryChild->getId();
            array_push($categorieChildsArray, $categoryId);
        }
        return $categorieChildsArray;
    }

    public function getCategoryChildrens($id)
    {
        $childrensAll = array($id);
        $childrensTemp = $this->getCategoryChildrensHelp($id);
        $endFlag = 0;

        do {
            $childrensTempHelp = array();
            foreach ($childrensTemp as $childrenId) {
                array_push($childrensAll, $childrenId);
                $currentChildrenChildrens = $this->getCategoryChildrensHelp($childrenId);
                foreach ($currentChildrenChildrens as $currentChildrenChildren) {
                    array_push($childrensTempHelp, $currentChildrenChildren);
                }
            }
            if (empty($childrensTempHelp)) {
                $endFlag = 1;
            }
            $childrensTemp = $childrensTempHelp;
        } while ($endFlag != 1);

        return $childrensAll;
    }
}
