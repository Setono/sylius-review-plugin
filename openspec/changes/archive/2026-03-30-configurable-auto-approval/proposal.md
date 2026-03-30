## Why

Auto-approval of reviews is always enabled with a hardcoded default threshold of 4. Plugin users have no way to disable auto-approval or configure the minimum rating threshold per review type (store vs product). This limits flexibility for shops that want manual moderation or different approval policies per review type.

## What Changes

- Add `auto_approval` configuration section with per-review-type settings (`store_review` and `product_review`), each with `enabled` (bool) and `minimum_rating` (int)
- Replace the single `ReviewAutoApprovalListener` with a generic `AutoApprovalListener` that accepts the review class, checker, workflow name, and transition via constructor — instantiated twice (once per review type), each conditionally registered based on config
- Register two separate `MinimumRatingAutoApprovalChecker` instances with per-type thresholds from config, replacing the single shared instance
- When a review type's auto-approval is disabled, neither its listener nor its checker is registered

## Capabilities

### New Capabilities

- `auto-approval-configuration`: Semantic configuration for auto-approval settings (enabled flag and minimum rating threshold per review type)

### Modified Capabilities

- `auto-approval-checker-tests`: The listener is redesigned from a single class handling both types to a generic class instantiated per type — existing listener tests need updating

## Impact

- `Configuration.php` — new `auto_approval` tree node
- `SetonoSyliusReviewExtension.php` — conditional service registration based on config
- `ReviewAutoApprovalListener` — replaced by generic `AutoApprovalListener`
- `services.xml` — remove static listener and single checker registration (now done dynamically in extension)
- `RegisterAutoApprovalCheckersPass` — may need to respect enabled flags (skip tagging for disabled composites)
- Existing listener tests need updating for the new class signature
- **Backward compatible**: defaults (`enabled: true`, `minimum_rating: 4`) preserve current behavior
