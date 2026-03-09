<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\EventListener\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\EventListener\Doctrine\StoreReplyNotificationListener;
use Setono\SyliusReviewPlugin\Mailer\StoreReplyNotificationEmailManagerInterface;
use Setono\SyliusReviewPlugin\Model\ReviewInterface;

final class StoreReplyNotificationListenerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_sends_notification_when_store_reply_changed_and_notify_is_true(): void
    {
        $review = $this->prophesize(ReviewInterface::class);
        $review->getNotifyReviewer()->willReturn(true);
        $review->setNotifyReviewer(false)->shouldBeCalledOnce();

        $emailManager = $this->prophesize(StoreReplyNotificationEmailManagerInterface::class);
        $emailManager->sendNotification($review->reveal())->shouldBeCalledOnce();

        $em = $this->prophesize(EntityManagerInterface::class);

        $changeSet = ['storeReply' => [null, 'Thank you!']];
        $preUpdateArgs = new PreUpdateEventArgs($review->reveal(), $em->reveal(), $changeSet);

        $listener = new StoreReplyNotificationListener($emailManager->reveal());
        $listener->preUpdate($preUpdateArgs);

        $postUpdateArgs = new LifecycleEventArgs($review->reveal(), $em->reveal());
        $listener->postUpdate($postUpdateArgs);
    }

    /**
     * @test
     */
    public function it_does_not_send_notification_when_notify_is_false(): void
    {
        $review = $this->prophesize(ReviewInterface::class);
        $review->getNotifyReviewer()->willReturn(false);

        $emailManager = $this->prophesize(StoreReplyNotificationEmailManagerInterface::class);
        $emailManager->sendNotification($review->reveal())->shouldNotBeCalled();

        $em = $this->prophesize(EntityManagerInterface::class);

        $changeSet = ['storeReply' => [null, 'Thank you!']];
        $preUpdateArgs = new PreUpdateEventArgs($review->reveal(), $em->reveal(), $changeSet);

        $listener = new StoreReplyNotificationListener($emailManager->reveal());
        $listener->preUpdate($preUpdateArgs);

        $postUpdateArgs = new LifecycleEventArgs($review->reveal(), $em->reveal());
        $listener->postUpdate($postUpdateArgs);
    }

    /**
     * @test
     */
    public function it_does_not_send_notification_when_store_reply_did_not_change(): void
    {
        $review = $this->prophesize(ReviewInterface::class);
        $review->getNotifyReviewer()->willReturn(true);

        $emailManager = $this->prophesize(StoreReplyNotificationEmailManagerInterface::class);
        $emailManager->sendNotification($review->reveal())->shouldNotBeCalled();

        $em = $this->prophesize(EntityManagerInterface::class);

        $changeSet = ['title' => ['Old title', 'New title']];
        $preUpdateArgs = new PreUpdateEventArgs($review->reveal(), $em->reveal(), $changeSet);

        $listener = new StoreReplyNotificationListener($emailManager->reveal());
        $listener->preUpdate($preUpdateArgs);

        $postUpdateArgs = new LifecycleEventArgs($review->reveal(), $em->reveal());
        $listener->postUpdate($postUpdateArgs);
    }

    /**
     * @test
     */
    public function it_ignores_non_review_entities(): void
    {
        $entity = new \stdClass();

        $emailManager = $this->prophesize(StoreReplyNotificationEmailManagerInterface::class);

        $em = $this->prophesize(EntityManagerInterface::class);

        $changeSet = ['storeReply' => [null, 'Reply']];
        $preUpdateArgs = new PreUpdateEventArgs($entity, $em->reveal(), $changeSet);

        $listener = new StoreReplyNotificationListener($emailManager->reveal());
        $listener->preUpdate($preUpdateArgs);

        $postUpdateArgs = new LifecycleEventArgs($entity, $em->reveal());
        $listener->postUpdate($postUpdateArgs);

        $emailManager->sendNotification($entity)->shouldNotHaveBeenCalled();
    }
}
