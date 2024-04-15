<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Processor;

use DoctrineBatchUtils\BatchProcessing\SimpleBatchIteratorAggregate;
use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\SyliusReviewPlugin\EligibilityChecker\ReviewRequestEligibilityCheckerInterface;
use Setono\SyliusReviewPlugin\Event\ReviewRequestProcessingStarted;
use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;
use Setono\SyliusReviewPlugin\Repository\ReviewRequestRepositoryInterface;
use Setono\SyliusReviewPlugin\Workflow\ReviewRequestWorkflow;
use Symfony\Component\Workflow\WorkflowInterface;

final class ReviewRequestProcessor implements ReviewRequestProcessorInterface
{
    public function __construct(
        private readonly ReviewRequestRepositoryInterface $reviewRequestRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly WorkflowInterface $reviewRequestWorkflow,
        private readonly ReviewRequestEligibilityCheckerInterface $reviewRequestEligibilityChecker,
    ) {
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

            // todo send email

            $this->reviewRequestWorkflow->apply($reviewRequest, ReviewRequestWorkflow::TRANSITION_COMPLETE);
        }
    }
}
