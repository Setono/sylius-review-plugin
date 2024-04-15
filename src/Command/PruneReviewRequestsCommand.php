<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Command;

use Setono\SyliusReviewPlugin\Repository\ReviewRequestRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class PruneReviewRequestsCommand extends Command
{
    protected static $defaultName = 'setono:sylius-review:prune';

    protected static $defaultDescription = 'Prunes review requests';

    public function __construct(private readonly ReviewRequestRepositoryInterface $reviewRequestRepository, private readonly string $threshold = '-1 month')
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->reviewRequestRepository->removeBefore(new \DateTimeImmutable($this->threshold));

        return 0;
    }
}
