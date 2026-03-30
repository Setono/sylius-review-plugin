## Why

The plugin currently injects `Symfony\Component\Workflow\WorkflowInterface` directly, which only works when the user configures Symfony Workflow as the state machine adapter. Sylius provides a `StateMachineInterface` abstraction that works with both Winzou and Symfony Workflow, allowing users to choose their preferred backend. Using the abstraction makes the plugin compatible with the default Sylius setup (Winzou) out of the box.

## What Changes

- **Replace all `WorkflowInterface` injections** with Sylius's `StateMachineInterface` in `ReviewController`, `ReviewAutoApprovalListener`, `ReviewRequestProcessor`, and `CheckEligibilityChecksSubscriber`
- **Provide dual workflow definitions** for the store review workflow: both `framework.workflows` (Symfony) and `winzou_state_machine` (Winzou) config via `prepend()`
- **Prepend the `request_edit` transition** for `sylius_product_review` to both Winzou and Symfony Workflow configs
- **Remove `CheckProductReviewWorkflowAdapterPass`** — no longer needed since the abstraction handles adapter routing
- **Remove the Symfony Workflow requirement** from README
- **Update service definitions** to inject `StateMachineInterface` instead of individual workflow services
- **Update `StoreReviewWorkflow`** to also generate Winzou config alongside Symfony Workflow config

## Capabilities

### New Capabilities

_None_

### Modified Capabilities

_None — this is an internal refactor that doesn't change externally observable behavior_

## Impact

- **4 PHP files**: `ReviewController`, `ReviewAutoApprovalListener`, `ReviewRequestProcessor`, `CheckEligibilityChecksSubscriber` — change `WorkflowInterface` to `StateMachineInterface`
- **Service definitions**: `services.xml` — replace workflow service IDs with `StateMachineInterface`
- **Extension**: `SetonoSyliusReviewExtension` — add Winzou config for store review and product review `request_edit` transition
- **Bundle**: `SetonoSyliusReviewPlugin` — remove compiler pass registration
- **Deleted**: `CheckProductReviewWorkflowAdapterPass`
- **README**: Remove Symfony Workflow requirement section
- **Test app config**: Remove `sylius_state_machine.yaml` (no longer needed)
- **Tests**: Update unit tests for `ReviewAutoApprovalListener` constructor change
