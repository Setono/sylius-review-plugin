<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Workflow;

use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Symfony\Component\Workflow\Transition;

final class StoreReviewWorkflow
{
    private const PROPERTY_NAME = 'state';

    final public const NAME = 'setono_sylius_review__store_review';

    final public const TRANSITION_SUBMIT = 'submit';

    final public const TRANSITION_ACCEPT = 'accept';

    final public const TRANSITION_REJECT = 'reject';

    private function __construct()
    {
    }

    /**
     * @return list<string>
     */
    public static function getStates(): array
    {
        return [
            StoreReviewInterface::STATE_PENDING,
            StoreReviewInterface::STATE_NEW,
            StoreReviewInterface::STATE_ACCEPTED,
            StoreReviewInterface::STATE_REJECTED,
        ];
    }

    /**
     * @return array<string, array<mixed>>
     */
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
                'supports' => StoreReviewInterface::class,
                'initial_marking' => StoreReviewInterface::STATE_PENDING,
                'places' => self::getStates(),
                'transitions' => $transitions,
            ],
        ];
    }

    /**
     * @return list<Transition>
     */
    public static function getTransitions(): array
    {
        return [
            new Transition(self::TRANSITION_SUBMIT, [StoreReviewInterface::STATE_PENDING], StoreReviewInterface::STATE_NEW),
            new Transition(self::TRANSITION_ACCEPT, [StoreReviewInterface::STATE_NEW], StoreReviewInterface::STATE_ACCEPTED),
            new Transition(self::TRANSITION_REJECT, [StoreReviewInterface::STATE_NEW], StoreReviewInterface::STATE_REJECTED),
        ];
    }
}
