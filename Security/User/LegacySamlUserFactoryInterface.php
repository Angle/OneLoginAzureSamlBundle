<?php

namespace Angle\OneLoginAzureSamlBundle\Security\User;

use Angle\OneLoginAzureSamlBundle\Security\Authentication\Token\SamlTokenInterface;

/**
 * @deprecated since 2.1
 */
interface LegacySamlUserFactoryInterface
{
    public function createUser(SamlTokenInterface $token);
}
