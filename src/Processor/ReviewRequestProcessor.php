<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Processor;

use DoctrineBatchUtils\BatchProcessing\SimpleBatchIteratorAggregate;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Setono\SyliusReviewPlugin\EligibilityChecker\ReviewRequestEligibilityCheckerInterface;
use Setono\SyliusReviewPlugin\Event\ReviewRequestProcessingStarted;
use Setono\SyliusReviewPlugin\Mailer\ReviewRequestEmailManagerInterface;
use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;
use Setono\SyliusReviewPlugin\Repository\ReviewRequestRepositoryInterface;
use Setono\SyliusReviewPlugin\Workflow\ReviewRequestWorkflow;
use Symfony\Component\Workflow\WorkflowInterface;

final class ReviewRequestProcessor implements ReviewRequestProcessorInterface, LoggerAwareInterface
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly ReviewRequestRepositoryInterface $reviewRequestRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly WorkflowInterface $reviewRequestWorkflow,
        private readonly ReviewRequestEligibilityCheckerInterface $reviewRequestEligibilityChecker,
        private readonly ReviewRequestEmailManagerInterface $reviewRequestEmailManager,
    ) {
        $this->logger = new NullLogger();
    }

    public function process(): void
    {
        /** @var SimpleBatchIteratorAggregate<array-key, ReviewRequestInterface> $reviewRequests */
        $reviewRequests = SimpleBatchIteratorAggregate::fromQuery(
            $this->reviewRequestRepository->createForProcessingQueryBuilder()->getQuery(),
            100,
        );

        foreach ($reviewRequests as $reviewRequest) {
            if (!$this->reviewRequestWorkflow->can($reviewRequest, ReviewRequestWorkflow::TRANSITION_COMPLETE)) {
                continue;
            }

            $this->eventDispatcher->dispatch(new ReviewRequestProcessingStarted($reviewRequest));

            if (!$this->reviewRequestEligibilityChecker->isEligible($reviewRequest)) {
                continue;
            }

            try {
                $this->reviewRequestEmailManager->sendReviewRequest($reviewRequest);
            } catch (\Throwable $e) {
                $this->logger->error(sprintf(
                    'There was an error trying to send a review request (id: %d). The error was: %s',
                    (int) $reviewRequest->getId(),
                    $e->getMessage(),
                ));

                continue;
            }

            $this->reviewRequestWorkflow->apply($reviewRequest, ReviewRequestWorkflow::TRANSITION_COMPLETE);
        }
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
