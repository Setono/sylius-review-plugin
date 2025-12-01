<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Model;

use Sylius\Component\Core\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Review\Model\ReviewableInterface;

interface ChannelInterface extends BaseChannelInterface, ReviewableInterface
{
}
