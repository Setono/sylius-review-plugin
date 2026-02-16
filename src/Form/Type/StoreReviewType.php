<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Form\Type;

use Setono\SyliusReviewPlugin\Model\ChannelInterface;
use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class StoreReviewType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rating', ChoiceType::class, [
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                ],
                'label' => 'setono_sylius_review.form.store_review.rating',
                'expanded' => true,
                'multiple' => false,
                'required' => false,
                'placeholder' => false,
            ])
            ->add('title', TextType::class, [
                'label' => 'setono_sylius_review.form.store_review.title',
                'required' => false,
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'setono_sylius_review.form.store_review.comment',
                'required' => false,
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($options): void {
                $storeReview = $event->getData();

                if (!$storeReview instanceof StoreReviewInterface) {
                    return;
                }

                /** @var OrderInterface $order */
                $order = $options['order'];

                $storeReview->setOrder($order);

                $channel = $order->getChannel();
                if ($channel instanceof ChannelInterface) {
                    $storeReview->setReviewSubject($channel);
                }

                $customer = $order->getCustomer();
                if ($customer instanceof ReviewerInterface) {
                    $storeReview->setAuthor($customer);
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('order');
        $resolver->setAllowedTypes('order', OrderInterface::class);
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_review_store_review';
    }
}
