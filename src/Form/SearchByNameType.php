<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class SearchByNameType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder->add('pattern', SearchType::class, [
				'required' => false,
				'label' => 'Le nom contient : ',
				'constraints' => [new Regex("/^[^0-9#@\\;:!<>{}\[\]`()]*$/")],
				'attr' => ['placeholder' => '🔎 Recherche', 'class' => 'px-2 ms-2 me-3']
		]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			// Configure your form options here
		]);
	}
}
