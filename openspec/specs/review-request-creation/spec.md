### Requirement: Data provider supplies orders eligible for review request creation
The data provider SHALL return orders that are eligible for review request creation using batch iteration.

#### Scenario: Order is fulfilled and has no review request
- **WHEN** an order is in fulfilled state, was completed within the pruning threshold, and has no associated review request
- **THEN** the data provider SHALL yield that order

#### Scenario: Order already has a review request
- **WHEN** an order already has an associated review request (regardless of review request state)
- **THEN** the data provider SHALL NOT yield that order

#### Scenario: Order is older than the pruning threshold
- **WHEN** an order was completed before the pruning threshold cutoff
- **THEN** the data provider SHALL NOT yield that order

#### Scenario: Order is not in fulfilled state
- **WHEN** an order is not in fulfilled state (e.g., new, cancelled)
- **THEN** the data provider SHALL NOT yield that order

#### Scenario: Query customization via event
- **WHEN** the data provider builds the query
- **THEN** it SHALL dispatch an event that allows listeners to modify the QueryBuilder before execution

### Requirement: Creator service creates review requests from eligible orders
The creator service SHALL use the data provider to find eligible orders and create review requests for each.

#### Scenario: Creating review requests for eligible orders
- **WHEN** the creator service runs and the data provider yields orders
- **THEN** the creator SHALL create a review request via the factory for each order and persist it

#### Scenario: No eligible orders
- **WHEN** the creator service runs and the data provider yields no orders
- **THEN** the creator SHALL complete without creating any review requests

#### Scenario: Logging
- **WHEN** the creator service has a logger set and creates review requests
- **THEN** it SHALL log the number of review requests created

### Requirement: Process command runs creation before processing
The `setono:sylius-review:process` command SHALL run review request creation before processing existing review requests.

#### Scenario: Normal execution
- **WHEN** the process command is executed
- **THEN** it SHALL first invoke the creator service, then invoke the processor service

### Requirement: Synchronous subscriber is removed
The `CreateReviewRequestSubscriber` SHALL be removed entirely.

#### Scenario: Order completion does not create review requests
- **WHEN** an order is completed via the Sylius checkout flow
- **THEN** no review request SHALL be created synchronously during the transaction
