<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function add(Product $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(Product $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */

    public function findProductByMax($value)
    {
        return $this->createQueryBuilder('p')
            ->setMaxResults($value)
            ->getQuery()
            ->getResult()
        ;
    }

    public function searchProductsByFilters($filtres){

        $q = $this->createQueryBuilder('p');

        if (isset($filtres['name'])) {
            $q->andWhere("LOWER(p.name) LIKE :name")
                ->setParameter('name', '%' . mb_strtolower($filtres['name']) . '%');
        }

        if(isset($filtres['category'])){
            $q->join('p.categories','c')
                ->andWhere('c.slug = :categorieid')
                ->setParameter('categorieid',$filtres['category']);

        }

        if (isset($filtres['prixMin'])) {
            $q->andWhere('p.price >= :prixMin')
                ->setParameter('prixMin', $filtres['prixMin']);
        }

        if (isset($filtres['prixMax'])){
            $q->andWhere('p.price <= :prixMax')
            ->setParameter('prixMax',$filtres['prixMax']);
        }

        return $q->getQuery()->getResult();
    }

}
