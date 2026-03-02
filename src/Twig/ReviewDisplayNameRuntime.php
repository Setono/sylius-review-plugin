<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Twig;

use Setono\SyliusReviewPlugin\DisplayName\Resolver\DisplayNameResolverInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class ReviewDisplayNameRuntime implements RuntimeExtensionInterface
{
    public function __construct(private readonly DisplayNameResolverInterface $displayNameResolver)
    {
    }

    public function resolve(ReviewInterface $review): string
    {
        return $this->displayNameResolver->resolve($review);
    }
}
