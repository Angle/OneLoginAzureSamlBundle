# OneLoginAzureSamlBundle
OneLogin SAML Bundle for Symfony, hardcoded for Azure AD specs

Soft forked from https://github.com/hslavich/OneloginSamlBundle, hardcoded for Azure AD.

Current target: Symfony 4.4 LTS

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
