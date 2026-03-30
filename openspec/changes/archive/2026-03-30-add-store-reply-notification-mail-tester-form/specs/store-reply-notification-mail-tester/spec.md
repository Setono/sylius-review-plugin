## ADDED Requirements

### Requirement: Mail tester form type for store reply notification email

A form type implementing `ResolvableFormTypeInterface` SHALL exist for the `setono_sylius_review__store_reply_notification` email key. It SHALL allow admins to select a store review entity to preview the notification email.

#### Scenario: Form type supports the store reply notification email key
- **WHEN** the mail tester resolves a form type for email key `setono_sylius_review__store_reply_notification`
- **THEN** the `StoreReplyNotificationEmailType` SHALL return `true` from `support()`
- **AND** `getCode()` SHALL return `setono_sylius_review__store_reply_notification`

#### Scenario: Form type does not support other email keys
- **WHEN** the mail tester resolves a form type for email key `setono_sylius_review__review_request`
- **THEN** the `StoreReplyNotificationEmailType` SHALL return `false` from `support()`

#### Scenario: Form presents a store review selector
- **WHEN** the admin selects the store reply notification email in the mail tester
- **THEN** the form SHALL display a `LimitedEntityType` dropdown for selecting a store review entity
- **AND** each choice SHALL display a human-readable label

### Requirement: Form provides all template variables on submit

On form submission, the `StoreReplyNotificationEmailType` SHALL derive the email template variables (`review`, `isStoreReview`, `isProductReview`, `reviewSubject`, `reviewSubjectName`) from the selected store review entity via a `FormEvents::SUBMIT` listener. The mail tester controller provides `channel` and `localeCode` automatically.

#### Scenario: Template variables are derived from selected store review
- **WHEN** the admin submits the mail tester form with a store review selected
- **THEN** the form data SHALL include `review` set to the selected store review entity
- **AND** `isStoreReview` SHALL be `true`
- **AND** `isProductReview` SHALL be `false`
- **AND** `reviewSubject` SHALL be the review's review subject
- **AND** `reviewSubjectName` SHALL be the review subject's name

### Requirement: Form type is only registered when mail tester plugin is present

The `StoreReplyNotificationEmailType` SHALL only be registered as a service when `SynoliaSyliusMailTesterPlugin` is in the kernel bundles. It SHALL be tagged with `form.type` and `app.resolvable_form_type.resolver`.

#### Scenario: Mail tester plugin is installed
- **WHEN** the `SynoliaSyliusMailTesterPlugin` bundle is registered
- **THEN** the `StoreReplyNotificationEmailType` service SHALL be registered with tags `form.type` and `app.resolvable_form_type.resolver`

#### Scenario: Mail tester plugin is not installed
- **WHEN** the `SynoliaSyliusMailTesterPlugin` bundle is not registered
- **THEN** the `StoreReplyNotificationEmailType` service SHALL NOT be registered
