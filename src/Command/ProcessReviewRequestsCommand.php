<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Command;

use Setono\SyliusReviewPlugin\Processor\ReviewRequestProcessorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ProcessReviewRequestsCommand extends Command
{
    protected static $defaultName = 'setono:sylius-review:process';

    protected static $defaultDescription = 'Process review requests';

    public function __construct(private readonly ReviewRequestProcessorInterface $reviewRequestProcessor)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->reviewRequestProcessor->process();

        return 0;
    }
}
