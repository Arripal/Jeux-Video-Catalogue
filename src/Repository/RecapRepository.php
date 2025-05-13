<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Player;
use App\Entity\Recap;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recap>
 */
class RecapRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recap::class);
    }

    //    /**
    //     * @return Recap[] Returns an array of Recap objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }


    public function findAllByPlayer(Player $player): array
    {
        return $this->createQueryBuilder('recap')
            ->andWhere('recap.player = :player')
            ->setParameter('player', $player)
            ->getQuery()
            ->getResult();
    }

    public function findOneByApiGameId(string $api_game_id, Player $player): ?Recap
    {
        return $this->createQueryBuilder('recap')
            ->join('recap.game', 'game')
            ->where('game.apiID = :apiID')
            ->andWhere('recap.player = :player')
            ->setParameter('apiID', $api_game_id)
            ->setParameter('player', $player)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
