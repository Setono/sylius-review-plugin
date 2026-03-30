## ADDED Requirements

### Requirement: Auto-approval is configurable per review type
The plugin SHALL expose an `auto_approval` configuration section with independent settings for `store_review` and `product_review`, each containing `enabled` (boolean) and `minimum_rating` (integer) options.

#### Scenario: Default configuration preserves current behavior
- **WHEN** no `auto_approval` configuration is specified
- **THEN** both `store_review` and `product_review` SHALL default to `enabled: true` with `minimum_rating: 4`

#### Scenario: Custom configuration is applied
- **WHEN** the user configures `auto_approval.store_review.minimum_rating` to `3` and `auto_approval.product_review.minimum_rating` to `5`
- **THEN** store reviews with rating >= 3 SHALL be auto-approved
- **AND** product reviews with rating >= 5 SHALL be auto-approved

### Requirement: Auto-approval listener is not registered when disabled
When auto-approval is disabled for a review type, the Doctrine event listener for that type SHALL NOT be registered in the container.

#### Scenario: Store auto-approval disabled
- **WHEN** `auto_approval.store_review.enabled` is `false`
- **THEN** no Doctrine listener for store review auto-approval SHALL be registered
- **AND** store reviews SHALL NOT be auto-approved regardless of rating

#### Scenario: Product auto-approval disabled
- **WHEN** `auto_approval.product_review.enabled` is `false`
- **THEN** no Doctrine listener for product review auto-approval SHALL be registered
- **AND** product reviews SHALL NOT be auto-approved regardless of rating

#### Scenario: Both types disabled
- **WHEN** both `auto_approval.store_review.enabled` and `auto_approval.product_review.enabled` are `false`
- **THEN** no auto-approval Doctrine listeners SHALL be registered

#### Scenario: Only one type enabled
- **WHEN** `auto_approval.store_review.enabled` is `false` and `auto_approval.product_review.enabled` is `true`
- **THEN** only the product review auto-approval listener SHALL be registered
- **AND** store reviews SHALL NOT be auto-approved

### Requirement: Generic AutoApprovalListener handles a single review type
The `AutoApprovalListener` SHALL accept a review class name, auto-approval checker, state machine, workflow name, and transition name via its constructor. It SHALL only process entities that are instances of the configured review class.

#### Scenario: Listener processes matching entity
- **WHEN** the listener is configured for `StoreReviewInterface` and receives a `StoreReviewInterface` entity
- **THEN** it SHALL check auto-approval eligibility and apply the transition if approved

#### Scenario: Listener ignores non-matching entity
- **WHEN** the listener is configured for `StoreReviewInterface` and receives a `ProductReviewInterface` entity
- **THEN** it SHALL NOT process the entity

#### Scenario: Listener respects state machine
- **WHEN** the state machine indicates the accept transition is not available for the entity
- **THEN** the listener SHALL NOT invoke the auto-approval checker

### Requirement: Per-type MinimumRatingAutoApprovalChecker instances
The DI extension SHALL register separate `MinimumRatingAutoApprovalChecker` instances for store and product reviews, each with the `minimum_rating` threshold from its respective configuration section.

#### Scenario: Different thresholds per type
- **WHEN** `auto_approval.store_review.minimum_rating` is `3` and `auto_approval.product_review.minimum_rating` is `5`
- **THEN** the store review checker instance SHALL use threshold `3`
- **AND** the product review checker instance SHALL use threshold `5`

### Requirement: Compiler pass respects disabled types
The `RegisterAutoApprovalCheckersPass` SHALL only tag generic `AutoApprovalCheckerInterface` implementations for composite services that exist in the container.

#### Scenario: Store auto-approval disabled removes store composite
- **WHEN** `auto_approval.store_review.enabled` is `false`
- **THEN** the store review composite checker service SHALL NOT exist in the container
- **AND** generic auto-approval checkers SHALL NOT be tagged for the store review composite

#### Scenario: Both enabled
- **WHEN** both types are enabled
- **THEN** generic auto-approval checkers SHALL be tagged for both composites (preserving current behavior)
