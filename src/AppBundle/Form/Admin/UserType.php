<?php


namespace AppBundle\Form\Admin;

use AppBundle\Entity\User;
use AppBundle\Form\PersonaliaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('username', EmailType::class)
                ->add('roles', ChoiceType::class,array(
                    'multiple' => true,
                    'choices'  => array(
                        'ZZP' => 'ROLE_ZZP',
                        'Consultant' => 'ROLE_USER',
                        'Manager' => 'ROLE_ADMIN',
                        'Beheerder' => 'ROLE_SUPER_ADMIN',
                )))
                ->add('personalia', PersonaliaType::class)
                ->add('enabled', CheckboxType::class, array('required' => false))
                ->add('rateTariff', NumberType::class, array('scale' => 2))
                ->add('getThreeMonthsEmail', CheckboxType::class, array('required' => false))
                ->add('submit', SubmitType::class, array('label' => 'Opslaan'))
                ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }

    public function getName()
    {
        return 'app_user_registration';
    }

}