<?php

namespace Angle\OneLoginAzureSamlBundle;

use Angle\OneLoginAzureSamlBundle\DependencyInjection\Compiler\SecurityCompilerPass;
use Angle\OneLoginAzureSamlBundle\DependencyInjection\Security\Factory\SamlFactory;
use Angle\OneLoginAzureSamlBundle\DependencyInjection\Security\Factory\SamlUserProviderFactory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AngleOneLoginAzureSamlBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addAuthenticatorFactory(new SamlFactory());
        $extension->addUserProviderFactory(new SamlUserProviderFactory());

        $container->addCompilerPass(new SecurityCompilerPass());
    }
}