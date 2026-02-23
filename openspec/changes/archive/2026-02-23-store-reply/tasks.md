## 1. Shared Interface and Trait

- [x] 1.1 Create `src/Model/StoreReplyInterface.php` with `getStoreReply(): ?string`, `setStoreReply(?string): void`, `getStoreRepliedAt(): ?DateTimeInterface`, `setStoreRepliedAt(?DateTimeInterface): void`
- [x] 1.2 Create `src/Model/StoreReplyTrait.php` with `storeReply` and `storeRepliedAt` properties, getters/setters, and auto-set logic (setting reply auto-sets timestamp, clearing reply clears timestamp). Include `#[ORM\Column]` attributes.

## 2. StoreReview Entity Extension

- [x] 2.1 Update `StoreReviewInterface` to extend `StoreReplyInterface`
- [x] 2.2 Update `StoreReview` to use `StoreReplyTrait`
- [x] 2.3 Update `StoreReview.orm.xml` to add `store_reply` (text, nullable) and `store_replied_at` (datetime, nullable) field mappings

## 3. ProductReview Extension (Channel Pattern)

- [x] 3.1 Create `src/Model/ProductReviewInterface.php` extending Sylius `ProductReviewInterface` and `StoreReplyInterface`
- [x] 3.2 Create `src/Model/ProductReviewTrait.php` that uses `StoreReplyTrait`
- [x] 3.3 Create `tests/Application/Entity/ProductReview.php` extending Sylius ProductReview, using `ProductReviewTrait`, implementing `ProductReviewInterface`
- [x] 3.4 Configure test application to use the custom ProductReview entity class

## 4. Admin Form Extension

- [x] 4.1 Create a Symfony form extension that adds a `storeReply` textarea field to the Sylius review admin forms
- [x] 4.2 Register the form extension as a service

## 5. Shop Template

- [x] 5.1 Update the shop review template to display store reply and replied-at date when present

## 6. Documentation

- [x] 6.1 Update README with ProductReview entity extension instructions (following Channel pattern documentation)

## 7. Verification

- [x] 7.1 Run PHPStan to confirm no static analysis errors
- [x] 7.2 Run unit tests to confirm all pass
- [x] 7.3 Run code style checks
