## 1. Model Changes

- [x] 1.1 Add `getNotifyReviewer(): bool` and `setNotifyReviewer(bool): void` to `ReviewInterface`
- [x] 1.2 Add `notifyReviewer` boolean property (ORM mapped, default `false`) with getter/setter to `ReviewTrait`
- [x] 1.3 Add `notify_reviewer` column to `StoreReview.orm.xml` Doctrine mapping
- [x] 1.4 Update unit test for `ReviewTrait` to cover the new `notifyReviewer` field

## 2. Email Infrastructure

- [x] 2.1 Add `STORE_REPLY_NOTIFICATION` constant to `Emails` class
- [x] 2.2 Create `StoreReplyNotificationEmailManagerInterface` with `sendNotification(ReviewInterface): void`
- [x] 2.3 Create `StoreReplyNotificationEmailManager` implementing the interface, using `SenderInterface` to send email
- [x] 2.4 Write unit tests for the email manager
- [x] 2.5 Register the email manager services in `services.xml`
- [x] 2.6 Register the email subject in the Sylius Mailer configuration

## 3. Doctrine Event Subscriber

- [x] 3.1 Create `StoreReplyNotificationSubscriber` with `preUpdate` and `postUpdate` methods
- [x] 3.2 In `preUpdate`: check entity is `ReviewInterface`, `notifyReviewer === true`, and `storeReply` is in the UnitOfWork changeset — reset `notifyReviewer` to `false` and flag entity in `SplObjectStorage`
- [x] 3.3 In `postUpdate`: send email for flagged entities
- [x] 3.4 Guard against author with no email (skip sending, handled in email manager)
- [x] 3.5 Register the subscriber in `services.xml` as a Doctrine event listener
- [x] 3.6 Write unit tests for the subscriber

## 4. Form Changes

- [x] 4.1 Add `notifyReviewer` checkbox field to `StoreReviewAdminType`
- [x] 4.2 Add `notifyReviewer` checkbox field to `ReviewTypeStoreReplyExtension`
- [x] 4.3 Update the store review admin form template (`_form.html.twig`) to render the checkbox
- [x] 4.4 Write form type tests for the new field

## 5. Email Template & Translations

- [x] 5.1 Create email template `store_reply_notification.html.twig` with conditional rendering for store vs product reviews
- [x] 5.2 Add translation key `setono_sylius_review.email.store_reply_notification.subject` to `messages.en.yaml`
- [x] 5.3 Add translation key `setono_sylius_review.form.review.notify_reviewer` to `messages.en.yaml`

## 6. Testing

- [x] 6.1 Write functional test: admin saves store review reply with notify checked → verify email would be sent
- [x] 6.2 Write functional test: admin saves product review reply with notify checked → verify email would be sent
- [x] 6.3 Run full test suite and static analysis to verify no regressions
