<?php

namespace App\Form;

use App\Entity\Campus;
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
            ->add('Campus', EntityType::class, [
                'class' => Campus::class,
                'label' => 'Campus',
                'choice_label' => 'nom'
            ])
            ->add('SearchOutingName', SearchType::class, [
                'label' => 'Le nom de la sortie contient : ',
							'required' => false
            ])
            ->add('DateOutingStart', DateType::class, [
                'label' => 'Entre ',
                'widget' => 'single_text',
							'required' => false
            ])
            ->add('DateOutingEnd', DateType::class, [
                'label' => ' et ',
                'widget' => 'single_text',
							'required' => false
            ])
            ->add('OutingCheckboxOptions', ChoiceType::class, [
                'choices' => [
                    'Sorties dont je suis l\'organisateur/trice' => 'sorties-organisateur',
                    'Sorties auxquelles je suis inscrit/e' => 'sorties-non-inscrit',
                    'Sorties auxquelles je ne suis pas inscrit/e' => 'sorties-inscrit',
                    'Sorties passées' => 'sorties-passees'
                ],
                'expanded' => true,
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
