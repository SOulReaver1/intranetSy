<?php

namespace App\Form;

use App\Entity\ClientStatutDocument;
use App\Entity\CustomerFiles;
use App\Entity\Files;
use App\Repository\ClientStatutDocumentRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
class FilesType extends AbstractType
{
    private $customer;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->customer = $options['customer'];
        $builder
            ->add('document', EntityType::class, array(
                'class' => ClientStatutDocument::class,
                'query_builder' => function(ClientStatutDocumentRepository $repository){
                    return $repository->createQueryBuilder('u')
                    ->leftJoin('u.client_statut', 'client') 
                    ->where('client.id = :id')
                    ->setParameter('id', $this->customer->getClientStatutId());
                },
                'label' => 'Type de document : <span class="text-danger">*</span>',
                'label_html' => true,
                'placeholder' => 'Image libre',
                'required' => false
                )
            )
            ->add('file', FileType::class, array(
                'label' => 'Votre document <span class="text-danger">*</span>',
                'label_html' => true,
                'required' => true,
                'data_class' => null,
                )
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Files::class,
            'customer' => CustomerFiles::class,
        ]);
    }
}
