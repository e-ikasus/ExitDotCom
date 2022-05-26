<?php

namespace App\Form;

use App\Entity\GroupePrive;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupePriveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du groupe privé : ',
                'attr' => [
                    'class' => 'm-2 px-2',
                    'oninvalid' => 'this.setCustomValidity("Veuillez saisir un nom.")',
                    'oninput' => 'this.setCustomValidity("")'
                ],
            ])
//            ->add('participant', EntityType::class, [
//                'class' => 'App\Entity\Participant',
//                'label' => 'Participants : '
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GroupePrive::class,
        ]);
    }
}
