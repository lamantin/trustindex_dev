<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /**
     * Cégenkénti statisztikák lekérdezése átlag szerint csökkenő sorrendben.
     * Miért: Az adatbázis sokkal gyorsabban aggregál (COUNT, AVG), mint a PHP memória.
     */
    public function getCompanyStatistics(?string $searchQuery = null): array
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r.companyName', 'COUNT(r.id) as reviewCount', 'AVG(r.rating) as averageRating')
            ->groupBy('r.companyName')
            ->orderBy('averageRating', 'DESC');

        // BÓNUSZ (2.5) - Keresés funkcionalitás integrálása a statisztikába
        if ($searchQuery) {
            $qb->andWhere('r.companyName LIKE :query')
                ->setParameter('query', '%'.$searchQuery.'%');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Legfrissebb vélemények lekérdezése keresési szűrővel.
     */
    public function findLatestReviews(?string $searchQuery = null): array
    {
        $qb = $this->createQueryBuilder('r')
            ->orderBy('r.createdAt', 'DESC');

        if ($searchQuery) {
            $qb->andWhere('r.companyName LIKE :query')
                ->setParameter('query', '%'.$searchQuery.'%');
        }

        return $qb->getQuery()->getResult();
    }
}
