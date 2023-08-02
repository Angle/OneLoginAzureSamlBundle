<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services->load('Angle\\OneLoginAzureSamlBundle\\Security\\', __DIR__.'/../../Security/');

    $services->set(\OneLogin\Saml2\Auth::class)
        //->args(['%angle_one_login_azure_saml.settings%'])
        ->args(['%angle_one_login_azure_saml.azure_saml_settings%'])
    ;

    $services->set(\Angle\OneLoginAzureSamlBundle\Controller\SamlController::class);

    $services->set(\Angle\OneLoginAzureSamlBundle\Security\Firewall\SamlListener::class)
        ->parent(service('security.authentication.listener.abstract'))
        ->abstract()
        ->call('setOneLoginAuth', [service(\OneLogin\Saml2\Auth::class)])
    ;

    $services->set(\Angle\OneLoginAzureSamlBundle\Security\Http\Authenticator\SamlAuthenticator::class)
        ->tag('monolog.logger', ['channel' => 'security'])
        ->args([
            /* 0 */ abstract_arg('security.http_utils'),
            /* 1 */ abstract_arg('user provider'),
            /* 2 */ service(\OneLogin\Saml2\Auth::class),
            /* 3 */ abstract_arg('success handler'),
            /* 4 */ abstract_arg('failure handler'),
            /* 5 */ abstract_arg('options'),
            /* 6 */ null,  // user factory
            /* 7 */ service(\Symfony\Contracts\EventDispatcher\EventDispatcherInterface::class)->nullOnInvalid(),
            /* 8 */ service(\Psr\Log\LoggerInterface::class)->nullOnInvalid(),
            /* 9 */ '%angle_one_login_azure_saml.trust_proxy%'
        ])
    ;

    $services->set(\Angle\OneLoginAzureSamlBundle\EventListener\Security\SamlLogoutListener::class)
        ->tag('kernel.event_listener', ['event' => \Symfony\Component\Security\Http\Event\LogoutEvent::class])
    ;

    $services->set(\Angle\OneLoginAzureSamlBundle\EventListener\User\UserCreatedListener::class)
        ->abstract()
        ->args([
            service(\Doctrine\ORM\EntityManagerInterface::class)->nullOnInvalid(),
            false,  // persist_user
        ])
    ;

    $services->set(\Angle\OneLoginAzureSamlBundle\EventListener\User\UserModifiedListener::class)
        ->abstract()
        ->args([
            service(\Doctrine\ORM\EntityManagerInterface::class)->nullOnInvalid(),
            false,  // persist_user
        ])
    ;


    $deprecatedAliases = [
        'angle_one_login_azure_saml.user_provider' => \Angle\OneLoginAzureSamlBundle\Security\User\SamlUserProvider::class,
        'angle_one_login_azure_saml.saml_provider' => \Angle\OneLoginAzureSamlBundle\Security\Authentication\Provider\SamlProvider::class,
        'angle_one_login_azure_saml.saml_token_factory' => \Angle\OneLoginAzureSamlBundle\Security\Authentication\Token\SamlTokenFactory::class,
        'angle_one_login_azure_saml.saml_authentication_success_handler' => \Angle\OneLoginAzureSamlBundle\Security\Http\Authentication\SamlAuthenticationSuccessHandler::class,
        'angle_one_login_azure_saml.saml_listener' => \Angle\OneLoginAzureSamlBundle\Security\Firewall\SamlListener::class,
        'angle_one_login_azure_saml.saml_logout_listener' => \Angle\OneLoginAzureSamlBundle\EventListener\Security\SamlLogoutListener::class,
    ];

    foreach ($deprecatedAliases as $alias => $class) {
        $services->alias($alias, $class)->deprecate('angle/oneloginazuresaml-bundle', '2.1', '');
    }
};
