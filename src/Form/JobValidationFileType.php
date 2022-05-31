<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;

class JobValidationFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'file',
                FileType::class,
                [
                    'mapped' => false,
                    'label' => 'Importez les BAT',
                    'multiple' => true,
                    'constraints' => [
                        new All([
                            'constraints' => [
                                new Image([
                                    'maxSize' => '5M',
                                    'mimeTypes' => [
                                        'image/jpg',
                                        'image/jpeg',
                                    ],
                                    'allowLandscape' => true,
                                    'allowPortrait' => false,
                                    'maxSizeMessage' => 'Le fichier importé est trop volumineux.',
                                    'mimeTypesMessage' => 'Vous ne pouvez ajouter qu\'un fichier au format jpg/jpeg.',
                                    'minWidthMessage' => "L'un des fichiers a une largeur incorecte",
                                    'maxWidthMessage' => "L'un des fichiers a une largeur incorecte",
                                    'minHeightMessage' => "L'un des fichiers a une hauteur incorecte",
                                    'maxHeightMessage' => "L'un des fichiers a une hauteur incorecte",
                                    'allowPortraitMessage' => 'Le fichier doit être au format 1684x1190px',
                                ])
                            ]
                        ])
                    ]
                ]
            );
    }
}
