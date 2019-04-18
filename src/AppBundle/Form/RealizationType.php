<?php
namespace AppBundle\Form;

use AppBundle\Entity\Realization;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;


class RealizationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $shipmentList = $options['data'];
      $shipmentListFormArray = array();
      foreach ($shipmentList as $key => $value) {
        $label = $value->getName()." - ".$value->getPrice();
        array_push($shipmentListFormArray, array($label => $value->getId()));
      }

        $builder
            ->add('name')
            ->add('lastname')
            ->add('address')
            ->add('postalCode')
            ->add('city')
            ->add('country')
            ->add('email')
            ->add('phoneNumber')
            ->add('shipmentId', ChoiceType::class, array(
                'choices' => $shipmentListFormArray,
                'expanded' => true))
            ->add('Podsumowanie', SubmitType::class)
        ;
    }
}
