<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Game>
 *
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function findPendingMatches()
    {
        return $this->createQueryBuilder('m')
            ->where('m.status = :status')
            ->setParameter('status', 'pending')
            ->getQuery()
            ->getResult();
    }

    public function findPendingMatchesForUser(int $userId): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.status = :status')
            ->andWhere('g.player1 = :userId OR g.player2 = :userId')
            ->setParameter('status', 'pending')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findOngoingMatchForUser(int $userId): ?Game
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.status = :status')
            ->andWhere('(g.player1 = :userId OR g.player2 = :userId) AND g.expiresAt > :currentDate')
            ->setParameter('status', 'ongoing')
            ->setParameter('userId', $userId)
            ->setParameter('currentDate', new \DateTime())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function updateMatchStatus(Game $game): void
    {
        $this->getEntityManager()->persist($game);
        $this->getEntityManager()->flush();
    }

    public function add(Game $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Game $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Game[] Returns an array of Game objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Game
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
