<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Form\Type\Admin;

use Setono\SyliusReviewPlugin\Form\Type\Admin\StoreReviewAdminType;
use Setono\SyliusReviewPlugin\Model\StoreReview;
use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

final class StoreReviewAdminTypeTest extends TypeTestCase
{
    /** @return list<PreloadedExtension> */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension(
                [
                    new StoreReviewAdminType(
                        StoreReviewInterface::class,
                        ['sylius'],
                    ),
                ],
                [],
            ),
        ];
    }

    /** @test */
    public function it_has_store_reply_and_notify_reviewer_fields(): void
    {
        $form = $this->factory->create(StoreReviewAdminType::class);

        self::assertTrue($form->has('storeReply'));
        self::assertTrue($form->has('notifyReviewer'));
    }

    /** @test */
    public function it_submits_notify_reviewer_value(): void
    {
        $review = new StoreReview();

        $form = $this->factory->create(StoreReviewAdminType::class, $review);
        $form->submit([
            'title' => 'Great store',
            'rating' => '5',
            'storeReply' => 'Thank you!',
            'notifyReviewer' => '1',
        ]);

        self::assertTrue($form->isSynchronized());
        self::assertTrue($review->getNotifyReviewer());
        self::assertSame('Thank you!', $review->getStoreReply());
    }

    /** @test */
    public function it_defaults_notify_reviewer_to_false_when_not_submitted(): void
    {
        $review = new StoreReview();

        $form = $this->factory->create(StoreReviewAdminType::class, $review);
        $form->submit([
            'title' => 'Great store',
            'rating' => '5',
        ]);

        self::assertTrue($form->isSynchronized());
        self::assertFalse($review->getNotifyReviewer());
    }
}
