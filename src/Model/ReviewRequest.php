<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Model;

use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

class ReviewRequest implements ReviewRequestInterface
{
    protected ?int $id = null;

    protected string $state = self::STATE_PENDING;

    protected \DateTimeInterface $nextEligibilityCheckAt;

    protected int $eligibilityChecks = 0;

    protected ?string $ineligibilityReason = null;

    protected ?string $processingError = null;

    protected ?OrderInterface $order = null;

    protected \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->nextEligibilityCheckAt = $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getNextEligibilityCheckAt(): \DateTimeInterface
    {
        return $this->nextEligibilityCheckAt;
    }

    public function setNextEligibilityCheckAt(\DateTimeInterface $nextEligibilityCheckAt): void
    {
        $this->nextEligibilityCheckAt = $nextEligibilityCheckAt;
    }

    public function getEligibilityChecks(): int
    {
        return $this->eligibilityChecks;
    }

    public function setEligibilityChecks(int $eligibilityChecks): void
    {
        Assert::greaterThanEq($eligibilityChecks, 0);

        $this->eligibilityChecks = $eligibilityChecks;
    }

    public function incrementEligibilityChecks(int $increment = 1): void
    {
        $this->eligibilityChecks += $increment;
    }

    public function getIneligibilityReason(): ?string
    {
        return $this->ineligibilityReason;
    }

    public function setIneligibilityReason(?string $ineligibilityReason): void
    {
        $this->ineligibilityReason = $ineligibilityReason;
    }

    public function getOrder(): ?OrderInterface
    {
        return $this->order;
    }

    public function getProcessingError(): ?string
    {
        return $this->processingError;
    }

    public function setProcessingError(?string $processingError): void
    {
        $this->processingError = $processingError;
    }

    public function setOrder(?OrderInterface $order): void
    {
        $this->order = $order;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
}
