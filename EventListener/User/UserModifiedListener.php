<?php

namespace Angle\OneLoginAzureSamlBundle\EventListener\User;

use Angle\OneLoginAzureSamlBundle\Event\UserModifiedEvent;

class UserModifiedListener extends AbstractUserListener
{
    public function __invoke(UserModifiedEvent $event)
    {
        $this->handleEvent($event);
    }
}
