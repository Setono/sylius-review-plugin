<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Factory;

use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class ReviewRequestFactory implements ReviewRequestFactoryInterface
{
    /**
     * @param FactoryInterface<ReviewRequestInterface> $decorated
     */
    public function __construct(
        private readonly FactoryInterface $decorated,
        private readonly string $initialDelay,
    ) {
    }

    public function createNew(): ReviewRequestInterface
    {
        $obj = $this->decorated->createNew();
        Assert::isInstanceOf($obj, ReviewRequestInterface::class);

        $obj->setNextEligibilityCheckAt(new \DateTimeImmutable($this->initialDelay));

        return $obj;
    }

    public function createFromOrder(OrderInterface $order): ReviewRequestInterface
    {
        $obj = $this->createNew();
        $obj->setOrder($order);

        return $obj;
    }
}
