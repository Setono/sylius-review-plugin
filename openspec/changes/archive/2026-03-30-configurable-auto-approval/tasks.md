## 1. Configuration

- [x] 1.1 Add `auto_approval` section to `Configuration.php` with `store_review` and `product_review` child nodes, each containing `enabled` (bool, default `true`) and `minimum_rating` (int, default `4`)
- [x] 1.2 Update the `@var` type annotation in `SetonoSyliusReviewExtension::load()` to include the new `auto_approval` config shape

## 2. Listener Refactoring

- [x] 2.1 Create `AutoApprovalListener` class accepting `string $reviewClass`, `AutoApprovalCheckerInterface $checker`, `StateMachineInterface $stateMachine`, `string $workflowName`, `string $transition` — with `prePersist`, `preUpdate`, and `handleAutoApproval` methods
- [x] 2.2 Delete the old `ReviewAutoApprovalListener` class

## 3. DI Wiring

- [x] 3.1 Remove the static `ReviewAutoApprovalListener` and `MinimumRatingAutoApprovalChecker` service definitions from `services.xml`
- [x] 3.2 In `SetonoSyliusReviewExtension::load()`, conditionally register `MinimumRatingAutoApprovalChecker` and `AutoApprovalListener` instances for each enabled review type, with the threshold and workflow/transition wired from config
- [x] 3.3 Conditionally register the composite checker services — only register `setono_sylius_review.checker.auto_approval.store_review` and `setono_sylius_review.checker.auto_approval.product_review` when their respective type is enabled
- [x] 3.4 Update `RegisterAutoApprovalCheckersPass` to check whether each composite service exists before tagging for it

## 4. Tests

- [x] 4.1 Rewrite `ReviewAutoApprovalListenerTest` as `AutoApprovalListenerTest` — test the generic listener with a single review type per test case (matching entity approved, non-matching entity ignored, state machine not ready skips checker)
- [x] 4.2 Add a functional/integration test verifying the DI configuration: both types enabled (default), one disabled, both disabled — asserting correct service presence/absence in the container
