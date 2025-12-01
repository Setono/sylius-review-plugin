<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Model;

use Sylius\Component\Core\Model\OrderInterface;

/**
 * This trait provides the implementation for ProductReviewInterface.
 * When using this trait, you must also define the properties in your entity
 * with appropriate ORM mappings:
 *
 * protected ?OrderInterface $order = null;
 * protected ?ReviewInterface $review = null;
 */
trait ProductReviewTrait
{
    public function getOrder(): ?OrderInterface
    {
        return $this->order;
    }

    public function setOrder(?OrderInterface $order): void
    {
        $this->order = $order;
    }

    public function getReview(): ?ReviewInterface
    {
        return $this->review;
    }

    public function setReview(?ReviewInterface $review): void
    {
        $this->review = $review;
    }
}
