## 1. StoreReviewTypeTest

- [x] 1.1 Create `tests/Unit/Form/Type/StoreReviewTypeTest.php` extending `TypeTestCase` with `ProphecyTrait`, `setUp()` for Prophecy mocks, and `getExtensions()` registering `StoreReviewType` via `PreloadedExtension`
- [x] 1.2 Add test `it_has_rating_title_and_comment_fields` — create form with `order` option, assert children `rating`, `title`, `comment` exist
- [x] 1.3 Add test `it_has_expanded_rating_choices_from_one_to_five` — inspect the `rating` field config for expanded, multiple, choices
- [x] 1.4 Add test `it_sets_order_on_store_review_after_submit` — submit form, assert `StoreReview::getOrder()` returns the order
- [x] 1.5 Add test `it_sets_review_subject_from_order_channel` — mock order with `ChannelInterface` channel, submit, assert `getReviewSubject()` returns the channel
- [x] 1.6 Add test `it_sets_author_from_order_customer` — mock order with `CustomerInterface` customer, submit, assert `getAuthor()` returns the customer
- [x] 1.7 Add test `it_does_not_set_review_subject_when_channel_is_not_plugin_channel_interface` — mock order with base Sylius `ChannelInterface` (not plugin's), submit, assert review subject is null
- [x] 1.8 Add test `it_requires_order_option` — create form without `order` option, assert exception is thrown
- [x] 1.9 Add test `it_has_correct_block_prefix` — assert block prefix is `setono_sylius_review_store_review`

## 2. ProductReviewTypeTest

- [x] 2.1 Create `tests/Unit/Form/Type/ProductReviewTypeTest.php` extending `TypeTestCase` with `getExtensions()` registering `ProductReviewType` via `PreloadedExtension`
- [x] 2.2 Add test `it_has_rating_title_and_comment_fields` — create form, assert children `rating`, `title`, `comment` exist
- [x] 2.3 Add test `it_has_expanded_rating_choices_from_one_to_five` — inspect the `rating` field config
- [x] 2.4 Add test `it_maps_submitted_data_to_entity` — submit form with rating/title/comment data, assert values are set on the data object
- [x] 2.5 Add test `it_has_correct_block_prefix` — assert block prefix is `setono_sylius_review_product_review`

## 3. Verify

- [x] 3.1 Run `vendor/bin/phpunit tests/Unit/Form/Type/StoreReviewTypeTest.php` — all tests pass
- [x] 3.2 Run `vendor/bin/phpunit tests/Unit/Form/Type/ProductReviewTypeTest.php` — all tests pass
- [x] 3.3 Run `composer analyse` — no new PHPStan errors
