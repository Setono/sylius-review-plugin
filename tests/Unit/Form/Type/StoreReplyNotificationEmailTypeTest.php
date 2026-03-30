<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Form\Type;

use PHPUnit\Framework\TestCase;
use Setono\SyliusReviewPlugin\Form\Type\StoreReplyNotificationEmailType;
use Setono\SyliusReviewPlugin\Mailer\Emails;

final class StoreReplyNotificationEmailTypeTest extends TestCase
{
    private StoreReplyNotificationEmailType $type;

    protected function setUp(): void
    {
        parent::setUp();

        $this->type = new StoreReplyNotificationEmailType('App\Entity\StoreReview');
    }

    /** @test */
    public function it_supports_the_store_reply_notification_email_key(): void
    {
        self::assertTrue($this->type->support(Emails::STORE_REPLY_NOTIFICATION));
    }

    /** @test */
    public function it_does_not_support_other_email_keys(): void
    {
        self::assertFalse($this->type->support(Emails::REVIEW_REQUEST));
        self::assertFalse($this->type->support('some_other_email'));
    }

    /** @test */
    public function it_returns_the_correct_code(): void
    {
        self::assertSame(Emails::STORE_REPLY_NOTIFICATION, $this->type->getCode());
    }

    /** @test */
    public function it_returns_itself_as_form_type(): void
    {
        self::assertSame($this->type, $this->type->getFormType(Emails::STORE_REPLY_NOTIFICATION));
    }
}
