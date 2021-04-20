<?php

namespace App\Form;

use App\Entity\Comments;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class CommentsType extends AbstractType
{
    /**
     * Undocumented function.
     *
     * @param array<string, mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('pseudo', TextType::class, [
            'required' => true,
            'label' => 'Votre pseudo',
            'attr' => [
                'minlength' => 4,
                'maxlength' => 20,
                'pattern' => '[A-Za-z]{4,20}.{0,}',
                'title' => 'Votre pseudo doit comporter entre 4 et 20 carctères et commencer par 4 lettres minimum',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Nom/pseudo requis',
                ]),
                new Length([
                    'min' => 4,
                    'max' => 20,
                    'minMessage' => 'Votre pseudo doit comporter au moins {{ limit }}
                    caractères.',
                    'maxMessage' => 'Votre pseudo doit comporter moins de {{ limit }}
                    caractères.',
                ]),
                new Regex([
                    'pattern' => '/^[a-zA-Z]{4,20}/',
                    'match' => true,
                    'message' => 'Votre pseudo doit commencer par 4 lettres au moins',
                ]),
            ],
        ])
        ->add('content', TextareaType::class, [
            'required' => true,
            'label' => 'Votre commentaire',
            'attr' => [
                'minlength' => 10,
                'maxlength' => 240,
                'pattern' => '[A-Za-z]{2,240}.{0,}',
                'title' => 'Votre commentaire doit comporter entre 2 et 240 carctères et commencer par 2 lettres minimum',
            ],
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
                new Regex([
                    'pattern' => '/^[a-zA-Z]{2,240}/',
                    'match' => true,
                    'message' => 'Votre commentaire doit commencer par 2 lettres au moins',
                ]),
            ],
        ])
        ->add('nosiar', HiddenType::class, [
            'required' => true,
            'mapped' => false,
            'constraints' => [
                new Blank([
                    'message' => 'Le champ doit rester vide.',
                ]),
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comments::class,
        ]);
    }
}
