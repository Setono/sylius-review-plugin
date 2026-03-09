## Why

When a store replies to a customer's review (store or product), the customer has no way of knowing unless they revisit the site. Sending an email notification when the store replies closes the feedback loop and improves customer engagement.

## What Changes

- Add a `notifyReviewer` boolean field to `ReviewInterface`/`ReviewTrait` (mapped, persisted)
- Add a "Notify reviewer" checkbox to the admin review forms (store review admin form + product review form extension)
- Add a Doctrine event subscriber (`preUpdate`/`postUpdate`) that detects when `storeReply` changes with `notifyReviewer === true`, sends a notification email, then resets `notifyReviewer` to `false`
- Add a new email manager for store reply notifications using Sylius Mailer (`SenderInterface`)
- Add a new email template for the notification (includes store/channel name, original review, and the reply)
- Register a new email subject constant in `Emails.php`

## Capabilities

### New Capabilities
- `store-reply-notification`: Email notification sent to the reviewer when a store reply is added or changed, triggered by a mapped checkbox on the admin form

### Modified Capabilities
- `store-reply-model`: Adding `notifyReviewer` boolean field to `ReviewInterface`/`ReviewTrait`
- `store-reply-admin`: Adding "Notify reviewer" checkbox to admin forms

## Impact

- **Model**: `ReviewInterface`, `ReviewTrait` gain a new `notifyReviewer` property + DB column
- **Forms**: `StoreReviewAdminType` and `ReviewTypeStoreReplyExtension` gain a checkbox field
- **Services**: New Doctrine event subscriber, new email manager class
- **Templates**: New email template, updated admin form templates
- **Config**: New email registration in `services.xml`, new translations
