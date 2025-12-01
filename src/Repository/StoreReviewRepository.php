<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Repository;

use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\OrderInterface;

class StoreReviewRepository extends EntityRepository implements StoreReviewRepositoryInterface
{
    public function findOneByOrder(OrderInterface $order): ?StoreReviewInterface
    {
        $result = $this->createQueryBuilder('o')
            ->andWhere('o.order = :order')
            ->setParameter('order', $order)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        \assert(null === $result || $result instanceof StoreReviewInterface);

        return $result;
    }
}
