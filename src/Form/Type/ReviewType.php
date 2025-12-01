<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Form\Type;

use Setono\SyliusReviewPlugin\Model\ProductReviewInterface;
use Setono\SyliusReviewPlugin\Model\ReviewInterface;
use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Setono\SyliusReviewPlugin\Repository\ProductReviewRepositoryInterface;
use Setono\SyliusReviewPlugin\Repository\StoreReviewRepositoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Review\Factory\ReviewFactoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<ReviewInterface>
 */
final class ReviewType extends AbstractType
{
    /**
     * @param FactoryInterface<StoreReviewInterface> $storeReviewFactory
     * @param ReviewFactoryInterface<ProductReviewInterface> $productReviewFactory
     */
    public function __construct(
        private readonly FactoryInterface $storeReviewFactory,
        private readonly ReviewFactoryInterface $productReviewFactory,
        private readonly StoreReviewRepositoryInterface $storeReviewRepository,
        private readonly ProductReviewRepositoryInterface $productReviewRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var OrderInterface $order */
        $order = $options['order'];

        /** @var ReviewInterface $review */
        $review = $builder->getData();

        // Get existing store review from the Review entity, or from repository, or create new one
        $storeReview = $review->getStoreReview()
            ?? $this->storeReviewRepository->findOneByOrder($order)
            ?? $this->storeReviewFactory->createNew();

        $builder->add('storeReview', StoreReviewType::class, [
            'required' => false,
            'data' => $storeReview,
            'order' => $order,
        ]);

        // Build product reviews from the Review entity or create new ones
        $productReviews = $this->buildProductReviews($order, $review);

        $builder->add('productReviews', CollectionType::class, [
            'entry_type' => ProductReviewType::class,
            'entry_options' => [
                'label' => false,
                'order' => $order,
            ],
            'allow_add' => false,
            'allow_delete' => false,
            'label' => false,
            'data' => $productReviews,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReviewInterface::class,
            'validation_groups' => ['setono_sylius_review'],
        ]);
        $resolver->setRequired(['order']);
        $resolver->setAllowedTypes('order', OrderInterface::class);
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_review';
    }

    /**
     * @return list<ProductReviewInterface>
     */
    private function buildProductReviews(OrderInterface $order, ReviewInterface $review): array
    {
        $reviews = [];
        $customer = $order->getCustomer();

        // First check existing reviews from the Review entity
        /** @var array<int|string, ProductReviewInterface> $existingByProduct */
        $existingByProduct = [];

        foreach ($review->getProductReviews() as $productReview) {
            $subject = $productReview->getReviewSubject();
            if ($subject instanceof ProductInterface) {
                $productId = $subject->getId();
                if (null !== $productId) {
                    $existingByProduct[$productId] = $productReview;
                }
            }
        }

        // Then check repository for existing reviews by order
        $existingReviews = $this->productReviewRepository->findByOrder($order);
        foreach ($existingReviews as $productReview) {
            $subject = $productReview->getReviewSubject();
            if ($subject instanceof ProductInterface) {
                $productId = $subject->getId();
                if (null !== $productId && !isset($existingByProduct[$productId])) {
                    $existingByProduct[$productId] = $productReview;
                }
            }
        }

        foreach ($order->getItems() as $item) {
            $product = $item->getProduct();
            if (null !== $product) {
                $productId = $product->getId();
                if (null !== $productId && isset($existingByProduct[$productId])) {
                    // Use existing review
                    $reviews[] = $existingByProduct[$productId];
                } else {
                    // Create new review
                    /** @var ProductReviewInterface $productReview */
                    $productReview = $this->productReviewFactory->createForSubjectWithReviewer($product, $customer); // @phpstan-ignore argument.type
                    $reviews[] = $productReview;
                }
            }
        }

        return $reviews;
    }
}
