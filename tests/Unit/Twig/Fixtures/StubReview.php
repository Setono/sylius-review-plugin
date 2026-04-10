<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Twig\Fixtures;

use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class StubReview implements ReviewInterface
{
    public function __construct(private readonly ?ReviewerInterface $author = null)
    {
    }

    public function getAuthor(): ?ReviewerInterface
    {
        return $this->author;
    }

    public function getId(): ?int
    {
        return null;
    }

    public function getTitle(): ?string
    {
        return null;
    }

    public function setTitle(?string $title): void
    {
    }

    public function getRating(): ?int
    {
        return null;
    }

    public function setRating(?int $rating): void
    {
    }

    public function getComment(): ?string
    {
        return null;
    }

    public function setComment(?string $comment): void
    {
    }

    public function setAuthor(?ReviewerInterface $author): void
    {
    }

    public function getStatus(): ?string
    {
        return null;
    }

    public function setStatus(?string $status): void
    {
    }

    public function getReviewSubject(): ?ReviewableInterface
    {
        return null;
    }

    public function setReviewSubject(?ReviewableInterface $reviewSubject): void
    {
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return null;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): void
    {
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return null;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): void
    {
    }
}
