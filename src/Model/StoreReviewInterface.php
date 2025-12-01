<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Model;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface StoreReviewInterface extends ResourceInterface, TimestampableInterface
{
    public const STATE_PENDING = 'pending';

    public const STATE_NEW = 'new';

    public const STATE_ACCEPTED = 'accepted';

    public const STATE_REJECTED = 'rejected';

    public function getId(): ?int;

    public function getRating(): ?int;

    public function setRating(?int $rating): void;

    public function getComment(): ?string;

    public function setComment(?string $comment): void;

    public function getState(): string;

    public function setState(string $state): void;

    public function getOrder(): ?OrderInterface;

    public function setOrder(?OrderInterface $order): void;

    public function getAuthorEmail(): ?string;

    public function setAuthorEmail(?string $email): void;

    public function getAuthorFirstName(): ?string;

    public function setAuthorFirstName(?string $firstName): void;

    public function getAuthorLastName(): ?string;

    public function setAuthorLastName(?string $lastName): void;

    public function getAuthorCity(): ?string;

    public function setAuthorCity(?string $city): void;

    public function getAuthorCountry(): ?string;

    public function setAuthorCountry(?string $country): void;

    public function getReview(): ?ReviewInterface;

    public function setReview(?ReviewInterface $review): void;
}
