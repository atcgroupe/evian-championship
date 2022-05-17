<?php

namespace App\Form;

use App\Entity\Job;
use App\Entity\Product;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class JobCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'product',
                EntityType::class,
                [
                    'label' => 'Sélectionnez votre produit',
                    'class' => Product::class,
                    'query_builder' => function(EntityRepository $_er) {
                        return $_er->createQueryBuilder('product')
                            ->andWhere('product.isActive = :isActive')
                            ->setParameter('isActive', true)
                            ->orderBy('product.id', 'ASC');
                    },
                    'choice_label' => 'name',
                ]
            )->add(
                'customerReference',
                TextType::class,
                [
                    'label' => 'Référence',
                ]
            )->add(
                'location',
                TextType::class,
                [
                    'label' => 'Emplacement',
                    'required' => false,
                ]
            )->add(
                'description',
                TextType::class,
                [
                    'label' => 'Descriptif',
                    'required' => false,
                ]
            )
            ->add(
                'width',
                NumberType::class,
                [
                    'label' => 'Largeur visible',
                    'help' => 'En millimètres.',
                    'html5' => true,
                ]
            )->add(
                'height',
                NumberType::class,
                [
                    'label' => 'Hauteur visible',
                    'help' => 'En millimètres.',
                    'html5' => true,
                ]
            )->add('leftBleed',
                NumberType::class,
                [
                    'label' => 'Gauche',
                    'help' => 'En millimètres.',
                    'html5' => true,
                ]
            )->add(
                'rightBleed',
                NumberType::class,
                [
                    'label' => 'Droite',
                    'help' => 'En millimètres.',
                    'html5' => true,
                ]
            )->add(
                'topBleed',
                NumberType::class,
                [
                    'label' => 'Haut',
                    'help' => 'En millimètres.',
                    'html5' => true,
                ]
            )->add(
                'bottomBleed',
                NumberType::class,
                [
                    'label' => 'Bas',
                    'help' => 'En millimètres.',
                    'html5' => true,
                ]
            )
            ->add(
                'finish',
                TextareaType::class,
                [
                    'label' => 'Finitions',
                    'required' => false,
                    'attr' => ['rows' => 2]
                ]
            )->add(
                'imageCount',
                NumberType::class,
                [
                    'label' => 'Nombre de modèles',
                    'help' => 'Nombre de visuels différents (1, 2, ...)',
                    'html5' => true,
                ]
            )->add(
                'imageQuantity',
                NumberType::class,
                [
                    'label' => 'Quantité pour chaque modèle',
                    'help' => 'Nombre d\'exemplaires pour chaque visuel',
                    'html5' => true,
                ]
            )->add(
                'file',
                FileType::class,
                [
                    'mapped' => false,
                    'label' => 'Fichier de production',
                    'help' => 'Seuls les fichiers au format Pdf sont pris en compte. Le poid maximum est de 510Mo',
                    'required' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '510M',
                            'mimeTypes' => [
                                'application/pdf',
                                'application/x-pdf',
                            ],
                            'maxSizeMessage' => 'Le fichier importé est trop volumineux.',
                            'mimeTypesMessage' => 'Vous ne pouvez ajouter qu\'un fichier au format Pdf.',
                        ])
                    ]
                ]
            )->add(
                'customerComment',
                TextareaType::class,
                [
                    'label' => 'Commentaire',
                    'required' => false,
                    'attr' => [
                        'rows' => 4,
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Job::class,
        ]);
    }
}
