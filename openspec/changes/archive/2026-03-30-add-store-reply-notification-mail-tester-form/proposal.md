## Why

The `setono_sylius_review__store_reply_notification` email cannot be tested via the Synolia SyliusMailTesterPlugin because no `ResolvableFormTypeInterface` implementation exists for it. The review request email already has a form type (`ReviewRequestEmailType`), but the store reply notification was added later without one.

## What Changes

- Add a `StoreReplyNotificationEmailType` form type that implements `ResolvableFormTypeInterface` for the `setono_sylius_review__store_reply_notification` email key
- The form lets the tester select a review (store or product) to preview the notification email
- Register the form type with the `app.resolvable_form_type.resolver` tag so the mail tester discovers it

## Capabilities

### New Capabilities
- `store-reply-notification-mail-tester`: Form type for testing the store reply notification email via the Synolia Mail Tester plugin

### Modified Capabilities
_(none)_

## Impact

- **New file**: `src/Form/Type/StoreReplyNotificationEmailType.php`
- **Service registration**: Auto-tagged in `SetonoSyliusReviewExtension.php` (same pattern as `ReviewRequestEmailType`)
- **Dependencies**: Only uses existing `synolia/sylius-mail-tester-plugin` interfaces (already a dependency)
