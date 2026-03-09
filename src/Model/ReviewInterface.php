<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Model;

use Sylius\Component\Review\Model\ReviewInterface as BaseReviewInterface;

interface ReviewInterface extends BaseReviewInterface
{
    public function getDisplayName(): ?string;

    public function setDisplayName(?string $displayName): void;

    public function getStoreReply(): ?string;

    public function setStoreReply(?string $storeReply): void;

    public function getStoreRepliedAt(): ?\DateTimeInterface;

    public function setStoreRepliedAt(?\DateTimeInterface $storeRepliedAt): void;

    public function getNotifyReviewer(): bool;

    public function setNotifyReviewer(bool $notifyReviewer): void;
}
