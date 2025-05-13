<?php

namespace App\Validation\Constraints;


use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ValidImage extends Constraint
{
    public string $invalidExtensionMessage = 'Extension "{{ extension }}" non autorisée.';
    public string $invalidMimeMessage = 'Le fichier n\'est pas une image valide.';

    public function validatedBy(): string
    {
        return \App\Validation\Constraints\Validator\ValidImageValidator::class;
    }
}
