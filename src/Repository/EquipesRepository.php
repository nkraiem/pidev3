<?php

namespace App\Repository;

use App\Entity\Equipes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Equipes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Equipes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Equipes[]    findAll()
 * @method Equipes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipes::class);
    }

    // /**
    //  * @return Equipes[] Returns an array of Equipes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Equipes
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findStartingWith($recherche)
    {
        return $this->createQueryBuilder('c')
            ->where('c.nom LIKE :val')
            ->setParameter("val", $recherche . '%')
            ->getQuery()
            ->getResult();
    }

    public function victoire()
    {
        return $this->createQueryBuilder('p')
                    ->select('count(p.nbr_vic)')
                    ->getQuery()
                    ->getSingleScalarResult();
    }

    public function defaite()
    {
        return $this->createQueryBuilder('p')
                    ->select('count(p.nbr_per)')
                    ->getQuery()
                    ->getSingleScalarResult();
    }

    public function nul()
    {
        return $this->createQueryBuilder('p')
                    ->select('count(p.nbr_null)')
                    ->getQuery()
                    ->getSingleScalarResult();
    }

}
