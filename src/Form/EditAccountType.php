<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\User;
use Symfony\Component\Validator\Constraints\Email as ConstraintsEmail;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'email',
                TextType::class,
                [
                    'attr' =>
                    [
                        'class' => 'form-control',
                        'placeholder' => 'Email'
                    ],
                    'label' => 'Adresse Email',
                    'constraints' => [
                        new NotBlank(),
                        new ConstraintsEmail()
                    ]

                ]
            )
            ->add(
                'username',
                TextType::class,
                [
                    'attr' =>
                    [
                        'class' => 'form-control',
                        'placeholder' => 'Nom d\'utilisateur'
                    ],
                    'label' => 'Nom d\'utilisateur',
                    'constraints' => [
                        new NotBlank(),
                    ]
                ]
            );

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault("data_class", User::class);
    }
}
