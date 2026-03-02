## Why

When customers submit reviews, they should be informed that their review will be publicly visible. This protects the store legally and sets clear expectations. This addresses [GitHub Issue #7](https://github.com/Setono/sylius-review-plugin/issues/7).

## What Changes

- Add a disclaimer text above the submit button on the review submission form
- The text informs customers that by submitting they agree their review will be publicly visible
- Disclaimer text is translatable

## Capabilities

### New Capabilities
- `review-disclaimer`: An informational disclaimer text on the review submission form informing customers their review will be publicly visible

### Modified Capabilities
_(none)_

## Impact

- **Templates**: `shop/review/index.html.twig` renders disclaimer text above the submit button
- **Translations**: New translation key for disclaimer text in `messages`
- **Tests**: Functional test verifying the disclaimer text is rendered
