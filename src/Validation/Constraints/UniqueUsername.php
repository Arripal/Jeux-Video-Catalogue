<?php

namespace App\Validation\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueUsername extends Constraint
{

    public string $unique_username_constraint_violation_message = 'Le pseudonyme "{{ pseudonyme }}" est déjà utilisé.';

    public function validatedBy(): string
    {
        return \App\Validation\Constraints\Validator\UniqueUsernameValidator::class;
    }
}
