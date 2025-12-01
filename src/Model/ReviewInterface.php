<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface ReviewInterface extends ResourceInterface, TimestampableInterface
{
    public function getId(): ?int;

    public function getOrder(): ?OrderInterface;

    public function setOrder(?OrderInterface $order): void;

    public function getStoreReview(): ?StoreReviewInterface;

    public function setStoreReview(?StoreReviewInterface $storeReview): void;

    /**
     * @return Collection<array-key, ProductReviewInterface>
     */
    public function getProductReviews(): Collection;

    public function addProductReview(ProductReviewInterface $productReview): void;

    public function removeProductReview(ProductReviewInterface $productReview): void;

    public function hasProductReview(ProductReviewInterface $productReview): bool;
}
