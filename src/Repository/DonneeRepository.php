<?php

namespace App\Repository;

use App\Entity\Donnee;
use Doctrine\ORM\Query;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Donnee>
 *
 * @method Donnee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Donnee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Donnee[]    findAll()
 * @method Donnee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DonneeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Donnee::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Donnee $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Donnee $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Donnee[] Returns an array of Donnee objects
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
    public function findOneBySomeField($value): ?Donnee
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function listeDonneeCompleteAdmin ($id, $nbL) : ?Query
    {
        return $this->createQueryBuilder('d')
            ->select('d')
            //->andWhere('d.id' = )
            ->getQuery(); 
    }
}
