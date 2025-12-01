<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Repository;

use Setono\SyliusReviewPlugin\Model\ReviewInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\OrderInterface;

class ReviewRepository extends EntityRepository implements ReviewRepositoryInterface
{
    public function findOneByOrder(OrderInterface $order): ?ReviewInterface
    {
        $result = $this->createQueryBuilder('o')
            ->andWhere('o.order = :order')
            ->setParameter('order', $order)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        \assert(null === $result || $result instanceof ReviewInterface);

        return $result;
    }
}
