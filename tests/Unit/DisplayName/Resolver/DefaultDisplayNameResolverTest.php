<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\DisplayName\Resolver;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\DisplayName\Resolver\DefaultDisplayNameResolver;
use Setono\SyliusReviewPlugin\Model\ReviewInterface as PluginReviewInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class DefaultDisplayNameResolverTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_returns_display_name_when_set_on_plugin_review(): void
    {
        $translator = $this->prophesize(TranslatorInterface::class);

        $review = $this->prophesize(PluginReviewInterface::class);
        $review->getDisplayName()->willReturn('John D.');

        $resolver = new DefaultDisplayNameResolver($translator->reveal());

        self::assertSame('John D.', $resolver->resolve($review->reveal()));
    }

    /**
     * @test
     */
    public function it_falls_back_to_author_first_name_when_display_name_is_null(): void
    {
        $translator = $this->prophesize(TranslatorInterface::class);

        $author = $this->prophesize(ReviewerInterface::class);
        $author->getFirstName()->willReturn('Jane');

        $review = $this->prophesize(PluginReviewInterface::class);
        $review->getDisplayName()->willReturn(null);
        $review->getAuthor()->willReturn($author->reveal());

        $resolver = new DefaultDisplayNameResolver($translator->reveal());

        self::assertSame('Jane', $resolver->resolve($review->reveal()));
    }

    /**
     * @test
     */
    public function it_falls_back_to_author_first_name_for_base_review_interface(): void
    {
        $translator = $this->prophesize(TranslatorInterface::class);

        $author = $this->prophesize(ReviewerInterface::class);
        $author->getFirstName()->willReturn('Jane');

        $review = $this->prophesize(ReviewInterface::class);
        $review->getAuthor()->willReturn($author->reveal());

        $resolver = new DefaultDisplayNameResolver($translator->reveal());

        self::assertSame('Jane', $resolver->resolve($review->reveal()));
    }

    /**
     * @test
     */
    public function it_returns_anonymous_when_no_display_name_and_no_author(): void
    {
        $translator = $this->prophesize(TranslatorInterface::class);
        $translator->trans('setono_sylius_review.ui.anonymous')->willReturn('Anonymous');

        $review = $this->prophesize(ReviewInterface::class);
        $review->getAuthor()->willReturn(null);

        $resolver = new DefaultDisplayNameResolver($translator->reveal());

        self::assertSame('Anonymous', $resolver->resolve($review->reveal()));
    }

    /**
     * @test
     */
    public function it_returns_anonymous_when_display_name_is_empty_and_author_first_name_is_empty(): void
    {
        $translator = $this->prophesize(TranslatorInterface::class);
        $translator->trans('setono_sylius_review.ui.anonymous')->willReturn('Anonymous');

        $author = $this->prophesize(ReviewerInterface::class);
        $author->getFirstName()->willReturn('');

        $review = $this->prophesize(PluginReviewInterface::class);
        $review->getDisplayName()->willReturn('');
        $review->getAuthor()->willReturn($author->reveal());

        $resolver = new DefaultDisplayNameResolver($translator->reveal());

        self::assertSame('Anonymous', $resolver->resolve($review->reveal()));
    }
}
