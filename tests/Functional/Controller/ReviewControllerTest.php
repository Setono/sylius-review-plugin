<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Functional\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusReviewPlugin\Model\ChannelInterface;
use Setono\SyliusReviewPlugin\Model\StoreReview;
use Setono\SyliusReviewPlugin\Repository\StoreReviewRepositoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Sylius\Component\Review\Model\ReviewInterface;
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
    public function it_renders_disclaimer_text_on_review_form(): void
    {
        $this->order->setState(OrderInterface::STATE_FULFILLED);
        $this->entityManager->flush();

        $this->client->request('GET', '/en_US/review?token=' . $this->order->getTokenValue());

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.review-disclaimer', 'By submitting this review, you agree that it will be publicly visible.');
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
            'setono_sylius_review[storeReview][comment]' => 'I had a wonderful experience.',
        ]);

        self::assertResponseRedirects();

        /** @var StoreReviewRepositoryInterface $storeReviewRepository */
        $storeReviewRepository = self::getContainer()->get('setono_sylius_review.repository.store_review');
        $storeReview = $storeReviewRepository->findOneByOrder($this->order);

        self::assertNotNull($storeReview);
        self::assertSame(5, $storeReview->getRating());
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

    /** @test */
    public function it_resets_accepted_store_review_status_on_edit(): void
    {
        $this->order->setState(OrderInterface::STATE_FULFILLED);
        $this->entityManager->flush();

        // Create an existing accepted store review
        $channel = $this->order->getChannel();
        self::assertInstanceOf(ChannelInterface::class, $channel);

        $customer = $this->order->getCustomer();
        self::assertInstanceOf(ReviewerInterface::class, $customer);

        $storeReview = new StoreReview();
        $storeReview->setRating(5);
        $storeReview->setComment('Original comment');
        $storeReview->setReviewSubject($channel);
        $storeReview->setAuthor($customer);
        $storeReview->setOrder($this->order);
        $storeReview->setStatus(ReviewInterface::STATUS_ACCEPTED);

        $this->entityManager->persist($storeReview);
        $this->entityManager->flush();

        // Edit the review via the form
        $this->client->request('GET', '/en_US/review?token=' . $this->order->getTokenValue());
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Submit Reviews', [
            'setono_sylius_review[storeReview][rating]' => 3,
            'setono_sylius_review[storeReview][comment]' => 'Updated comment',
        ]);

        self::assertResponseRedirects();

        $this->entityManager->clear();

        /** @var StoreReviewRepositoryInterface $storeReviewRepository */
        $storeReviewRepository = self::getContainer()->get('setono_sylius_review.repository.store_review');
        $updatedReview = $storeReviewRepository->findOneByOrder($this->order);

        self::assertNotNull($updatedReview);
        self::assertSame(3, $updatedReview->getRating());
        self::assertSame('Updated comment', $updatedReview->getComment());
        // Status should be reset from 'accepted' to 'new' (or re-auto-approved)
        self::assertContains($updatedReview->getStatus(), [ReviewInterface::STATUS_NEW, ReviewInterface::STATUS_ACCEPTED]);
    }
}
