<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Checker\AutoApproval;

use Setono\CompositeCompilerPass\CompositeService;
use Sylius\Component\Review\Model\ReviewInterface;

/**
 * @template T of ReviewInterface
 * @extends CompositeService<AutoApprovalCheckerInterface<T>>
 * @implements AutoApprovalCheckerInterface<T>
 */
final class CompositeAutoApprovalChecker extends CompositeService implements AutoApprovalCheckerInterface
{
    /** @param T $review */
    public function shouldAutoApprove(ReviewInterface $review): bool
    {
        foreach ($this->services as $service) {
            if (!$service->shouldAutoApprove($review)) {
                return false;
            }
        }

        return true;
    }
}
