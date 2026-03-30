## MODIFIED Requirements

### Requirement: Email template for reply notification
A Twig email template SHALL render the notification with the store/channel name (for store reviews) or product name (for product reviews), the customer's original review (title, rating, comment), and the store's reply text rendered as HTML from markdown.

#### Scenario: Template renders for store review
- **WHEN** the email is rendered for a store review reply
- **THEN** the intro text SHALL include the store/channel name (not hardcoded "The store")
- **AND** it SHALL include the review title/rating/comment
- **AND** the store reply SHALL be converted from markdown to HTML using the `markdown_to_html` filter

#### Scenario: Template renders for product review
- **WHEN** the email is rendered for a product review reply
- **THEN** the intro text SHALL include the store/channel name and the product name
- **AND** it SHALL include the review title/rating/comment
- **AND** the store reply SHALL be converted from markdown to HTML using the `markdown_to_html` filter

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
