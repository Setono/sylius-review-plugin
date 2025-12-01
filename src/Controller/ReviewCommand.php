<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Sylius\Component\Core\Model\ProductReviewInterface;

final class ReviewCommand
{
    private ?StoreReviewInterface $storeReview = null;

    /** @var Collection<array-key, ProductReviewInterface> */
    private Collection $productReviews;

    public function __construct()
    {
        $this->productReviews = new ArrayCollection();
    }

    public function getStoreReview(): ?StoreReviewInterface
    {
        return $this->storeReview;
    }

    public function setStoreReview(?StoreReviewInterface $storeReview): void
    {
        $this->storeReview = $storeReview;
    }

    /**
     * @return Collection<array-key, ProductReviewInterface>
     */
    public function getProductReviews(): Collection
    {
        return $this->productReviews;
    }

    /**
     * @param Collection<array-key, ProductReviewInterface> $productReviews
     */
    public function setProductReviews(Collection $productReviews): void
    {
        $this->productReviews = $productReviews;
    }

    public function addProductReview(ProductReviewInterface $productReview): void
    {
        if (!$this->productReviews->contains($productReview)) {
            $this->productReviews->add($productReview);
        }
    }

    public function removeProductReview(ProductReviewInterface $productReview): void
    {
        $this->productReviews->removeElement($productReview);
    }
}
