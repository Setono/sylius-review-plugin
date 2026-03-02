<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\DisplayName\Provider;

use Sylius\Component\Review\Model\ReviewerInterface;
use function Symfony\Component\String\u;

final class FirstNameLastInitialCandidateProvider implements DisplayNameCandidateProviderInterface
{
    public function candidates(ReviewerInterface $reviewer): iterable
    {
        $firstName = (string) $reviewer->getFirstName();
        if('' === $firstName) {
            return [];
        }

        $lastName = (string) $reviewer->getLastName();
        if('' === $lastName) {
            return [];
        }

        yield sprintf('%s %s.', u($firstName)->title(true)->toString(), u($lastName)->slice(0, 1)->upper()->toString());
    }
}
