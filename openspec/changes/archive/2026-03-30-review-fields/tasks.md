## 1. Remove title from form types

- [x] 1.1 Remove `title` field from `StoreReviewType::buildForm()`
- [x] 1.2 Remove `title` field from `ProductReviewType::buildForm()`
- [x] 1.3 Remove `title` field from `StoreReviewAdminType::buildForm()`
- [x] 1.4 Set `required: true` on `comment` field in `StoreReviewType`
- [x] 1.5 Set `required: true` on `comment` field in `StoreReviewAdminType`

## 2. Remove title validation

- [x] 2.1 Remove `title` property constraints from `src/Resources/config/validation/StoreReview.xml`

## 3. Update templates

- [x] 3.1 Remove `form_row(form.storeReview.title)` from `shop/review/_store_review.html.twig`
- [x] 3.2 Remove `form_row(form.productReviews[index].title)` from `shop/review/_product_review_item.html.twig`
- [x] 3.3 Remove `form_row(form.title)` from `admin/store_review/_form.html.twig`
- [x] 3.4 Remove `review.title` display from `bundles/SyliusShopBundle/ProductReview/_single.html.twig`
- [x] 3.5 Remove title display from `email/store_reply_notification.html.twig`

## 4. Update tests

- [x] 4.1 Update `StoreReviewType` unit test: assert no `title` child, assert `comment` is required
- [x] 4.2 Update `ProductReviewType` unit test: assert no `title` child
- [x] 4.3 Update `ReviewController` functional test: remove title from form submission data
- [x] 4.4 Update admin store review CRUD functional test: remove title from form data and assertions
