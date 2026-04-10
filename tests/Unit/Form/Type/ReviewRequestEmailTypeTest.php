<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Form\Type;

use PHPUnit\Framework\TestCase;
use Setono\SyliusReviewPlugin\Form\Type\ReviewRequestEmailType;
use Setono\SyliusReviewPlugin\Mailer\Emails;
use Setono\SyliusReviewPlugin\Model\ReviewRequest;

final class ReviewRequestEmailTypeTest extends TestCase
{
    private ReviewRequestEmailType $type;

    protected function setUp(): void
    {
        $this->type = new ReviewRequestEmailType(ReviewRequest::class);
    }

    /** @test */
    public function it_supports_the_review_request_email_key(): void
    {
        self::assertTrue($this->type->support(Emails::REVIEW_REQUEST));
    }

    /** @test */
    public function it_does_not_support_other_email_keys(): void
    {
        self::assertFalse($this->type->support('some_other_email'));
    }

    /** @test */
    public function it_returns_the_review_request_email_code(): void
    {
        self::assertSame(Emails::REVIEW_REQUEST, $this->type->getCode());
    }

    /** @test */
    public function it_returns_itself_as_form_type(): void
    {
        self::assertSame($this->type, $this->type->getFormType(Emails::REVIEW_REQUEST));
    }
}
