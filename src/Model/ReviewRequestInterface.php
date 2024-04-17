<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Model;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface ReviewRequestInterface extends ResourceInterface
{
    public const STATE_PENDING = 'pending';

    public const STATE_COMPLETED = 'completed';

    public const STATE_CANCELLED = 'cancelled';

    public function getId(): ?int;

    public function getState(): string;

    public function setState(string $state): void;

    public function getNextEligibilityCheckAt(): \DateTimeInterface;

    public function setNextEligibilityCheckAt(\DateTimeInterface $nextEligibilityCheckAt): void;

    public function getEligibilityChecks(): int;

    public function setEligibilityChecks(int $eligibilityChecks): void;

    /**
     * Will increment the eligibility checks by the given increment
     */
    public function incrementEligibilityChecks(int $increment = 1): void;

    public function getIneligibilityReason(): ?string;

    public function setIneligibilityReason(?string $ineligibilityReason): void;

    public function getOrder(): ?OrderInterface;

    public function setOrder(?OrderInterface $order): void;

    public function getCreatedAt(): \DateTimeInterface;
}
