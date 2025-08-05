<?php

namespace App\Repository;

use App\Entity\TenantMedia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TenantMedia>
 *
 * @method TenantMedia|null find($id, $lockMode = null, $lockVersion = null)
 * @method TenantMedia|null findOneBy(array $criteria, array $orderBy = null)
 * @method TenantMedia[]    findAll()
 * @method TenantMedia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TenantMediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TenantMedia::class);
    }

//    /**
//     * @return TenantMedia[] Returns an array of TenantMedia objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TenantMedia
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
