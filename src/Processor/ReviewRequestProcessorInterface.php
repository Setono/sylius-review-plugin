<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Processor;

interface ReviewRequestProcessorInterface
{
    public function process(): void;
}
