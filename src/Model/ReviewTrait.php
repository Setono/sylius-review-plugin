<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Model;

use Doctrine\ORM\Mapping as ORM;

trait ReviewTrait
{
    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $displayName = null;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $storeReply = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?\DateTimeInterface $storeRepliedAt = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    protected bool $notifyReviewer = true;

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): void
    {
        $this->displayName = $displayName;
    }

    public function getStoreReply(): ?string
    {
        return $this->storeReply;
    }

    public function setStoreReply(?string $storeReply): void
    {
        $this->storeReply = $storeReply;
        $this->storeRepliedAt = null !== $storeReply ? new \DateTime() : null;
    }

    public function getStoreRepliedAt(): ?\DateTimeInterface
    {
        return $this->storeRepliedAt;
    }

    public function setStoreRepliedAt(?\DateTimeInterface $storeRepliedAt): void
    {
        $this->storeRepliedAt = $storeRepliedAt;
    }

    public function getNotifyReviewer(): bool
    {
        return $this->notifyReviewer;
    }

    public function setNotifyReviewer(bool $notifyReviewer): void
    {
        $this->notifyReviewer = $notifyReviewer;
    }
}
