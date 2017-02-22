<?php


namespace AppBundle\Form;

use AppBundle\Entity\CurriculumvitaeProject;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
        $userId = $options['userId'];

        $builder
                ->add('project', EntityType::class, array(
                    'class' => 'AppBundle:Project',
                    'choice_label' => function ($project) {
                        return $project->getFunctionTitle()." - ".$project->getCustomerName();
                    },
                    'query_builder' => function (EntityRepository $er) use ($userId) {
                        return $er->createQueryBuilder('u')
                            ->where('u.user = '.$userId)
                            ->orderBy('u.endDate', 'DESC');
                    },

                ))
                ->add('important', CheckboxType::class, array(
                    'required' => false,
                    'data' => false
                ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => CurriculumvitaeProject::class,
        ));
        $resolver->setRequired('userId');
    }

}