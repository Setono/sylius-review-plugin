<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Form\Extension;

use Sylius\Bundle\CoreBundle\Form\Type\Product\ProductReviewType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

final class ReviewTypeStoreReplyExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('storeReply', TextareaType::class, [
            'label' => 'setono_sylius_review.form.review.store_reply',
            'required' => false,
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [ProductReviewType::class];
    }
}
