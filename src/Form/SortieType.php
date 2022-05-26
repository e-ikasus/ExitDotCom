<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'oninvalid' => 'this.setCustomValidity("Veuillez saisir un nom.")',
                    'oninput' => 'this.setCustomValidity("")'
                ]
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie : ',
                'html5' => true,
                'widget' => 'single_text',
                'attr' => [
                    'oninvalid' => 'this.setCustomValidity("Veuillez saisir une date.")',
                    'oninput' => 'this.setCustomValidity("")'
                ]
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label' => 'Date limite d\'inscription : ',
                'html5' => true,
                'widget' => 'single_text',
                'attr' => [
                    'oninvalid' => 'this.setCustomValidity("Veuillez saisir une date.")',
                    'oninput' => 'this.setCustomValidity("")'
                ]
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'label' => 'Nombre de places : ',
                'attr' => [
                    'min' => 1,
                    'oninvalid' => 'this.setCustomValidity("Veuillez saisir le nombre maximum d\'inscription.")',
                    'oninput' => 'this.setCustomValidity("")'
                ]
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée : ',
                'attr' => [
                    'min' => 30, 'step' => 15,
                    'oninvalid' => 'this.setCustomValidity("Veuillez saisir une durée en minutes.")',
                    'oninput' => 'this.setCustomValidity("")'
                ],
//                'constraints' => [
//                    new NotBlank([
//                        'message' => 'Veuillez rentrer une durée',
//                    ]),
//                    new Length([
//                        'min' => 30,
//                        'minMessage' => 'Your message should be at least {{ limit }} characters',
//                        // max length allowed by Symfony for security reasons
//                        'max' => 4096,
//        ]),
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Description et infos : ',
                'attr' => [
                    'rows' => 5,
                    'cols' => 50,
                    'oninvalid' => 'this.setCustomValidity("Veuillez saisir une description.")',
                    'oninput' => 'this.setCustomValidity("")'
                ],
            ])
            ->add('lieu', EntityType::class, [
                'label' => 'Lieu : ',
                'class' => Lieu::class,
                'choice_label' => 'nom'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
