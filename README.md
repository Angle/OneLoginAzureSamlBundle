# OneLoginAzureSamlBundle
OneLogin SAML Bundle for Symfony, hardcoded for Azure AD specs

Soft forked from https://github.com/hslavich/OneloginSamlBundle, hardcoded for Azure AD.

Current target: Symfony 4.4 LTS


Design goals:
- User should specify the "Azure App ID" and the rest of the SAML settings should be predefined and autowired. Required parameters:
  - azure_app_id (abcd123789....)
  - base_url (https://myapp.com)
- Create a command that outputs the current SP settings (basically, should print out the EntityID using the router)


## Installation
Install with composer

```
composer require anglemx/onelogin-azure-saml-bundle
```

Enable the bundle in `config/bundles.php` if you're not using Symfony Flex.

```
return [
    // ...
    Angle\OneLoginAzureSamlBundle\AngleOneLoginAzureSamlBundle::class => ['all' => true],
]
```

## Configuration
_TO-DO_

REFERENCE FROM hslavich repository

Configure SAML metadata in `config/packages/hslavich_onelogin_saml.yaml`. Check https://github.com/onelogin/php-saml#settings for more info.
``` yml
hslavich_onelogin_saml:
    # Basic settings
    idp:
        entityId: 'http://id.example.com/saml2/idp/metadata.php'
        singleSignOnService:
            url: 'http://id.example.com/saml2/idp/SSOService.php'
            binding: 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect'
        singleLogoutService:
            url: 'http://id.example.com/saml2/idp/SingleLogoutService.php'
            binding: 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect'
        x509cert: ''
    sp:
        entityId: 'http://myapp.com/app_dev.php/saml/metadata'
        assertionConsumerService:
            url: 'http://myapp.com/app_dev.php/saml/acs'
            binding: 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'
        singleLogoutService:
            url: 'http://myapp.com/app_dev.php/saml/logout'
            binding: 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect'
        privateKey: ''    
    # Optional settings
    baseurl: 'http://myapp.com'
    strict: true
    debug: true    
    security:
        nameIdEncrypted: false
        authnRequestsSigned: false
        logoutRequestSigned: false
        logoutResponseSigned: false
        wantMessagesSigned: false
        wantAssertionsSigned: false
        wantNameIdEncrypted: false
        requestedAuthnContext: true
        signMetadata: false
        wantXMLValidation: true
        relaxDestinationValidation: false
        destinationStrictlyMatches: true
        rejectUnsolicitedResponsesWithInResponseTo: false
        signatureAlgorithm: 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256'
        digestAlgorithm: 'http://www.w3.org/2001/04/xmlenc#sha256'
    contactPerson:
        technical:
            givenName: 'Tech User'
            emailAddress: 'techuser@example.com'
        support:
            givenName: 'Support User'
            emailAddress: 'supportuser@example.com'
        administrative:
            givenName: 'Administrative User'
            emailAddress: 'administrativeuser@example.com'
    organization:
        en:
            name: 'Example'
            displayname: 'Example'
            url: 'http://example.com'
```


Custom notes:

Azure App ID is used to cnstruct the EntityId and other URLs

https://login.microsoftonline.com/xxxXXXxxxXXX/saml2

The setting `use_attribute_friendly_name` inside the security.yml should be FALSE:

use_attribute_friendly_name: false

Azure AD configuration reference:

```yaml
idp:
        entityId: 'https://login.microsoftonline.com/xxxXXXxxxXXX/saml2'
        singleSignOnService:
            url: 'https://login.microsoftonline.com/xxxXXXxxxXXX/saml2'
            binding: 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect'
        singleLogoutService:
            url: 'https://login.microsoftonline.com/xxxXXXxxxXXX/saml2'
            binding: 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect'
        x509cert: 'xxxXXXxxxXXXxxxXXXxxxXXXxxxXXXxxxXXXxxxXXXxxxXXX'
    sp:
        entityId: 'https://myapp.com/saml/metadata'
        assertionConsumerService:
            url: 'https://myapp.com/saml/acs'
            binding: 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'
        singleLogoutService:
            url: 'https://myapp.com/saml/logout'
            binding: 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect'
        privateKey: '-----BEGIN PRIVATE KEY-----
                    xxxXXXxxxXXXxxxXXXxxxXXXxxxXXXxxxXXX
                     -----END PRIVATE KEY-----'

    # Optional settings
    baseurl: 
    strict: true
    debug: true
    security:
        nameIdEncrypted:       false
        authnRequestsSigned:   false
        logoutRequestSigned:   false
        logoutResponseSigned:  false
        wantMessagesSigned:    false
        wantAssertionsSigned:  false
        wantNameIdEncrypted:   false
        requestedAuthnContext: true
        signMetadata: false
        wantXMLValidation: true
        signatureAlgorithm: 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256'
        digestAlgorithm: 'http://www.w3.org/2001/04/xmlenc#sha256'
    contactPerson:
        technical:
            givenName: 'Tech User'
            emailAddress: 'techuser@example.com'
        support:
            givenName: 'Support User'
            emailAddress: 'supportuser@example.com'
```


Configure SAML metadata in `config/packages/hslavich_onelogin_saml.yaml`. Check https://github.com/onelogin/php-saml#settings for more info.
``` yml
angle_onelogin_azure_saml:
    idp:
        entityId: ''


hslavich_onelogin_saml:
    # Basic settings
    idp:
        entityId: 'http://id.example.com/saml2/idp/metadata.php'
        singleSignOnService:
            url: 'http://id.example.com/saml2/idp/SSOService.php'
            binding: 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect'
        singleLogoutService:
            url: 'http://id.example.com/saml2/idp/SingleLogoutService.php'
            binding: 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect'
        x509cert: ''
    sp:
        entityId: 'http://myapp.com/app_dev.php/saml/metadata'
        assertionConsumerService:
            url: 'http://myapp.com/app_dev.php/saml/acs'
            binding: 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'
        singleLogoutService:
            url: 'http://myapp.com/app_dev.php/saml/logout'
            binding: 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect'
        privateKey: ''    
    # Optional settings
    baseurl: 'http://myapp.com'
    strict: true
    debug: true    
    security:
        nameIdEncrypted: false
        authnRequestsSigned: false
        logoutRequestSigned: false
        logoutResponseSigned: false
        wantMessagesSigned: false
        wantAssertionsSigned: false
        wantNameIdEncrypted: false
        requestedAuthnContext: true
        signMetadata: false
        wantXMLValidation: true
        relaxDestinationValidation: false
        destinationStrictlyMatches: true
        rejectUnsolicitedResponsesWithInResponseTo: false
        signatureAlgorithm: 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256'
        digestAlgorithm: 'http://www.w3.org/2001/04/xmlenc#sha256'
    contactPerson:
        technical:
            givenName: 'Tech User'
            emailAddress: 'techuser@example.com'
        support:
            givenName: 'Support User'
            emailAddress: 'supportuser@example.com'
        administrative:
            givenName: 'Administrative User'
            emailAddress: 'administrativeuser@example.com'
    organization:
        en:
            name: 'Example'
            displayname: 'Example'
            url: 'http://example.com'
```


If you don't want to set contactPerson or organization, don't add those parameters instead of leaving them blank.

Configure firewall and user provider in `config/packages/security.yaml`
``` yml
security:
    # ...

    providers:
        saml_provider:
            # Basic provider instantiates a user with default roles
            saml:
                user_class: 'AppBundle\Entity\User'
                default_roles: ['ROLE_USER']

    firewalls:
        app:
            pattern: ^/
            saml:
                # Match SAML attribute 'uid' with username.
                # Uses getNameId() method by default.
                username_attribute: uid
                # Use the attribute's friendlyName instead of the name 
                # NOTE: Azure requires this to be FALSE
                use_attribute_friendly_name: false
                check_path: saml_acs
                login_path: saml_login
            logout:
                path: saml_logout

    access_control:
        - { path: ^/saml/login, roles: PUBLIC_ACCESS }
        - { path: ^/saml/metadata, roles: PUBLIC_ACCESS }
        - { path: ^/, roles: ROLE_USER }
```

Edit your `config/routing` or `config/routes.yaml` depending on your Symfony version.
``` yml
hslavich_saml_sp:
    resource: "@HslavichOneloginSamlBundle/Resources/config/routing.yml"