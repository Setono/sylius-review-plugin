## 1. Provide dual state machine definitions

- [x] 1.1 Add `getWinzouConfig()` to `StoreReviewWorkflow` (returns winzou-format config including `request_edit` transition)
- [x] 1.2 Prepend winzou config for store review workflow in `SetonoSyliusReviewExtension::prepend()`
- [x] 1.3 Prepend the `request_edit` transition for `sylius_product_review` to `winzou_state_machine` config in `prepend()`

## 2. Replace WorkflowInterface with StateMachineInterface

- [x] 2.1 Refactor `ReviewController` to use `StateMachineInterface` instead of two `WorkflowInterface` services
- [x] 2.2 Refactor `ReviewAutoApprovalListener` to use `StateMachineInterface` instead of two `WorkflowInterface` services
- [x] 2.3 Refactor `ReviewRequestProcessor` to use `StateMachineInterface` instead of `WorkflowInterface`
- [x] 2.4 Refactor `CheckEligibilityChecksSubscriber` to use `StateMachineInterface` instead of `WorkflowInterface`

## 3. Update service definitions

- [x] 3.1 Update `services.xml`: replace all `state_machine.*` service references with `StateMachineInterface` for the four refactored services

## 4. Remove compiler pass and Symfony Workflow requirement

- [x] 4.1 Delete `CheckProductReviewWorkflowAdapterPass`
- [x] 4.2 Remove compiler pass registration from `SetonoSyliusReviewPlugin`
- [x] 4.3 Remove the "Configure Symfony Workflow" section from README
- [x] 4.4 Remove `tests/Application/config/packages/sylius_state_machine.yaml`

## 5. Update tests

- [x] 5.1 Update `ReviewAutoApprovalListenerTest` unit test for new constructor signature
- [x] 5.2 Run full test suite to verify
