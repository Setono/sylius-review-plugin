<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

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
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_review_product_review';
    }
}
