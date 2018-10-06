<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\CompanySource;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanySourceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('path')
            ->add('priceSelector')
            ->add('company', EntityType::class, array(
                'class' => Company::class,
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CompanySource::class,
            'validation_groups' => ['form'],
        ]);
    }
}
