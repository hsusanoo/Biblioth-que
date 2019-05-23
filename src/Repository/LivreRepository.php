<?php

namespace App\Repository;

use App\Entity\Livre;
use App\Entity\User;
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
     * @param $start
     * @param $end
     * @return mixed
     * @throws \Exception
     */
    public function findByDateRange($start, $end)
    {
        $startDate = new \DateTime(str_replace('/', '-', $start) . ' 00:00:00');
        $endDate = new \DateTime(str_replace('/', '-', $end) . ' 23:59:59');

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

    /**
     * @param $year
     * @param $month
     * @return mixed
     * @throws \Exception
     */
    public function findByYearAndMonth($year, $month)
    {
        $endDay = '31';

        switch ($month) {
            case 2 :
                $endDay = '28';
                break;
            case (4 || 6 || 9 || 11 || 10 || 12):
                $endDay = '30';
                break;
        }

        $startDate = new \DateTime('01-' . $month . '-' . $year . ' 00:00:00');
        $endDate = new \DateTime($endDay . '-' . $month . '-' . $year . ' 23:59:59');

        return $this->createQueryBuilder('l')
            ->andWhere('l.dateAquis BETWEEN :from and :to')
            ->setParameter('from', $startDate)
            ->setParameter('to', $endDate)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Livre[]
     */
    public function findBySearchQuery(string $rawQuery): array
    {
        $query = $this->sanitizeSearchQuery($rawQuery);
        $searchTerms = $this->extractSearchTerms($query);

        if (0 === \count($searchTerms)) {
            return [];
        }

        $queryBuilder = $this->createQueryBuilder('l');

        foreach ($searchTerms as $key => $term) {
            $queryBuilder
                ->orWhere('l.titrePrincipale LIKE :t_'.$key)
                ->orWhere('l.titreSecondaire LIKE :t_'.$key)
                ->orWhere('l.observation LIKE :t_'.$key)
                ->setParameter('t_'.$key, '%'.$term.'%')
            ;
        }

        return $queryBuilder
            ->orderBy('l.dateAquis', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Removes all non-alphanumeric characters except whitespaces.
     */
    private function sanitizeSearchQuery(string $query): string
    {
        return trim(preg_replace('/[[:space:]]+/', ' ', $query));
    }

    /**
     * Splits the search query into terms and removes the ones which are irrelevant.
     */
    private function extractSearchTerms(string $searchQuery): array
    {
        $terms = array_unique(explode(' ', $searchQuery));

        return array_filter($terms, function ($term) {
            return 2 <= mb_strlen($term);
        });
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
