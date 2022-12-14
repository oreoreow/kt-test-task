<?php

namespace App\Products\Domain\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ProductImportForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('xml_file', FileType::class, [
                'label' => 'Import products from XML file',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                         'maxSize' => '400000k',
                         'mimeTypes' => [
                             'application/xml',
                             'application/x-xml',
                             'text/xml'
                         ],
                         'mimeTypesMessage' => 'Please upload a valid XML document',
                     ])
                ],
            ])->add('submit', SubmitType::class, ['label' => 'Start Import'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
                                   'data_class' => ProductImportForm::class,
       ]);
    }
}