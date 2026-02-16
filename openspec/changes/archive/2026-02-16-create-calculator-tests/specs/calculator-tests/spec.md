## ADDED Requirements

### Requirement: AverageRatingCalculator computes correct average from database
The functional test SHALL verify that the calculator produces the correct average rating using a real database query against Sylius entities.

#### Scenario: Product with accepted reviews returns correct average
- **WHEN** a product has multiple accepted reviews with different ratings in the database
- **THEN** the calculator returns the arithmetic mean of those ratings as a float

#### Scenario: Product with no accepted reviews returns zero
- **WHEN** a product has no accepted reviews (or only non-accepted reviews)
- **THEN** the calculator returns `0.0`

#### Scenario: Only accepted reviews are included in calculation
- **WHEN** a product has reviews in different statuses (accepted, new, rejected)
- **THEN** only reviews with status `accepted` are included in the average

### Requirement: CachedAverageRatingCalculator falls back when reviewable is not a ResourceInterface
The unit test SHALL verify that when the reviewable does not implement `ResourceInterface`, the calculator delegates to the decorated calculator without caching.

#### Scenario: Non-resource reviewable
- **WHEN** the reviewable does not implement `ResourceInterface`
- **THEN** the decorated calculator's `calculate()` method is called directly (no cache interaction)

### Requirement: CachedAverageRatingCalculator falls back when reviewable has no ID
The unit test SHALL verify that when the reviewable's `getId()` returns null, the calculator delegates to the decorated calculator without caching.

#### Scenario: Reviewable with null ID
- **WHEN** the reviewable implements `ResourceInterface` but `getId()` returns null
- **THEN** the decorated calculator's `calculate()` method is called directly (no cache interaction)

### Requirement: CachedAverageRatingCalculator caches results with correct key and TTL
The unit test SHALL verify that for a valid reviewable, the calculator uses the Symfony cache with the correct key format and configured TTL.

#### Scenario: Cache miss computes and stores result
- **WHEN** the reviewable implements `ResourceInterface` with a non-null ID
- **THEN** `CacheInterface::get()` is called with key format `setono_sylius_review_avg_rating_{class}_{id}` (backslashes replaced with underscores)
- **AND** the cache callback sets expiration to the configured `cacheLifetime`
- **AND** the decorated calculator's result is returned

#### Scenario: Custom cache lifetime is respected
- **WHEN** the calculator is constructed with a custom `cacheLifetime` value
- **THEN** the cache item's `expiresAfter()` is called with that custom value
