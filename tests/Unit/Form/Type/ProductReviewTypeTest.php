<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Form\Type;

use Setono\SyliusReviewPlugin\Form\Type\ProductReviewType;
use Sylius\Component\Core\Model\ProductReview;
use Sylius\Component\Core\Model\ProductReviewInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

final class ProductReviewTypeTest extends TypeTestCase
{
    /** @return list<PreloadedExtension> */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension(
                [
                    new ProductReviewType(
                        ProductReviewInterface::class,
                        ['sylius'],
                    ),
                ],
                [],
            ),
        ];
    }

    /** @test */
    public function it_has_rating_title_and_comment_fields(): void
    {
        $form = $this->factory->create(ProductReviewType::class);

        self::assertTrue($form->has('rating'));
        self::assertTrue($form->has('title'));
        self::assertTrue($form->has('comment'));
    }

    /** @test */
    public function it_has_expanded_rating_choices_from_one_to_five(): void
    {
        $form = $this->factory->create(ProductReviewType::class);

        $ratingConfig = $form->get('rating')->getConfig();

        self::assertSame(ChoiceType::class, $ratingConfig->getType()->getInnerType()::class);
        self::assertTrue($ratingConfig->getOption('expanded'));
        self::assertFalse($ratingConfig->getOption('multiple'));
        self::assertSame(['1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5], $ratingConfig->getOption('choices'));
    }

    /** @test */
    public function it_maps_submitted_data_to_entity(): void
    {
        $review = new ProductReview();

        $form = $this->factory->create(ProductReviewType::class, $review);
        $form->submit([
            'rating' => '4',
            'title' => 'Great product',
            'comment' => 'I really enjoyed using this product.',
        ]);

        self::assertTrue($form->isSynchronized());
        self::assertSame(4, $review->getRating());
        self::assertSame('Great product', $review->getTitle());
        self::assertSame('I really enjoyed using this product.', $review->getComment());
    }

    /** @test */
    public function it_has_correct_block_prefix(): void
    {
        $form = $this->factory->create(ProductReviewType::class);

        self::assertSame('setono_sylius_review_product_review', $form->getConfig()->getName());
    }
}
