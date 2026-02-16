<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusReviewPlugin\Repository\StoreReviewRepositoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ReviewControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    private EntityManagerInterface $entityManager;

    private OrderInterface $order;

    protected function setUp(): void
    {
        $this->client = self::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->entityManager = $entityManager;

        /** @var OrderInterface|null $order */
        $order = $this->entityManager->getRepository(OrderInterface::class)->findOneBy([]);
        self::assertNotNull($order, 'No fixture order found. Make sure Sylius fixtures are loaded.');

        $this->order = $order;
    }

    /** @test */
    public function it_returns_404_when_token_is_missing(): void
    {
        $this->client->request('GET', '/en_US/review');

        self::assertResponseStatusCodeSame(404);
    }

    /** @test */
    public function it_returns_404_when_order_is_not_found(): void
    {
        $this->client->request('GET', '/en_US/review?token=nonexistent_token_value');

        self::assertResponseStatusCodeSame(404);
    }

    /** @test */
    public function it_shows_error_when_order_is_not_reviewable(): void
    {
        self::assertNotSame(OrderInterface::STATE_FULFILLED, $this->order->getState());

        $this->client->request('GET', '/en_US/review?token=' . $this->order->getTokenValue());

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('.ui.error.message');
        self::assertSelectorNotExists('form[name="setono_sylius_review"]');
    }

    /** @test */
    public function it_renders_review_form_for_fulfilled_order(): void
    {
        $this->order->setState(OrderInterface::STATE_FULFILLED);
        $this->entityManager->flush();

        $this->client->request('GET', '/en_US/review?token=' . $this->order->getTokenValue());

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form');
        self::assertSelectorExists('button[type="submit"]');
    }

    /** @test */
    public function it_submits_review_successfully(): void
    {
        $this->order->setState(OrderInterface::STATE_FULFILLED);
        $this->entityManager->flush();

        $this->client->request('GET', '/en_US/review?token=' . $this->order->getTokenValue());
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Submit Reviews', [
            'setono_sylius_review[storeReview][rating]' => 5,
            'setono_sylius_review[storeReview][title]' => 'Great store!',
            'setono_sylius_review[storeReview][comment]' => 'I had a wonderful experience.',
        ]);

        self::assertResponseRedirects();

        /** @var StoreReviewRepositoryInterface $storeReviewRepository */
        $storeReviewRepository = self::getContainer()->get('setono_sylius_review.repository.store_review');
        $storeReview = $storeReviewRepository->findOneByOrder($this->order);

        self::assertNotNull($storeReview);
        self::assertSame(5, $storeReview->getRating());
        self::assertSame('Great store!', $storeReview->getTitle());
        self::assertSame('I had a wonderful experience.', $storeReview->getComment());

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();
    }
}
