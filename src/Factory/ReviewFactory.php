<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Factory;

use Setono\SyliusReviewPlugin\Model\ReviewInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class ReviewFactory implements ReviewFactoryInterface
{
    /**
     * @param FactoryInterface<ReviewInterface> $decorated
     */
    public function __construct(private readonly FactoryInterface $decorated)
    {
    }

    public function createNew(): ReviewInterface
    {
        $obj = $this->decorated->createNew();
        Assert::isInstanceOf($obj, ReviewInterface::class);

        return $obj;
    }

    public function createFromOrder(OrderInterface $order): ReviewInterface
    {
        $obj = $this->createNew();
        $obj->setOrder($order);

        return $obj;
    }
}
