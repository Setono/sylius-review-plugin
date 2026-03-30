## Context

Auto-approval is currently always active. A single `ReviewAutoApprovalListener` handles both store and product reviews, delegating to two composite checkers. The only built-in checker is `MinimumRatingAutoApprovalChecker` with a hardcoded default threshold of 4. There is no configuration in `Configuration.php` for auto-approval, and no way to disable it or set different thresholds per review type.

## Goals / Non-Goals

**Goals:**
- Allow plugin users to enable/disable auto-approval independently for store and product reviews
- Allow plugin users to set a minimum rating threshold independently for each review type
- Preserve backward compatibility (defaults: `enabled: true`, `minimum_rating: 4`)

**Non-Goals:**
- Adding new auto-approval checker types (beyond the existing `MinimumRatingAutoApprovalChecker`)
- Admin UI for configuring auto-approval (configuration is file-based only)
- Per-channel auto-approval settings

## Decisions

### 1. Generic `AutoApprovalListener` instantiated per review type

Replace the single `ReviewAutoApprovalListener` (which hardcodes both review types in one class) with a generic `AutoApprovalListener` that accepts the review class, checker, workflow name, and transition name via constructor.

Two service instances are registered — one for store reviews, one for product reviews — each conditionally based on its `enabled` config flag. When a type is disabled, its listener instance is simply not registered.

**Why this over splitting into two separate listener classes:** The logic is identical for both types — only the injected values differ. A single generic class eliminates duplication and is easier to maintain. The per-type behavior comes from the DI wiring, not from the class itself.

**Why this over the current dual-type listener:** The current approach can't cleanly disable one type without the other. With constructor-injected config, each instance is independent and can be conditionally registered.

### 2. Per-type `MinimumRatingAutoApprovalChecker` instances

Replace the single `MinimumRatingAutoApprovalChecker` service registration with two named instances, each receiving its threshold from config:

- `setono_sylius_review.checker.auto_approval.minimum_rating.store_review` (threshold from `auto_approval.store_review.minimum_rating`)
- `setono_sylius_review.checker.auto_approval.minimum_rating.product_review` (threshold from `auto_approval.product_review.minimum_rating`)

Each is tagged with only the appropriate composite tag. The generic FQCN-based service registration (`Setono\...\MinimumRatingAutoApprovalChecker`) is removed from `services.xml`.

**Why not keep the single service:** Per-type thresholds require separate instances. Registering them dynamically in the extension keeps the threshold wired from config.

### 3. Dynamic service registration in the DI extension

The listener and per-type checker instances are registered programmatically in `SetonoSyliusReviewExtension::load()` rather than in `services.xml`. This is because:
- Their existence depends on config (`enabled` flag)
- Their constructor arguments depend on config (`minimum_rating`)
- `services.xml` is loaded unconditionally

The composite checker services and the compiler pass remain in `services.xml` — they're always needed (custom checkers may still be registered even if the built-in checker is not).

### 4. Compiler pass respects disabled types

`RegisterAutoApprovalCheckersPass` currently tags generic `AutoApprovalCheckerInterface` implementations for both composites unconditionally. After this change, it will check whether each composite service exists in the container before tagging for it. If store auto-approval is disabled, the store composite won't exist, so the pass won't tag for it.

### 5. Configuration structure

```yaml
setono_sylius_review:
    auto_approval:
        store_review:
            enabled: true          # default: true
            minimum_rating: 4      # default: 4
        product_review:
            enabled: true          # default: true
            minimum_rating: 4      # default: 4
```

Both sections have `addDefaultsIfNotSet()` so the entire `auto_approval` block is optional. Existing users with no `auto_approval` config get the same behavior as before.

## Risks / Trade-offs

- **Custom checkers for a disabled type are silently ignored** — If a user disables store auto-approval but has registered a custom `StoreAutoApprovalCheckerInterface`, the checker service exists but is never called. This is correct behavior (disabled means disabled), but could be surprising. → No mitigation needed; this is the expected semantics.
- **Removing the old listener class is a breaking change for anyone extending it** — The class is `final`, so no one can extend it. Service decoration is possible but unlikely. → Acceptable risk. The old service ID will no longer exist, which is a minor BC break for anyone referencing it directly.
