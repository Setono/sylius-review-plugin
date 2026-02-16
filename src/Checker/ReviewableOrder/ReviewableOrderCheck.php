<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Checker\ReviewableOrder;

final class ReviewableOrderCheck
{
    private function __construct(
        public readonly bool $reviewable,
        public readonly ?string $reason = null,
    ) {
    }

    public static function reviewable(): self
    {
        return new self(true);
    }

    public static function notReviewable(string $reason): self
    {
        return new self(false, $reason);
    }
}
