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
        $qb = $this->reviewRequestRepository->createForProcessingQueryBuilder();

        /** @var SimpleBatchIteratorAggregate<array-key, ReviewRequestInterface> $reviewRequests */
        $reviewRequests = SimpleBatchIteratorAggregate::fromQuery(
            $qb->getQuery(),
            100,
        );

        $i = 0;
        foreach ($reviewRequests as $reviewRequest) {
            try {
                $this->eventDispatcher->dispatch(new ReviewRequestProcessingStarted($reviewRequest));

                if (!$this->reviewRequestWorkflow->can($reviewRequest, ReviewRequestWorkflow::TRANSITION_COMPLETE)) {
                    continue;
                }

                $check = $this->reviewRequestEligibilityChecker->check($reviewRequest);
                if (!$check->eligible) {
                    $this->logger->debug(sprintf(
                        'Review request (id: %d) is not eligible. Reason: %s',
                        (int) $reviewRequest->getId(),
                        (string) $check->reason,
                    ));

                    $reviewRequest->setIneligibilityReason($check->reason);

                    continue;
                }

                $this->reviewRequestEmailManager->sendReviewRequest($reviewRequest);

                $this->reviewRequestWorkflow->apply($reviewRequest, ReviewRequestWorkflow::TRANSITION_COMPLETE);
                ++$i;
            } catch (\Throwable $e) {
                $this->logger->error(sprintf(
                    'There was an error processing a review request (id: %d). The error was: %s',
                    (int) $reviewRequest->getId(),
                    $e->getMessage(),
                ));

                $reviewRequest->setProcessingError($e->getMessage());
            }
        }

        $this->logger->debug(sprintf('%d review request%s were completed', $i, 1 === $i ? '' : 's'));
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
