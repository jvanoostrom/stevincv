<?php


namespace AppBundle\Form;

use AppBundle\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('customerName', TextType::class)
                ->add('functionTitle', TextType::class)
                ->add('customerProfile', TextareaType::class)
                ->add('taskText', TextareaType::class)
                ->add('resultText', TextareaType::class)
                ->add('startDate', DateType::class, array(
                    'widget' => 'single_text',
                    ))
                ->add('endDate', DateType::class, array(
                    'widget' => 'single_text',
                    ))
                ->add('submit', SubmitType::class, array('label' => 'Opslaan'))
                ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Project::class,
        ));
    }
}