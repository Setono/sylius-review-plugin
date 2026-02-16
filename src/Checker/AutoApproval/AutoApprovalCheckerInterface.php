<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Checker\AutoApproval;

use Sylius\Component\Review\Model\ReviewInterface;

/**
 * @template T of ReviewInterface
 */
interface AutoApprovalCheckerInterface
{
    /**
     * Returns true if the review should be auto-approved
     *
     * @param T $review
     */
    public function shouldAutoApprove(ReviewInterface $review): bool;
}
