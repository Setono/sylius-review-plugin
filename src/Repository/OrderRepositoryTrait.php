<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Repository;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

trait OrderRepositoryTrait
{
    public function findLatestByCustomer(CustomerInterface $customer): ?OrderInterface
    {
        $result = $this->createQueryBuilder('o')
            ->andWhere('o.customer = :customer')
            ->setParameter('customer', $customer)
            ->addOrderBy('o.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        Assert::nullOrIsInstanceOf($result, OrderInterface::class);

        return $result;
    }
}
