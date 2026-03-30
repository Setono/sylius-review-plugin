## 1. Create Form Type

- [x] 1.1 Create `src/Form/Type/StoreReplyNotificationEmailType.php` implementing `ResolvableFormTypeInterface`, following the `ReviewRequestEmailType` pattern
- [x] 1.2 Add a `LimitedEntityType` field for selecting a store review entity with a human-readable choice label
- [x] 1.3 Implement `support()`, `getCode()`, and `getFormType()` for the `Emails::STORE_REPLY_NOTIFICATION` key

## 2. Register Service

- [x] 2.1 In `SetonoSyliusReviewExtension::registerEmailFormType()`, add the `StoreReplyNotificationEmailType` service definition alongside the existing `ReviewRequestEmailType`, tagged with `form.type` and `app.resolvable_form_type.resolver`

## 3. Test

- [x] 3.1 Add a unit test for `StoreReplyNotificationEmailType` verifying `support()` returns true/false for correct/incorrect email keys
- [x] 3.2 Verify visually via Playwright: navigate to /admin/mail/tester, select the store reply notification email, and confirm the form renders without error
