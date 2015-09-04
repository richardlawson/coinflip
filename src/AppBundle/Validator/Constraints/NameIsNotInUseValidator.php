<?php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;

class NameIsNotInUseValidator extends ConstraintValidator
{
	private $em;
	
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}
	
    public function validate($value, Constraint $constraint)
    {
    	$isInUse = $this->em->getRepository('AppBundle:Game')->isNameInUse($value);
        if($isInUse){
            // If you're using the new 2.5 validation API (you probably are!)
            $this->context->buildViolation($constraint->message)
                ->setParameter('%string%', $value)
                ->addViolation();
        }
    }
}