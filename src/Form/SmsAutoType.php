<?php

namespace App\Form;

use App\Entity\SmsAuto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\CustomerFilesRepository;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;

class SmsAutoType extends AbstractType
{
    private $stepFields;

    public function __construct(CustomerFilesRepository $customerFilesRepository)
    {
        $this->stepFields = $customerFilesRepository->getStepFields();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => 'Message : <span class="text-danger">*</span>',
                'label_html' => true,
                'required' => true
            ])
            ->add('fields', ChoiceType::class, [
                'label' => 'Champs à remplir : <span class="text-danger">*</span>',
                'label_html' => true,
                'choices' => array_flip($this->stepFields),
                'multiple' => true,
                'required' => true,
                'attr' => ['class' => 'ui fluid dropdown search'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SmsAuto::class
        ]);
    }
}
