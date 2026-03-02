<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\DisplayName\Provider;

use Sylius\Component\Review\Model\ReviewerInterface;

interface DisplayNameCandidateProviderInterface
{
    /**
     * @return iterable<string>
     */
    public function candidates(ReviewerInterface $reviewer): iterable;
}
