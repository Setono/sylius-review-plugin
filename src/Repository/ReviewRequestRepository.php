<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class ReviewRequestRepository extends EntityRepository implements ReviewRequestRepositoryInterface
{
    public function createForProcessingQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.nextEligibilityCheck <= :now')
            ->andWhere('o.state = :state')
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('state', ReviewRequestInterface::STATE_PENDING)
        ;
    }
}