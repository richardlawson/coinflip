<?php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NameIsNotInUse extends Constraint
{
	public $message = 'Name "%string%" is already in use by another game';
	
	public function validatedBy()
	{
		return 'name_is_not_in_use';
	}
}