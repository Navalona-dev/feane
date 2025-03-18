<?php

namespace App\Repository;

use App\Entity\DropdownMenu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DropdownMenu>
 *
 * @method DropdownMenu|null find($id, $lockMode = null, $lockVersion = null)
 * @method DropdownMenu|null findOneBy(array $criteria, array $orderBy = null)
 * @method DropdownMenu[]    findAll()
 * @method DropdownMenu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DropdownMenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DropdownMenu::class);
    }

//    /**
//     * @return DropdownMenu[] Returns an array of DropdownMenu objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DropdownMenu
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
