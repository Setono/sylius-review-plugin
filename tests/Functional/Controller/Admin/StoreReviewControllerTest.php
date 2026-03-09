<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Functional\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusReviewPlugin\Model\ChannelInterface;
use Setono\SyliusReviewPlugin\Model\StoreReview;
use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Security\Core\User\UserInterface;

final class StoreReviewControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    private EntityManagerInterface $entityManager;

    private ChannelInterface $channel;

    private ReviewerInterface $customer;

    private OrderInterface $order;

    protected function setUp(): void
    {
        $this->client = self::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->entityManager = $entityManager;

        /** @var ChannelInterface|null $channel */
        $channel = $this->entityManager->getRepository(ChannelInterface::class)->findOneBy([]);
        self::assertNotNull($channel, 'No fixture channel found. Make sure Sylius fixtures are loaded.');
        $this->channel = $channel;

        /** @var OrderInterface|null $order */
        $order = $this->entityManager->getRepository(OrderInterface::class)->findOneBy([]);
        self::assertNotNull($order, 'No fixture order found. Make sure Sylius fixtures are loaded.');
        $order->setState(OrderInterface::STATE_FULFILLED);
        $this->order = $order;

        $customer = $order->getCustomer();
        self::assertInstanceOf(ReviewerInterface::class, $customer);
        $this->customer = $customer;

        $this->entityManager->flush();

        $this->loginAdmin();
    }

    /** @test */
    public function it_lists_store_reviews(): void
    {
        $storeReview = $this->createStoreReview();

        $this->client->request('GET', '/admin/store-reviews/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('body', (string) $storeReview->getTitle());
    }

    /** @test */
    public function it_shows_empty_index_when_no_reviews_exist(): void
    {
        $this->client->request('GET', '/admin/store-reviews/');

        self::assertResponseIsSuccessful();
    }

    /** @test */
    public function it_shows_the_update_form(): void
    {
        $storeReview = $this->createStoreReview();

        $this->client->request('GET', sprintf('/admin/store-reviews/%d/edit', $storeReview->getId()));

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form');
    }

    /** @test */
    public function it_updates_a_store_review(): void
    {
        $storeReview = $this->createStoreReview();

        $this->client->request('GET', sprintf('/admin/store-reviews/%d/edit', $storeReview->getId()));
        self::assertResponseIsSuccessful();

        $this->client->submitForm('sylius_save_changes_button', [
            'setono_sylius_review_admin_store_review[rating]' => 3,
            'setono_sylius_review_admin_store_review[title]' => 'Updated title',
            'setono_sylius_review_admin_store_review[comment]' => 'Updated comment',
        ]);

        self::assertResponseRedirects();

        $this->entityManager->clear();

        /** @var StoreReviewInterface|null $updated */
        $updated = $this->entityManager->getRepository(StoreReviewInterface::class)->find($storeReview->getId());
        self::assertNotNull($updated);
        self::assertSame(3, $updated->getRating());
        self::assertSame('Updated title', $updated->getTitle());
        self::assertSame('Updated comment', $updated->getComment());
    }

    /** @test */
    public function it_adds_a_store_reply(): void
    {
        $storeReview = $this->createStoreReview();

        $this->client->request('GET', sprintf('/admin/store-reviews/%d/edit', $storeReview->getId()));
        self::assertResponseIsSuccessful();

        $this->client->submitForm('sylius_save_changes_button', [
            'setono_sylius_review_admin_store_review[rating]' => 5,
            'setono_sylius_review_admin_store_review[title]' => 'Great store',
            'setono_sylius_review_admin_store_review[comment]' => 'Wonderful experience',
            'setono_sylius_review_admin_store_review[storeReply]' => 'Thank you for your review!',
        ]);

        self::assertResponseRedirects();

        $this->entityManager->clear();

        /** @var StoreReviewInterface|null $updated */
        $updated = $this->entityManager->getRepository(StoreReviewInterface::class)->find($storeReview->getId());
        self::assertNotNull($updated);
        self::assertSame('Thank you for your review!', $updated->getStoreReply());
    }

    /** @test */
    public function it_accepts_a_store_review(): void
    {
        // Use rating 1 to avoid auto-approval (threshold is 4)
        $storeReview = $this->createStoreReview('Decent store', 1);

        $crawler = $this->client->request('GET', '/admin/store-reviews/');
        self::assertResponseIsSuccessful();

        $csrfToken = $this->extractCsrfTokenFromDeleteForm($crawler, (int) $storeReview->getId());

        $this->client->request('PUT', sprintf('/admin/store-review/%d/accept', $storeReview->getId()), [
            '_csrf_token' => $csrfToken,
        ]);

        self::assertResponseRedirects();

        $this->entityManager->clear();

        /** @var StoreReviewInterface|null $updated */
        $updated = $this->entityManager->getRepository(StoreReviewInterface::class)->find($storeReview->getId());
        self::assertNotNull($updated);
        self::assertSame('accepted', $updated->getStatus());
    }

    /** @test */
    public function it_rejects_a_store_review(): void
    {
        $storeReview = $this->createStoreReview('Bad store', 1);

        $crawler = $this->client->request('GET', '/admin/store-reviews/');
        self::assertResponseIsSuccessful();

        $csrfToken = $this->extractCsrfTokenFromDeleteForm($crawler, (int) $storeReview->getId());

        $this->client->request('PUT', sprintf('/admin/store-review/%d/reject', $storeReview->getId()), [
            '_csrf_token' => $csrfToken,
        ]);

        self::assertResponseRedirects();

        $this->entityManager->clear();

        /** @var StoreReviewInterface|null $updated */
        $updated = $this->entityManager->getRepository(StoreReviewInterface::class)->find($storeReview->getId());
        self::assertNotNull($updated);
        self::assertSame('rejected', $updated->getStatus());
    }

    /** @test */
    public function it_deletes_a_store_review(): void
    {
        $storeReview = $this->createStoreReview('To delete', 1);
        $id = $storeReview->getId();

        $crawler = $this->client->request('GET', '/admin/store-reviews/');
        self::assertResponseIsSuccessful();

        $csrfToken = $this->extractCsrfTokenFromDeleteForm($crawler, (int) $id);

        $this->entityManager->clear();

        $this->client->request('DELETE', sprintf('/admin/store-reviews/%d', $id), [
            '_csrf_token' => $csrfToken,
        ]);

        self::assertResponseRedirects();

        $this->entityManager->clear();

        $deleted = $this->entityManager->getRepository(StoreReviewInterface::class)->find($id);
        self::assertNull($deleted);
    }

    /** @test */
    public function it_bulk_deletes_store_reviews(): void
    {
        $review1 = $this->createStoreReview('Review 1', 1);
        $review2 = $this->createStoreReview('Review 2', 1);
        $id1 = $review1->getId();
        $id2 = $review2->getId();

        $crawler = $this->client->request('GET', '/admin/store-reviews/');
        self::assertResponseIsSuccessful();

        $bulkDeleteForm = $crawler->filter('form[action$="/store-reviews/bulk-delete"]');
        self::assertGreaterThan(0, $bulkDeleteForm->count(), 'Bulk delete form not found on index page');

        $csrfToken = $bulkDeleteForm->filter('input[name="_csrf_token"]')->attr('value');

        $this->entityManager->clear();

        $this->client->request('DELETE', '/admin/store-reviews/bulk-delete', [
            '_csrf_token' => $csrfToken,
            'ids' => [$id1, $id2],
        ]);

        self::assertResponseRedirects();

        $this->entityManager->clear();

        self::assertNull($this->entityManager->getRepository(StoreReviewInterface::class)->find($id1));
        self::assertNull($this->entityManager->getRepository(StoreReviewInterface::class)->find($id2));
    }

    /** @test */
    public function it_saves_store_reply_with_notify_reviewer_and_resets_flag(): void
    {
        $storeReview = $this->createStoreReview();

        $this->client->request('GET', sprintf('/admin/store-reviews/%d/edit', $storeReview->getId()));
        self::assertResponseIsSuccessful();

        $this->client->submitForm('sylius_save_changes_button', [
            'setono_sylius_review_admin_store_review[rating]' => 5,
            'setono_sylius_review_admin_store_review[title]' => 'Great store',
            'setono_sylius_review_admin_store_review[storeReply]' => 'Thank you for your feedback!',
            'setono_sylius_review_admin_store_review[notifyReviewer]' => '1',
        ]);

        self::assertResponseRedirects();

        $this->entityManager->clear();

        /** @var StoreReviewInterface|null $updated */
        $updated = $this->entityManager->getRepository(StoreReviewInterface::class)->find($storeReview->getId());
        self::assertNotNull($updated);
        self::assertSame('Thank you for your feedback!', $updated->getStoreReply());
        self::assertFalse($updated->getNotifyReviewer(), 'notifyReviewer should be reset to false after save');
    }

    /** @test */
    public function it_denies_access_for_unauthenticated_users(): void
    {
        self::ensureKernelShutdown();
        $client = self::createClient();

        $client->request('GET', '/admin/store-reviews/');

        self::assertResponseRedirects();
        self::assertStringContainsString('login', (string) $client->getResponse()->headers->get('Location'));
    }

    private function loginAdmin(): void
    {
        /** @var AdminUserInterface|null $adminUser */
        $adminUser = $this->entityManager->getRepository(AdminUserInterface::class)->findOneBy(['username' => 'sylius']);
        self::assertNotNull($adminUser, 'No admin user found. Make sure Sylius fixtures are loaded.');
        self::assertInstanceOf(UserInterface::class, $adminUser);

        $this->client->loginUser($adminUser, 'admin');
    }

    private function createStoreReview(string $title = 'Great store', int $rating = 5): StoreReview
    {
        $storeReview = new StoreReview();
        $storeReview->setRating($rating);
        $storeReview->setTitle($title);
        $storeReview->setComment('Wonderful experience');
        $storeReview->setReviewSubject($this->channel);
        $storeReview->setAuthor($this->customer);
        $storeReview->setOrder($this->order);

        $this->entityManager->persist($storeReview);
        $this->entityManager->flush();

        return $storeReview;
    }

    private function extractCsrfTokenFromDeleteForm(Crawler $crawler, int $entityId): string
    {
        $deleteForm = $crawler->filter(sprintf('form[action$="/store-reviews/%d"]', $entityId));
        self::assertGreaterThan(0, $deleteForm->count(), sprintf('Delete form for entity %d not found on page', $entityId));

        $token = $deleteForm->filter('input[name="_csrf_token"]')->attr('value');
        self::assertNotEmpty($token, 'CSRF token not found in delete form');

        return $token;
    }
}
