<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\EventSubscriber\Workflow;

use Setono\SyliusReviewPlugin\Checker\ReviewAutoApprovalCheckerInterface;
use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Setono\SyliusReviewPlugin\Workflow\ProductReviewWorkflow;
use Setono\SyliusReviewPlugin\Workflow\StoreReviewWorkflow;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\EnteredEvent;
use Symfony\Component\Workflow\WorkflowInterface;

final class ReviewAutoApprovalSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ReviewAutoApprovalCheckerInterface $autoApprovalChecker,
        private readonly WorkflowInterface $storeReviewStateMachine,
        private readonly WorkflowInterface $productReviewStateMachine,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.' . StoreReviewWorkflow::NAME . '.entered.' . StoreReviewInterface::STATE_NEW => 'onStoreReviewEnteredNew',
            'workflow.' . ProductReviewWorkflow::NAME . '.entered.' . ReviewInterface::STATUS_NEW => 'onProductReviewEnteredNew',
        ];
    }

    public function onStoreReviewEnteredNew(EnteredEvent $event): void
    {
        /** @var StoreReviewInterface $review */
        $review = $event->getSubject();

        if ($this->autoApprovalChecker->shouldAutoApprove($review)) {
            $this->storeReviewStateMachine->apply($review, StoreReviewWorkflow::TRANSITION_ACCEPT);
        }
    }

    public function onProductReviewEnteredNew(EnteredEvent $event): void
    {
        /** @var ReviewInterface $review */
        $review = $event->getSubject();

        if ($this->autoApprovalChecker->shouldAutoApproveProductReview($review)) {
            $this->productReviewStateMachine->apply($review, ProductReviewWorkflow::TRANSITION_ACCEPT);
        }
    }
}
