<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Model;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Review\Model\ReviewInterface;

trait ChannelTrait
{
    /** @var Collection<array-key, ReviewInterface> */
    #[ORM\OneToMany(mappedBy: 'reviewSubject', targetEntity: StoreReviewInterface::class, fetch: 'EXTRA_LAZY')]
    protected Collection $reviews;

    #[ORM\Column(type: 'float', nullable: true)]
    protected ?float $averageRating = null;

    /**
     * @return Collection<array-key, ReviewInterface>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(ReviewInterface $review): void
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setReviewSubject($this);
        }
    }

    public function removeReview(ReviewInterface $review): void
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->removeElement($review);
            $review->setReviewSubject(null);
        }
    }

    public function getAverageRating(): ?float
    {
        return $this->averageRating;
    }

    public function setAverageRating(float $averageRating): void
    {
        $this->averageRating = $averageRating;
    }
}
