<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function add(Order $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(Order $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Chargement des commandes passées avec une pagination.
     * @return Query
     */
    public function getPaginator(): Query
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.created_at', 'DESC')
            ->getQuery();
    }

    /**
     * Récupération des commandes réalisées sur une année donnée.
     * @param string $year
     * @return Order[]
     */
    public function getOrdersFromYear(string $year): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('YEAR(o.created_at) = :year')
            ->andWhere('o.status IN (:statuses)')
            ->setParameters(new ArrayCollection([
                new Query\Parameter('year', $year),
                new Query\Parameter('statuses', [Order::STATUS_SHIPPED, Order::STATUS_PREPARATION, Order::STATUS_DELIVERED])
            ]))
            ->orderBy('o.created_at', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
