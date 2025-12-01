<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Form\Type;

use Setono\SyliusReviewPlugin\Model\ProductReviewInterface;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductReviewType extends AbstractResourceType
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
                'label' => 'sylius.form.review.rating',
                'expanded' => true,
                'multiple' => false,
                'required' => false,
                'placeholder' => false,
            ])
            ->add('title', TextType::class, [
                'label' => 'sylius.form.review.title',
                'required' => false,
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'sylius.form.review.comment',
                'required' => false,
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($options): void {
            $data = $event->getData();

            if (!$data instanceof ProductReviewInterface) {
                return;
            }

            /** @var OrderInterface|null $order */
            $order = $options['order'] ?? null;
            if (null === $order) {
                return;
            }

            // Only set order if this is a new review (no ID yet)
            if (null !== $data->getId()) {
                return;
            }

            $data->setOrder($order);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('order', null);
        $resolver->setAllowedTypes('order', ['null', OrderInterface::class]);
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_review_product_review';
    }
}
