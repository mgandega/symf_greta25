<?php

namespace App\Repository;

use App\Entity\Commentaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commentaire>
 */
class CommentaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commentaire::class);
    }

       /**
        * @return Commentaire[] Returns an array of Commentaire objects
        */
       public function findByConference($conf): array
       {
        return $this->createQueryBuilder('com')
            ->innerJoin('com.conference', 'conf')
            ->Where('conf.id= :val')
            ->setParameter('val', $conf->getId())
            ->orderBy('com.id', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
        ;
       }

    //    public function findOneBySomeField($value): ?Commentaire
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
