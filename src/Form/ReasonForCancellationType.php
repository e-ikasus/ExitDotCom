<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReasonForCancellationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('motifAnnulation', TextareaType::class, [
                'required' => true,
                'label' => 'Motif :',
                'attr' => [
                    'rows' => "5",
                    'cols' => "33",
                    'placeholder' => 'Veuillez renseigner le motif d\'annulation de la sortie.',
                    'oninvalid' => 'this.setCustomValidity("Veuillez saisir un Motif d\'annulation.")',
                    'oninput' => 'this.setCustomValidity("")'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
