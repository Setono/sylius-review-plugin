<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Form\Type;

use Setono\SyliusReviewPlugin\Controller\ReviewCommand;
use Setono\SyliusReviewPlugin\Repository\StoreReviewRepositoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductReviewInterface;
use Sylius\Component\Core\Repository\ProductReviewRepositoryInterface;
use Sylius\Component\Review\Factory\ReviewFactoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Webmozart\Assert\Assert;

/**
 * @extends AbstractType<ReviewCommand>
 */
final class ReviewType extends AbstractType
{
    /**
     * @param ReviewFactoryInterface<ProductReviewInterface> $productReviewFactory
     * @param ProductReviewRepositoryInterface<ProductReviewInterface> $productReviewRepository
     */
    public function __construct(
        private readonly StoreReviewRepositoryInterface $storeReviewRepository,
        private readonly ReviewFactoryInterface $productReviewFactory,
        private readonly ProductReviewRepositoryInterface $productReviewRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var OrderInterface $order */
        $order = $options['order'];

        $builder
            ->add('storeReview', StoreReviewType::class, [
                'order' => $order,
            ])
            ->add('productReviews', CollectionType::class, [
                'entry_type' => ProductReviewType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => false,
                'allow_delete' => false,
                'label' => false,
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($order): void {
                /** @var mixed|ReviewCommand $reviewCommand */
                $reviewCommand = $event->getData();

                Assert::isInstanceOf($reviewCommand, ReviewCommand::class);

                $reviewCommand->setStoreReview($this->storeReviewRepository->findOneByOrder($order));

                foreach ($this->buildProductReviews($order) as $productReview) {
                    $reviewCommand->addProductReview($productReview);
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => ReviewCommand::class,
                'validation_groups' => ['setono_sylius_review'],
            ])
            ->setRequired(['order'])
            ->setAllowedTypes('order', OrderInterface::class)
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_review';
    }

    /**
     * @return list<ProductReviewInterface>
     */
    private function buildProductReviews(OrderInterface $order): array
    {
        $reviews = [];
        $customer = $order->getCustomer();
        $seenProductIds = [];

        foreach ($order->getItems() as $item) {
            $product = $item->getProduct();
            if (null === $product) {
                continue;
            }

            $productId = $product->getId();
            if (null !== $productId && isset($seenProductIds[$productId])) {
                continue;
            }

            if (null !== $productId) {
                $seenProductIds[$productId] = true;
            }

            /** @var ProductReviewInterface|null $existingReview */
            $existingReview = $this->productReviewRepository->findOneBy([
                'reviewSubject' => $product,
                'author' => $customer,
            ]);

            if (null !== $existingReview) {
                $reviews[] = $existingReview;
            } else {
                /** @var ProductReviewInterface $productReview */
                $productReview = $this->productReviewFactory->createForSubjectWithReviewer($product, $customer); // @phpstan-ignore argument.type
                $reviews[] = $productReview;
            }
        }

        return $reviews;
    }
}
