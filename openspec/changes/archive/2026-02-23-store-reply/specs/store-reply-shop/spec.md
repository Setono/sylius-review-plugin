## ADDED Requirements

### Requirement: Store reply is displayed on the shop review page
The shop-facing review display SHALL show the store reply below the customer's comment when a reply exists.

#### Scenario: Review has a store reply
- **WHEN** a review has a non-null `storeReply`
- **THEN** the shop template SHALL display the reply text and the `storeRepliedAt` date

#### Scenario: Review has no store reply
- **WHEN** a review has a null `storeReply`
- **THEN** the shop template SHALL NOT display any reply section for that review
