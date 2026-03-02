<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\DisplayName\Provider;

use Setono\CompositeCompilerPass\CompositeService;
use Sylius\Component\Review\Model\ReviewerInterface;

/**
 * @extends CompositeService<DisplayNameCandidateProviderInterface>
 */
final class CompositeDisplayNameCandidateProvider extends CompositeService implements DisplayNameCandidateProviderInterface
{
    public function candidates(ReviewerInterface $reviewer): iterable
    {
        $seen = [];

        foreach ($this->services as $service) {
            foreach ($service->candidates($reviewer) as $candidate) {
                if ('' === $candidate || isset($seen[$candidate])) {
                    continue;
                }

                $seen[$candidate] = true;

                yield $candidate;
            }
        }
    }
}
