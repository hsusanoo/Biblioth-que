<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Livre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livre[]    findAll()
 * @method Livre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Livre::class);
    }

    /**
     * @return Livre[] Returns an array of Livre objects
     * @param $category
     * @param $year
     * @return mixed
     * @throws \Exception
     */
    public function getByCategoryAndYear($category, $year)
    {
        $startDate = new \DateTime('01-01-' . $year . ' 00:00:00');
        $endDate = new \DateTime('31-12-' . $year . ' 23:59:59');

        return $this->createQueryBuilder('l')
            ->andWhere('l.categorie = :category')
            ->andWhere('l.dateAquis BETWEEN :from and :to')
            ->setParameters([
                'category' => $category,
                'from' => $startDate,
                'to' => $endDate
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Livre[] Returns an array of Livre objects
     * @param $year
     * @return mixed
     * @throws \Exception
     */
    public function getByYear($year)
    {

        $startDate = new \DateTime('01-01-' . $year . ' 00:00:00');
        $endDate = new \DateTime('31-12-' . $year . ' 23:59:59');

        return $result = $this->createQueryBuilder('l')
            ->andWhere('l.dateAquis BETWEEN :from and :to')
            ->setParameters([
                'from' => $startDate,
                'to' => $endDate
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $year
     * @return Livre[] Returns an array of Livre objects
     * @throws \Exception
     */
    public function findByYear($year)
    {
        $startDate = new \DateTime('01-01-' . $year . ' 00:00:00');
        $endDate = new \DateTime('31-12-' . $year . ' 23:59:59');

        return $this->createQueryBuilder('l')
            ->andWhere('l.dateAquis BETWEEN :from and :to')
            ->setParameter('from', $startDate)
            ->setParameter('to', $endDate)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Livre[] Returns an array of Livre objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Livre
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
