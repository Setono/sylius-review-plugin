<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Form\Extension;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\Form\Extension\ReviewTypeStoreReplyExtension;
use Setono\SyliusReviewPlugin\Form\Type\MarkdownTextareaType;
use Sylius\Bundle\CoreBundle\Form\Type\Product\ProductReviewType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

final class ReviewTypeStoreReplyExtensionTest extends TestCase
{
    use ProphecyTrait;

    /** @test */
    public function it_extends_product_review_type(): void
    {
        $types = ReviewTypeStoreReplyExtension::getExtendedTypes();
        self::assertSame([ProductReviewType::class], is_array($types) ? $types : iterator_to_array($types));
    }

    /** @test */
    public function it_adds_store_reply_and_notify_reviewer_fields(): void
    {
        $builder = $this->prophesize(FormBuilderInterface::class);

        $builder->add('storeReply', MarkdownTextareaType::class, Argument::type('array'))
            ->willReturn($builder->reveal())
            ->shouldBeCalledOnce();

        $builder->add('notifyReviewer', CheckboxType::class, Argument::type('array'))
            ->willReturn($builder->reveal())
            ->shouldBeCalledOnce();

        $extension = new ReviewTypeStoreReplyExtension();
        $extension->buildForm($builder->reveal(), []);
    }
}
