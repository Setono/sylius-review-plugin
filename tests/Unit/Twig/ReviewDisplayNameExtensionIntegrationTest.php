<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Twig;

use Setono\SyliusReviewPlugin\DisplayName\Resolver\DisplayNameResolverInterface;
use Setono\SyliusReviewPlugin\Twig\ReviewDisplayNameExtension;
use Setono\SyliusReviewPlugin\Twig\ReviewDisplayNameRuntime;
use Sylius\Component\Review\Model\ReviewInterface;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Twig\Test\IntegrationTestCase;

final class ReviewDisplayNameExtensionIntegrationTest extends IntegrationTestCase
{
    protected function getExtensions(): array
    {
        return [
            new ReviewDisplayNameExtension(),
        ];
    }

    protected function getRuntimeLoaders(): array
    {
        $resolver = new class implements DisplayNameResolverInterface {
            public function resolve(ReviewInterface $review): string
            {
                return $review->getAuthor()?->getFirstName() ?? 'Anonymous';
            }
        };

        $runtimeLoader = new class($resolver) implements RuntimeLoaderInterface {
            public function __construct(private readonly DisplayNameResolverInterface $resolver)
            {
            }

            public function load(string $class): ?object
            {
                if (ReviewDisplayNameRuntime::class === $class) {
                    return new ReviewDisplayNameRuntime($this->resolver);
                }

                return null;
            }
        };

        return [$runtimeLoader];
    }

    protected static function getFixturesDirectory(): string
    {
        return __DIR__ . '/Fixtures/';
    }
}
