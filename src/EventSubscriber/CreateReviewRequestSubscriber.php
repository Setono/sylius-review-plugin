<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\EventSubscriber;

use Doctrine\Persistence\ManagerRegistry;
use Setono\Doctrine\ORMTrait;
use Setono\SyliusReviewPlugin\Factory\ReviewRequestFactoryInterface;
use Setono\SyliusReviewPlugin\Repository\ReviewRequestRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webmozart\Assert\Assert;

final class CreateReviewRequestSubscriber implements EventSubscriberInterface
{
    use ORMTrait;

    public function __construct(
        ManagerRegistry $managerRegistry,
        private readonly ReviewRequestFactoryInterface $reviewRequestFactory,
    ) {
        $this->managerRegistry = $managerRegistry;
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

        $reviewRequestRepository = $this->getRepository($reviewRequest, ReviewRequestRepositoryInterface::class);
        if ($reviewRequestRepository->hasExistingForOrder($order)) {
            return;
        }

        $this->getManager($reviewRequest)->persist($reviewRequest);
    }
}
