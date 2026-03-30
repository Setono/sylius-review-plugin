<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Workflow;

use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\Workflow\Transition;

final class StoreReviewWorkflow
{
    private const PROPERTY_NAME = 'status';

    final public const NAME = 'setono_sylius_review__store_review';

    final public const TRANSITION_ACCEPT = 'accept';

    final public const TRANSITION_REJECT = 'reject';

    final public const TRANSITION_REQUEST_EDIT = 'request_edit';

    private function __construct()
    {
    }

    /**
     * @return list<string>
     */
    public static function getStates(): array
    {
        return [
            ReviewInterface::STATUS_NEW,
            ReviewInterface::STATUS_ACCEPTED,
            ReviewInterface::STATUS_REJECTED,
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
                'initial_marking' => ReviewInterface::STATUS_NEW,
                'places' => self::getStates(),
                'transitions' => $transitions,
            ],
        ];
    }

    /**
     * @return array<string, array<mixed>>
     */
    public static function getWinzouConfig(): array
    {
        $transitions = [];
        foreach (self::getTransitions() as $transition) {
            $tos = $transition->getTos();
            $transitions[$transition->getName()] = [
                'from' => $transition->getFroms(),
                'to' => $tos[0],
            ];
        }

        $states = [];
        foreach (self::getStates() as $state) {
            $states[$state] = null;
        }

        return [
            self::NAME => [
                'class' => '%setono_sylius_review.model.store_review.class%',
                'property_path' => self::PROPERTY_NAME,
                'graph' => self::NAME,
                'state_machine_class' => '%sylius.state_machine.class%',
                'states' => $states,
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
            new Transition(self::TRANSITION_ACCEPT, [ReviewInterface::STATUS_NEW], ReviewInterface::STATUS_ACCEPTED),
            new Transition(self::TRANSITION_REJECT, [ReviewInterface::STATUS_NEW], ReviewInterface::STATUS_REJECTED),
            new Transition(self::TRANSITION_REQUEST_EDIT, [ReviewInterface::STATUS_ACCEPTED, ReviewInterface::STATUS_REJECTED], ReviewInterface::STATUS_NEW),
        ];
    }
}
