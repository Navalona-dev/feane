<?php

namespace App\Repository;

use App\Entity\MenuRestaurant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MenuRestaurant>
 *
 * @method MenuRestaurant|null find($id, $lockMode = null, $lockVersion = null)
 * @method MenuRestaurant|null findOneBy(array $criteria, array $orderBy = null)
 * @method MenuRestaurant[]    findAll()
 * @method MenuRestaurant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuRestaurantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuRestaurant::class);
    }

//    /**
//     * @return MenuRestaurant[] Returns an array of MenuRestaurant objects
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

//    public function findOneBySomeField($value): ?MenuRestaurant
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
