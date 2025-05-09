<?php

namespace App\Validation\Constraints\Validator;

use App\Entity\Game;
use App\Entity\Player;
use App\Repository\RecapRepository;
use App\Validation\Constraints\UniqueRecap;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueRecapValidator extends ConstraintValidator
{
    public function __construct(private RecapRepository $recap_repository, private EntityManagerInterface $entity_manager) {}

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueRecap) {
            throw new \InvalidArgumentException('La contrainte est invalide.');
        }

        if (!$value instanceof \App\Validation\Recap) {
            return;
        }
        /*
        $player = $this->entity_manager->getRepository(Player::class)->findOneBy([
            'username' => $value->getPlayer()->getUsername(),
        ]);

        $game = $this->entity_manager->getRepository(Game::class)->findOneBy([
            'apiID' => $value->getGame()->getApiID(),
        ]);
*/

        $existing_recap = $this->recap_repository->createQueryBuilder('r')
            ->join('r.player', 'p')
            ->join('r.game', 'g')
            ->where('p.username = :username')
            ->andWhere('g.apiID = :apiID')
            ->setParameter('username', $value->getPlayer()->getUsername())
            ->setParameter('apiID', $value->getGame()->getApiID())
            ->getQuery()
            ->getOneOrNullResult();

        if ($existing_recap) {
            $this->context->buildViolation($constraint->unique_recap_constraint_violation_message)
                ->addViolation();
            return;
        }
    }
}
