<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo',
                'attr' => [
                    'oninvalid' => 'this.setCustomValidity("Veuillez saisir un pseudo.")',
                    'oninput' => 'this.setCustomValidity("")'
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'oninvalid' => 'this.setCustomValidity("Veuillez saisir un prénom.")',
                    'oninput' => 'this.setCustomValidity("")'
                ]
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'oninvalid' => 'this.setCustomValidity("Veuillez saisir un nom.")',
                    'oninput' => 'this.setCustomValidity("")'
                ]
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'oninvalid' => 'this.setCustomValidity("Veuillez saisir un numéro de téléphone.")',
                    'oninput' => 'this.setCustomValidity("")'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'attr' => [
                    'oninvalid' => 'this.setCustomValidity("Veuillez saisir un email.")',
                    'oninput' => 'this.setCustomValidity("")'
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent être identiques.',
                'required' => true,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation'],
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'attr' => [
                    'autocomplete' => 'new-password',
                    'oninvalid' => 'this.setCustomValidity("Veuillez saisir un mot de passe.")',
                    'oninput' => 'this.setCustomValidity("")'
                ],
            ])
            ->add('campus', EntityType::class, [
                'label' => 'Campus',
                'class' => Campus::class,
                'choice_label' => 'nom'
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Merci d\'utiliser un des formats suivant : .jpg .jpeg .png',
                    ])
                ],
            ])
            ->add('administrateur', CheckboxType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
