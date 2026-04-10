<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Form\Type;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\Form\Type\StoreReplyNotificationEmailType;
use Setono\SyliusReviewPlugin\Mailer\Emails;
use Setono\SyliusReviewPlugin\Model\ChannelInterface;
use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

final class StoreReplyNotificationEmailTypeTest extends TestCase
{
    use ProphecyTrait;

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

    /** @test */
    public function it_registers_a_submit_listener(): void
    {
        $builder = $this->prophesize(\Symfony\Component\Form\FormBuilderInterface::class);
        $builder->add('storeReview', \Synolia\SyliusMailTesterPlugin\Form\Type\LimitedEntityType::class, \Prophecy\Argument::type('array'))
            ->willReturn($builder->reveal());
        $builder->addEventListener(FormEvents::SUBMIT, \Prophecy\Argument::type('callable'))
            ->shouldBeCalledOnce()
            ->willReturn($builder->reveal());

        $this->type->buildForm($builder->reveal(), []);
    }

    /** @test */
    public function it_enriches_submitted_data_with_review_context(): void
    {
        $channel = $this->prophesize(ChannelInterface::class);
        $channel->getName()->willReturn('My Store');

        $review = $this->prophesize(StoreReviewInterface::class);
        $review->getReviewSubject()->willReturn($channel->reveal());

        $data = ['storeReview' => $review->reveal()];

        $form = $this->prophesize(FormInterface::class);

        $event = new FormEvent($form->reveal(), $data);

        $listener = $this->extractSubmitListener();
        $listener($event);

        /** @var array<string, mixed> $result */
        $result = $event->getData();

        self::assertSame($review->reveal(), $result['review']);
        self::assertTrue($result['isStoreReview']);
        self::assertFalse($result['isProductReview']);
        self::assertSame($channel->reveal(), $result['reviewSubject']);
        self::assertSame('My Store', $result['reviewSubjectName']);
    }

    /** @test */
    public function it_does_nothing_when_store_review_is_missing_from_data(): void
    {
        $data = ['storeReview' => 'not a store review'];

        $form = $this->prophesize(FormInterface::class);

        $event = new FormEvent($form->reveal(), $data);

        $listener = $this->extractSubmitListener();
        $listener($event);

        self::assertSame($data, $event->getData());
    }

    private function extractSubmitListener(): callable
    {
        /** @var callable|null $captured */
        $captured = null;

        $builder = $this->prophesize(\Symfony\Component\Form\FormBuilderInterface::class);
        $builder->add(\Prophecy\Argument::cetera())->willReturn($builder->reveal());
        $builder->addEventListener(FormEvents::SUBMIT, \Prophecy\Argument::type('callable'))
            ->will(function (array $args) use (&$captured, $builder) {
                $captured = $args[1];

                return $builder->reveal();
            });

        $this->type->buildForm($builder->reveal(), []);

        self::assertNotNull($captured, 'Expected a SUBMIT listener to be registered');
        assert(is_callable($captured));

        return $captured;
    }
}
