<?php

namespace Angle\OneLoginAzureSamlBundle\Exception;

use RuntimeException;
use Throwable;

class PublicMessageRuntimeException extends RuntimeException
{
    private $publicMessage = '';

    public function __construct(string $message, string $publicMessage)
    {
        parent::__construct($message, 0, null);
        $this->publicMessage = $publicMessage;
    }

    public function getPublicMessage(): string
    {
        return $this->publicMessage;
    }
}