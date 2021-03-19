<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('password', RepeatedType::class, array(
            'type'              => PasswordType::class,
            'mapped'            => true,
            'first_options'     => array('label' => 'Nouveau mot de passe', 'attr' => ['class' => 'form-control','placeholder' => 'Nouveau mots de passe']),
            'second_options'    => array('label' => 'Confirmer votre mots de passe', 'attr' => ['class' => 'form-control','placeholder' => 'Répéter mots de passe']),
            'invalid_message' => 'Les mots de passe ne sont pas identique',
        ))
    ;
    }
}
