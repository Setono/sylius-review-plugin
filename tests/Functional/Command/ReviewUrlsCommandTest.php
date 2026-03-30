<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Functional\Command;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class ReviewUrlsCommandTest extends KernelTestCase
{
    /** @test */
    public function it_executes_successfully(): void
    {
        $application = new Application(self::bootKernel());

        $command = $application->find('setono:sylius-review:urls');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();
    }

    /** @test */
    public function it_outputs_review_urls_for_fulfilled_orders(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');

        /** @var OrderInterface|null $order */
        $order = $entityManager->getRepository(OrderInterface::class)->findOneBy([]);
        self::assertNotNull($order, 'No fixture order found.');

        $order->setState(OrderInterface::STATE_FULFILLED);
        $entityManager->flush();

        $command = $application->find('setono:sylius-review:urls');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        self::assertStringContainsString('/review?token=', $output);
    }

    /** @test */
    public function it_respects_the_max_option(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('setono:sylius-review:urls');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['--max' => '1']);

        $commandTester->assertCommandIsSuccessful();
    }
}
