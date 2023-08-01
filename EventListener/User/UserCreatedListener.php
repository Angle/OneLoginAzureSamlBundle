<?php

namespace Angle\OneLoginAzureSamlBundle\EventListener\User;

use Angle\OneLoginAzureSamlBundle\Event\UserCreatedEvent;

class UserCreatedListener extends AbstractUserListener
{
    public function __invoke(UserCreatedEvent $event)
    {
        $this->handleEvent($event);
    }
}
