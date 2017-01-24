<?php


namespace AppBundle\Form;

use AppBundle\Entity\Curriculumvitae;
use AppBundle\Form\DataTransformer\TagsDataTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
        $this->userId = $options['userId'];

        $builder
                ->add('curriculumvitaeName', TextType::class)
                ->add('tags', TextType::class)
                ->add('profile', EntityType::class, array(
                    'expanded' => true,
                    'multiple' => false,
                    'class' => 'AppBundle:Profile',
                    'choice_label' => 'shortDescription',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->where('u.user = '.$this->userId)
                            ->orderBy('u.updatedAt', 'ASC');
                    },
                    'choice_attr' => array('class' => 'with-gap'),
                ))
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
                ->add('education', EntityType::class, array(
                    'expanded' => true,
                    'multiple' => true,
                    'class' => 'AppBundle:Education',
                    'choice_label' => 'educationName',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->where('u.user = '.$this->userId)
                            ->orderBy('u.endDate', 'DESC');
                    },
                ))
                ->add('submit', SubmitType::class, array('label' => 'Opslaan'))
                ->getForm();

        $builder->get('tags')
            ->addModelTransformer(new TagsDataTransformer($this->em));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Curriculumvitae::class,
            'userId' => null
        ));
    }

}