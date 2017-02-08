<?php


namespace AppBundle\Form;

use AppBundle\Entity\Curriculumvitae_Project;
use AppBundle\Form\DataTransformer\TagsDataTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class CurriculumvitaeProjectType extends AbstractType
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->userId = $options['userId'];

        $builder
                ->add('projects', EntityType::class, array(
                    'expanded' => true,
                    'multiple' => true,
                    'class' => 'AppBundle:Project',
                    'choice_label' => 'functionTitle',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->where('u.user = '.$this->userId)
                            ->orderBy('u.endDate', 'DESC');
                    },
                ))
                ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Curriculumvitae_Project::class,
            'userId' => null
        ));
    }

}