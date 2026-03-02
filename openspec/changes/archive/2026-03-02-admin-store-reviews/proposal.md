## Why

Store reviews exist as a resource in the plugin but have no admin UI. Product reviews are manageable through the Sylius admin sidebar (list, view, accept/reject, delete), but store reviews are invisible to administrators. Admins need the same level of visibility and moderation control over store reviews.

## What Changes

- Add a Sylius grid configuration for store reviews (list view with date, title, rating, status, channel, customer columns)
- Add admin routing for store review CRUD (index, update, delete) and workflow transitions (accept, reject)
- Add an event subscriber to insert a "Store reviews" menu item in the admin sidebar under Marketing, next to "Product reviews"
- Add an admin form type for store reviews exposing title, comment, rating, and store reply fields (not status — status is managed via accept/reject workflow actions)
- Add admin templates: form layout with sidebar panels for channel and customer info

## Capabilities

### New Capabilities
- `admin-store-review-grid`: Grid configuration, admin routing, menu integration, and templates for listing and managing store reviews in the admin panel

### Modified Capabilities

None — this is purely additive UI on top of existing store review infrastructure.

## Impact

- New files in `src/Resources/config/` for grid and routing configuration
- New event subscriber in `src/EventSubscriber/` for admin menu
- New admin form type in `src/Form/Type/Admin/`
- New templates in `src/Resources/views/admin/StoreReview/`
- New translation keys for menu label and subheader
- Service registration in `services.xml` for the event subscriber and form type
