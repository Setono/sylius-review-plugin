<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Functional\Form\Type;

use Setono\SyliusReviewPlugin\Form\Type\ReviewRequestEmailType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

final class ReviewRequestEmailTypeTest extends KernelTestCase
{
    private FormFactoryInterface $formFactory;

    protected function setUp(): void
    {
        self::bootKernel();

        /** @var FormFactoryInterface $formFactory */
        $formFactory = self::getContainer()->get('form.factory');
        $this->formFactory = $formFactory;
    }

    /** @test */
    public function it_builds_form_with_review_request_field(): void
    {
        $form = $this->formFactory->create(ReviewRequestEmailType::class);

        self::assertTrue($form->has('reviewRequest'));
    }

    /** @test */
    public function it_submits_with_valid_review_request(): void
    {
        $form = $this->formFactory->create(ReviewRequestEmailType::class);

        $form->submit(['reviewRequest' => null]);

        self::assertTrue($form->isSynchronized());
    }
}
