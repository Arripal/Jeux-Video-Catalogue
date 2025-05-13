<?php

namespace App\Validation\Constraints;

use Symfony\Component\Validator\Constraint;


#[\Attribute]
class UniqueEmail extends Constraint
{
    public string $unique_email_constraint_violation_message = 'Le mail "{{ email }}" est déjà utilisé.';

    public function validatedBy(): string
    {
        return \App\Validation\Constraints\Validator\UniqueEmailValidator::class;
    }
}
