<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Form\Type;

use Setono\SyliusReviewPlugin\Mailer\Emails;
use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Synolia\SyliusMailTesterPlugin\Form\Type\LimitedEntityType;
use Synolia\SyliusMailTesterPlugin\Resolver\ResolvableFormTypeInterface;

final class StoreReplyNotificationEmailType extends AbstractType implements ResolvableFormTypeInterface
{
    public function __construct(private readonly string $storeReviewClass)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('storeReview', LimitedEntityType::class, [
            'class' => $this->storeReviewClass,
            'choice_label' => static fn (StoreReviewInterface $review): string => sprintf('Store review #%d - %s', (int) $review->getId(), (string) $review->getReviewSubject()?->getName()),
        ]);

        $builder->addEventListener(FormEvents::SUBMIT, static function (FormEvent $event): void {
            /** @var array<string, mixed> $data */
            $data = $event->getData();

            $review = $data['storeReview'] ?? null;
            if (!$review instanceof StoreReviewInterface) {
                return;
            }

            $reviewSubject = $review->getReviewSubject();

            $data['review'] = $review;
            $data['isStoreReview'] = true;
            $data['isProductReview'] = false;
            $data['reviewSubject'] = $reviewSubject;
            $data['reviewSubjectName'] = $reviewSubject?->getName();

            $event->setData($data);
        });
    }

    public function support(string $emailKey): bool
    {
        return Emails::STORE_REPLY_NOTIFICATION === $emailKey;
    }

    public function getCode(): string
    {
        return Emails::STORE_REPLY_NOTIFICATION;
    }

    public function getFormType(string $emailKey): ResolvableFormTypeInterface
    {
        return $this;
    }
}
