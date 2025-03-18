<?php

namespace App\Repository;

use App\Entity\HomePageBlock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HomePageBlock>
 *
 * @method HomePageBlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method HomePageBlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method HomePageBlock[]    findAll()
 * @method HomePageBlock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HomePageBlockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HomePageBlock::class);
    }

//    /**
//     * @return HomePageBlock[] Returns an array of HomePageBlock objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?HomePageBlock
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
