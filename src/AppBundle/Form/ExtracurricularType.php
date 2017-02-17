<?php


namespace AppBundle\Form;

use AppBundle\Entity\Extracurricular;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ExtracurricularType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('extracurricularName', TextType::class)
                ->add('startDate', DateType::class, array(
                    'widget' => 'single_text',
                    ))
                ->add('endDate', DateType::class, array(
                    'widget' => 'single_text',
                    'required' => 'false'
                    ))
                ->add('submit', SubmitType::class, array('label' => 'Opslaan'))
                ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Extracurricular::class,
        ));
    }
}