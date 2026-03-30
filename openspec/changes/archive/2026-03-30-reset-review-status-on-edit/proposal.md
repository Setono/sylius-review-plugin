## Why

When a customer edits an already-accepted review within the editable period, the modified content goes live immediately without re-approval. This bypasses moderation — a customer could get a review accepted, then change it to something entirely different while it stays published.

## What Changes

- **Add `request_edit` workflow transition** to both store review and product review workflows (accepted/rejected → new)
- **Apply the transition in `ReviewController`** when persisting an existing review, resetting its status to `new`
- **Re-run auto-approval** after the reset so reviews that still meet auto-approval thresholds are re-approved immediately
- **Compiler pass** to detect whether the product review graph uses Symfony Workflow (vs Winzou) and conditionally add the transition

## Capabilities

### New Capabilities

_None_

### Modified Capabilities

- `review-controller-test`: Controller functional tests need a new scenario for editing an accepted review and verifying status reset

## Impact

- **Workflows**: `StoreReviewWorkflow` gains a new transition; `sylius_product_review` gains one conditionally via compiler pass
- **Controller**: `ReviewController` gets workflow + auto-approval checker dependencies
- **New compiler pass**: Checks product review state machine adapter
- **No breaking changes**: Existing transitions are unchanged
