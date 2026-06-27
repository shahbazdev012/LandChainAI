<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class VerificationException extends RuntimeException
{
    public static function noVerifiableDocument(): self
    {
        return new self('Upload a document (an image scan works best) before running AI verification.');
    }
}
