<?php

namespace App\Form;

use App\Entity\Ville;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'label' => 'Nom de la ville :',
                'attr' => [
                    'oninvalid' => 'this.setCustomValidity("Veuillez saisir un nom.")',
                    'oninput' => 'this.setCustomValidity("")'
                ]
            ])
            ->add('codePostal', null, [
                'label' => 'Code postal : ',
                'attr' => [
                    'oninvalid' => 'this.setCustomValidity("Veuillez saisir un code postal.")',
                    'oninput' => 'this.setCustomValidity("")'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ville::class,
        ]);
    }
}
