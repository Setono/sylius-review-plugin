<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Functional\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class ProcessCommandTest extends KernelTestCase
{
    /** @test */
    public function it_executes_successfully(): void
    {
        $application = new Application(self::bootKernel());

        $command = $application->find('setono:sylius-review:process');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();
    }
}
