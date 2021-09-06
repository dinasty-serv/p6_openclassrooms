<?php


namespace App\Form;
use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

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

        if ($options['action'] == "create"){

            $builder->add('images', CollectionType::class,[
                'entry_type' => ImgType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,

            ]);

            $builder->add('videos', CollectionType::class,[
                'entry_type' => VideoType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,

            ]);
        }

    }
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,

        ]);
    }
}