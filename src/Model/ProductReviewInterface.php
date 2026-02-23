<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Model;

use Sylius\Component\Core\Model\ProductReviewInterface as BaseProductReviewInterface;

interface ProductReviewInterface extends BaseProductReviewInterface, StoreReplyInterface
{
}
