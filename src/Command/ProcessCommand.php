<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Command;

use Psr\Log\LoggerAwareInterface;
use Setono\SyliusReviewPlugin\Processor\ReviewRequestProcessorInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'setono:sylius-review:process',
    description: 'Process review requests',
)]
final class ProcessCommand extends Command
{
    public function __construct(private readonly ReviewRequestProcessorInterface $reviewRequestProcessor)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->reviewRequestProcessor instanceof LoggerAwareInterface && $output->isVerbose()) {
            $this->reviewRequestProcessor->setLogger(new ConsoleLogger($output));
        }

        $this->reviewRequestProcessor->process();

        return 0;
    }
}
