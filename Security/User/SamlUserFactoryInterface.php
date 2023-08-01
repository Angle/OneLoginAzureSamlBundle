<?php

namespace Angle\OneLoginAzureSamlBundle\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;

interface SamlUserFactoryInterface
{
    /**
     * Creates a new User object.
     */
    public function createUser($username, array $attributes = []): UserInterface;
}
