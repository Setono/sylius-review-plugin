<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_CLASS)]
final class HasAtLeastOneReview extends Constraint
{
    public string $message = 'setono_sylius_review.review.at_least_one_review';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
