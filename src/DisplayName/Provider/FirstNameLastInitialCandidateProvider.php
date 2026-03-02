<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\DisplayName\Provider;

use Sylius\Component\Review\Model\ReviewerInterface;

final class FirstNameLastInitialCandidateProvider implements DisplayNameCandidateProviderInterface
{
    public function candidates(ReviewerInterface $reviewer): iterable
    {
        $firstName = $reviewer->getFirstName();
        $lastName = $reviewer->getLastName();

        if (null !== $firstName && '' !== $firstName && null !== $lastName && '' !== $lastName) {
            yield sprintf('%s %s.', $firstName, mb_substr($lastName, 0, 1));
        }
    }
}
