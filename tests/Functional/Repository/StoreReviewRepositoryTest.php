<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Functional\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusReviewPlugin\Model\ChannelInterface;
use Setono\SyliusReviewPlugin\Model\StoreReview;
use Setono\SyliusReviewPlugin\Repository\StoreReviewRepositoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class StoreReviewRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private StoreReviewRepositoryInterface $repository;

    protected function setUp(): void
    {
        self::bootKernel();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->entityManager = $entityManager;

        /** @var StoreReviewRepositoryInterface $repository */
        $repository = self::getContainer()->get('setono_sylius_review.repository.store_review');
        $this->repository = $repository;
    }

    /** @test */
    public function it_finds_store_review_by_order(): void
    {
        $order = $this->findFulfilledOrder();

        $channel = $this->findChannel();

        $storeReview = new StoreReview();
        $storeReview->setOrder($order);
        $storeReview->setChannel($channel);
        $storeReview->setRating(5);
        $storeReview->setTitle('Great');

        $this->entityManager->persist($storeReview);
        $this->entityManager->flush();

        $result = $this->repository->findOneByOrder($order);

        self::assertNotNull($result);
        self::assertSame($storeReview->getId(), $result->getId());
    }

    /** @test */
    public function it_returns_null_when_no_store_review_exists_for_order(): void
    {
        $order = $this->findFulfilledOrder();

        $result = $this->repository->findOneByOrder($order);

        self::assertNull($result);
    }

    private function findFulfilledOrder(): OrderInterface
    {
        /** @var OrderInterface|null $order */
        $order = $this->entityManager->getRepository(OrderInterface::class)->findOneBy([]);
        self::assertNotNull($order, 'No fixture order found. Make sure Sylius fixtures are loaded.');

        return $order;
    }

    private function findChannel(): ChannelInterface
    {
        /** @var ChannelInterface|null $channel */
        $channel = $this->entityManager->getRepository(ChannelInterface::class)->findOneBy([]);
        self::assertNotNull($channel, 'No fixture channel found. Make sure Sylius fixtures are loaded.');

        return $channel;
    }
}
