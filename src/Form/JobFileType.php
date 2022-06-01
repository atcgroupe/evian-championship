<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class JobFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'file',
                FileType::class,
                [
                    'mapped' => false,
                    'label' => 'Fichier de production',
                    'help' => 'Seuls les fichiers au format Pdf sont pris en compte. Le poid maximum est de 100Mo',
                    'required' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '100M',
                            'mimeTypes' => [
                                'application/pdf',
                                'application/x-pdf',
                            ],
                            'maxSizeMessage' => 'Le fichier importé est trop volumineux.',
                            'mimeTypesMessage' => 'Vous ne pouvez ajouter qu\'un fichier au format Pdf.',
                        ]),
                        new NotBlank(['message' => 'Merci d\'ajouter un fichier de production'])
                    ]
                ]
            );
    }
}
