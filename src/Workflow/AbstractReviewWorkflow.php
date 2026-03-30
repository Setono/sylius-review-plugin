<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Workflow;

abstract class AbstractReviewWorkflow
{
    final public const TRANSITION_ACCEPT = 'accept';

    final public const TRANSITION_REJECT = 'reject';

    final public const TRANSITION_REQUEST_EDIT = 'request_edit';

    private function __construct()
    {
    }
}
