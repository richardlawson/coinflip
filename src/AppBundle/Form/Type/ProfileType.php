<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ProfileType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		->add('firstName')
		->add('lastName')
		->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
		->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
		->add('current_password', 'password', array(
				'label' => 'form.current_password',
				'translation_domain' => 'FOSUserBundle',
				'mapped' => false,
				'constraints' => new UserPassword(),
		));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver )
	{
	    $resolver->setDefaults( array(
	        'data_class' => 'AppBundle\Entity\User',
	        'intention'  => 'profile',
	    ));
	}

	public function getName()
	{
		return 'app_user_profile';
	}
}