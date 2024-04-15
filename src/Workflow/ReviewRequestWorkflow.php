<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Workflow;

use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;
use Symfony\Component\Workflow\Transition;

final class ReviewRequestWorkflow
{
    private const PROPERTY_NAME = 'state';

    final public const NAME = 'setono_sylius_review__review_request';

    final public const TRANSITION_COMPLETE = 'complete';

    final public const TRANSITION_CANCEL = 'cancel';

    private function __construct()
    {
    }

    /**
     * @return array<array-key, string>
     */
    public static function getStates(): array
    {
        return [
            ReviewRequestInterface::STATE_PENDING,
            ReviewRequestInterface::STATE_COMPLETED,
            ReviewRequestInterface::STATE_CANCELLED,
        ];
    }

    public static function getConfig(): array
    {
        $transitions = [];
        foreach (self::getTransitions() as $transition) {
            $transitions[$transition->getName()] = [
                'from' => $transition->getFroms(),
                'to' => $transition->getTos(),
            ];
        }

        return [
            self::NAME => [
                'type' => 'state_machine',
                'marking_store' => [
                    'type' => 'method',
                    'property' => self::PROPERTY_NAME,
                ],
                'supports' => ReviewRequestInterface::class,
                'initial_marking' => ReviewRequestInterface::STATE_PENDING,
                'places' => self::getStates(),
                'transitions' => $transitions,
            ],
        ];
    }

    /**
     * @return array<array-key, Transition>
     */
    public static function getTransitions(): array
    {
        return [
            new Transition(self::TRANSITION_COMPLETE, [ReviewRequestInterface::STATE_PENDING], ReviewRequestInterface::STATE_COMPLETED),
            new Transition(self::TRANSITION_CANCEL, [ReviewRequestInterface::STATE_PENDING], ReviewRequestInterface::STATE_CANCELLED),
        ];
    }
}
