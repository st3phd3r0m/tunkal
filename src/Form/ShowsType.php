<?php

namespace App\Form;

use App\Entity\Shows;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ShowsType extends AbstractType
{
    /**
     * Undocumented function
     *
     * @param FormBuilderInterface $builder
     * @param array<string, mixed> $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'Titre du concert : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un titre.',
                    ]),
                ],
            ])
            ->add('location', TextType::class, [
                'required' => true,
                'label' => 'Lieu : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un Lieu.',
                    ]),
                ],
            ])
            ->add('metaTitle', TextType::class, [
                'required' => true,
                'label' => 'Titre méta-donnée du concert : ',
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
            ->add('description', CKEditorType::class, [
                // 'config_name'=> 'main_config',
                'required' => true,
                'label' => 'Description : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir la description',
                    ]),
                ],
            ])
            ->add('metaDescription', TextareaType::class, [
                'required' => true,
                'label' => 'Description en méta-données: ',
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
                'label' => 'Ajouter des mots-clés, délimités par des hashtags ("#"), afin de référencer votre publication : ',
                'mapped' => false,
                'data' => (null != $builder->getData()->getKeywords()) ? implode('#', $builder->getData()->getKeywords()) : '',
            ])
            ->add('metaKeywords', TextType::class, [
                'required' => false,
                'label' => 'Ajouter des mots-clés en méta-données, délimités par des hashtags ("#"), afin de référencer votre publication : ',
                'mapped' => false,
                'data' => (null != $builder->getData()->getMetaKeywords()) ? implode('#', $builder->getData()->getMetaKeywords()) : '',
            ])
            ->add('expected_at', DateType::class, [
                'required' => true,
                'label' => 'Date du concert',
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une date',
                    ]),
                    new DateTime([
                        'message' => 'La date est invalide',
                    ]),
                ],
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'label' => 'Image de présentation',
                'download_uri' => false,
                'imagine_pattern' => 'min300',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Choisir un drapeau',
                        'groups' => ['new'],
                    ]),
                    new Image([
                        'maxSize' => '2M',
                        'maxSizeMessage' => 'Votre image dépasse les 2Mo',
                        'mimeTypes' => ['image/webp'],
                        'mimeTypesMessage' => 'Votre image doit être de type webp',
                        'groups' => ['new', 'update'],
                    ]),
                ],
            ])
            ->add('slug', TextType::class, [
                'required' => false,
                'label' => 'titre ("slug") en barre d\'url : ',
                // 'constraints' => [
                //     new NotBlank([
                //         'message' => 'Veuillez saisir un slug.',
                //     ])
                // ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Shows::class,
        ]);
    }
}
