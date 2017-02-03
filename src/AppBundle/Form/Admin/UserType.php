<?php


namespace AppBundle\Form\Admin;

use AppBundle\Entity\Personalia;
use AppBundle\Entity\User;
use AppBundle\Form\PersonaliaType;
use FOS\UserBundle\Util\LegacyFormHelper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('username', TextType::class)
                ->add('plainPassword', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'options' => array('translation_domain' => 'FOSUserBundle'),
                    'first_options' => array('label' => 'Wachtwoord'),
                    'second_options' => array('label' => 'Herhaal wachtwoord'),
                    'invalid_message' => 'fos_user.password.mismatch',
                ))
                ->add('roles', ChoiceType::class,array(
                    'multiple' => true,
                    'choices'  => array(
                        'Consultant' => 'ROLE_USER',
                        'Manager' => 'ROLE_ADMIN',
                        'Beheerder' => 'ROLE_SUPER_ADMIN',
                )))
                ->add('personalia', PersonaliaType::class)
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