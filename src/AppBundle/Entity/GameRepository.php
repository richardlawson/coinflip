<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\User;

class GameRepository extends EntityRepository
{

	public function getFinishedGamesThatUserHasNotViewed(User $user){
		$query = $this->getEntityManager()->createQuery('SELECT g FROM AppBundle:Game g JOIN g.players p JOIN p.user u WHERE u.id =:userId AND g.gameState = 2 AND p.viewedGame = false');
		$query->setParameter('userId', $user->getId());
		$result = $query->getResult();
		return $result;
	}
}