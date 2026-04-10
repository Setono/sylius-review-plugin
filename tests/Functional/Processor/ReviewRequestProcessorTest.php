<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Functional\Processor;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusReviewPlugin\Model\ReviewRequest;
use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;
use Setono\SyliusReviewPlugin\Processor\ReviewRequestProcessor;
use Setono\SyliusReviewPlugin\Processor\ReviewRequestProcessorInterface;
use Setono\SyliusReviewPlugin\Repository\ReviewRequestRepositoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ReviewRequestProcessorTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private ReviewRequestProcessorInterface $processor;

    private ReviewRequestRepositoryInterface $reviewRequestRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->entityManager = $entityManager;

        /** @var ReviewRequestProcessorInterface $processor */
        $processor = self::getContainer()->get(ReviewRequestProcessor::class);
        $this->processor = $processor;

        /** @var ReviewRequestRepositoryInterface $reviewRequestRepository */
        $reviewRequestRepository = self::getContainer()->get('setono_sylius_review.repository.review_request');
        $this->reviewRequestRepository = $reviewRequestRepository;
    }

    /** @test */
    public function it_completes_review_request_for_fulfilled_order(): void
    {
        $order = $this->findOrder();
        $order->setState(OrderInterface::STATE_FULFILLED);
        $order->setCheckoutCompletedAt(new \DateTimeImmutable());

        $reviewRequest = $this->createReviewRequest($order);
        $id = $reviewRequest->getId();

        $this->processor->process();

        $reviewRequest = $this->findReviewRequest($id);

        self::assertSame(ReviewRequestInterface::STATE_COMPLETED, $reviewRequest->getState());
    }

    /** @test */
    public function it_does_not_complete_review_request_for_non_fulfilled_order(): void
    {
        $order = $this->findOrder();
        $order->setState(OrderInterface::STATE_NEW);

        $reviewRequest = $this->createReviewRequest($order);
        $id = $reviewRequest->getId();

        $this->processor->process();

        $reviewRequest = $this->findReviewRequest($id);

        self::assertSame(ReviewRequestInterface::STATE_PENDING, $reviewRequest->getState());
        self::assertSame('Order is not fulfilled', $reviewRequest->getIneligibilityReason());
    }

    /** @test */
    public function it_does_not_process_review_requests_not_yet_due(): void
    {
        $order = $this->findOrder();
        $order->setState(OrderInterface::STATE_FULFILLED);
        $order->setCheckoutCompletedAt(new \DateTimeImmutable());

        $reviewRequest = $this->createReviewRequest($order);
        $id = $reviewRequest->getId();
        $reviewRequest->setNextEligibilityCheckAt(new \DateTimeImmutable('+1 day'));
        $this->entityManager->flush();

        $this->processor->process();

        $reviewRequest = $this->findReviewRequest($id);

        self::assertSame(ReviewRequestInterface::STATE_PENDING, $reviewRequest->getState());
        self::assertNull($reviewRequest->getIneligibilityReason());
    }

    /** @test */
    public function it_increments_eligibility_checks_on_each_processing(): void
    {
        $order = $this->findOrder();
        $order->setState(OrderInterface::STATE_NEW);

        $reviewRequest = $this->createReviewRequest($order);
        $id = $reviewRequest->getId();

        self::assertSame(0, $reviewRequest->getEligibilityChecks());

        $this->processor->process();

        $reviewRequest = $this->findReviewRequest($id);

        self::assertSame(1, $reviewRequest->getEligibilityChecks());
    }

    /** @test */
    public function it_cancels_review_request_after_maximum_eligibility_checks(): void
    {
        $order = $this->findOrder();
        $order->setState(OrderInterface::STATE_NEW);

        $reviewRequest = $this->createReviewRequest($order);
        $id = $reviewRequest->getId();

        $maxChecks = (int) self::getContainer()->getParameter('setono_sylius_review.eligibility.maximum_checks');
        $reviewRequest->setEligibilityChecks($maxChecks);
        $this->entityManager->flush();

        $this->processor->process();

        $reviewRequest = $this->findReviewRequest($id);

        self::assertSame(ReviewRequestInterface::STATE_CANCELLED, $reviewRequest->getState());
    }

    private function findReviewRequest(?int $id): ReviewRequestInterface
    {
        $reviewRequest = $this->reviewRequestRepository->find($id);
        self::assertInstanceOf(ReviewRequestInterface::class, $reviewRequest);

        return $reviewRequest;
    }

    /** @test */
    public function it_catches_exceptions_and_sets_processing_error(): void
    {
        $order = $this->findOrder();
        $order->setState(OrderInterface::STATE_FULFILLED);
        $order->setCheckoutCompletedAt(new \DateTimeImmutable());
        $order->setCustomer(null);

        $reviewRequest = $this->createReviewRequest($order);
        $id = $reviewRequest->getId();

        $this->processor->process();

        $reviewRequest = $this->findReviewRequest($id);

        self::assertSame(ReviewRequestInterface::STATE_PENDING, $reviewRequest->getState());
        self::assertNotNull($reviewRequest->getProcessingError());
    }

    private function createReviewRequest(OrderInterface $order): ReviewRequest
    {
        $reviewRequest = new ReviewRequest();
        $reviewRequest->setOrder($order);
        $reviewRequest->setNextEligibilityCheckAt(new \DateTimeImmutable('-1 hour'));

        $this->entityManager->persist($reviewRequest);
        $this->entityManager->flush();

        return $reviewRequest;
    }

    private function findOrder(): OrderInterface
    {
        /** @var OrderInterface|null $order */
        $order = $this->entityManager->getRepository(OrderInterface::class)->findOneBy([]);
        self::assertNotNull($order, 'No fixture order found. Make sure Sylius fixtures are loaded.');

        return $order;
    }
}
