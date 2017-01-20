<?php


namespace AppBundle\Form;

use AppBundle\Entity\Profile;
use AppBundle\Form\DataTransformer\TagsDataTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProfileType extends AbstractType
{

    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('shortDescription', TextType::class)
                ->add('tags', TextType::class)
                ->add('quoteLine', TextType::class)
                ->add('profileText', TextareaType::class)
                ->add('submit', SubmitType::class, array('label' => 'Opslaan'))
                ->getForm();

        $builder->get('tags')
            ->addModelTransformer(new TagsDataTransformer($this->em));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Profile::class,
        ));
    }
}