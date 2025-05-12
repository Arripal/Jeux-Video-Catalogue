<?php

namespace App\Services;

use App\Exception\ValidationException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validation
{

    public function __construct(private ValidatorInterface $validator_interface, private SerializerInterface $serializer_interface) {}

    public function validate(mixed $value, Constraint|array|null $constraint = null, string|GroupSequence|array|null $groups = null)
    {
        $errors = $this->validator_interface->validate($value, $constraint, $groups);

        if (count($errors) > 0) {
            throw new ValidationException("La validation a échoué.", $errors);
        }

        return $value;
    }
}
