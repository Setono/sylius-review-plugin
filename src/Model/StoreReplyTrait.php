<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Model;

use Doctrine\ORM\Mapping as ORM;

trait StoreReplyTrait
{
    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $storeReply = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?\DateTimeInterface $storeRepliedAt = null;

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
}
