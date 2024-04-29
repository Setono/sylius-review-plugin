<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\OrderInterface;

class ReviewRequestRepository extends EntityRepository implements ReviewRequestRepositoryInterface
{
    public function createForProcessingQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.nextEligibilityCheckAt <= :now')
            ->andWhere('o.state = :state')
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('state', ReviewRequestInterface::STATE_PENDING)
        ;
    }

    public function removeBefore(\DateTimeInterface $threshold): void
    {
        $this->createQueryBuilder('o')
            ->delete()
            ->andWhere('o.createdAt < :threshold')
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->execute()
        ;
    }

    public function removeCancelled(): void
    {
        $this->createQueryBuilder('o')
            ->delete()
            ->andWhere('o.state = :state')
            ->setParameter('state', ReviewRequestInterface::STATE_CANCELLED)
            ->getQuery()
            ->execute()
        ;
    }

    public function hasExistingForOrder(OrderInterface $order): bool
    {
        return (int) $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->andWhere('o.order = :order')
            ->setParameter('order', $order)
            ->getQuery()
            ->getSingleScalarResult() > 0
        ;
    }
}
