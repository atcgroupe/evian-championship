<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserUpdateType extends UserCreateType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['user_update']
        ]);
    }
}
