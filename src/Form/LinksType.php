<?php

namespace App\Form;

use App\Entity\Links;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;

class LinksType extends AbstractType
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
                'label' => 'Titre du lien (Visible lors du survol de la souris sur le lien) : ',

                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un titre.',
                    ]),
                ],
            ])
            ->add('content', TextType::class, [
                'required' => true,
                'label' => 'Texte du lien (Contenu directement visible à l\'écran) : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le contenu du lien',
                    ]),
                ],
            ])
            ->add('position', ChoiceType::class, [
                'label' => 'Où voulez-vous placer le lien ?',
                'choices' => [
                    'Bas de page (footer)' => 'footer',
                    'Haut de page (header)' => 'header',
                    'Barre de navigation (haut de page)' => 'nav',
                    'Zone des médias audio/vidéo' => 'media',
                    'copyright' => 'copyright',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une position pour le lien',
                    ]),
                ],
            ])
            ->add('position_order', NumberType::class, [
                'required' => true,
                'label' => 'Ordre d\'apparition du lien ? (Saisir un chiffre. Du plus petit au plus grand : liens de la gauche vers la droite)',
                'html5' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un nombre entier',
                    ]),
                ],
            ])
            ->add('type', ChoiceType::class, [
                'required' => true,
                'label' => 'Type de lien à ajouter',
                'choices' => [
                    // 'Concert' => '/show/',
                    // 'Article' => '/post/',
                    'Lien sortant' => 'external',
                    'Page des News' => '/news/',
                    'Page des concerts passés' => '/previous/concerts/',
                    'Page des concerts à venir' => '/upcoming/concerts/',
                ],
                'data' => $options['data']->getType(), //'/accueil',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir un type de lien',
                    ]),
                ],
            ])
            // ->add('hyperlink')
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'label' => 'Choisir une icône',
                'download_uri' => false,
                'imagine_pattern' => 'min300',
                'constraints' => [
                    new Image([
                        'maxSize' => '2M',
                        'maxSizeMessage' => 'Votre image dépasse les 2Mo',
                        'mimeTypes' => ['image/webp'],
                        'mimeTypesMessage' => 'Votre image doit être de type webp',
                    ]),
                ],
            ])
            ->add('alt', TextType::class, [
                'required' => true,
                'label' => 'Texte alternatif : ',
            ])
            ->add('is_active', ChoiceType::class, [
                'required' => true,
                'label' => 'Lien actif ?',
                'choices' => [
                    'Oui' => 1,
                    'Non' => 0,
                ],
            ])
            ->add('is_video', ChoiceType::class, [
                'required' => true,
                'label' => 'Lien video ?',
                'choices' => [
                    'Oui' => 1,
                    'Non' => 0,
                ],
            ])
            ->add('uploaded_at', DateType::class, [
                'required' => true,
                'label' => 'Date téléchargement vidéo',
                'widget' => 'single_text',
                'constraints' => [
                    new DateTime([
                        'message' => 'La date est invalide',
                    ]),
                ],
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Links::class,
        ]);
    }
}
