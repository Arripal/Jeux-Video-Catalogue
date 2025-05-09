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

    public function validate(string $class_name, string|array $data, array $options = [])
    {
        $constraints = null;
        $groups = null;

        if (array_key_exists('constraints', $options)) {
            if (
                $options['constraints'] instanceof Constraint ||
                is_string($options['constraints']) ||
                is_array($options['constraints'])
            ) {
                $constraints = $options['constraints'];
            }
        }

        if (array_key_exists('groups', $options)) {
            if (
                $options['groups'] instanceof GroupSequence ||
                is_string($options['groups']) ||
                is_array($options['groups'])
            ) {
                $groups = $options['groups'];
            }
        }

        try {
            if (is_array($data)) {
                $data = json_encode($data);
            }

            $object = $this->serializer_interface->deserialize($data, $class_name, 'json');
        } catch (ExceptionInterface $e) {
            throw new ValidationException("Erreur de désérialisation : " . $e->getMessage());
        }

        $errors = $this->validator_interface->validate($object, $constraints, $groups);

        if (count($errors) > 0) {
            throw new ValidationException("La validation a échoué.", $errors);
        }

        return $object;
    }
}
