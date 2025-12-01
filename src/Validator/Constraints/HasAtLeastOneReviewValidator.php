<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Validator\Constraints;

use Setono\SyliusReviewPlugin\Model\ReviewInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class HasAtLeastOneReviewValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof HasAtLeastOneReview) {
            throw new UnexpectedTypeException($constraint, HasAtLeastOneReview::class);
        }

        if (null === $value) {
            return;
        }

        if (!$value instanceof ReviewInterface) {
            throw new UnexpectedValueException($value, ReviewInterface::class);
        }

        $hasStoreReviewWithRating = null !== $value->getStoreReview()?->getRating();

        $hasProductReviewWithRating = false;
        foreach ($value->getProductReviews() as $productReview) {
            if (null !== $productReview->getRating()) {
                $hasProductReviewWithRating = true;

                break;
            }
        }

        if (!$hasStoreReviewWithRating && !$hasProductReviewWithRating) {
            $this->context->buildViolation($constraint->message)
                ->atPath('storeReview')
                ->addViolation();
        }
    }
}
