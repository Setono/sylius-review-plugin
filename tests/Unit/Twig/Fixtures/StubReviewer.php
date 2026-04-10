<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Twig\Fixtures;

use Sylius\Component\Review\Model\ReviewerInterface;

final class StubReviewer implements ReviewerInterface
{
    public function __construct(private readonly ?string $firstName = null)
    {
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
    }

    public function getLastName(): ?string
    {
        return null;
    }

    public function setLastName(?string $lastName): void
    {
    }

    public function getEmail(): ?string
    {
        return null;
    }

    public function setEmail(?string $email): void
    {
    }

    public function getId(): ?int
    {
        return null;
    }
}
