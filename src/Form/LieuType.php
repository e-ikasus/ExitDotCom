<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom : ',
                'attr' => [
                    'class' => 'm-2 px-2',
                    'oninvalid' => 'this.setCustomValidity("Veuillez saisir un nom.")',
                    'oninput' => 'this.setCustomValidity("")'
                ],
            ])
            ->add('rue', TextType::class, [
                'label' => 'Rue : ',
                'attr' => [
                    'class' => 'm-2 px-2',
                    'oninvalid' => 'this.setCustomValidity("Veuillez saisir une rue.")',
                    'oninput' => 'this.setCustomValidity("")'
                ],
            ])
            ->add('latitude', null, [
                'label' => 'Latitude : ',
                'attr' => [
                    'class' => 'm-2 px-2',
                    'oninvalid' => 'this.setCustomValidity("Veuillez saisir un latitude au format decimal.")',
                    'oninput' => 'this.setCustomValidity("")'
                ],
            ])
            ->add('longitude', null, [
                'label' => 'Longitude : ',
                'attr' => [
                    'class' => 'm-2 px-2',
                    'oninvalid' => 'this.setCustomValidity("Veuillez saisir un longitude au format decimal.")',
                    'oninput' => 'this.setCustomValidity("")'
                ],
            ])
            ->add('ville', EntityType::class, [
                'label' => 'Ville : ',
                'class' => Ville::class,
                'choice_label' => 'nom',
                'attr' => [
                    'class' => 'm-2 px-2'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
