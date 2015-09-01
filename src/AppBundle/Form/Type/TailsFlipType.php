<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TailsFlipType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		->add('flipType', 'integer')
    	->add('tails', 'submit')
		;
	}

	public function getName()
	{
		return 'tailsFlip';
	}
}