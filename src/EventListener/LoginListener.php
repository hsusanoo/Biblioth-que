<?php

namespace App\EventListener;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{

    private $manager;

    /**
     * LoginListener constructor.
     * @param $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        // Get current user
        $user = $event->getAuthenticationToken()->getUser();

        // Update last login field
        $user->setLastLogin(new DateTime());

        // Persist to DB
        $this->manager->persist($user);
        $this->manager->flush();
    }

}
