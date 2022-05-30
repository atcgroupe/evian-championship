<?php

namespace App\Form;

use App\Entity\User;
use App\Enum\UserRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, ['label' => 'Email'])
            ->add(
                'roles',
                ChoiceType::class,
                [
                    'label' => 'Role',
                    'choices' => UserRole::getFormChoices(),
                ]
            )
            ->add(
                'plainPassword',
                PasswordType::class,
                [
                    'label' => 'Mot de passe',
                    'help' => 'Ne remplir que si il doit être changé.'
                ]
            )->add('firstName', TextType::class, ['label' => 'Prénom'])
            ->add('lastName', TextType::class, ['label' => 'Nom'])
            ->add('isActive',
                ChoiceType::class,
                [
                    'label' => 'Statut',
                    'choices' => [
                        'Activé' => true,
                        'Désactivé' => false,
                    ]
                ]
            )
        ;

        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesAsArray) {
                    return empty($rolesAsArray) ? '' : $rolesAsArray[0];
                },
                function ($rolesAsString) {
                    return [$rolesAsString];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['user_create'],
        ]);
    }
}
