<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\EventSubscriber\Doctrine;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ObjectManager;
use Setono\SyliusReviewPlugin\Mailer\StoreReplyNotificationEmailManagerInterface;
use Setono\SyliusReviewPlugin\Model\ReviewInterface;

final class StoreReplyNotificationSubscriber
{
    /** @var \SplObjectStorage<ReviewInterface, true> */
    private \SplObjectStorage $flagged;

    public function __construct(private readonly StoreReplyNotificationEmailManagerInterface $emailManager)
    {
        $this->flagged = new \SplObjectStorage();
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof ReviewInterface) {
            return;
        }

        if (!$entity->getNotifyReviewer()) {
            return;
        }

        if (!$args->hasChangedField('storeReply')) {
            return;
        }

        $entity->setNotifyReviewer(false);

        $this->flagged->attach($entity);
    }

    /** @param LifecycleEventArgs<ObjectManager> $args */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof ReviewInterface) {
            return;
        }

        if (!$this->flagged->contains($entity)) {
            return;
        }

        $this->flagged->detach($entity);

        $this->emailManager->sendNotification($entity);
    }
}
