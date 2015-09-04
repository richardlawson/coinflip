<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use AppBundle\Entity\Game;


class GameType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		->add('name', 'text')
    	->add('save', 'submit')
		;
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'AppBundle\Entity\Game',
			'validation_groups' => function(FormInterface $form){
				if(Game::$oldName){
					$data = $form->getData();
					if(Game::$oldName == $data->getName()){
						return array('Default');
					}
				}
				return array('Default','New');
			},
		));
	}

	public function getName()
	{
		return 'game';
	}
}