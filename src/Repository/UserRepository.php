<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param $page
     * @param $limit
     * @param $actualUser_id
     * @return Paginator Returns an array of User objects
     */
    public function findAllUsers($page, $limit, $actualUser_id)
    {
        $query = $this->createQueryBuilder('p')
            ->where(' p.client = ?1')
            ->setParameter(1, $actualUser_id)
            ->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);
        return new Paginator($query);
    }


    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
