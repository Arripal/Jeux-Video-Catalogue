<?php

namespace App\Validation\Constraints\Validator;

use App\enums\ImageTypes;
use App\Validation\Constraints\ValidImage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidImageValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof ValidImage) {
            throw new UnexpectedTypeException($constraint, ValidImage::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!$value instanceof UploadedFile) {
            return;
        }

        //Validation de l'extension du fichier
        $extension = strtolower($value->getClientOriginalExtension());
        $valid_extensions = array_column(ImageTypes::cases(), 'value');

        if (!in_array($extension, $valid_extensions, true)) {
            $this->context->buildViolation($constraint->invalidExtensionMessage)->setParameter('{{ extension }}', $extension)->addViolation();
            return;
        }

        //Vérification du Mime Type du fichier

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($value->getPathname());

        if (!str_starts_with($mime_type, 'image/')) {
            $this->context->buildViolation($constraint->invalidMimeMessage)->addViolation();
        }
    }
}
