<?php

namespace Angle\OneLoginAzureSamlBundle\Controller;

use Angle\OneLoginAzureSamlBundle\Exception\PublicMessageRuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;

use OneLogin\Saml2\Auth;

class SamlController extends AbstractController
{
    protected $samlAuth;

    public function __construct(Auth $samlAuth)
    {
        $this->samlAuth = $samlAuth;
    }

    public function login(Request $request)
    {
        $authErrorKey = Security::AUTHENTICATION_ERROR;
        $session = $targetPath = $error = null;

        if ($request->hasSession()) {
            $session = $request->getSession();
            $firewallName = array_slice(explode('.', trim($request->attributes->get('_firewall_context'))), -1)[0];
            $targetPath = $session->get('_security.'.$firewallName.'.target_path');
        }

        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        }

        if ($error instanceof \Exception) {
            if ($error instanceof BadCredentialsException) {
                throw new PublicMessageRuntimeException($error->getMessage(), 'User authentication with Azure SAML2 SSO failed. User is not authorized or registered for this Application, or bad credentials were provided.');
            }

            // make a best effort to display this error to the end user
            throw new PublicMessageRuntimeException($error->getMessage(), 'User authentication with Azure SAML2 SSO failed. ' . $error->getMessage()); // TODO: this will potentially make
        }

        $this->samlAuth->login($targetPath);
    }

    public function metadata()
    {
        $metadata = $this->samlAuth->getSettings()->getSPMetadata();

        $response = new Response($metadata);
        $response->headers->set('Content-Type', 'xml');

        return $response;
    }

    public function assertionConsumerService()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall.');
    }

    public function singleLogoutService()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }
}