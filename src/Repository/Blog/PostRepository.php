<?php

namespace App\Repository\Blog;

use App\Entity\Blog\Category;
use App\Entity\Blog\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findAllOrderedByNewest()
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->addSelect('c')
            ->leftJoin('p.tags', 't')
            ->addSelect('t')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllActiveOrderedByNewest()
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->addSelect('c')
            ->leftJoin('p.tags', 't')
            ->addSelect('t')
            ->andWhere('p.isActive = true')
            ->andWhere('c.isActive = true')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findOneActiveBySlug(string $slug): ?Post
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'c')
            ->where('p.slug = :slug')
            ->setParameter('slug', $slug)
            ->leftJoin('p.category', 'c')
            ->andwhere('p.isActive = true')
            ->andWhere('c.isActive = true')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllActiveInCategory(Category $category)
    {
        return $this->createQueryBuilder('p')
            ->select('p', 't')
            ->leftJoin('p.tags', 't')
            ->where('p.category = :category')
            ->setParameter('category', $category)
            ->andWhere('p.isActive = true')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findRecentActiveSlugTitle(int $num = 5)
    {
        return $this->createQueryBuilder('p')
            ->select('p.slug', 'p.title', 'c.name')
            ->leftJoin('p.category', 'c')
            ->where('p.isActive = true')
            ->andWhere('c.isActive = true')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($num)
            ->getQuery()
            ->getResult();
    }
}
