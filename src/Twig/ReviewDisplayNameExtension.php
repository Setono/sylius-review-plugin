<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ReviewDisplayNameExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('setono_sylius_review_display_name', [ReviewDisplayNameRuntime::class, 'resolve']),
        ];
    }
}
