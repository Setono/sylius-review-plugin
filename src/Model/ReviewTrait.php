<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Model;

use Doctrine\ORM\Mapping as ORM;

trait ReviewTrait
{
    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $displayName = null;

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): void
    {
        $this->displayName = $displayName;
    }
}
