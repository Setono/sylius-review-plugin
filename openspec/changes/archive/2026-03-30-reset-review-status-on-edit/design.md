## Context

The `ReviewController` persists reviews via `$manager->persist()` + `$manager->flush()`. For new reviews, `ReviewAutoApprovalListener` (a `prePersist` listener) may auto-approve them. For existing reviews, `persist()` on a managed entity is effectively a no-op for Doctrine's identity map — the entity is already managed, so `prePersist` does NOT fire again. This means auto-approval doesn't re-run on edits.

Two workflows govern review status:
- **Store review** (`setono_sylius_review__store_review`): Plugin-owned, always Symfony Workflow. Transitions: `accept` (new→accepted), `reject` (new→rejected).
- **Product review** (`sylius_product_review`): Sylius-owned. Could be Symfony Workflow or Winzou state machine depending on user configuration (`sylius_state_machine_abstraction.default_adapter` or per-graph mapping). Both adapters define the same transitions.

Neither workflow currently supports going from `accepted`/`rejected` back to `new`.

## Goals / Non-Goals

**Goals:**
- Add a `request_edit` transition (accepted/rejected → new) to both review workflows
- Use the workflow transition in `ReviewController` to reset status on edit
- Detect at compile time whether the product review workflow uses Symfony Workflow (vs Winzou) and only prepend the transition config if so
- Re-run auto-approval after the status reset

**Non-Goals:**
- Changing admin edit behavior (admin edits should not reset status)
- Adding configuration to toggle this behavior
- Supporting Winzou state machine for the product review `request_edit` transition (Winzou config would need a different extension mechanism)

## Decisions

### 1. Add `request_edit` transition via workflow configuration

**Decision:** Add a `request_edit` transition to both workflows that goes from `accepted` or `rejected` back to `new`.

**Store review:** Add directly in `StoreReviewWorkflow::getTransitions()` — we own this workflow.

**Product review:** Prepend the transition onto `framework.workflows.sylius_product_review.transitions` in the extension's `prepend()` method — but only if the user is using Symfony Workflow for this graph.

**Rationale:** Using the workflow system keeps status transitions in one place and makes them auditable/hookable via workflow events. Direct `setStatus()` calls bypass the workflow, which is fragile.

### 2. Compiler pass to enforce Symfony Workflow for product reviews

**Decision:** Create a compiler pass that checks whether `sylius_product_review` is using Symfony Workflow. The check examines:
1. `sylius_abstraction.state_machine.graphs_to_adapters_mapping` parameter for an explicit `sylius_product_review` mapping
2. Falls back to `sylius_abstraction.state_machine.default_adapter`

If the adapter is NOT `symfony_workflow`, the compiler pass throws an exception telling the user they must configure `sylius_product_review` to use Symfony Workflow.

Since the compiler pass enforces Symfony Workflow, the extension's `prepend()` can unconditionally prepend the `request_edit` transition onto `framework.workflows.sylius_product_review`.

**Rationale:** The plugin requires Symfony Workflow for its features to work correctly. Rather than silently degrading, failing fast at compile time gives the user a clear error message and path to fix it.

### 3. Apply workflow transition in the controller

**Decision:** Inject the workflow services into `ReviewController`. Before flushing an existing review (has an ID), apply the `request_edit` transition if the workflow `can()` apply it (i.e., the review is in `accepted` or `rejected` state).

**Rationale:** Using `can()` + `apply()` is safe — it's a no-op if the review is already in `new` state. The controller is the right boundary since admin edits should not trigger this.

### 4. Re-run auto-approval after status reset

**Decision:** After applying `request_edit` (which sets status to `new`), invoke the auto-approval checker directly. The `prePersist` listener won't fire on updates, so auto-approval must be invoked explicitly.

**Alternative considered:** Adding a workflow event listener on `request_edit` completion — viable but adds indirection. Direct invocation in the controller is simpler and keeps the scope clear.

## Risks / Trade-offs

- **Accepted reviews temporarily disappear** — After edit, the review goes back to `new` until auto-approved or manually re-approved. This is intended behavior.
- **Plugin requires Symfony Workflow for product reviews** — Users on Winzou will get a compile-time error. This is a hard requirement, not a soft preference. The plugin needs workflow transitions to function correctly.
