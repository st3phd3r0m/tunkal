<?php

namespace App\Form;

use App\Entity\Posts;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PostsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $firmSlugs = [
            'accueil',
            'news',
            'previous-concerts',
            'upcoming-concerts',
        ];

        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'Titre de l\'article : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un titre.',
                    ]),
                ],
            ])
            ->add('metaTitle', TextType::class, [
                'required' => true,
                'label' => 'Titre méta-donnée de l\'article : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un titre.',
                    ]),
                    new Length([
                        'min' => 50,
                        'minMessage' => 'Le titre doit comporter au minimum {{ limit }} caractères.',
                        'max' => 70,
                        'maxMessage' => 'Le titre doit comporter au maximum {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('metaDescription', TextareaType::class, [
                'required' => true,
                'label' => 'Description méta-donnée de l\'article : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une description.',
                    ]),
                    new Length([
                        'min' => 150,
                        'minMessage' => 'La description doit comporter au minimum {{ limit }} caractères.',
                        'max' => 200,
                        'maxMessage' => 'La description doit comporter au maximum {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('keywords', TextType::class, [
                'required' => false,
                'label' => 'Ajouter des mots-clés, délimités par des hashtags ("#"), afin de référencer votre produit : ',
                'mapped' => false,
                'data' => (null != $builder->getData()->getKeywords()) ? implode('#', $builder->getData()->getKeywords()) : '',
            ])
            ->add('metaKeywords', TextType::class, [
                'required' => false,
                'label' => 'Ajouter des mots-clés en méta-données, délimités par des hashtags ("#"), afin de référencer votre produit : ',
                'mapped' => false,
                'data' => (null != $builder->getData()->getMetaKeywords()) ? implode('#', $builder->getData()->getMetaKeywords()) : '',
            ])
            ->add('content', CKEditorType::class, [
                // 'config_name' => 'main_config',
                'required' => true,
                'label' => 'Description/contenu de l\'article : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir la description de l\'article',
                    ]),
                ],
            ])
            ->add('images', CollectionType::class, [
                'required' => false,
                'label' => 'Images d\'illustration',
                'entry_type' => ImagesType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
            ]);
        if (!in_array($options['data']->getSlug(), $firmSlugs)) {
            $builder->add('slug', TextType::class, [
                        'required' => false,
                        'label' => 'titre ("slug") en barre d\'url : ',
                        // 'constraints' => [
                        //     new NotBlank([
                        //         'message' => 'Veuillez saisir un slug.',
                        //     ])
                        // ]
                    ])
                    ->add('is_past_concert', ChoiceType::class, [
                        'label' => 'Concert passé ? ',
                        'required' => true,
                        'choices' => [
                            'Non' => 0,
                            'Oui' => 1,
                        ],
                    ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Posts::class,
        ]);
    }
}
