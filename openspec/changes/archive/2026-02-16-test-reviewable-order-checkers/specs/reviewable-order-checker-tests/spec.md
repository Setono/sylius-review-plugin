## ADDED Requirements

### Requirement: ReviewableOrderCheck value object works correctly
The `ReviewableOrderCheck` value object SHALL provide factory methods that create instances with correct state.

#### Scenario: Creating a reviewable check
- **WHEN** `ReviewableOrderCheck::reviewable()` is called
- **THEN** the result SHALL have `reviewable` set to `true` and `reason` set to `null`

#### Scenario: Creating a not-reviewable check
- **WHEN** `ReviewableOrderCheck::notReviewable('some.reason')` is called
- **THEN** the result SHALL have `reviewable` set to `false` and `reason` set to `'some.reason'`

### Requirement: CompositeReviewableOrderChecker aggregates checkers with short-circuit
The composite checker SHALL iterate through registered checkers and return the first non-reviewable result, or reviewable if all pass.

#### Scenario: All checkers pass
- **WHEN** all registered checkers return `reviewable`
- **THEN** the composite SHALL return a reviewable check

#### Scenario: First checker fails
- **WHEN** the first registered checker returns `notReviewable`
- **THEN** the composite SHALL return that not-reviewable check and NOT call subsequent checkers

#### Scenario: Second checker fails
- **WHEN** the first checker returns `reviewable` and the second returns `notReviewable`
- **THEN** the composite SHALL return the second checker's not-reviewable check

#### Scenario: No checkers registered
- **WHEN** no checkers are registered in the composite
- **THEN** the composite SHALL return a reviewable check

### Requirement: OrderFulfilledReviewableOrderChecker validates order state
The checker SHALL verify the order is in one of the configured reviewable states.

#### Scenario: Order is in fulfilled state with default config
- **WHEN** the order state is `fulfilled` and default reviewable states are used
- **THEN** the checker SHALL return a reviewable check

#### Scenario: Order is not in a reviewable state
- **WHEN** the order state is `new` (or any non-reviewable state)
- **THEN** the checker SHALL return a not-reviewable check with reason `'setono_sylius_review.ui.order_not_fulfilled'`

#### Scenario: Order is in a custom reviewable state
- **WHEN** the checker is configured with custom reviewable states `['fulfilled', 'completed']` and the order state is `completed`
- **THEN** the checker SHALL return a reviewable check

### Requirement: StoreReviewEditableReviewableOrderChecker validates review editable period
The checker SHALL determine reviewability based on existing store reviews and the configured editable period.

#### Scenario: No existing review for the order
- **WHEN** no store review exists for the order
- **THEN** the checker SHALL return a reviewable check

#### Scenario: Existing review within editable period
- **WHEN** a store review exists and was created within the editable period
- **THEN** the checker SHALL return a reviewable check

#### Scenario: Existing review past editable period
- **WHEN** a store review exists and was created before the editable period
- **THEN** the checker SHALL return a not-reviewable check with reason `'setono_sylius_review.ui.review_period_expired'`

#### Scenario: Editing disabled (null editable period)
- **WHEN** a store review exists and the editable period is configured as `null`
- **THEN** the checker SHALL return a not-reviewable check with reason `'setono_sylius_review.ui.review_already_submitted'`

#### Scenario: Existing review with null createdAt
- **WHEN** a store review exists but has no `createdAt` date
- **THEN** the checker SHALL return a reviewable check
