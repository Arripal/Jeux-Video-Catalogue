<?php

namespace App\Validation\Constraints\Validator;

use App\Repository\UserRepository;
use App\Validation\Constraints\UniqueEmail;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use UnexpectedValueException;

class UniqueEmailValidator extends ConstraintValidator
{

    public function __construct(private readonly UserRepository $user_repository) {}

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueEmail) {
            throw new UnexpectedTypeException($constraint, UniqueEmail::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $existing_user = $this->user_repository->findOneBy(['email' => $value]);

        if ($existing_user) {
            $email = $existing_user->getEmail();

            $this->context->buildViolation($constraint->unique_email_constraint_violation_message)->setParameter('{{ email }}', $email)->addViolation();
            return;
        }
    }
}
