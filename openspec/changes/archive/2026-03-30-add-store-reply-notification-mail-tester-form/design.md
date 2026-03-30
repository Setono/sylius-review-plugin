## Context

The Synolia SyliusMailTesterPlugin lets admins preview emails by selecting data via a form. Each email needs a `ResolvableFormTypeInterface` implementation. The `setono_sylius_review__review_request` email already has one (`ReviewRequestEmailType`), but the `setono_sylius_review__store_reply_notification` email does not.

The store reply notification email requires a `ReviewInterface` entity (store or product review) and derives all other template variables (`channel`, `localeCode`, etc.) from it via `StoreReplyNotificationEmailManager::sendNotification()`.

## Goals / Non-Goals

**Goals:**
- Allow admins to test the store reply notification email via the mail tester
- Follow the exact same pattern as `ReviewRequestEmailType`

**Non-Goals:**
- Filtering reviews to only those with a `storeReply` set (any review works for preview)
- Separating store vs product review selection (a single review selector is sufficient)

## Decisions

### 1. Single entity selector for any review type

**Decision**: The form will use a `LimitedEntityType` field for the store review entity class. Store reviews are the most common case for store reply notifications and simplify the form. The `StoreReplyNotificationEmailManager` already handles both store and product reviews.

**Rationale**: Keeps the form simple. Admins just need to pick a review to preview the email template.

### 2. Register in the same `registerEmailFormType` method

**Decision**: Add the new form type registration alongside the existing `ReviewRequestEmailType` in `SetonoSyliusReviewExtension::registerEmailFormType()`, guarded by the same `SynoliaSyliusMailTesterPlugin` bundle check.

**Rationale**: Reuses the existing guard clause. Both form types share the same lifecycle — only registered when the mail tester plugin is present.

## Risks / Trade-offs

- **[Risk] No store reviews exist in DB** → `LimitedEntityType` will show an empty dropdown, same as the review request form type. Acceptable.
