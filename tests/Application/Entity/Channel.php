<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Setono\SyliusReviewPlugin\Model\ChannelInterface;
use Setono\SyliusReviewPlugin\Model\ChannelTrait;
use Sylius\Component\Core\Model\Channel as BaseChannel;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_channel')]
class Channel extends BaseChannel implements ChannelInterface
{
    use ChannelTrait;

    public function __construct()
    {
        parent::__construct();

        $this->reviews = new ArrayCollection();
    }
}
