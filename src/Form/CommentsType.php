<?php

namespace App\Form;

use App\Entity\Comments;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('pseudo', TextType::class, [
            'required' => true,
            'label' => 'Pseudo',
            'constraints' => [
                new NotBlank([
                    'message' => 'Nom/pseudo requis',
                ]),
            ],
        ])
        ->add('content', TextareaType::class, [
            'required' => true,
            'label' => 'Votre commentaire',
            'constraints' => [
                new NotBlank([
                    'message' => 'Commentaire requis',
                ]),
                new Length([
                    'min' => 10,
                    'max' => 240,
                    'minMessage' => 'Votre commentaire doit comporter au moins {{ limit }}
                    caractères.',
                    'maxMessage' => 'Votre commentaire doit comporter moins de {{ limit }}
                    caractères.',
                ]),
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comments::class,
        ]);
    }
}
