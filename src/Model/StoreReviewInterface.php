<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Model;

use Sylius\Component\Channel\Model\ChannelAwareInterface;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface StoreReviewInterface extends ReviewInterface, ChannelAwareInterface, StoreReplyInterface
{
    public function getOrder(): ?OrderInterface;

    public function setOrder(?OrderInterface $order): void;

    public function getReviewSubject(): ?ChannelInterface;

    public function getChannel(): ?BaseChannelInterface;

    public function setChannel(?BaseChannelInterface $channel): void;
}
