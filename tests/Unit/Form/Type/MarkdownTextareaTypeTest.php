<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Form\Type;

use Setono\SyliusReviewPlugin\Form\Type\MarkdownTextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Test\TypeTestCase;

final class MarkdownTextareaTypeTest extends TypeTestCase
{
    /** @test */
    public function it_has_the_correct_block_prefix(): void
    {
        $form = $this->factory->create(MarkdownTextareaType::class);

        self::assertSame('setono_sylius_review_markdown_textarea', $form->getConfig()->getType()->getBlockPrefix());
    }

    /** @test */
    public function it_extends_textarea_type(): void
    {
        $form = $this->factory->create(MarkdownTextareaType::class);

        self::assertInstanceOf(TextareaType::class, $form->getConfig()->getType()->getInnerType());
    }

    /** @test */
    public function it_submits_valid_data(): void
    {
        $form = $this->factory->create(MarkdownTextareaType::class);
        $form->submit('**bold** and _italic_');

        self::assertTrue($form->isSynchronized());
        self::assertSame('**bold** and _italic_', $form->getData());
    }
}
