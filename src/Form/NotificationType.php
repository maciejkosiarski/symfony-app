<?php

namespace App\Form;

use App\Entity\Notification;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotificationType extends AbstractType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array                $options
	 * @throws \ReflectionException
	 */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
    	$notification = new Notification();

        $builder
            ->add('type', ChoiceType::class, [
            	'choices' => $notification->getTypeList(),
			])
            ->add('message', TextType::class)
            ->add('intervalExpression', TextType::class)
            ->add('recurrent', CheckboxType::class, [
				'required' => false,
			])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Notification::class,
        ]);
    }
}
