<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Form\Type;

use Setono\SyliusReviewPlugin\Mailer\Emails;
use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Synolia\SyliusMailTesterPlugin\Form\Type\LimitedEntityType;
use Synolia\SyliusMailTesterPlugin\Resolver\ResolvableFormTypeInterface;

final class ReviewRequestEmailType extends AbstractType implements ResolvableFormTypeInterface
{
    public function __construct(private readonly string $reviewRequestClass)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('reviewRequest', LimitedEntityType::class, [
            'class' => $this->reviewRequestClass,
            'choice_label' => static fn (ReviewRequestInterface $reviewRequest): string => sprintf('Review request #%d - Order %s', (int) $reviewRequest->getId(), (string) $reviewRequest->getOrder()?->getNumber()),
        ]);
    }

    public function support(string $emailKey): bool
    {
        return Emails::REVIEW_REQUEST === $emailKey;
    }

    public function getCode(): string
    {
        return Emails::REVIEW_REQUEST;
    }

    public function getFormType(string $emailKey): ResolvableFormTypeInterface
    {
        return $this;
    }
}
