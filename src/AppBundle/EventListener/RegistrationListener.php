<?php
namespace AppBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class RegistrationListener implements EventSubscriberInterface {
	private $router;

	public function __construct(UrlGeneratorInterface $router) {
		$this->router = $router;
	}

	public static function getSubscribedEvents() {
		return [
				FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
		];
	}

	public function onRegistrationSuccess(FormEvent $event) {
		$url = $this->router->generate('game_home');
		$event->setResponse(new RedirectResponse($url));
	}
}