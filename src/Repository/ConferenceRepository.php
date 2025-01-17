<?php

namespace App\Repository;

use App\Entity\Conference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conference>
 */
class ConferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conference::class);
    }

    //    /**
    //     * @return Conference[] Returns an array of Conference objects
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
       /**
        * @return Conference[] Returns an array of Conference objects
        */
       public function findByExampleField($value): array
       {
           return $this->createQueryBuilder('c')
               ->andWhere('c.exampleField = :val')
               ->setParameter('val', $value)
               ->orderBy('c.id', 'ASC')
               ->setMaxResults(10)
               ->getQuery()
               ->getResult()
           ;
       }
       /**
        * @return Conference[] Returns an array of Conference objects
        */
       public function findByCategorie($nom): array
       {
           return $this->createQueryBuilder('c')
           ->innerJoin('c.categorie', 'cat')
               ->Where('cat.nom= :val')
               ->setParameter('val', $nom)
               ->getQuery()
               ->getResult()
           ;
       }
       /** 
        * @return Conference[] Returns an array of Conference objects
        */
       public function filtreConferences($prix,$date,$categorie)
       {
           $qb = $this->createQueryBuilder('c')
           ->innerJoin('c.categorie', 'cat');
            if(!empty($categorie)){
                $qb->andWhere('cat.nom= :val')
                ->setParameter('val', $categorie);
            }
            if((!empty($prix))){
                $qb->andWhere('c.prix <= :prix')
                ->setParameter('prix', $prix);
            }
            if(!empty($date)){
                $qb->andWhere('c.date <= :date')
                ->setParameter('date', $date);
            }

            return   $qb->getQuery()->getResult();
       }

    //    public function findOneBySomeField($value): ?Conference
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
