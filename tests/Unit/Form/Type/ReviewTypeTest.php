<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Setono\SyliusReviewPlugin\Controller\ReviewCommand;
use Setono\SyliusReviewPlugin\Form\Type\ProductReviewType;
use Setono\SyliusReviewPlugin\Form\Type\ReviewType;
use Setono\SyliusReviewPlugin\Form\Type\StoreReviewType;
use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Setono\SyliusReviewPlugin\Repository\StoreReviewRepositoryInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductReviewInterface;
use Sylius\Component\Core\Repository\ProductReviewRepositoryInterface;
use Sylius\Component\Review\Factory\ReviewFactoryInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

final class ReviewTypeTest extends TypeTestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<StoreReviewRepositoryInterface> */
    private ObjectProphecy $storeReviewRepository;

    /** @var ObjectProphecy<ReviewFactoryInterface> @phpstan-ignore missingType.generics */
    private ObjectProphecy $productReviewFactory;

    /** @var ObjectProphecy<ProductReviewRepositoryInterface> @phpstan-ignore missingType.generics */
    private ObjectProphecy $productReviewRepository;

    protected function setUp(): void
    {
        $this->storeReviewRepository = $this->prophesize(StoreReviewRepositoryInterface::class);
        $this->productReviewFactory = $this->prophesize(ReviewFactoryInterface::class);
        $this->productReviewRepository = $this->prophesize(ProductReviewRepositoryInterface::class);

        parent::setUp();
    }

    /** @return list<PreloadedExtension> */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension(
                [
                    new ReviewType(
                        $this->storeReviewRepository->reveal(),
                        $this->productReviewFactory->reveal(), // @phpstan-ignore argument.type
                        $this->productReviewRepository->reveal(), // @phpstan-ignore argument.type
                    ),
                    new StoreReviewType(
                        StoreReviewInterface::class,
                        ['setono_sylius_review'],
                    ),
                    new ProductReviewType(
                        ProductReviewInterface::class,
                        ['sylius'],
                    ),
                ],
                [],
            ),
        ];
    }

    /**
     * @test
     */
    public function it_deduplicates_order_items_with_the_same_product(): void
    {
        $customer = $this->prophesize(CustomerInterface::class);
        $product = $this->prophesize(ProductInterface::class);
        $product->getId()->willReturn(1);

        $item1 = $this->prophesize(OrderItemInterface::class);
        $item1->getProduct()->willReturn($product->reveal());

        $item2 = $this->prophesize(OrderItemInterface::class);
        $item2->getProduct()->willReturn($product->reveal());

        $order = $this->prophesize(OrderInterface::class);
        $order->getCustomer()->willReturn($customer->reveal());
        $order->getItems()->willReturn(new ArrayCollection([$item1->reveal(), $item2->reveal()]));

        $newReview = $this->prophesize(ProductReviewInterface::class);

        $this->storeReviewRepository->findOneByOrder($order->reveal())->willReturn(null);
        $this->productReviewFactory->createForSubjectWithReviewer($product->reveal(), $customer->reveal())
            ->willReturn($newReview->reveal())
            ->shouldBeCalledOnce();
        $this->productReviewRepository->findOneBy([
            'reviewSubject' => $product->reveal(),
            'author' => $customer->reveal(),
        ])->willReturn(null);

        $reviewCommand = new ReviewCommand();
        $form = $this->factory->create(ReviewType::class, $reviewCommand, ['order' => $order->reveal()]);

        self::assertTrue($form->isSynchronized());
        self::assertCount(1, $reviewCommand->getProductReviews());
        self::assertSame($newReview->reveal(), $reviewCommand->getProductReviews()->first());
    }

    /**
     * @test
     */
    public function it_reuses_existing_product_reviews(): void
    {
        $customer = $this->prophesize(CustomerInterface::class);
        $product = $this->prophesize(ProductInterface::class);
        $product->getId()->willReturn(1);

        $item = $this->prophesize(OrderItemInterface::class);
        $item->getProduct()->willReturn($product->reveal());

        $order = $this->prophesize(OrderInterface::class);
        $order->getCustomer()->willReturn($customer->reveal());
        $order->getItems()->willReturn(new ArrayCollection([$item->reveal()]));

        $existingReview = $this->prophesize(ProductReviewInterface::class);

        $this->storeReviewRepository->findOneByOrder($order->reveal())->willReturn(null);
        $this->productReviewFactory->createForSubjectWithReviewer($product->reveal(), $customer->reveal())
            ->shouldNotBeCalled();
        $this->productReviewRepository->findOneBy([
            'reviewSubject' => $product->reveal(),
            'author' => $customer->reveal(),
        ])->willReturn($existingReview->reveal());

        $reviewCommand = new ReviewCommand();
        $form = $this->factory->create(ReviewType::class, $reviewCommand, ['order' => $order->reveal()]);

        self::assertTrue($form->isSynchronized());
        self::assertCount(1, $reviewCommand->getProductReviews());
        self::assertSame($existingReview->reveal(), $reviewCommand->getProductReviews()->first());
    }

    /**
     * @test
     */
    public function it_creates_new_reviews_when_none_exist(): void
    {
        $customer = $this->prophesize(CustomerInterface::class);

        $product1 = $this->prophesize(ProductInterface::class);
        $product1->getId()->willReturn(1);

        $product2 = $this->prophesize(ProductInterface::class);
        $product2->getId()->willReturn(2);

        $item1 = $this->prophesize(OrderItemInterface::class);
        $item1->getProduct()->willReturn($product1->reveal());

        $item2 = $this->prophesize(OrderItemInterface::class);
        $item2->getProduct()->willReturn($product2->reveal());

        $order = $this->prophesize(OrderInterface::class);
        $order->getCustomer()->willReturn($customer->reveal());
        $order->getItems()->willReturn(new ArrayCollection([$item1->reveal(), $item2->reveal()]));

        $newReview1 = $this->prophesize(ProductReviewInterface::class);
        $newReview2 = $this->prophesize(ProductReviewInterface::class);

        $this->storeReviewRepository->findOneByOrder($order->reveal())->willReturn(null);
        $this->productReviewFactory->createForSubjectWithReviewer($product1->reveal(), $customer->reveal())
            ->willReturn($newReview1->reveal());
        $this->productReviewFactory->createForSubjectWithReviewer($product2->reveal(), $customer->reveal())
            ->willReturn($newReview2->reveal());

        $this->productReviewRepository->findOneBy([
            'reviewSubject' => $product1->reveal(),
            'author' => $customer->reveal(),
        ])->willReturn(null);
        $this->productReviewRepository->findOneBy([
            'reviewSubject' => $product2->reveal(),
            'author' => $customer->reveal(),
        ])->willReturn(null);

        $reviewCommand = new ReviewCommand();
        $form = $this->factory->create(ReviewType::class, $reviewCommand, ['order' => $order->reveal()]);

        self::assertTrue($form->isSynchronized());
        self::assertCount(2, $reviewCommand->getProductReviews());
        self::assertSame($newReview1->reveal(), $reviewCommand->getProductReviews()->first());
        self::assertSame($newReview2->reveal(), $reviewCommand->getProductReviews()->last());
    }
}
