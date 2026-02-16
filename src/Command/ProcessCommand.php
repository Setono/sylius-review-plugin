<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Command;

use Psr\Log\LoggerAwareInterface;
use Setono\SyliusReviewPlugin\Creator\ReviewRequestCreatorInterface;
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
    public function __construct(
        private readonly ReviewRequestCreatorInterface $reviewRequestCreator,
        private readonly ReviewRequestProcessorInterface $reviewRequestProcessor,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $consoleLogger = $output->isVerbose() ? new ConsoleLogger($output) : null;

        if ($this->reviewRequestCreator instanceof LoggerAwareInterface && null !== $consoleLogger) {
            $this->reviewRequestCreator->setLogger($consoleLogger);
        }

        $this->reviewRequestCreator->create();

        if ($this->reviewRequestProcessor instanceof LoggerAwareInterface && null !== $consoleLogger) {
            $this->reviewRequestProcessor->setLogger($consoleLogger);
        }

        $this->reviewRequestProcessor->process();

        return 0;
    }
}
