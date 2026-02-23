<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setono\SyliusReviewPlugin\Model\ProductReviewInterface;
use Setono\SyliusReviewPlugin\Model\ProductReviewTrait;
use Sylius\Component\Core\Model\ProductReview as BaseProductReview;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_product_review')]
class ProductReview extends BaseProductReview implements ProductReviewInterface
{
    use ProductReviewTrait;
}
