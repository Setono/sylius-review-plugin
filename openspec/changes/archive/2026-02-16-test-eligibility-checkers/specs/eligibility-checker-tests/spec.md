## ADDED Requirements

### Requirement: EligibilityCheck value object tests
The test suite SHALL verify that the `EligibilityCheck` value object correctly represents eligible and ineligible states.

#### Scenario: Creating an eligible check
- **WHEN** `EligibilityCheck::eligible()` is called
- **THEN** the result SHALL have `eligible` set to `true` and `reason` set to `null`

#### Scenario: Creating an ineligible check with reason
- **WHEN** `EligibilityCheck::ineligible('some reason')` is called
- **THEN** the result SHALL have `eligible` set to `false` and `reason` set to `'some reason'`

### Requirement: CompositeReviewRequestEligibilityChecker tests
The test suite SHALL verify that the composite checker aggregates multiple checkers correctly, returning the first ineligible result or eligible if all pass.

#### Scenario: No checkers registered
- **WHEN** `check()` is called on a composite with no registered checkers
- **THEN** the result SHALL be eligible

#### Scenario: All registered checkers return eligible
- **WHEN** `check()` is called and all registered checkers return eligible
- **THEN** the result SHALL be eligible

#### Scenario: First checker returns ineligible
- **WHEN** `check()` is called and the first registered checker returns ineligible
- **THEN** the result SHALL be the ineligible check from the first checker and subsequent checkers SHALL NOT be called

#### Scenario: Second checker returns ineligible
- **WHEN** `check()` is called with two checkers where the first returns eligible and the second returns ineligible
- **THEN** the result SHALL be the ineligible check from the second checker

### Requirement: OrderFulfilledReviewRequestEligibilityChecker tests
The test suite SHALL verify that the order fulfillment checker correctly evaluates order state.

#### Scenario: Order is fulfilled
- **WHEN** `check()` is called with a review request whose order state is `fulfilled`
- **THEN** the result SHALL be eligible

#### Scenario: Order is not fulfilled
- **WHEN** `check()` is called with a review request whose order state is not `fulfilled` (e.g., `new`, `cancelled`)
- **THEN** the result SHALL be ineligible with reason `'Order is not fulfilled'`

#### Scenario: Order is null
- **WHEN** `check()` is called with a review request that has no order (null)
- **THEN** the result SHALL be ineligible with reason `'Order is not fulfilled'`
