<?php

namespace App\Repository\Blog;

use App\Entity\Blog\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function findAllOrdered()
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.posts', 'p')
            ->addSelect('p')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneActiveBySlug(string $slug): ?Category
    {
        return $this->createQueryBuilder('p')
            ->where('p.slug = :slug')
            ->setParameter('slug', $slug)
            ->andWhere('p.isActive = true')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllActiveSlugName()
    {
        return $this->createQueryBuilder('c')
            ->select('c.slug', 'c.name')
            ->where('c.isActive = true')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
