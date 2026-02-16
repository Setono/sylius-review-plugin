## Context

The codebase has a mix of service ID styles: some services already use FQCN (controllers, form types, checkers), while others use the legacy `setono_sylius_review.*` convention. This inconsistency makes it harder to navigate and prevents autowiring for the string-based services.

## Goals / Non-Goals

**Goals:**
- Use FQCN as service ID for all services where the class is unique
- Add interface aliases to enable autowiring by interface
- Update all internal references to use the new IDs

**Non-Goals:**
- Changing services managed by Sylius resource bundle (`setono_sylius_review.repository.*`, `setono_sylius_review.factory.review_request`)
- Changing tag names (those are not service IDs)
- Changing parameter names

## Decisions

### 1. Service ID mapping

Services to rename (class is unique → use FQCN as ID, drop `class` attribute):

| Old ID | New ID (FQCN) | Interface alias |
|--------|---------------|-----------------|
| `setono_sylius_review.command.process` | `ProcessCommand` | — |
| `setono_sylius_review.command.prune` | `PruneCommand` | — |
| `setono_sylius_review.review_request_eligibility_checker.composite` | `CompositeReviewRequestEligibilityChecker` | `ReviewRequestEligibilityCheckerInterface` |
| `setono_sylius_review.review_request_eligibility_checker.order_fulfilled` | `OrderFulfilledReviewRequestEligibilityChecker` | — |
| `setono_sylius_review.creator.review_request` | `ReviewRequestCreator` | `ReviewRequestCreatorInterface` |
| `setono_sylius_review.event_subscriber.review_request.check_eligibility_checks` | `CheckEligibilityChecksSubscriber` | — |
| `setono_sylius_review.event_subscriber.review_request.increment_eligibility_checks` | `IncrementEligibilityChecksSubscriber` | — |
| `setono_sylius_review.event_subscriber.review_request.reset` | `ResetSubscriber` | — |
| `setono_sylius_review.event_subscriber.review_request.update_next_eligibility_check` | `UpdateNextEligibilityCheckSubscriber` | — |
| `setono_sylius_review.mailer.review_request_email_manager` | `ReviewRequestEmailManager` | `ReviewRequestEmailManagerInterface` |
| `setono_sylius_review.processor.review_request` | `ReviewRequestProcessor` | `ReviewRequestProcessorInterface` |

Services that CANNOT use FQCN (two instances of the same class):

| Service ID (keep as-is) | Reason |
|--------------------------|--------|
| `setono_sylius_review.checker.auto_approval.store_review` | `CompositeAutoApprovalChecker` used for both store and product |
| `setono_sylius_review.checker.auto_approval.product_review` | Same class, different instance |

Services managed by Sylius resource bundle (not touched):

| Service ID (keep as-is) | Reason |
|--------------------------|--------|
| `setono_sylius_review.repository.review_request` | Registered by Sylius resource bundle |
| `setono_sylius_review.repository.store_review` | Registered by Sylius resource bundle |
| `setono_sylius_review.factory.review_request` | Registered by Sylius resource bundle |

The email form type service registered in `SetonoSyliusReviewExtension::registerEmailFormType()` also keeps its string ID since it's conditionally registered.

### 2. Update all internal references

Every place that references an old string ID as a service argument, compiler pass target, or container `get()` call must be updated to the new FQCN.

### 3. Alias the composite eligibility checker

The `CompositeCompilerPass` in `SetonoSyliusReviewPlugin::build()` references the composite service by ID. Update it to use the FQCN.

## Risks / Trade-offs

**[Breaking change for plugin users who reference old service IDs]** → Documented in proposal. Users decorating or referencing the old IDs must update. This is a one-time migration.
