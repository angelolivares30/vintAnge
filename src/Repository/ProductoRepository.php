<?php

namespace App\Repository;

use App\Entity\Producto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Producto>
 */
class ProductoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Producto::class);
    }

    //    /**
    //     * @return Producto[] Returns an array of Producto objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }
    public function findAll(): array
    {
        return $this->createQueryBuilder('p')
            ->getQuery()
            ->getResult();
    }


    public function findProductoByCategoria($value): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.categoria = :categoria')
            ->setParameter('categoria', $value)
            ->getQuery()
            ->getResult();
    }

    
}
