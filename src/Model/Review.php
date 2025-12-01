<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Resource\Model\TimestampableTrait;

class Review implements ReviewInterface
{
    use TimestampableTrait;

    protected ?int $id = null;

    protected ?OrderInterface $order = null;

    protected ?StoreReviewInterface $storeReview = null;

    /** @var Collection<array-key, ProductReviewInterface> */
    protected Collection $productReviews;

    public function __construct()
    {
        $this->productReviews = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrder(): ?OrderInterface
    {
        return $this->order;
    }

    public function setOrder(?OrderInterface $order): void
    {
        $this->order = $order;
    }

    public function getStoreReview(): ?StoreReviewInterface
    {
        return $this->storeReview;
    }

    public function setStoreReview(?StoreReviewInterface $storeReview): void
    {
        $this->storeReview = $storeReview;

        if (null !== $storeReview) {
            $storeReview->setReview($this);
        }
    }

    /**
     * @return Collection<array-key, ProductReviewInterface>
     */
    public function getProductReviews(): Collection
    {
        return $this->productReviews;
    }

    public function addProductReview(ProductReviewInterface $productReview): void
    {
        if (!$this->hasProductReview($productReview)) {
            $this->productReviews->add($productReview);
            $productReview->setReview($this);
        }
    }

    public function removeProductReview(ProductReviewInterface $productReview): void
    {
        if ($this->hasProductReview($productReview)) {
            $this->productReviews->removeElement($productReview);
            $productReview->setReview(null);
        }
    }

    public function hasProductReview(ProductReviewInterface $productReview): bool
    {
        return $this->productReviews->contains($productReview);
    }
}
