<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setono\SyliusReviewPlugin\Model\ProductReviewInterface;
use Setono\SyliusReviewPlugin\Model\ProductReviewTrait;
use Setono\SyliusReviewPlugin\Model\ReviewInterface;
use Sylius\Component\Core\Model\ProductReview as BaseProductReview;
use Sylius\Component\Order\Model\OrderInterface;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_product_review')]
class ProductReview extends BaseProductReview implements ProductReviewInterface
{
    use ProductReviewTrait;

    #[ORM\ManyToOne(targetEntity: OrderInterface::class)]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    protected ?OrderInterface $order = null;

    #[ORM\ManyToOne(targetEntity: ReviewInterface::class, inversedBy: 'productReviews')]
    #[ORM\JoinColumn(name: 'review_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    protected ?ReviewInterface $review = null;

    public function __construct()
    {
        parent::__construct();

        $this->status = self::STATUS_PENDING;
    }
}
