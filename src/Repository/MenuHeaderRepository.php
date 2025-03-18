<?php

namespace App\Repository;

use App\Entity\MenuHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MenuHeader>
 *
 * @method MenuHeader|null find($id, $lockMode = null, $lockVersion = null)
 * @method MenuHeader|null findOneBy(array $criteria, array $orderBy = null)
 * @method MenuHeader[]    findAll()
 * @method MenuHeader[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuHeaderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuHeader::class);
    }

//    /**
//     * @return MenuHeader[] Returns an array of MenuHeader objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MenuHeader
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
