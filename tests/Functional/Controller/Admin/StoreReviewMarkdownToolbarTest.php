<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Functional\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusReviewPlugin\Model\ChannelInterface;
use Setono\SyliusReviewPlugin\Model\StoreReview;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\UserInterface;

final class StoreReviewMarkdownToolbarTest extends WebTestCase
{
    private KernelBrowser $client;

    private EntityManagerInterface $entityManager;

    private ChannelInterface $channel;

    private ReviewerInterface $customer;

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

        /** @var ReviewerInterface|null $customer */
        $customer = $this->entityManager->getRepository(ReviewerInterface::class)->findOneBy([]);
        self::assertNotNull($customer, 'No fixture customer found. Make sure Sylius fixtures are loaded.');
        $this->customer = $customer;

        $this->loginAdmin();
    }

    /** @test */
    public function it_renders_the_markdown_toolbar_on_the_store_review_update_page(): void
    {
        $storeReview = $this->createStoreReview();

        $crawler = $this->client->request('GET', sprintf('/admin/store-reviews/%d/edit', $storeReview->getId()));

        self::assertResponseIsSuccessful();
        self::assertGreaterThan(0, $crawler->filter('markdown-toolbar')->count(), 'Expected <markdown-toolbar> element on the store review update page');
    }

    /** @test */
    public function it_renders_markdown_toolbar_buttons(): void
    {
        $storeReview = $this->createStoreReview();

        $crawler = $this->client->request('GET', sprintf('/admin/store-reviews/%d/edit', $storeReview->getId()));

        self::assertResponseIsSuccessful();
        self::assertGreaterThan(0, $crawler->filter('markdown-toolbar md-bold')->count(), 'Expected <md-bold> button');
        self::assertGreaterThan(0, $crawler->filter('markdown-toolbar md-italic')->count(), 'Expected <md-italic> button');
        self::assertGreaterThan(0, $crawler->filter('markdown-toolbar md-link')->count(), 'Expected <md-link> button');
    }

    /** @test */
    public function it_loads_the_markdown_toolbar_script(): void
    {
        $storeReview = $this->createStoreReview();

        $crawler = $this->client->request('GET', sprintf('/admin/store-reviews/%d/edit', $storeReview->getId()));

        self::assertResponseIsSuccessful();

        $scripts = $crawler->filter('script[type="module"]');
        $found = false;

        foreach ($scripts as $script) {
            $src = $script->getAttribute('src');
            if (str_contains($src, 'markdown-toolbar-element')) {
                $found = true;

                break;
            }
        }

        self::assertTrue($found, 'Expected a <script type="module"> tag loading markdown-toolbar-element');
    }

    /** @test */
    public function it_connects_the_toolbar_to_the_store_reply_textarea(): void
    {
        $storeReview = $this->createStoreReview();

        $crawler = $this->client->request('GET', sprintf('/admin/store-reviews/%d/edit', $storeReview->getId()));

        self::assertResponseIsSuccessful();

        $toolbar = $crawler->filter('markdown-toolbar');
        self::assertGreaterThan(0, $toolbar->count());

        $forAttribute = $toolbar->attr('for');
        self::assertNotNull($forAttribute, 'Expected "for" attribute on <markdown-toolbar>');

        $textarea = $crawler->filter(sprintf('textarea#%s', $forAttribute));
        self::assertGreaterThan(0, $textarea->count(), 'Expected the toolbar "for" attribute to reference an existing textarea');
    }

    private function loginAdmin(): void
    {
        /** @var AdminUserInterface|null $adminUser */
        $adminUser = $this->entityManager->getRepository(AdminUserInterface::class)->findOneBy(['username' => 'sylius']);
        self::assertNotNull($adminUser, 'No admin user found. Make sure Sylius fixtures are loaded.');
        self::assertInstanceOf(UserInterface::class, $adminUser);

        $this->client->loginUser($adminUser, 'admin');
    }

    private function createStoreReview(): StoreReview
    {
        $storeReview = new StoreReview();
        $storeReview->setRating(5);
        $storeReview->setTitle('Great store');
        $storeReview->setComment('Wonderful experience');
        $storeReview->setReviewSubject($this->channel);
        $storeReview->setAuthor($this->customer);

        $this->entityManager->persist($storeReview);
        $this->entityManager->flush();

        return $storeReview;
    }
}
