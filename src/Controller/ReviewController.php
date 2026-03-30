<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Setono\Doctrine\ORMTrait;
use Setono\SyliusReviewPlugin\Checker\ReviewableOrder\ReviewableOrderCheckerInterface;
use Setono\SyliusReviewPlugin\Form\Type\ReviewType;
use Setono\SyliusReviewPlugin\Model\ReviewInterface;
use Setono\SyliusReviewPlugin\Workflow\ProductReviewWorkflow;
use Setono\SyliusReviewPlugin\Workflow\StoreReviewWorkflow;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductReviewInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Sylius\Component\Review\Model\ReviewInterface as BaseReviewInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Workflow\WorkflowInterface;

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
        private readonly WorkflowInterface $storeReviewWorkflow,
        private readonly WorkflowInterface $productReviewWorkflow,
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
            $displayName = $reviewCommand->getDisplayName();
            $manager = $this->getManager();

            $storeReview = $reviewCommand->getStoreReview();
            if (null !== $storeReview) {
                $storeReview->setDisplayName($displayName);
                $this->resetReviewStatus($storeReview, $this->storeReviewWorkflow, StoreReviewWorkflow::TRANSITION_REQUEST_EDIT);
                $manager->persist($storeReview);
            }

            foreach ($reviewCommand->getProductReviews() as $productReview) {
                if ($productReview instanceof ProductReviewInterface && null !== $productReview->getRating()) {
                    if ($productReview instanceof ReviewInterface) {
                        $productReview->setDisplayName($displayName);
                    }
                    $this->resetReviewStatus($productReview, $this->productReviewWorkflow, ProductReviewWorkflow::TRANSITION_REQUEST_EDIT);
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

    private function resetReviewStatus(BaseReviewInterface $review, WorkflowInterface $workflow, string $transition): void
    {
        if (null === $review->getId()) {
            return;
        }

        if ($workflow->can($review, $transition)) {
            $workflow->apply($review, $transition);
        }
    }
}
