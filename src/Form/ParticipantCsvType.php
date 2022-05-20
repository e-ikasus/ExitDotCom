<?php

namespace App\Form;

use App\Entity\Ville;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ParticipantCsvType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('moncsv', FileType::class, ['label' => 'Fichier .csv' , 'mapped' => false,
                'constraints' => [new File([
                    'maxSize' => '4096k',   //revoir la taille du fichier
                    'mimeTypes' => ['text/csv', 'application/csv', 'text/x-comma-separated-values', 'text/x-csv',
                    ],
                    'mimeTypesMessage' => 'Merci d\'utiliser un des formats suivant : .csv',
                ])
                ],
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
