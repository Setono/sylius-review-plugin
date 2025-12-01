<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Checker\AutoApproval;

use Sylius\Component\Core\Model\ProductReviewInterface;

/**
 * @extends AutoApprovalCheckerInterface<ProductReviewInterface>
 */
interface ProductAutoApprovalCheckerInterface extends AutoApprovalCheckerInterface
{
}
