<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Repository;

use Setono\SyliusReviewPlugin\Model\ProductReviewInterface;
use Sylius\Component\Core\Model\OrderInterface;

trait ProductReviewRepositoryTrait
{
    public function findByOrder(OrderInterface $order): array
    {
        /** @var list<ProductReviewInterface> $result */
        $result = $this->createQueryBuilder('r')
            ->andWhere('r.order = :order')
            ->setParameter('order', $order)
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }
}
