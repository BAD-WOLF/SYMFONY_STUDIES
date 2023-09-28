<?php

namespace App\Repository;

use App\Entity\Pessoas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pessoas>
 *
 * @method Pessoas|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pessoas|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pessoas[]    findAll()
 * @method Pessoas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PessoasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pessoas::class);
    }

//    /**
//     * @return Pessoas[] Returns an array of Pessoas objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Pessoas
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
