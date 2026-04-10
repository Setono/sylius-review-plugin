<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Command;

use Doctrine\Persistence\ManagerRegistry;
use Setono\Doctrine\ORMTrait;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

#[AsCommand(
    name: 'setono:sylius-review:urls',
    description: 'Output review page URLs for fulfilled orders, grouped by channel',
)]
final class ReviewUrlsCommand extends Command
{
    use ORMTrait;

    /**
     * @param ChannelRepositoryInterface<ChannelInterface> $channelRepository
     * @param class-string<OrderInterface> $orderClass
     */
    public function __construct(
        private readonly ChannelRepositoryInterface $channelRepository,
        ManagerRegistry $managerRegistry,
        private readonly string $orderClass,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
        $this->managerRegistry = $managerRegistry;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('max', null, InputOption::VALUE_REQUIRED, 'Maximum number of URLs per channel', '5');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $max = (int) $input->getOption('max');

        /** @var list<ChannelInterface> $channels */
        $channels = $this->channelRepository->findEnabled();

        foreach ($channels as $channel) {
            $channelName = $channel->getName() ?? $channel->getCode() ?? 'Unknown';
            $io->section($channelName);

            $hostname = $channel->getHostname();
            Assert::notNull($hostname, sprintf('Channel "%s" has no hostname configured.', $channelName));

            $locale = $channel->getDefaultLocale()?->getCode() ?? 'en_US';

            $manager = $this->getManager($this->orderClass);

            /** @var list<OrderInterface> $orders */
            $orders = $manager->createQueryBuilder()
                ->select('o')
                ->from($this->orderClass, 'o')
                ->where('o.channel = :channel')
                ->andWhere('o.state = :state')
                ->andWhere('o.tokenValue IS NOT NULL')
                ->setParameter('channel', $channel)
                ->setParameter('state', OrderInterface::STATE_FULFILLED)
                ->setMaxResults($max)
                ->getQuery()
                ->getResult()
            ;

            if ([] === $orders) {
                $io->text('No fulfilled orders found.');

                continue;
            }

            foreach ($orders as $order) {
                $token = $order->getTokenValue();
                Assert::notNull($token);

                $path = $this->urlGenerator->generate('setono_sylius_review__review', ['token' => $token, '_locale' => $locale]);

                $io->text(sprintf('https://%s%s', $hostname, $path));
            }
        }

        return 0;
    }
}
