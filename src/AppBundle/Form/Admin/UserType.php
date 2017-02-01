<?php


namespace AppBundle\Form\Admin;

use AppBundle\Entity\User;
use FOS\UserBundle\Util\LegacyFormHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
                ->add('plainPassword', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\RepeatedType'), array(
                    'type' => LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\PasswordType'),
                    'options' => array('translation_domain' => 'FOSUserBundle'),
                    'first_options' => array('label' => 'form.password'),
                    'second_options' => array('label' => 'form.password_confirmation'),
                    'invalid_message' => 'fos_user.password.mismatch',
                ))
                ->add('roles', ChoiceType::class,array(
                    'multiple' => true,
                    'choices'  => array(
                        'Consultant' => 'ROLE_USER',
                        'Manager' => 'ROLE_ADMIN',
                        'Beheerder' => 'ROLE_SUPER_ADMIN',
                )))
                ->add('firstName', TextType::class, array('mapped' => false))
                ->add('lastName', TextType::class, array('mapped' => false))
                ->add('placeOfResidence', TextType::class, array('mapped' => false))
                ->add('dateOfBirth', DateType::class, array('widget' => 'single_text', 'format' => 'dd-MM-yyyy', 'mapped' => false))
//                ->add('profileImageFile', FileType::class, array(
//                    'block_name' => 'file_widget',
//                    'label' => 'Foto',
//                    'required' => false,
//                    ))
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