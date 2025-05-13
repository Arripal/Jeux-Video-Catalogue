<?php

namespace App\Validation\Constraints\Validator;

use App\Repository\PlayerRepository;
use App\Validation\Constraints\UniqueUsername;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use UnexpectedValueException;

class UniqueUsernameValidator extends ConstraintValidator
{

    public function __construct(private readonly PlayerRepository $player_repository) {}

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueUsername) {
            throw new UnexpectedTypeException($constraint, UniqueUsername::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $existing_player = $this->player_repository->findOneBy(['username' => $value]);

        if ($existing_player) {

            $username = $existing_player->getUsername();

            $this->context->buildViolation($constraint->unique_username_constraint_violation_message)->setParameter('{{ pseudonyme }}', $username)->addViolation();
            return;
        }
    }
}
