<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Functional\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusReviewPlugin\Model\ReviewInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductReviewInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\UserInterface;

final class ProductReviewStoreReplyTest extends WebTestCase
{
    private KernelBrowser $client;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = self::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->entityManager = $entityManager;

        $this->loginAdmin();
    }

    /** @test */
    public function it_saves_store_reply_with_notify_reviewer_on_product_review(): void
    {
        // Find an order to get a customer that has orders (needed for channel resolution)
        /** @var OrderInterface|null $order */
        $order = $this->entityManager->getRepository(OrderInterface::class)->findOneBy([]);
        self::assertNotNull($order, 'No fixture order found. Make sure Sylius fixtures are loaded.');

        $customer = $order->getCustomer();
        self::assertInstanceOf(CustomerInterface::class, $customer);

        // Find a product review by this customer, or set the author on an existing one
        /** @var (ProductReviewInterface&ReviewInterface)|null $productReview */
        $productReview = $this->entityManager->getRepository(ProductReviewInterface::class)->findOneBy([]);
        self::assertNotNull($productReview, 'No fixture product review found. Make sure Sylius fixtures are loaded.');
        self::assertInstanceOf(ReviewInterface::class, $productReview);

        $productReview->setAuthor($customer);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', sprintf('/admin/product-reviews/%d/edit', $productReview->getId()));
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('sylius_save_changes_button')->form();
        $form['sylius_product_review[storeReply]'] = 'Thanks for reviewing our product!';

        $notifyField = $form['sylius_product_review[notifyReviewer]'];
        \assert($notifyField instanceof \Symfony\Component\DomCrawler\Field\ChoiceFormField);
        $notifyField->tick();

        $this->client->submit($form);

        self::assertResponseRedirects();

        $this->entityManager->clear();

        /** @var (ProductReviewInterface&ReviewInterface)|null $updated */
        $updated = $this->entityManager->getRepository(ProductReviewInterface::class)->find($productReview->getId());
        self::assertNotNull($updated);
        self::assertInstanceOf(ReviewInterface::class, $updated);
        self::assertSame('Thanks for reviewing our product!', $updated->getStoreReply());
        self::assertFalse($updated->getNotifyReviewer(), 'notifyReviewer should be reset to false after save');
    }

    private function loginAdmin(): void
    {
        /** @var AdminUserInterface|null $adminUser */
        $adminUser = $this->entityManager->getRepository(AdminUserInterface::class)->findOneBy(['username' => 'sylius']);
        self::assertNotNull($adminUser, 'No admin user found. Make sure Sylius fixtures are loaded.');
        self::assertInstanceOf(UserInterface::class, $adminUser);

        $this->client->loginUser($adminUser, 'admin');
    }
}
