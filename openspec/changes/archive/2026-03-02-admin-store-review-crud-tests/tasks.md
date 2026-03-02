## 1. Test Class Setup

- [x] 1.1 Create `tests/Functional/Controller/Admin/StoreReviewControllerTest.php` with `WebTestCase` base class, `setUp()` that creates the client, fetches EntityManager, loads fixture Channel/Customer, and authenticates via `loginUser()`
- [x] 1.2 Add a private `createStoreReview()` helper method that creates and persists a StoreReview entity with rating, title, comment, reviewSubject (Channel), and author (Customer)

## 2. Index Tests

- [x] 2.1 Test `it_lists_store_reviews` — create a store review, GET `/admin/store-reviews/`, assert 200 and review title visible
- [x] 2.2 Test `it_shows_empty_index_when_no_reviews_exist` — GET `/admin/store-reviews/` with no reviews, assert 200

## 3. Update Tests

- [x] 3.1 Test `it_shows_the_update_form` — create a store review, GET `/admin/store-reviews/{id}/edit`, assert 200 and form exists
- [x] 3.2 Test `it_updates_a_store_review` — submit the update form with modified title/comment, assert redirect and persisted values
- [x] 3.3 Test `it_adds_a_store_reply` — submit the update form with a store reply value, assert the reply is persisted on the entity

## 4. Workflow Transition Tests

- [x] 4.1 Test `it_accepts_a_store_review` — PUT `/admin/store-review/{id}/accept` with CSRF token, assert redirect and status is "accepted"
- [x] 4.2 Test `it_rejects_a_store_review` — PUT `/admin/store-review/{id}/reject` with CSRF token, assert redirect and status is "rejected"

## 5. Delete Tests

- [x] 5.1 Test `it_deletes_a_store_review` — DELETE `/admin/store-reviews/{id}` with CSRF token, assert redirect and entity removed
- [x] 5.2 Test `it_bulk_deletes_store_reviews` — DELETE `/admin/store-reviews/bulk-delete` with multiple IDs and CSRF token, assert redirect and entities removed

## 6. Authentication Guard Test

- [x] 6.1 Test `it_denies_access_for_unauthenticated_users` — GET `/admin/store-reviews/` without login, assert redirect to login page
