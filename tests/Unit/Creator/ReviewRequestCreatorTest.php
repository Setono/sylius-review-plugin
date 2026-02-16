<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Creator;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\Creator\ReviewRequestCreator;
use Setono\SyliusReviewPlugin\DataProvider\OrderForReviewRequestDataProviderInterface;
use Setono\SyliusReviewPlugin\Factory\ReviewRequestFactoryInterface;
use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class ReviewRequestCreatorTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_creates_review_requests_for_orders_from_data_provider(): void
    {
        $order1 = $this->prophesize(OrderInterface::class);
        $order2 = $this->prophesize(OrderInterface::class);

        $reviewRequest1 = $this->prophesize(ReviewRequestInterface::class);
        $reviewRequest2 = $this->prophesize(ReviewRequestInterface::class);

        $dataProvider = $this->prophesize(OrderForReviewRequestDataProviderInterface::class);
        $dataProvider->getOrders()->willReturn([$order1->reveal(), $order2->reveal()]);

        $factory = $this->prophesize(ReviewRequestFactoryInterface::class);
        $factory->createFromOrder($order1->reveal())->willReturn($reviewRequest1->reveal());
        $factory->createFromOrder($order2->reveal())->willReturn($reviewRequest2->reveal());

        $entityManager = $this->prophesize(EntityManagerInterface::class);
        $entityManager->persist($reviewRequest1->reveal())->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();
        $entityManager->persist($reviewRequest2->reveal())->shouldBeCalled();

        $managerRegistry = $this->prophesize(ManagerRegistry::class);
        $managerRegistry->getManagerForClass($reviewRequest1->reveal()::class)->willReturn($entityManager->reveal());
        $managerRegistry->getManagerForClass($reviewRequest2->reveal()::class)->willReturn($entityManager->reveal());

        $creator = new ReviewRequestCreator(
            $managerRegistry->reveal(),
            $dataProvider->reveal(),
            $factory->reveal(),
        );

        $creator->create();
    }

    /**
     * @test
     */
    public function it_does_nothing_when_no_orders_are_provided(): void
    {
        $dataProvider = $this->prophesize(OrderForReviewRequestDataProviderInterface::class);
        $dataProvider->getOrders()->willReturn([]);

        $factory = $this->prophesize(ReviewRequestFactoryInterface::class);
        $factory->createFromOrder()->shouldNotBeCalled();

        $managerRegistry = $this->prophesize(ManagerRegistry::class);

        $creator = new ReviewRequestCreator(
            $managerRegistry->reveal(),
            $dataProvider->reveal(),
            $factory->reveal(),
        );

        $creator->create();
    }
}
