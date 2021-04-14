<?php


namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class MediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class,
                ['attr' =>
                [
                    'class' => 'form-control',
                ],
                    'choices' => [
                        'VideoService' => 'video',
                        'Photos' => 'photo'
                    ],
                    'mapped' => false,
                'label' => 'Type de média'
                ]);

        $builder->get('type')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $data = $form->getData();
               if ($data == "photo"){
                   $form->getParent()->add('media', ImgType::class);

               }elseif ($data == 'video'){
                   $form->getParent()->add('video', VideoType::class);
               }
            }
        );
    }
}