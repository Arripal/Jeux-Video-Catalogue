<?php


namespace App\Utils;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class FormatingErrors
{
    public static function format(array | ConstraintViolationListInterface $errors): array
    {
        $error_messages = [];
        foreach ($errors as $error) {
            $error_messages[$error->getPropertyPath()] = $error->getMessage();
        }

        return $error_messages;
    }
}
