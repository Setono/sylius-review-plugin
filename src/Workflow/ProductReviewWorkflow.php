<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Workflow;

use Sylius\Component\Review\Model\ReviewInterface;

/**
 * Constants for Sylius's sylius_product_review workflow
 */
final class ProductReviewWorkflow
{
    final public const NAME = 'sylius_product_review';

    final public const TRANSITION_ACCEPT = 'accept';

    final public const TRANSITION_REJECT = 'reject';

    final public const STATE_NEW = ReviewInterface::STATUS_NEW;

    final public const STATE_ACCEPTED = ReviewInterface::STATUS_ACCEPTED;

    final public const STATE_REJECTED = ReviewInterface::STATUS_REJECTED;

    private function __construct()
    {
    }
}
