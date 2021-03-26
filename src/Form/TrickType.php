<?php


namespace App\Form;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'attr'=>['class' => 'form-control'],
                'label' => 'Nom de la figure'
            ])
            ->add('content', TextareaType::class,[
                'attr'=>['class' => 'form-control', 'style' => 'height: 165px;'],
                'label' => 'Contenu de l\'article'
            ])
            ->add('category', EntityType::class,[
                'class' => Category::class,
                'attr'=>['class' => 'form-control'],
                'label' => 'Catégorie',
                'choice_label' => 'name'
            ]);

    }
}