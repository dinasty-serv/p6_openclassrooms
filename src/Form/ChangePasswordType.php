<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add(
                'oldPassword',
                PasswordType::class,
                [
                    'attr' =>
                    [
                        'class' => 'form-control',
                        'placeholder' => 'Mots de passe actuel'
                    ],
                    'label' => 'Mots de passe actuel',
                    "constraints" => [
                        new NotBlank(),
                        new UserPassword()
                    ]
                ]
            )
            ->add('plainPassword', RepeatedType::class, array(
                'type'              => PasswordType::class,
                'mapped'            => true,
                'first_options'     => array('label' => 'Nouveau mots de passe', 'attr' => ['class' => 'form-control', 'placeholder' => 'Nouveau mots de passe']),
                'second_options'    => array('label' => 'Confirmer votre mots de passe', 'attr' => ['class' => 'form-control', 'placeholder' => 'Confirmer votre mots de passe']),
                'invalid_message' => 'Les mots de passe ne sont pas identique',
                "constraints" => [
                    new Length(["min" => 8]),
                    new NotBlank()
                ]
            ));
    }
}
