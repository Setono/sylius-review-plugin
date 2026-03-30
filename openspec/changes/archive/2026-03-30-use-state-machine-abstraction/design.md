## Context

Sylius provides `Sylius\Abstraction\StateMachine\StateMachineInterface` as a unified API over both Winzou State Machine and Symfony Workflow. The `CompositeStateMachine` routes calls to the correct adapter based on graph name and user configuration. The plugin currently bypasses this by injecting `WorkflowInterface` directly.

Four files inject `WorkflowInterface`:
- `ReviewController` — store review + product review workflows
- `ReviewAutoApprovalListener` — store review + product review workflows
- `ReviewRequestProcessor` — review request workflow
- `CheckEligibilityChecksSubscriber` — review request workflow

## Goals / Non-Goals

**Goals:**
- Use `StateMachineInterface` everywhere, making the plugin work with both Winzou and Symfony Workflow
- Provide state machine definitions for both backends
- Drop the compiler pass that enforced Symfony Workflow

**Non-Goals:**
- Changing any state machine logic or transitions
- Supporting additional state machine backends beyond the two Sylius supports

## Decisions

### 1. Inject one `StateMachineInterface` instead of multiple `WorkflowInterface`

**Decision:** Replace all `WorkflowInterface` constructor arguments with a single `StateMachineInterface`. The graph name is passed as a parameter to each `can()`/`apply()` call.

**Before:** `$this->storeReviewWorkflow->can($review, 'accept')`
**After:** `$this->stateMachine->can($review, StoreReviewWorkflow::NAME, StoreReviewWorkflow::TRANSITION_ACCEPT)`

**Rationale:** The abstraction routes to the correct backend internally. One service replaces multiple workflow injections.

### 2. Provide both Winzou and Symfony Workflow config for store review

**Decision:** `StoreReviewWorkflow` gets a new `getWinzouConfig()` method alongside the existing `getConfig()` (Symfony). The extension's `prepend()` calls both and prepends to both `winzou_state_machine` and `framework.workflows`.

**Rationale:** Sylius core does exactly this — each graph has both a `.yml` (winzou) and `.yaml` (symfony) definition. The abstraction picks the right one based on user config.

### 3. Prepend product review `request_edit` to both backends

**Decision:** The extension prepends the `request_edit` transition to both `winzou_state_machine.sylius_product_review` and `framework.workflows.sylius_product_review`.

**Rationale:** We don't know which backend the user configured, so we define the transition in both. Only the active one will be used.

### 4. Service ID for the abstraction

**Decision:** Use `Sylius\Abstraction\StateMachine\StateMachineInterface` as the service type-hint. The actual service ID in Sylius is `sylius_abstraction.state_machine.composite` with an alias to the interface.

### 5. Review request workflow also needs dual config

**Decision:** `ReviewRequestWorkflow` also needs `getWinzouConfig()`. The plugin forces it to use `symfony_workflow` via `sylius_state_machine_abstraction` config, but to be fully adapter-agnostic, both definitions should exist.

**Correction:** Actually, the review request workflow is plugin-internal (not user-facing like product reviews). The plugin can keep forcing `symfony_workflow` for it via `sylius_state_machine_abstraction.graphs_to_adapters_mapping`. This is simpler than providing dual config for an internal workflow. Keep the existing behavior for review requests — only change the review workflows (store + product).

## Risks / Trade-offs

- **Winzou doesn't support context** — `apply()` context parameter is ignored by the Winzou adapter. This is fine since none of our transitions pass context.
- **Both configs are always loaded** — Both Winzou and Symfony Workflow definitions are prepended regardless of which adapter is active. Only the active one is used. Sylius core does the same.
