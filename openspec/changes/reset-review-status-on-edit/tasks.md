## 1. Add `request_edit` transition to store review workflow

- [x] 1.1 Add `TRANSITION_REQUEST_EDIT` constant and transition (accepted/rejected → new) to `StoreReviewWorkflow`

## 2. Add `request_edit` transition to product review workflow

- [x] 2.1 Add `TRANSITION_REQUEST_EDIT` constant to `ProductReviewWorkflow`
- [x] 2.2 Create compiler pass that throws if `sylius_product_review` is not using Symfony Workflow
- [x] 2.3 Prepend the `request_edit` transition onto `framework.workflows.sylius_product_review` in the extension's `prepend()` method
- [x] 2.4 Register the compiler pass in the bundle

## 3. Apply transition in ReviewController

- [x] 3.1 Inject store review and product review workflow services into `ReviewController`
- [x] 3.2 Before flushing, apply `request_edit` transition on existing store review (if `can()`)
- [x] 3.3 Before flushing, apply `request_edit` transition on existing product reviews (if `can()`)
- [x] 3.4 After resetting status, re-run auto-approval checkers on affected reviews
- [x] 3.5 Register the new constructor arguments in the service definition

## 4. Documentation

- [x] 4.1 Update README with requirement that `sylius_product_review` must use Symfony Workflow

## 5. Tests

- [x] 5.1 Add functional test: editing an accepted store review resets status to `new`
