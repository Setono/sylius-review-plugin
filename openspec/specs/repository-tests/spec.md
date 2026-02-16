## Requirements

### Requirement: ReviewRequestRepository.createForProcessingQueryBuilder returns pending requests ready for processing
The query builder SHALL return review requests in `pending` state with `nextEligibilityCheckAt` at or before the current time.

#### Scenario: Pending request with past eligibility check date is returned
- **WHEN** a review request exists with state `pending` and `nextEligibilityCheckAt` in the past
- **THEN** `createForProcessingQueryBuilder()->getQuery()->getResult()` SHALL include that request

#### Scenario: Pending request with future eligibility check date is not returned
- **WHEN** a review request exists with state `pending` and `nextEligibilityCheckAt` in the future
- **THEN** `createForProcessingQueryBuilder()->getQuery()->getResult()` SHALL NOT include that request

#### Scenario: Completed request is not returned
- **WHEN** a review request exists with state `completed` and `nextEligibilityCheckAt` in the past
- **THEN** `createForProcessingQueryBuilder()->getQuery()->getResult()` SHALL NOT include that request

#### Scenario: Cancelled request is not returned
- **WHEN** a review request exists with state `cancelled` and `nextEligibilityCheckAt` in the past
- **THEN** `createForProcessingQueryBuilder()->getQuery()->getResult()` SHALL NOT include that request

### Requirement: ReviewRequestRepository.removeBefore deletes requests created before threshold
The method SHALL delete all review requests whose `createdAt` is before the given threshold, regardless of state.

#### Scenario: Request created before threshold is removed
- **WHEN** a review request was created before the threshold date and `removeBefore()` is called
- **THEN** the request SHALL no longer exist in the database

#### Scenario: Request created after threshold is not removed
- **WHEN** a review request was created after the threshold date and `removeBefore()` is called
- **THEN** the request SHALL still exist in the database

### Requirement: ReviewRequestRepository.removeCancelled deletes all cancelled requests
The method SHALL delete all review requests with state `cancelled`.

#### Scenario: Cancelled request is removed
- **WHEN** a cancelled review request exists and `removeCancelled()` is called
- **THEN** the request SHALL no longer exist in the database

#### Scenario: Pending request is not removed
- **WHEN** a pending review request exists and `removeCancelled()` is called
- **THEN** the request SHALL still exist in the database

### Requirement: ReviewRequestRepository.hasExistingForOrder checks order association
The method SHALL return `true` if a review request exists for the given order, `false` otherwise.

#### Scenario: Review request exists for the order
- **WHEN** a review request is associated with an order
- **THEN** `hasExistingForOrder()` SHALL return `true`

#### Scenario: No review request exists for the order
- **WHEN** no review request is associated with an order
- **THEN** `hasExistingForOrder()` SHALL return `false`

### Requirement: StoreReviewRepository.findOneByOrder finds review by order
The method SHALL return the store review associated with the given order, or `null` if none exists.

#### Scenario: Store review exists for the order
- **WHEN** a store review is associated with an order
- **THEN** `findOneByOrder()` SHALL return that store review

#### Scenario: No store review exists for the order
- **WHEN** no store review is associated with an order
- **THEN** `findOneByOrder()` SHALL return `null`
