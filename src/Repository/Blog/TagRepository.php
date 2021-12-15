<?php

namespace App\Repository\Blog;

use App\Entity\Blog\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    public function findAllOrderedByNewest()
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.posts', 'p')
            ->addSelect('p')
            ->orderBy('t.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findOneBySlug(string $slug): ?Tag
    {
        return $this->createQueryBuilder('p')
            ->where('p.slug = :slug')
            ->setParameter('slug', $slug)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllHasPostsOrderedByName()
    {
        return $this->createQueryBuilder('t')
            ->join('t.posts', 'p')
            ->addSelect('p')
            ->orderBy('t.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
