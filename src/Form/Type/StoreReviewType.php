<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Form\Type;

use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class StoreReviewType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rating', ChoiceType::class, [
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                ],
                'label' => 'setono_sylius_review.form.store_review.rating',
                'expanded' => true,
                'multiple' => false,
                'required' => false,
                'placeholder' => false,
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'setono_sylius_review.form.store_review.comment',
                'required' => false,
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($options): void {
            $data = $event->getData();

            if (!$data instanceof StoreReviewInterface) {
                return;
            }

            /** @var OrderInterface|null $order */
            $order = $options['order'] ?? null;
            if (null === $order) {
                return;
            }

            // Only populate if this is a new review (no ID yet)
            if (null !== $data->getId()) {
                return;
            }

            $data->setOrder($order);

            $customer = $order->getCustomer();
            if ($customer instanceof CustomerInterface) {
                $data->setAuthorEmail($customer->getEmail());
                $data->setAuthorFirstName($customer->getFirstName());
                $data->setAuthorLastName($customer->getLastName());

                $billingAddress = $order->getBillingAddress();
                if (null !== $billingAddress) {
                    $data->setAuthorCity($billingAddress->getCity());
                    $data->setAuthorCountry($billingAddress->getCountryCode());
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('order', null);
        $resolver->setAllowedTypes('order', ['null', OrderInterface::class]);
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_review_store_review';
    }
}
