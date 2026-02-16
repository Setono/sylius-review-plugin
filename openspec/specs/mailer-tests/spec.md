### Requirement: Emails constant is correct
The `Emails::REVIEW_REQUEST` constant SHALL equal `'setono_sylius_review__review_request'`.

#### Scenario: Constant value matches expected string
- **WHEN** accessing `Emails::REVIEW_REQUEST`
- **THEN** the value SHALL be `'setono_sylius_review__review_request'`

### Requirement: ReviewRequestEmailManager sends email with correct arguments
The `ReviewRequestEmailManager::sendReviewRequest()` method SHALL send an email using the Sylius `SenderInterface` with the correct email code, recipient, and template data.

#### Scenario: Successful email sending
- **WHEN** `sendReviewRequest()` is called with a review request that has a valid order with a customer email
- **THEN** the email sender SHALL be called with:
  - Email code: `Emails::REVIEW_REQUEST`
  - Recipient: the customer's email address
  - Data containing: `reviewRequest`, `channel` (from order), and `localeCode` (from order)

### Requirement: ReviewRequestEmailManager validates order exists
The `ReviewRequestEmailManager::sendReviewRequest()` method SHALL throw an exception when the review request has no associated order.

#### Scenario: Null order throws exception
- **WHEN** `sendReviewRequest()` is called with a review request that returns null for `getOrder()`
- **THEN** an `InvalidArgumentException` SHALL be thrown

### Requirement: ReviewRequestEmailManager validates customer email exists
The `ReviewRequestEmailManager::sendReviewRequest()` method SHALL throw an exception when the order's customer has no email.

#### Scenario: Null customer email throws exception
- **WHEN** `sendReviewRequest()` is called with a review request whose order has no customer email (customer is null)
- **THEN** an `InvalidArgumentException` SHALL be thrown

#### Scenario: Customer exists but email is null
- **WHEN** `sendReviewRequest()` is called with a review request whose order has a customer with null email
- **THEN** an `InvalidArgumentException` SHALL be thrown
