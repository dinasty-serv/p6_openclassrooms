<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email as ConstraintsEmail;

/**
 * Class RegisterType
 * @package App\Form
 */
class RegisterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
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
            ->add('password', RepeatedType::class, array(
                'type'              => PasswordType::class,
                'mapped'            => true,
                'first_options'     => array('label' => 'Votre mot de passe', 'attr' => ['class' => 'form-control', 'placeholder' => 'Mot de passe']),
                'second_options'    => array('label' => 'Confirmer votre mot de passe', 'attr' => ['class' => 'form-control', 'placeholder' => 'Confirmer votre mots de passe']),
                'invalid_message' => 'Les mots de passes ne sont pas identique',
                "constraints" => [
                    new Length(["min" => 8]),
                    new NotBlank()
                ]
            ))
            ->add('username', TextType::class,  [
                'attr' =>
                    [
                        'class' => 'form-control',
                        'placeholder' => 'Nom d\'utilisateur'
                    ],
                'label' => 'Nom d\'utilisateur',
                'constraints' => [
                    new NotBlank(),
                ]

            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
