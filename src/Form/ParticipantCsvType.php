<?php

namespace App\Form;

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
            ->add('moncsv', FileType::class, ['label' => 'Fichier .csv', 'mapped' => false,
                'constraints' => [new File([
                    'maxSize' => '4096k',   //revoir la taille du fichier
                    'mimeTypes' => [
                        'text/x-comma-separated-values',
                        'text/comma-separated-values',
                        'text/x-csv',
                        'text/csv',
                        'text/plain',
                        'application/octet-stream',
                        'application/vnd.ms-excel',
                        'application/x-csv',
                        'application/csv',
                        'application/excel',
                        'application/vnd.msexcel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
                    'mimeTypesMessage' => 'Merci d\'utiliser un des formats suivant : .csv',
                ])
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
