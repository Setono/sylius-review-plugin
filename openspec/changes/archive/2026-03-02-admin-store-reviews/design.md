## Context

Sylius product reviews have full admin support: a grid under Marketing, an update form, and accept/reject workflow actions. This is built with three pieces — a grid YAML config, a resource routing YAML, and a menu entry in `MainMenuBuilder`.

This plugin's store reviews use the same `new → accepted/rejected` state pattern (via Symfony Workflow, graph `setono_sylius_review__store_review`) and are registered as a Sylius resource (`setono_sylius_review.store_review`). They additionally have a `storeReply` field for admin responses. All the infrastructure exists — we just need the admin UI layer.

## Goals / Non-Goals

**Goals:**
- Mirror the Sylius product review admin experience for store reviews
- Allow admins to list, filter, update, accept, reject, and delete store reviews
- Allow admins to write store replies from the update form
- Place store reviews in the Marketing sidebar section, next to product reviews

**Non-Goals:**
- Custom admin controller — use Sylius resource controller entirely
- Modifying the store review model or workflow
- Admin UI for product reviews (already handled by Sylius core)
- Bulk accept/reject actions (only bulk delete, matching Sylius pattern)

## Decisions

### 1. Mirror Sylius product review grid structure exactly

Use the same field types, filters, and action patterns as `sylius_admin_product_review`. The `reviewSubject` field shows the channel name instead of product name. The `status` field reuses Sylius's `@SyliusAdmin/ProductReview/Label/Status` templates since the status values (`new`, `accepted`, `rejected`) are identical.

**Alternative**: Custom status label templates. Rejected because the status values and visual treatment are the same — no reason to duplicate.

### 2. Event subscriber for menu (not a compiler pass or menu builder override)

Use a Symfony event subscriber listening to `sylius.menu.admin.main` to add the store reviews item under `marketing`. This is the standard Sylius plugin pattern for extending the admin menu — non-invasive, composable, and follows how Sylius documents plugin development.

The subscriber lives in `src/EventSubscriber/AdminMenuSubscriber.php`.

### 3. Workflow transitions via Sylius resource controller

The accept/reject routes use `applyStateMachineTransitionAction` on the Sylius resource controller (`setono_sylius_review.controller.store_review`). Sylius's `Workflow` adapter wraps Symfony's workflow registry, so this works with our `setono_sylius_review__store_review` graph.

**Alternative**: Custom controller actions. Rejected — the resource controller already handles this, including CSRF protection, events, and flash messages.

### 4. Dedicated admin form type excluding status

Create `StoreReviewAdminType` with fields: `title`, `comment`, `rating`, `storeReply`. Status is deliberately excluded — it's managed only through accept/reject grid actions. The form type is registered as a service and referenced in the routing config via `templates.form`.

### 5. Template structure under plugin views

Templates go in `src/Resources/views/admin/store_review/`:
- `_form.html.twig` — 12-wide main column (form fields) + 4-wide sidebar (channel + author info)
- `_channel.html.twig` — sidebar panel showing the channel name
- `_author.html.twig` — sidebar panel reusing Sylius's `@SyliusAdmin/Customer/_info.html.twig`

### 6. Config file organization

- Grid: Prepended programmatically in `SetonoSyliusReviewExtension::prepend()` as `sylius_grid` config (consistent with how the extension already prepends workflow and mailer config)
- Routing: `src/Resources/config/routes/admin.yaml` (already exists, add store review routes there)

## Risks / Trade-offs

- **Sylius version coupling** — Reusing `@SyliusAdmin/ProductReview/Label/Status` templates ties us to Sylius's template structure. If Sylius renames or restructures these in a future version, the grid breaks. → Mitigation: These templates have been stable across Sylius versions; low risk.
- **Workflow adapter compatibility** — We rely on Sylius's `Workflow` class to bridge `applyStateMachineTransitionAction` with Symfony Workflow. → Mitigation: This is a supported Sylius feature and the plugin already depends on it.
