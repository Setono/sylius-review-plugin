<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Form\Type;

use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Setono\SyliusReviewPlugin\Form\Type\StoreReviewType;
use Setono\SyliusReviewPlugin\Model\ChannelInterface;
use Setono\SyliusReviewPlugin\Model\StoreReview;
use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

final class StoreReviewTypeTest extends TypeTestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<OrderInterface> */
    private ObjectProphecy $order;

    protected function setUp(): void
    {
        $this->order = $this->prophesize(OrderInterface::class);
        $this->order->getChannel()->willReturn(null);
        $this->order->getCustomer()->willReturn(null);

        parent::setUp();
    }

    /** @return list<PreloadedExtension> */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension(
                [
                    new StoreReviewType(
                        StoreReviewInterface::class,
                        ['setono_sylius_review'],
                    ),
                ],
                [],
            ),
        ];
    }

    /** @test */
    public function it_has_rating_title_and_comment_fields(): void
    {
        $form = $this->factory->create(StoreReviewType::class, null, ['order' => $this->order->reveal()]);

        self::assertTrue($form->has('rating'));
        self::assertTrue($form->has('title'));
        self::assertTrue($form->has('comment'));
    }

    /** @test */
    public function it_has_expanded_rating_choices_from_one_to_five(): void
    {
        $form = $this->factory->create(StoreReviewType::class, null, ['order' => $this->order->reveal()]);

        $ratingConfig = $form->get('rating')->getConfig();

        self::assertSame(ChoiceType::class, $ratingConfig->getType()->getInnerType()::class);
        self::assertTrue($ratingConfig->getOption('expanded'));
        self::assertFalse($ratingConfig->getOption('multiple'));
        self::assertSame(['1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5], $ratingConfig->getOption('choices'));
    }

    /** @test */
    public function it_sets_order_on_store_review_after_submit(): void
    {
        $storeReview = new StoreReview();

        $form = $this->factory->create(StoreReviewType::class, $storeReview, ['order' => $this->order->reveal()]);
        $form->submit([]);

        self::assertSame($this->order->reveal(), $storeReview->getOrder());
    }

    /** @test */
    public function it_sets_review_subject_from_order_channel(): void
    {
        $channel = $this->prophesize(ChannelInterface::class);
        $this->order->getChannel()->willReturn($channel->reveal());

        $storeReview = new StoreReview();

        $form = $this->factory->create(StoreReviewType::class, $storeReview, ['order' => $this->order->reveal()]);
        $form->submit([]);

        self::assertSame($channel->reveal(), $storeReview->getReviewSubject());
    }

    /** @test */
    public function it_sets_author_from_order_customer(): void
    {
        $customer = $this->prophesize(CustomerInterface::class);
        $this->order->getCustomer()->willReturn($customer->reveal());

        $storeReview = new StoreReview();

        $form = $this->factory->create(StoreReviewType::class, $storeReview, ['order' => $this->order->reveal()]);
        $form->submit([]);

        self::assertSame($customer->reveal(), $storeReview->getAuthor());
    }

    /** @test */
    public function it_does_not_set_review_subject_when_channel_is_not_plugin_channel_interface(): void
    {
        $channel = $this->prophesize(BaseChannelInterface::class);
        $this->order->getChannel()->willReturn($channel->reveal());

        $storeReview = new StoreReview();

        $form = $this->factory->create(StoreReviewType::class, $storeReview, ['order' => $this->order->reveal()]);
        $form->submit([]);

        self::assertNull($storeReview->getReviewSubject());
    }

    /** @test */
    public function it_requires_order_option(): void
    {
        $this->expectException(MissingOptionsException::class);

        $this->factory->create(StoreReviewType::class);
    }

    /** @test */
    public function it_has_correct_block_prefix(): void
    {
        $form = $this->factory->create(StoreReviewType::class, null, ['order' => $this->order->reveal()]);

        self::assertSame('setono_sylius_review_store_review', $form->getConfig()->getName());
    }
}
