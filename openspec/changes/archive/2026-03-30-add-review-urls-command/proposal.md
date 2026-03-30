## Why

During development and testing, developers need to quickly access review page URLs for fulfilled orders. Currently this requires manually querying the database for order tokens and constructing URLs by hand, which is tedious and error-prone.

## What Changes

- Add a new Symfony console command `setono:sylius-review:urls` that outputs review page URLs
- The command iterates over all enabled channels and finds fulfilled orders with review tokens
- Outputs up to N URLs per channel (default: 5), grouped by channel
- Uses the router to generate absolute URLs based on each channel's hostname

## Capabilities

### New Capabilities
- `review-urls-command`: A CLI command that generates and displays review page URLs for fulfilled orders, grouped by channel

### Modified Capabilities
(none)

## Impact

- New file: `src/Command/ReviewUrlsCommand.php`
- New service registration in `src/Resources/config/services.xml`
- Dependencies: Sylius Channel repository, Order repository, Symfony Router
