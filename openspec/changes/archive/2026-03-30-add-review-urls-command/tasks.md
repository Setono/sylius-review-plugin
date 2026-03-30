## 1. Command Implementation

- [x] 1.1 Create `src/Command/ReviewUrlsCommand.php` with `setono:sylius-review:urls` name and `--max` option (default 5)
- [x] 1.2 Implement channel iteration: fetch all enabled channels and loop through them
- [x] 1.3 For each channel, query fulfilled orders with tokens (limited by `--max`)
- [x] 1.4 Generate absolute review URLs using the router and output grouped by channel

## 2. Service Registration

- [x] 2.1 Register `ReviewUrlsCommand` in `src/Resources/config/services.xml` with required dependencies

## 3. Testing

- [x] 3.1 Add a unit test for the command verifying output format and `--max` option behavior
