<?php

declare(strict_types=1);

namespace App\Repository\Library;

use App\Entity\Library\Article;
use App\Entity\Library\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findAllPaginated(int $page = 1, int $limit = 10): Paginator
    {
        return new Paginator(
            $this->createQueryBuilder('a')
                ->setFirstResult($page * $limit - $limit)
                ->orderBy('a.createdAt', 'DESC')
                ->setMaxResults($limit)
                ->getQuery(),
            $fetchJoinCollection = true
        );
    }

    public function findByTagsPaginated(int $page = 1, int $limit = 10, array $tags = [])
    {
        return new Paginator(
            $this->createQueryBuilder('a')
                ->setFirstResult($page * $limit - $limit)
                ->innerJoin('a.tags', 't')
                ->where('t.id IN (:tags)')
                ->setParameter('tags', $tags)
                ->setMaxResults($limit)
                ->getQuery(),
            $fetchJoinCollection = true
        );
    }

    public function findByTags(array $tags = []): ArrayCollection
    {
        $result = $this->createQueryBuilder('a')
            ->innerJoin('a.tags', 't')
            ->where('t.id IN (:tags)')
            ->setParameter('tags', $tags)
            ->getQuery()
            ->getResult();

        $collection = new ArrayCollection($result);
        return $collection->filter(function (Article $article) use ($tags) {
            $articleTags = $article->getTags()->map(function (Tag $tag) {
                return $tag->getId();
            });

            return count(array_intersect($articleTags->toArray(), $tags)) === count($tags);
        });
    }

    public function findDifferentTagsIDsThanGiven(array $tags): array
    {
        return array_column($this->createQueryBuilder('a')
            ->select('t.id')
            ->innerJoin('a.tags', 't')
            ->where('t.id NOT IN (:tags)')
            ->setParameter('tags', $tags)
            ->getQuery()
            ->getResult(), 'id');
    }
}