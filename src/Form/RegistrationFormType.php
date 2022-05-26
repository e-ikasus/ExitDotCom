<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', null, [
                'label' => 'Pseudo',
                'attr' => [
                    'oninvalid' => 'this.setCustomValidity("Veuillez saisir un pseudo.")',
                    'oninput' => 'this.setCustomValidity("")'
                ]
            ])
            ->add('nom', null, [
                'label' => 'Nom',
                'attr' => [
                    'oninvalid' => 'this.setCustomValidity("Veuillez saisir un nom.")',
                    'oninput' => 'this.setCustomValidity("")'
                ]
            ])
            ->add('prenom', null, [
                'label' => 'Prénom',
                'attr' => [
                    'oninvalid' => 'this.setCustomValidity("Veuillez saisir un prénom.")',
                    'oninput' => 'this.setCustomValidity("")'
                ]
            ])
            ->add('telephone', null, [
                'label' => 'Téléphone',
                'attr' => [
                    'oninvalid' => 'this.setCustomValidity("Veuillez saisir un numéro de téléphone.")',
                    'oninput' => 'this.setCustomValidity("")'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'oninvalid' => 'this.setCustomValidity("Veuillez saisir un email.")',
                    'oninput' => 'this.setCustomValidity("")'
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'options' => ['attr' => ['class' => 'password-field']],
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation du mot de passe'],
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'oninvalid' => 'this.setCustomValidity("Veuillez saisir un mot de passe.")',
                    'oninput' => 'this.setCustomValidity("")'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 64,
                    ]),
                ],
            ])
            ->add('campus', EntityType::class, [
                'label' => 'Campus',
                'class' => Campus::class,
                'choice_label' => 'nom'
            ])
            ->add('administrateur', CheckboxType::class, [
                'label' => 'Administrateur',
                'attr' => ['class' => 'checkbox-administrateur']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
