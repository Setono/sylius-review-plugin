<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Creator;

use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Setono\Doctrine\ORMTrait;
use Setono\SyliusReviewPlugin\DataProvider\OrderForReviewRequestDataProviderInterface;
use Setono\SyliusReviewPlugin\Factory\ReviewRequestFactoryInterface;

final class ReviewRequestCreator implements ReviewRequestCreatorInterface, LoggerAwareInterface
{
    use ORMTrait;

    private LoggerInterface $logger;

    public function __construct(
        ManagerRegistry $managerRegistry,
        private readonly OrderForReviewRequestDataProviderInterface $dataProvider,
        private readonly ReviewRequestFactoryInterface $reviewRequestFactory,
    ) {
        $this->managerRegistry = $managerRegistry;
        $this->logger = new NullLogger();
    }

    public function create(): void
    {
        $i = 0;

        foreach ($this->dataProvider->getOrders() as $order) {
            $reviewRequest = $this->reviewRequestFactory->createFromOrder($order);

            $manager = $this->getManager($reviewRequest);
            $manager->persist($reviewRequest);
            $manager->flush();

            ++$i;
        }

        $this->logger->debug(sprintf('%d review request%s created', $i, 1 === $i ? '' : 's'));
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
