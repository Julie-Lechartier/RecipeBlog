<?php

namespace App\Repository;

use App\Entity\Recipe;
use App\Entity\RecipeCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    /**
     * Find recipes by category
     */
    public function findByCategory(RecipeCategory $category): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.category', 'c')
            ->andWhere('c.id = :categoryId')
            ->setParameter('categoryId', $category->getId())
            ->getQuery()
            ->getResult();
    }

    public function findByNameAndCategory(string $search, string $category): QueryBuilder
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.category', 'c')
            ->addSelect('c');

        if ($search) {
            $qb->andWhere('r.title LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($category) {
            $qb->andWhere('c.id = :category')
                ->setParameter('category', $category);
        }

        return $qb;
    }
    //    /**
    //     * @return Recipe[] Returns an array of Recipe objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Recipe
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
