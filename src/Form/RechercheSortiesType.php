<?php

namespace App\Form;

use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class RechercheSortiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'label' => 'Campus',
                'choice_label' => 'nom',
                'attr' => [
                    'class' => 'm-2 px-2'
                ],
            ])
            ->add('searchOutingName', SearchType::class, [
                'constraints' => [new Regex("/^[^0-9#@\\;:!<>{}\[\]`()]*$/")],
                'label' => 'Le nom de la sortie contient : ',
                'attr' => [
                    'placeholder' => '🔎 Recherche',
                    'class' => 'm-2 px-2'
                ],
                'required' => false
            ])
            ->add('dateOutingStart', DateType::class, [
                'label' => 'Entre ',
                'widget' => 'single_text',
                'required' => false,
                'attr' => [
                    'class' => 'm-2 px-2'
                ],
            ])
            ->add('dateOutingEnd', DateType::class, [
                'label' => 'et',
                'widget' => 'single_text',
                'required' => false,
                'attr' => [
                    'class' => 'm-2 px-2'
                ],
            ])
            ->add('sortiesOrganisateur', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur/trice',
                'required' => false,
                'attr' => [
                    'class' => 'm-2 px-2 pb-1'
                ],
            ])
            ->add('sortiesInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit/e',
                'required' => false,
                'attr' => [
                    'class' => 'm-2 px-2 pb-1'
                ],
            ])
            ->add('sortiesNonInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit/e',
                'required' => false,
                'attr' => [
                    'class' => 'm-2 px-2 pb-1'
                ],
            ])
            ->add('sortiesPassees', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required' => false,
                'attr' => [
                    'class' => 'm-2 px-2 pb-1'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
