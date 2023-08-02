<?php

namespace Angle\OneLoginAzureSamlBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class AngleOneLoginAzureSamlExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.php');

        $container->setParameter('angle_one_login_azure_saml.settings', $config);

        // at this point, parameters should have already been loaded, so we can read directly from them instead of traversing the $config array
        $azureAppId     = $config['azure_app_id'];
        $azureX509Cert  = $config['azure_x509_cert'];
        $appBaseUrl     = $config['app_base_url'];
        $appTrustProxy  = $config['app_trust_proxy'];

        $azureSamlUrl = 'https://login.microsoftonline.com/' . $azureAppId . '/saml2';

        $samlSettings = [
            'idp' => [
                'entityId' => $azureSamlUrl,
                'singleSignOnService' => [
                    'url' => $azureSamlUrl,
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                ],
                'singleLogoutService' => [
                    'url' => $azureSamlUrl,
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                ],
                'x509cert' => $azureX509Cert,
            ],
            'sp' => [
                'entityId' => $appBaseUrl . '/sp',
                'assertionConsumerService' => [
                    'url' => $appBaseUrl . '/saml/acs',
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
                ],
                'singleLogoutService' => [
                    'url' => $appBaseUrl . '/saml/logout',
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                //'privateKey' => '', // TODO: we won't be using signature nor encryption
                ],
            ],
            'baseUrl' => $appBaseUrl,
            'strict' => true,
            'debug' => true,
            'security' => [
                'use_attribute_friendly_name' => false, // Important to set to false
                'nameIdEncrypted' => false,
                'authnRequestsSigned' => false,
                'logoutRequestSigned' => false,
                'logoutResponseSigned' => false,
                'wantMessagesSigned' => false,
                'wantAssertionsSigned' => false,
                'wantNameIdEncrypted' => false,
                'requestedAuthnContext' => true,
                'signMetadata' => false,
                'wantXMLValidation' => true,
                'signatureAlgorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
                'digestAlgorithm' => 'http://www.w3.org/2001/04/xmlenc#sha256',
            ],
            /* TODO: we won't be passing contact details
            'contactPerson' => [
                //
            ],
            */
            /* TODO: we won't be passing organization details
            'organization' => [
                'en' => [
                    'name' => 'Example',
                    'displayname' => 'Example',
                    'url' => 'http://example.com',
                ],
            ],
            */
        ];

        // Compile the SAML Settings
        $container->setParameter('angle_one_login_azure_saml.azure_saml_settings', $samlSettings);

        // Allow use of ProxyVars. This must only be enabled if the appserver sits behind a _trusted_ proxy
        if ($appTrustProxy) {
            \OneLogin\Saml2\Utils::setProxyVars(true);
        }

        if (!empty($config['entityManagerName'])) {
            $container->setParameter('angle_one_login_azure_saml.entity_manager', $config['entityManagerName']);
        }
    }
}
