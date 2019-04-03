<?php

namespace App\Repository;

use App\Entity\Descripteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Descripteur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Descripteur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Descripteur[]    findAll()
 * @method Descripteur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DescripteurRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Descripteur::class);
    }

    // /**
    //  * @return Descripteur[] Returns an array of Descripteur objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Descripteur
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
