<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Setono\Doctrine\ORMTrait;
use Setono\SyliusReviewPlugin\Checker\ReviewableOrder\ReviewableOrderCheckerInterface;
use Setono\SyliusReviewPlugin\Form\Type\ReviewType;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductReviewInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ReviewController extends AbstractController
{
    use ORMTrait;

    /**
     * @param OrderRepositoryInterface<OrderInterface> $orderRepository
     */
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        ManagerRegistry $managerRegistry,
        private readonly ReviewableOrderCheckerInterface $reviewableOrderChecker,
    ) {
        $this->managerRegistry = $managerRegistry;
    }

    public function __invoke(Request $request): Response
    {
        $token = $request->query->get('token');
        if (!is_string($token) || '' === $token) {
            throw $this->createNotFoundException('Token is required.');
        }

        $order = $this->orderRepository->findOneByTokenValue($token);
        if (!$order instanceof OrderInterface) {
            throw $this->createNotFoundException('Order not found.');
        }

        $reviewableCheck = $this->reviewableOrderChecker->check($order);

        // If not reviewable, render template with error message
        if (!$reviewableCheck->reviewable) {
            return $this->render('@SetonoSyliusReviewPlugin/shop/review/index.html.twig', [
                'order' => $order,
                'reviewableCheck' => $reviewableCheck,
            ]);
        }

        $reviewCommand = new ReviewCommand();

        $form = $this->createForm(ReviewType::class, $reviewCommand, [
            'order' => $order,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getManager();

            if ($reviewCommand->getStoreReview() !== null) {
                $manager->persist($reviewCommand->getStoreReview());
            }

            foreach ($reviewCommand->getProductReviews() as $productReview) {
                if ($productReview instanceof ProductReviewInterface && null !== $productReview->getRating()) {
                    $manager = $this->getManager($productReview);
                    $manager->persist($productReview);
                }
            }

            $manager->flush();

            $this->addFlash('success', 'setono_sylius_review.review.submitted_successfully');

            return $this->redirectToRoute('setono_sylius_review__review', ['token' => $token]);
        }

        return $this->render('@SetonoSyliusReviewPlugin/shop/review/index.html.twig', [
            'order' => $order,
            'reviewableCheck' => $reviewableCheck,
            'form' => $form->createView(),
        ]);
    }
}
