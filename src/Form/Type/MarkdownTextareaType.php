<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class MarkdownTextareaType extends TextareaType
{
    public function getBlockPrefix(): string
    {
        return 'setono_sylius_review_markdown_textarea';
    }
}
