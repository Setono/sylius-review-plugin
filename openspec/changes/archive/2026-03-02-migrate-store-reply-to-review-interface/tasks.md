## 1. Merge interface and trait

- [x] 1.1 Add store reply methods to `ReviewInterface` (`getStoreReply`, `setStoreReply`, `getStoreRepliedAt`, `setStoreRepliedAt`)
- [x] 1.2 Add store reply properties and method implementations from `StoreReplyTrait` into `ReviewTrait` (with ORM attribute mapping)
- [x] 1.3 Remove `StoreReplyInterface` from the `extends` clause of `StoreReviewInterface` and `ProductReviewInterface`
- [x] 1.4 Remove `use StoreReplyTrait` from `StoreReview` and `ProductReviewTrait`

## 2. Remove old files

- [x] 2.1 Delete `src/Model/StoreReplyInterface.php`
- [x] 2.2 Delete `src/Model/StoreReplyTrait.php`

## 3. Doctrine mapping

- [x] 3.1 Keep `storeReply` and `storeRepliedAt` fields in `StoreReview.orm.xml` (XML mapping takes precedence for StoreReview; attribute mapping only applies to ProductReview)

## 4. Plugin class and service config

- [x] 4.1 Remove any `StoreReplyInterface` references from `SetonoSyliusReviewPlugin` model mapping (none found)

## 5. Tests

- [x] 5.1 Migrate `StoreReplyTraitTest` to test the store reply behavior through `ReviewTrait` instead (test already uses StoreReview which now gets behavior via ReviewTrait)
- [x] 5.2 Run full test suite to verify nothing breaks (142 tests, 355 assertions — all pass)
