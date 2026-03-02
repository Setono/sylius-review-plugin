<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Functional\Controller;

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
        /** @var OrderInterface|null $order */
        $order = $this->entityManager->getRepository(OrderInterface::class)->findOneBy(['state' => OrderInterface::STATE_NEW]);
        self::assertNotNull($order, 'No fixture order in "new" state found.');

        $this->client->request('GET', '/en_US/review?token=' . $order->getTokenValue());

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

    /** @test */
    public function it_submits_review_with_display_name(): void
    {
        $this->order->setState(OrderInterface::STATE_FULFILLED);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/en_US/review?token=' . $this->order->getTokenValue());
        self::assertResponseIsSuccessful();

        // Check that the display name field exists
        $displayNameSelect = $crawler->filter('select[name="setono_sylius_review[displayName]"]');
        if (0 === $displayNameSelect->count()) {
            self::markTestSkipped('Display name field not rendered (customer may have no name in fixtures).');
        }

        // Get the first option value
        $firstOption = $displayNameSelect->filter('option')->first();
        $displayNameValue = $firstOption->attr('value');
        self::assertNotNull($displayNameValue);

        $this->client->submitForm('Submit Reviews', [
            'setono_sylius_review[displayName]' => $displayNameValue,
            'setono_sylius_review[storeReview][rating]' => 4,
            'setono_sylius_review[storeReview][title]' => 'Good store',
            'setono_sylius_review[storeReview][comment]' => 'Nice experience with display name.',
        ]);

        self::assertResponseRedirects();

        /** @var StoreReviewRepositoryInterface $storeReviewRepository */
        $storeReviewRepository = self::getContainer()->get('setono_sylius_review.repository.store_review');
        $storeReview = $storeReviewRepository->findOneByOrder($this->order);

        self::assertNotNull($storeReview);
        self::assertSame(4, $storeReview->getRating());
        self::assertSame($displayNameValue, $storeReview->getDisplayName());
    }
}
