## REMOVED Requirements

### Requirement: Data provider supplies orders eligible for review request creation
**Reason**: The data provider abstraction (`OrderForReviewRequestDataProviderInterface` / `OrderForReviewRequestDataProvider`) is removed. Its responsibilities (query building, event dispatch, batch iteration) are inlined into the creator service.
**Migration**: Use the `QueryBuilderForReviewRequestCreationCreated` event to customize the order query. This event is unchanged and continues to be dispatched by the creator.

## MODIFIED Requirements

### Requirement: Creator service creates review requests from eligible orders
The creator service SHALL query for eligible orders, dispatch an event for query customization, iterate results in batches, and create review requests for each order.

#### Scenario: Creating review requests for eligible orders
- **WHEN** the creator service runs and there are fulfilled orders within the pruning threshold that have no associated review request
- **THEN** the creator SHALL create a review request via the factory for each order and persist it

#### Scenario: No eligible orders
- **WHEN** the creator service runs and no orders match the eligibility criteria
- **THEN** the creator SHALL complete without creating any review requests

#### Scenario: Order already has a review request
- **WHEN** an order already has an associated review request (regardless of review request state)
- **THEN** the creator SHALL NOT create another review request for that order

#### Scenario: Order is older than the pruning threshold
- **WHEN** an order was completed before the pruning threshold cutoff
- **THEN** the creator SHALL NOT create a review request for that order

#### Scenario: Order is not in fulfilled state
- **WHEN** an order is not in fulfilled state (e.g., new, cancelled)
- **THEN** the creator SHALL NOT create a review request for that order

#### Scenario: Query customization via event
- **WHEN** the creator builds the order query
- **THEN** it SHALL dispatch `QueryBuilderForReviewRequestCreationCreated` to allow listeners to modify the QueryBuilder before execution

#### Scenario: Batch persistence
- **WHEN** the creator iterates over eligible orders
- **THEN** it SHALL use batch processing to flush and clear entities periodically, avoiding memory exhaustion on large order sets

#### Scenario: Logging
- **WHEN** the creator service has a logger set and creates review requests
- **THEN** it SHALL log the number of review requests created
