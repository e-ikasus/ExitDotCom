<?php

namespace App\Form;

use App\Entity\Campus;
use PhpParser\Node\Expr\BinaryOp\GreaterOrEqual;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                    'class' => 'm-2 px-2'
                ],
            ])
            ->add('sortiesInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit/e',
                'required' => false,
                'attr' => [
                    'class' => 'm-2 px-2'
                ],
            ])
            ->add('sortiesNonInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit/e',
                'required' => false,
                'attr' => [
                    'class' => 'm-2 px-2'
                ],
            ])
            ->add('sortiesPassees', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required' => false,
                'attr' => [
                    'class' => 'm-2 px-2'
                ],
            ])
//            ->add('outingCheckboxOptions', ChoiceType::class, [
//                'label' => ' ',
//                'attr' => [
//                    'class' => 'd-flex flex-column',
//                ],
//                'choices' => [
//                    'Sorties dont je suis l\'organisateur/trice' => 'sorties-organisateur',
//                    'Sorties auxquelles je suis inscrit/e' => 'sorties-non-inscrit',
//                    'Sorties auxquelles je ne suis pas inscrit/e' => 'sorties-inscrit',
//                    'Sorties passées' => 'sorties-passees'
//                ],
//                'expanded' => true,
//                'multiple' => true,
//            ])
;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
