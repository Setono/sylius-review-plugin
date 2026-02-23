<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Model;

use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Webmozart\Assert\Assert;

class StoreReview implements StoreReviewInterface
{
    use StoreReplyTrait;
    use TimestampableTrait;

    protected ?int $id = null;

    protected ?int $rating = null;

    protected ?string $title = null;

    protected ?string $comment = null;

    protected ?string $status = self::STATUS_NEW;

    protected ?OrderInterface $order = null;

    protected ?ReviewerInterface $author = null;

    protected ?ChannelInterface $reviewSubject = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): void
    {
        $this->rating = $rating;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    public function getAuthor(): ?ReviewerInterface
    {
        return $this->author;
    }

    public function setAuthor(?ReviewerInterface $author): void
    {
        $this->author = $author;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function getReviewSubject(): ?ChannelInterface
    {
        return $this->reviewSubject;
    }

    public function setReviewSubject(?ReviewableInterface $reviewSubject): void
    {
        Assert::nullOrIsInstanceOf($reviewSubject, ChannelInterface::class);

        $this->reviewSubject = $reviewSubject;
    }

    public function getOrder(): ?OrderInterface
    {
        return $this->order;
    }

    public function setOrder(?OrderInterface $order): void
    {
        $this->order = $order;
    }

    public function getChannel(): ?BaseChannelInterface
    {
        return $this->reviewSubject;
    }

    public function setChannel(?BaseChannelInterface $channel): void
    {
        Assert::nullOrIsInstanceOf($channel, ChannelInterface::class);

        $this->reviewSubject = $channel;
    }
}
