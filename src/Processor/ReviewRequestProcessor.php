<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Processor;

use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;
use DoctrineBatchUtils\BatchProcessing\SimpleBatchIteratorAggregate;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Setono\SyliusReviewPlugin\EligibilityChecker\ReviewRequestEligibilityCheckerInterface;
use Setono\SyliusReviewPlugin\Event\ReviewRequestProcessingStarted;
use Setono\SyliusReviewPlugin\Mailer\ReviewRequestEmailManagerInterface;
use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;
use Setono\SyliusReviewPlugin\Repository\ReviewRequestRepositoryInterface;
use Setono\SyliusReviewPlugin\Workflow\ReviewRequestWorkflow;
use Symfony\Component\Workflow\WorkflowInterface;

final class ReviewRequestProcessor implements ReviewRequestProcessorInterface, LoggerAwareInterface
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly ReviewRequestRepositoryInterface $reviewRequestRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly WorkflowInterface $reviewRequestWorkflow,
        private readonly ReviewRequestEligibilityCheckerInterface $reviewRequestEligibilityChecker,
        private readonly ReviewRequestEmailManagerInterface $reviewRequestEmailManager,
    ) {
        $this->logger = new NullLogger();
    }

    public function process(): void
    {
        $qb = $this->reviewRequestRepository->createForProcessingQueryBuilder();

        /** @var SimpleBatchIteratorAggregate<array-key, ReviewRequestInterface> $reviewRequests */
        $reviewRequests = SimpleBatchIteratorAggregate::fromQuery(
            $qb->getQuery(),
            100,
        );

        $this->logQuery($qb);

        $i = 0;
        foreach ($reviewRequests as $reviewRequest) {
            if (!$this->reviewRequestWorkflow->can($reviewRequest, ReviewRequestWorkflow::TRANSITION_COMPLETE)) {
                continue;
            }

            $this->eventDispatcher->dispatch(new ReviewRequestProcessingStarted($reviewRequest));

            $check = $this->reviewRequestEligibilityChecker->check($reviewRequest);
            if (!$check->eligible) {
                $this->logger->debug(sprintf(
                    'Review request (id: %d) is not eligible. Reason: %s',
                    (int) $reviewRequest->getId(),
                    (string) $check->reason,
                ));

                $reviewRequest->setIneligibilityReason($check->reason);

                continue;
            }

            try {
                $this->reviewRequestEmailManager->sendReviewRequest($reviewRequest);
            } catch (\Throwable $e) {
                $this->logger->error(sprintf(
                    'There was an error trying to send a review request (id: %d). The error was: %s',
                    (int) $reviewRequest->getId(),
                    $e->getMessage(),
                ));

                continue;
            }

            $this->reviewRequestWorkflow->apply($reviewRequest, ReviewRequestWorkflow::TRANSITION_COMPLETE);
            ++$i;
        }

        $this->logger->debug(sprintf('%d review request%s were completed', $i, 1 === $i ? '' : 's'));
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    private function logQuery(QueryBuilder $qb): void
    {
        $this->logger->debug("DQL\n" . $qb->getDQL() . "\n");

        $parameters = $qb
            ->getParameters()
            ->map(static fn (Parameter $parameter) => sprintf(
                '%s: %s',
                $parameter->getName(),
                self::formatParameter($parameter->getValue()),
            ))
            ->toArray()
        ;

        $this->logger->debug("Parameters\n" . implode("\n", $parameters) . "\n");
    }

    private static function formatParameter(mixed $parameter): string
    {
        if (is_array($parameter)) {
            try {
                return json_encode($parameter, \JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                return 'Array';
            }
        }

        if (is_object($parameter)) {
            if ($parameter instanceof \DateTimeInterface) {
                return $parameter->format(\DateTime::ATOM);
            }

            if ($parameter instanceof \Stringable) {
                return (string) $parameter;
            }

            return get_class($parameter);
        }

        return (string) $parameter;
    }
}
