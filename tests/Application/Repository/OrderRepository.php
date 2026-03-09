<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Application\Repository;

use Setono\SyliusReviewPlugin\Repository\OrderRepositoryInterface;
use Setono\SyliusReviewPlugin\Repository\OrderRepositoryTrait;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository as BaseOrderRepository;

class OrderRepository extends BaseOrderRepository implements OrderRepositoryInterface
{
    use OrderRepositoryTrait;
}
