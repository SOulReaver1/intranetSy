<?php

namespace App\Form;

use App\Entity\Sms;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SmsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('phone_number', HiddenType::class, [
                'label' => 'Numéro de téléphone : <span class="text-danger">*</span>',
                'label_html' => true,
                'required' => true
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Message : <span class="text-danger">*</span>',
                'label_html' => true,
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sms::class,
        ]);
    }
}
