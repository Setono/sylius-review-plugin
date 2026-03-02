<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\DisplayName\Provider;

use Sylius\Component\Review\Model\ReviewerInterface;

final class FirstNameCandidateProvider implements DisplayNameCandidateProviderInterface
{
    public function candidates(ReviewerInterface $reviewer): iterable
    {
        $firstName = $reviewer->getFirstName();
        if (null !== $firstName && '' !== $firstName) {
            yield $firstName;
        }
    }
}
