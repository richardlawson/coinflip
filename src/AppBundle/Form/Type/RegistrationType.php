<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		->add('firstName')
		->add('lastName')
		->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
		->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
		->add('plainPassword', 'repeated', array(
				'type' => 'password',
				'options' => array('translation_domain' => 'FOSUserBundle'),
				'first_options' => array('label' => 'form.password'),
				'second_options' => array('label' => 'form.password_confirmation'),
				'invalid_message' => 'fos_user.password.mismatch',
		));
	}
	
	public function setDefaultOptions(OptionsResolverInterface $resolver )
	{
	    $resolver->setDefaults( array(
	        'data_class' => 'AppBundle\Entity\User',
	        'intention'  => 'registration',
	    ));
	}
	

	public function getName()
	{
		return 'app_user_registration';
	}
}