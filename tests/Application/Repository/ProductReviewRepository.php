<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Application\Repository;

use Setono\SyliusReviewPlugin\Repository\ProductReviewRepositoryInterface;
use Setono\SyliusReviewPlugin\Repository\ProductReviewRepositoryTrait;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductReviewRepository as BaseProductReviewRepository;

class ProductReviewRepository extends BaseProductReviewRepository implements ProductReviewRepositoryInterface
{
    use ProductReviewRepositoryTrait;
}
