## 1. Grid and Routing Configuration

- [x] 1.1 Add grid config in `SetonoSyliusReviewExtension::prepend()` by prepending `sylius_grid` config with fields (date, title, rating, status, reviewSubject, author), filters (title, status), and actions (update, accept, reject, delete, bulk delete)
- [x] 1.2 Add store review resource routes and accept/reject transition routes to `src/Resources/config/routes/admin.yaml`

## 2. Admin Form Type

- [x] 2.1 Create `StoreReviewAdminType` form type in `src/Form/Type/Admin/` with fields: title, comment, rating, storeReply (no status field)
- [x] 2.2 Register the form type as a service in `src/Resources/config/services.xml`

## 3. Admin Templates

- [x] 3.1 Create `src/Resources/views/admin/store_review/_form.html.twig` with 12-wide main column (form fields) and 4-wide sidebar
- [x] 3.2 Create `src/Resources/views/admin/store_review/_channel.html.twig` sidebar panel showing channel name
- [x] 3.3 Create `src/Resources/views/admin/store_review/_author.html.twig` sidebar panel reusing Sylius's customer info template

## 4. Admin Menu Event Subscriber

- [x] 4.1 Create `AdminMenuSubscriber` in `src/EventSubscriber/` listening to `sylius.menu.admin.main`, adding store reviews under marketing
- [x] 4.2 Register the event subscriber as a service in `src/Resources/config/services.xml`

## 5. Translations

- [x] 5.1 Add translation keys for the menu label and grid subheader to `src/Resources/translations/messages.en.yaml`

## 6. Verification

- [x] 6.1 Run `composer analyse` to verify PHPStan passes
- [x] 6.2 Run `composer check-style` to verify code style
- [x] 6.3 Run `vendor/bin/phpunit --testsuite=Unit` to verify existing tests still pass
