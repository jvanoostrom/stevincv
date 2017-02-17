<?php


namespace AppBundle\Form;

use AppBundle\Entity\Curriculumvitae;
use AppBundle\Form\DataTransformer\TagsDataTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityRepository;

class CurriculumvitaeType extends AbstractType
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
                ->add('curriculumvitaeName', TextType::class)
                ->add('tags', TextType::class)
                ->add('profile', EntityType::class, array(
                    'expanded' => true,
                    'multiple' => false,
                    'class' => 'AppBundle:Profile',
                    'choice_label' => 'shortDescription',
                    'query_builder' => function (EntityRepository $er) use ($userId) {
                        return $er->createQueryBuilder('u')
                            ->where('u.user = '.$userId)
                            ->orderBy('u.updatedAt', 'ASC');
                    },
                    'choice_attr' => array('class' => 'with-gap'),
                ))

                ->add('curriculumvitaeProjects', CollectionType::class, array(
                    'entry_type' => CurriculumvitaeProjectType::class,
                    'entry_options' => array(
                        'userId' => $userId
                    ),
                    'allow_add' => true,
                    'allow_delete' => true,
                ))

                ->add('education', EntityType::class, array(
                    'expanded' => true,
                    'multiple' => true,
                    'class' => 'AppBundle:Education',
                    'choice_label' => 'educationName',
                    'query_builder' => function (EntityRepository $er) use ($userId) {
                        return $er->createQueryBuilder('u')
                            ->where('u.user = '.$userId)
                            ->orderBy('u.endDate', 'DESC');
                    },
                ))
                ->add('certificates', EntityType::class, array(
                    'expanded' => true,
                    'multiple' => true,
                    'class' => 'AppBundle:Certificate',
                    'choice_label' => 'certificateName',
                    'query_builder' => function (EntityRepository $er) use ($userId) {
                        return $er->createQueryBuilder('u')
                            ->where('u.user = '.$userId)
                            ->orderBy('u.obtainedDate', 'DESC');
                    },
                ))
                ->add('extracurricular', EntityType::class, array(
                    'expanded' => true,
                    'multiple' => true,
                    'class' => 'AppBundle:Extracurricular',
                    'choice_label' => 'extracurricularName',
                    'query_builder' => function (EntityRepository $er) use ($userId) {
                        return $er->createQueryBuilder('u')
                            ->where('u.user = '.$userId)
                            ->orderBy('u.endDate', 'DESC');
                    },
                    'required' => false
                ))
                ->add('publications', EntityType::class, array(
                    'expanded' => true,
                    'multiple' => true,
                    'class' => 'AppBundle:Publication',
                    'choice_label' => 'publicationTitle',
                    'query_builder' => function (EntityRepository $er) use ($userId) {
                        return $er->createQueryBuilder('u')
                            ->where('u.user = '.$userId)
                            ->orderBy('u.publishedDate', 'DESC');
                    },
                    'required' => false
                ))
                ->add('skills', EntityType::class, array(
                    'expanded' => true,
                    'multiple' => true,
                    'class' => 'AppBundle:Skill',
                    'choice_label' => 'skillText',
                    'query_builder' => function (EntityRepository $er) use ($userId) {
                        return $er->createQueryBuilder('u')
                            ->where('u.user = '.$userId)
                            ->orderBy('u.skillText', 'ASC');
                    },
                ))
                ->add('submit', SubmitType::class, array('label' => 'Opslaan'));

        $builder->get('tags')
            ->addModelTransformer(new TagsDataTransformer($this->em));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Curriculumvitae::class,
        ));
        $resolver->setRequired('userId');
    }

}