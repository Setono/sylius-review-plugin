<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\EventSubscriber;

use Doctrine\Persistence\ManagerRegistry;
use Setono\SyliusReviewPlugin\Factory\ReviewRequestFactoryInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webmozart\Assert\Assert;

final class CreateReviewRequestSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly ReviewRequestFactoryInterface $reviewRequestFactory,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.order.pre_complete' => 'create',
        ];
    }

    public function create(ResourceControllerEvent $event): void
    {
        /** @var OrderInterface|mixed $order */
        $order = $event->getSubject();
        Assert::isInstanceOf($order, OrderInterface::class);

        $reviewRequest = $this->reviewRequestFactory->createFromOrder($order);

        $this->managerRegistry->getManagerForClass($reviewRequest::class)?->persist($reviewRequest);
    }
}
