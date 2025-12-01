<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Model;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductReviewInterface as BaseProductReviewInterface;

interface ProductReviewInterface extends BaseProductReviewInterface
{
    public const STATUS_PENDING = 'pending';

    public function getOrder(): ?OrderInterface;

    public function setOrder(?OrderInterface $order): void;

    public function getReview(): ?ReviewInterface;

    public function setReview(?ReviewInterface $review): void;
}
