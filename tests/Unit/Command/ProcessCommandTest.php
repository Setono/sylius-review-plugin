<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Command;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Setono\SyliusReviewPlugin\Command\ProcessCommand;
use Setono\SyliusReviewPlugin\Creator\ReviewRequestCreatorInterface;
use Setono\SyliusReviewPlugin\Processor\ReviewRequestProcessorInterface;
use Symfony\Component\Console\Tester\CommandTester;

final class ProcessCommandTest extends TestCase
{
    use ProphecyTrait;

    /** @test */
    public function it_sets_logger_on_creator_and_processor_when_verbose(): void
    {
        $creator = $this->prophesize(LoggerAwareCreatorInterface::class);
        $creator->create()->shouldBeCalledOnce();
        $creator->setLogger(\Prophecy\Argument::type(LoggerInterface::class))->shouldBeCalledOnce();

        $processor = $this->prophesize(LoggerAwareProcessorInterface::class);
        $processor->process()->shouldBeCalledOnce();
        $processor->setLogger(\Prophecy\Argument::type(LoggerInterface::class))->shouldBeCalledOnce();

        $command = new ProcessCommand($creator->reveal(), $processor->reveal());

        $tester = new CommandTester($command);
        $tester->execute([], ['verbosity' => \Symfony\Component\Console\Output\OutputInterface::VERBOSITY_VERBOSE]);

        $tester->assertCommandIsSuccessful();
    }

    /** @test */
    public function it_does_not_set_logger_when_not_verbose(): void
    {
        $creator = $this->prophesize(LoggerAwareCreatorInterface::class);
        $creator->create()->shouldBeCalledOnce();
        $creator->setLogger(\Prophecy\Argument::any())->shouldNotBeCalled();

        $processor = $this->prophesize(LoggerAwareProcessorInterface::class);
        $processor->process()->shouldBeCalledOnce();
        $processor->setLogger(\Prophecy\Argument::any())->shouldNotBeCalled();

        $command = new ProcessCommand($creator->reveal(), $processor->reveal());

        $tester = new CommandTester($command);
        $tester->execute([]);

        $tester->assertCommandIsSuccessful();
    }

    /** @test */
    public function it_does_not_set_logger_when_services_are_not_logger_aware(): void
    {
        $creator = $this->prophesize(ReviewRequestCreatorInterface::class);
        $creator->create()->shouldBeCalledOnce();

        $processor = $this->prophesize(ReviewRequestProcessorInterface::class);
        $processor->process()->shouldBeCalledOnce();

        $command = new ProcessCommand($creator->reveal(), $processor->reveal());

        $tester = new CommandTester($command);
        $tester->execute([], ['verbosity' => \Symfony\Component\Console\Output\OutputInterface::VERBOSITY_VERBOSE]);

        $tester->assertCommandIsSuccessful();
    }
}

interface LoggerAwareCreatorInterface extends ReviewRequestCreatorInterface, LoggerAwareInterface
{
}

interface LoggerAwareProcessorInterface extends ReviewRequestProcessorInterface, LoggerAwareInterface
{
}
