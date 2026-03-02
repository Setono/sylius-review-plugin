<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Model;

use Sylius\Component\Review\Model\ReviewInterface as BaseReviewInterface;

interface ReviewInterface extends BaseReviewInterface
{
    public function getDisplayName(): ?string;

    public function setDisplayName(?string $displayName): void;
}
