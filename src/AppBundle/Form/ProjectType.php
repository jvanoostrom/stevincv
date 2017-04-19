<?php


namespace AppBundle\Form;

use AppBundle\Entity\Project;
use Symfony\Component\Form\AbstractType;
use AppBundle\Form\DataTransformer\TagsDataTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProjectType extends AbstractType
{

    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('projectName', TextType::class)
                ->add('customerName', TextType::class)
                ->add('tags', TextType::class)
                ->add('functionTitle', TextType::class)
                ->add('situationText', TextareaType::class)
                ->add('taskText', TextareaType::class)
                ->add('resultText', TextareaType::class)
                ->add('startDate', DateType::class, array(
                    'widget' => 'text',
                    'label' => 'Startdatum',
                    ))
                ->add('endDate', DateType::class, array(
                    'widget' => 'text',
                    'label' => 'Einddatum',
                    'required' => false
                    ))
                ->add('submit', SubmitType::class, array('label' => 'Opslaan'))
                ->getForm();

        $builder->get('tags')
            ->addModelTransformer(new TagsDataTransformer($this->em));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Project::class,
        ));
    }
}