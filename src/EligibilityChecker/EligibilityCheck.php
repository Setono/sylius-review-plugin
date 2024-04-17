<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\EligibilityChecker;

final class EligibilityCheck
{
    private function __construct(public readonly bool $eligible, public readonly ?string $reason = null)
    {
    }

    public static function eligible(): self
    {
        return new self(true);
    }

    public static function ineligible(string $reason): self
    {
        return new self(false, $reason);
    }
}
