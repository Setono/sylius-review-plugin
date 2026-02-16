<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Creator;

interface ReviewRequestCreatorInterface
{
    public function create(): void;
}
