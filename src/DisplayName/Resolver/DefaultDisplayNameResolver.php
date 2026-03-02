<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\DisplayName\Resolver;

use Setono\SyliusReviewPlugin\Model\ReviewInterface as PluginReviewInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class DefaultDisplayNameResolver implements DisplayNameResolverInterface
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function resolve(ReviewInterface $review): string
    {
        if ($review instanceof PluginReviewInterface) {
            $displayName = $review->getDisplayName();
            if (null !== $displayName && '' !== $displayName) {
                return $displayName;
            }
        }

        $author = $review->getAuthor();
        if (null !== $author) {
            $firstName = $author->getFirstName();
            if (null !== $firstName && '' !== $firstName) {
                return $firstName;
            }
        }

        return $this->translator->trans('setono_sylius_review.ui.anonymous');
    }
}
