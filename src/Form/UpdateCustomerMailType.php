<?php

namespace App\Form;

use App\Entity\CustomerFiles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateCustomerMailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('mail_al', EmailType::class, [
            'required' => false,
            'label' => 'Mail AL',
            'row_attr' => ['class' => 'col-md-6'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomerFiles::class,
        ]);
    }
}
