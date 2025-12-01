<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Workflow;

use Setono\SyliusReviewPlugin\Model\ProductReviewInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\Workflow\Transition;

/**
 * This workflow overrides Sylius's sylius_product_review workflow to add a 'pending' state
 */
final class ProductReviewWorkflow
{
    private const PROPERTY_NAME = 'status';

    final public const NAME = 'sylius_product_review';

    final public const STATE_PENDING = ProductReviewInterface::STATUS_PENDING;

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
            ProductReviewInterface::STATUS_PENDING,
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
                'supports' => ProductReviewInterface::class,
                'initial_marking' => ProductReviewInterface::STATUS_PENDING,
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
            new Transition(self::TRANSITION_SUBMIT, [ProductReviewInterface::STATUS_PENDING], ReviewInterface::STATUS_NEW),
            new Transition(self::TRANSITION_ACCEPT, [ReviewInterface::STATUS_NEW], ReviewInterface::STATUS_ACCEPTED),
            new Transition(self::TRANSITION_REJECT, [ReviewInterface::STATUS_NEW], ReviewInterface::STATUS_REJECTED),
        ];
    }
}
