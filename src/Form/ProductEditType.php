<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom du produit'])
            ->add(
                'price', TextType::class,
                [
                    'label' => 'Prix au m2 du produit',
                    'help' => 'Exemple 10.00'
                ]
            )
            ->add(
                'isActive',
                ChoiceType::class,
                [
                    'label' => 'Statut du produit',
                    'help' => 'Le produit sera visible pour le client',
                    'choices' => [
                        'Activé' => true,
                        'Désactivé' => false,
                    ]
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
