<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Model;

interface StoreReplyInterface
{
    public function getStoreReply(): ?string;

    public function setStoreReply(?string $storeReply): void;

    public function getStoreRepliedAt(): ?\DateTimeInterface;

    public function setStoreRepliedAt(?\DateTimeInterface $storeRepliedAt): void;
}
