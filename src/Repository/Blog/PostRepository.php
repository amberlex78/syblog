<?php

namespace App\Repository\Blog;

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

    public function findOneActiveBySlug(string $slug): ?Post
    {
        return $this->createQueryBuilder('p')
            ->where('p.slug = :slug')
            ->setParameter('slug', $slug)
            ->andWhere('p.isActive = true')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllActiveInCategory(int $categoryId)
    {
        return $this->createQueryBuilder('p')
            ->where('p.category = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->andWhere('p.isActive = true')
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findRecentActiveSlugTitle(int $num = 5)
    {
        return $this->createQueryBuilder('p')
            ->select('p.slug', 'p.title')
            ->where('p.isActive = true')
            ->orderBy('p.created_at', 'DESC')
            ->setMaxResults($num)
            ->getQuery()
            ->getResult();
    }
}
