## Context

The plugin already supports store replies on both store and product reviews via `ReviewInterface`/`ReviewTrait`. The admin can write replies through `StoreReviewAdminType` (store reviews) and `ReviewTypeStoreReplyExtension` (product reviews). Emails are sent using Sylius Mailer (`SenderInterface`) — see `ReviewRequestEmailManager` for the existing pattern.

Currently, when a store replies, the customer is not notified. This change adds an opt-in email notification triggered by a mapped `notifyReviewer` checkbox on the admin form.

## Goals / Non-Goals

**Goals:**
- Notify the reviewer via email when a store reply is added or changed
- Give the admin explicit control over when notifications are sent (mapped checkbox)
- Support both store reviews and product reviews
- Follow existing patterns (Sylius Mailer, Doctrine event subscribers)

**Non-Goals:**
- Async email sending (Symfony Messenger) — use synchronous Sylius Mailer for now
- JavaScript to hide/disable checkbox when reply is empty
- Link back to the review in the email
- Notification history/audit trail beyond the `notifyReviewer` flag reset

## Decisions

### 1. Mapped `notifyReviewer` boolean on `ReviewInterface`/`ReviewTrait`

**Decision**: Add a persisted boolean field rather than an unmapped form field.

**Rationale**: A mapped field allows a Doctrine event subscriber to check the flag directly on the entity without reading raw request data. After sending, the flag is reset to `false` so the checkbox appears unchecked on next form load — preventing accidental re-sends.

**Alternative considered**: Unmapped checkbox + reading request data in a Sylius resource event subscriber. Rejected because it couples the subscriber to HTTP request context.

### 2. Doctrine `preUpdate`/`postUpdate` event subscriber

**Decision**: Use a Doctrine event subscriber with `preUpdate` to detect changes and flag entities, and `postUpdate` to send emails.

**Rationale**: `preUpdate` gives access to the `UnitOfWork` changeset to check if `storeReply` actually changed (not just any field). `postUpdate` ensures the entity is already persisted before sending the email. The subscriber flags entities in a `SplObjectStorage` between the two events.

**Re-entry safety**: After sending, the subscriber resets `notifyReviewer` to `false` and flushes. This triggers another `preUpdate`/`postUpdate` cycle, but `storeReply` won't be in the changeset on the second pass (only `notifyReviewer` changed), so no re-entry occurs.

**Alternative considered**: Sylius resource `post_update` event. Rejected because it doesn't provide changeset access to check if `storeReply` specifically changed.

### 3. Separate email manager class

**Decision**: Create `StoreReplyNotificationEmailManager` separate from the existing `ReviewRequestEmailManager`.

**Rationale**: Different concern (reply notification vs. review request), different email template, different data context. Follows Single Responsibility Principle.

### 4. Single email template handling both review types

**Decision**: One email template that adapts based on whether the review is a store review or product review.

**Rationale**: The email content is nearly identical — only the "what was reviewed" context differs (channel name vs. product name). A single template with conditional blocks keeps things simple.

## Risks / Trade-offs

- **[Risk] Flush in postUpdate triggers another lifecycle cycle** → Mitigated by the changeset check in preUpdate (storeReply won't be in the changeset on the re-triggered cycle)
- **[Risk] Author has no email** → Guard with a null check before sending; skip silently if no email
- **[Trade-off] Synchronous sending may slow admin form save** → Acceptable for now; async can be added later if needed
