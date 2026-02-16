<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Factory;

use PHPUnit\Framework\TestCase;
use Setono\SyliusReviewPlugin\Factory\ReviewRequestFactory;
use Setono\SyliusReviewPlugin\Model\ReviewRequest;
use Sylius\Component\Core\Model\Order;
use Sylius\Resource\Factory\Factory;

final class ReviewRequestFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_new(): void
    {
        $reviewRequest = self::getFactory()->createNew();

        self::assertGreaterThan(new \DateTimeImmutable('-10 seconds'), $reviewRequest->getNextEligibilityCheckAt());
        self::assertLessThan(new \DateTimeImmutable('+10 seconds'), $reviewRequest->getNextEligibilityCheckAt());
    }

    /**
     * @test
     */
    public function it_creates_with_order(): void
    {
        $order = new Order();
        $reviewRequest = self::getFactory()->createFromOrder($order);

        self::assertSame($order, $reviewRequest->getOrder());
    }

    private static function getFactory(): ReviewRequestFactory
    {
        /** @phpstan-ignore argument.type */
        return new ReviewRequestFactory(new Factory(ReviewRequest::class), 'now');
    }
}
