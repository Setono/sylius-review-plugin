<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Form\Type\Admin;

use Setono\SyliusReviewPlugin\Form\Type\MarkdownTextareaType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class StoreReviewAdminType extends AbstractResourceType
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
                'label' => 'sylius.ui.rating',
                'expanded' => true,
                'multiple' => false,
                'placeholder' => false,
            ])
            ->add('title', TextType::class, [
                'label' => 'sylius.ui.title',
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'sylius.ui.comment',
                'required' => false,
            ])
            ->add('storeReply', MarkdownTextareaType::class, [
                'label' => 'setono_sylius_review.form.review.store_reply',
                'required' => false,
            ])
            ->add('notifyReviewer', CheckboxType::class, [
                'label' => 'setono_sylius_review.form.review.notify_reviewer',
                'required' => false,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_review_admin_store_review';
    }
}
