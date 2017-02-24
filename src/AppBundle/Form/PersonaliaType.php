<?php


namespace AppBundle\Form;

use AppBundle\Entity\Personalia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PersonaliaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('firstName', TextType::class)
                ->add('lastName', TextType::class)
                ->add('placeOfResidence', TextType::class)
                ->add('dateOfBirth', DateType::class, array('widget' => 'single_text'))
                ->add('profileImageFile', FileType::class, array(
                    'block_name' => 'file_widget',
                    'label' => 'Foto',
                    'required' => false,
                    ))
                ->add('submit', SubmitType::class, array('label' => 'Opslaan'))
                ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Personalia::class,
            'csrf_protection' => false,
        ));
    }
}