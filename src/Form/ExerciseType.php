<?php

namespace App\Form;

use App\Entity\Exercise;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExerciseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', EntityType::class, [
            	'class' => \App\Entity\ExerciseType::class,
				'choice_label' => 'name',
			]);

		$builder->add('minutes', IntegerType::class);
		$builder->add('note', TextType::class, [
			'required' => false,
		]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Exercise::class,
			'validation_groups' => ['form'],
        ]);
    }
}
