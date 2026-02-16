## Requirements

### Requirement: ReviewAutoApprovalSubscriber sets accepted status for auto-approved store reviews
The test SHALL verify that when a `StoreReviewInterface` entity triggers `prePersist` and the store auto-approval checker returns true, the entity's status is set to `ReviewInterface::STATUS_ACCEPTED`.

#### Scenario: Store review is auto-approved
- **WHEN** `prePersist` is called with a `StoreReviewInterface` entity and the store checker returns `shouldAutoApprove() === true`
- **THEN** the entity's status SHALL be set to `ReviewInterface::STATUS_ACCEPTED`

#### Scenario: Store review is not auto-approved
- **WHEN** `prePersist` is called with a `StoreReviewInterface` entity and the store checker returns `shouldAutoApprove() === false`
- **THEN** the entity's status SHALL NOT be changed

### Requirement: ReviewAutoApprovalSubscriber sets accepted status for auto-approved product reviews
The test SHALL verify that when a `ProductReviewInterface` entity triggers `prePersist` and the product auto-approval checker returns true, the entity's status is set to `ReviewInterface::STATUS_ACCEPTED`.

#### Scenario: Product review is auto-approved
- **WHEN** `prePersist` is called with a `ProductReviewInterface` entity and the product checker returns `shouldAutoApprove() === true`
- **THEN** the entity's status SHALL be set to `ReviewInterface::STATUS_ACCEPTED`

#### Scenario: Product review is not auto-approved
- **WHEN** `prePersist` is called with a `ProductReviewInterface` entity and the product checker returns `shouldAutoApprove() === false`
- **THEN** the entity's status SHALL NOT be changed

### Requirement: ReviewAutoApprovalSubscriber ignores unrelated entities
The test SHALL verify that entities not implementing `StoreReviewInterface` or `ProductReviewInterface` are ignored.

#### Scenario: Unrelated entity triggers prePersist
- **WHEN** `prePersist` is called with an entity that is neither a store review nor a product review
- **THEN** no checker SHALL be called and no status SHALL be changed

### Requirement: CheckEligibilityChecksSubscriber cancels requests exceeding maximum checks
The test SHALL verify that when a review request's eligibility checks exceed the maximum, the workflow cancel transition is applied and a processing error is set.

#### Scenario: Eligibility checks exceed maximum
- **WHEN** a `ReviewRequestProcessingStarted` event fires and `getEligibilityChecks()` returns a value greater than `maximumChecks`
- **THEN** the workflow `cancel` transition SHALL be applied and `setProcessingError` SHALL be called with an error message

#### Scenario: Eligibility checks equal maximum
- **WHEN** a `ReviewRequestProcessingStarted` event fires and `getEligibilityChecks()` returns exactly `maximumChecks`
- **THEN** no workflow transition SHALL be applied (checks <= max is allowed)

#### Scenario: Eligibility checks below maximum
- **WHEN** a `ReviewRequestProcessingStarted` event fires and `getEligibilityChecks()` returns a value less than `maximumChecks`
- **THEN** no workflow transition SHALL be applied

### Requirement: CheckEligibilityChecksSubscriber subscribes with correct priority
The test SHALL verify `getSubscribedEvents()` returns the correct event class, method, and priority.

#### Scenario: Subscribed events mapping
- **WHEN** `getSubscribedEvents()` is called
- **THEN** it SHALL return `ReviewRequestProcessingStarted::class` mapped to `['check', 200]`

### Requirement: IncrementEligibilityChecksSubscriber increments the counter
The test SHALL verify that `incrementEligibilityChecks()` is called on the review request.

#### Scenario: Counter is incremented
- **WHEN** a `ReviewRequestProcessingStarted` event fires
- **THEN** `incrementEligibilityChecks()` SHALL be called on the review request

### Requirement: IncrementEligibilityChecksSubscriber subscribes with correct priority
The test SHALL verify `getSubscribedEvents()` returns the correct event class, method, and priority.

#### Scenario: Subscribed events mapping
- **WHEN** `getSubscribedEvents()` is called
- **THEN** it SHALL return `ReviewRequestProcessingStarted::class` mapped to `['incrementEligibilityChecks', 300]`

### Requirement: ResetSubscriber clears processing state
The test SHALL verify that both `setIneligibilityReason(null)` and `setProcessingError(null)` are called.

#### Scenario: State is reset
- **WHEN** a `ReviewRequestProcessingStarted` event fires
- **THEN** `setIneligibilityReason(null)` and `setProcessingError(null)` SHALL both be called on the review request

### Requirement: ResetSubscriber subscribes with correct priority
The test SHALL verify `getSubscribedEvents()` returns the correct event class, method, and priority.

#### Scenario: Subscribed events mapping
- **WHEN** `getSubscribedEvents()` is called
- **THEN** it SHALL return `ReviewRequestProcessingStarted::class` mapped to `['reset', 400]`

### Requirement: UpdateNextEligibilityCheckSubscriber calculates exponential backoff
The test SHALL verify that `setNextEligibilityCheckAt()` is called with a datetime computed as `initialDelayHours * 2^(eligibilityChecks - 1)` hours from now.

#### Scenario: First eligibility check (checks = 1, default delay = 24h)
- **WHEN** a `ReviewRequestProcessingStarted` event fires with `getEligibilityChecks() === 1` and default `initialDelayHours` of 24
- **THEN** `setNextEligibilityCheckAt()` SHALL be called with a datetime approximately 24 hours from now

#### Scenario: Second eligibility check (checks = 2, default delay = 24h)
- **WHEN** a `ReviewRequestProcessingStarted` event fires with `getEligibilityChecks() === 2` and default `initialDelayHours` of 24
- **THEN** `setNextEligibilityCheckAt()` SHALL be called with a datetime approximately 48 hours from now

#### Scenario: Custom initial delay
- **WHEN** the subscriber is constructed with `initialDelayHours = 12` and event fires with `getEligibilityChecks() === 1`
- **THEN** `setNextEligibilityCheckAt()` SHALL be called with a datetime approximately 12 hours from now

### Requirement: UpdateNextEligibilityCheckSubscriber subscribes with correct priority
The test SHALL verify `getSubscribedEvents()` returns the correct event class, method, and priority.

#### Scenario: Subscribed events mapping
- **WHEN** `getSubscribedEvents()` is called
- **THEN** it SHALL return `ReviewRequestProcessingStarted::class` mapped to `['updateNextEligibilityCheck', 100]`
