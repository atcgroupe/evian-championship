<?php

namespace App\Form;

use App\Entity\Delivery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeliveryEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'Titre',
                    'help' => 'Permet de distinguer cette livraison. Exemple: Camion 1, ...'
                ]
            )->add(
                'date',
                DateType::class,
                [
                    'label' => 'Date de départ',
                    'help' => 'Date de départ de chez ATC Groupe',
                    'widget' => 'single_text',
                ]
            )->add(
                'comment',
                TextareaType::class,
                [
                    'label' => 'Commentaire',
                    'help' => 'Informations à propos de cette livraison',
                    'required' => false,
                    'attr' => ['rows' => 6],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Delivery::class,
        ]);
    }
}
