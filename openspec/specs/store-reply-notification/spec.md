### Requirement: Doctrine subscriber sends notification on reply change
A Doctrine event subscriber SHALL listen to `preUpdate` and `postUpdate` on entities implementing `ReviewInterface`. In `preUpdate`, if `notifyReviewer` is `true` AND the `storeReply` field is in the UnitOfWork changeset, `notifyReviewer` SHALL be reset to `false` on the entity and it SHALL be flagged for notification. In `postUpdate`, flagged entities SHALL have a notification email sent.

#### Scenario: Reply changed with notify checked
- **WHEN** an admin saves a review with `notifyReviewer === true` and `storeReply` has changed
- **THEN** a notification email SHALL be sent to the reviewer and `notifyReviewer` SHALL be reset to `false`

#### Scenario: Reply unchanged with notify checked
- **WHEN** an admin saves a review with `notifyReviewer === true` but `storeReply` has NOT changed
- **THEN** no notification email SHALL be sent

#### Scenario: Reply changed with notify unchecked
- **WHEN** an admin saves a review with `notifyReviewer === false` and `storeReply` has changed
- **THEN** no notification email SHALL be sent

#### Scenario: No re-entry on notifyReviewer reset
- **WHEN** `notifyReviewer` is reset to `false` in `preUpdate`
- **THEN** subsequent `preUpdate` calls for the same entity SHALL NOT flag it (since `notifyReviewer` is already `false`)

#### Scenario: Author has no email
- **WHEN** a flagged review's author has no email address
- **THEN** the email manager SHALL skip sending

### Requirement: Email manager for store reply notifications
A `StoreReplyNotificationEmailManager` SHALL send notification emails using Sylius Mailer (`SenderInterface`). It SHALL accept a `ReviewInterface` entity and send the email to the review author's email address. For product reviews, the channel SHALL be resolved from the customer's latest order via `OrderRepositoryInterface::findLatestByCustomer()`.

#### Scenario: Send store review reply notification
- **WHEN** called with a `StoreReviewInterface` entity
- **THEN** it SHALL send an email with the channel (from review subject), original review details, and the store reply

#### Scenario: Send product review reply notification
- **WHEN** called with a `ProductReviewInterface` entity that also implements `ReviewInterface`
- **THEN** it SHALL resolve the channel from the customer's latest order and send an email with the product name, original review details, and the store reply

### Requirement: OrderRepositoryInterface with findLatestByCustomer
The plugin SHALL provide `OrderRepositoryInterface` (extending Sylius's `OrderRepositoryInterface`) with a `findLatestByCustomer(CustomerInterface): ?OrderInterface` method. A `OrderRepositoryTrait` SHALL provide the default implementation. Plugin users MUST extend their order repository to implement this interface.

#### Scenario: findLatestByCustomer returns the most recent order
- **WHEN** called with a customer that has orders
- **THEN** it SHALL return the order with the most recent `createdAt`

#### Scenario: findLatestByCustomer returns null when no orders exist
- **WHEN** called with a customer that has no orders
- **THEN** it SHALL return null

### Requirement: Email template for reply notification
A Twig email template SHALL render the notification with the store/channel name (for store reviews) or product name (for product reviews), the customer's original review (rating, comment), and the store's reply text rendered as HTML from markdown. The template SHALL NOT display the review title.

#### Scenario: Template renders for store review
- **WHEN** the email is rendered for a store review reply
- **THEN** the intro text SHALL include the store/channel name (not hardcoded "The store")
- **AND** it SHALL include the review rating and comment
- **AND** it SHALL NOT include the review title
- **AND** the store reply SHALL be converted from markdown to HTML using the `markdown_to_html` filter

#### Scenario: Template renders for product review
- **WHEN** the email is rendered for a product review reply
- **THEN** the intro text SHALL include the store/channel name and the product name
- **AND** it SHALL include the review rating and comment
- **AND** it SHALL NOT include the review title
- **AND** the store reply SHALL be converted from markdown to HTML using the `markdown_to_html` filter

### Requirement: Email subject registered as constant
The `Emails` class SHALL define a constant `STORE_REPLY_NOTIFICATION` with value `setono_sylius_review__store_reply_notification`.

#### Scenario: Constant is available
- **WHEN** referencing `Emails::STORE_REPLY_NOTIFICATION`
- **THEN** it SHALL equal `'setono_sylius_review__store_reply_notification'`

### Requirement: Translations for notification
Translation keys SHALL exist for the email subject, intro texts, and the "Notify reviewer" form label. The intro texts SHALL use `%store%` for the store/channel name and `%name%` for the review subject name.

#### Scenario: Email subject translation
- **WHEN** the notification email is rendered
- **THEN** the subject SHALL use translation key `setono_sylius_review.email.store_reply_notification.subject`

#### Scenario: Store review intro translation
- **WHEN** the notification email is rendered for a store review
- **THEN** the intro SHALL use `%store%` placeholder for the store name (not hardcoded "The store")

#### Scenario: Product review intro translation
- **WHEN** the notification email is rendered for a product review
- **THEN** the intro SHALL use `%store%` for the store name and `%name%` for the product name

#### Scenario: Form label translation
- **WHEN** the admin form renders the notify checkbox
- **THEN** the label SHALL use translation key `setono_sylius_review.form.review.notify_reviewer`
