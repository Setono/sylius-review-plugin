<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Command;

use Setono\SyliusReviewPlugin\Repository\ReviewRequestRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'setono:sylius-review:prune',
    description: 'Prunes review requests that are old or cancelled. You can define how old review requests should be before pruning by setting the parameter setono_sylius_review.pruning.threshold',
)]
final class PruneCommand extends Command
{
    public function __construct(
        private readonly ReviewRequestRepositoryInterface $reviewRequestRepository,
        private readonly string $threshold,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->reviewRequestRepository->removeCancelled();
        $this->reviewRequestRepository->removeBefore(new \DateTimeImmutable($this->threshold));

        return 0;
    }
}
