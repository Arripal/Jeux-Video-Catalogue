<?php

namespace App\Validation\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class UniqueRecap extends Constraint
{

    public string $unique_recap_constraint_violation_message = 'Un récapitulatif existe déjà pour ce jeu.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return \App\Validation\Constraints\Validator\UniqueRecapValidator::class;
    }
}
