<?php

namespace App\Exception;

use RuntimeException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends RuntimeException
{
    private ?ConstraintViolationListInterface $errors = null;

    public function __construct(string $message, ?ConstraintViolationListInterface $errors = null)
    {
        parent::__construct($message);
        $this->errors = $errors;
    }

    public function getErrors(): ?ConstraintViolationListInterface
    {
        return $this->errors;
    }
}
