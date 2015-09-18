<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TailsFlipType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		->add('flipType', 'hidden')
    	->add('tails', 'submit', array('attr' => array('class' => 'btn btn-danger')))
		;
	}

	public function getName()
	{
		return 'tailsFlip';
	}
}