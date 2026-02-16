<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Repository;

use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

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

        Assert::nullOrIsInstanceOf($result, StoreReviewInterface::class);

        return $result;
    }
}
