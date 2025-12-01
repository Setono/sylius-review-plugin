<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Model;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Resource\Model\TimestampableTrait;

class StoreReview implements StoreReviewInterface
{
    use TimestampableTrait;

    protected ?int $id = null;

    protected ?int $rating = null;

    protected ?string $comment = null;

    protected string $state = self::STATE_PENDING;

    protected ?OrderInterface $order = null;

    protected ?string $authorEmail = null;

    protected ?string $authorFirstName = null;

    protected ?string $authorLastName = null;

    protected ?string $authorCity = null;

    protected ?string $authorCountry = null;

    protected ?ReviewInterface $review = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): void
    {
        $this->rating = $rating;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getOrder(): ?OrderInterface
    {
        return $this->order;
    }

    public function setOrder(?OrderInterface $order): void
    {
        $this->order = $order;
    }

    public function getAuthorEmail(): ?string
    {
        return $this->authorEmail;
    }

    public function setAuthorEmail(?string $email): void
    {
        $this->authorEmail = $email;
    }

    public function getAuthorFirstName(): ?string
    {
        return $this->authorFirstName;
    }

    public function setAuthorFirstName(?string $firstName): void
    {
        $this->authorFirstName = $firstName;
    }

    public function getAuthorLastName(): ?string
    {
        return $this->authorLastName;
    }

    public function setAuthorLastName(?string $lastName): void
    {
        $this->authorLastName = $lastName;
    }

    public function getAuthorCity(): ?string
    {
        return $this->authorCity;
    }

    public function setAuthorCity(?string $city): void
    {
        $this->authorCity = $city;
    }

    public function getAuthorCountry(): ?string
    {
        return $this->authorCountry;
    }

    public function setAuthorCountry(?string $country): void
    {
        $this->authorCountry = $country;
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
