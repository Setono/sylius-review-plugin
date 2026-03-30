## Why

The review page (GitHub issue #12) presents all product reviews with fully expanded rating + comment fields, giving no indication that reviewing individual products is optional. Customers feel pressured to rate every product or don't understand they can skip some. Products without a rating are silently discarded on submission, but the UI doesn't communicate this. The result is a cluttered form that discourages engagement.

## What Changes

- Hide product review comment fields by default; reveal them only after the customer selects a star rating for that product
- If the customer clears a rating (clicks the active star to deselect), hide the comment field again and clear its value
- For returning customers editing an existing review (pre-set rating > 0), show the comment field immediately on page load
- Update the `product_reviews_description` translation to signal that product reviews are optional (e.g., "Rate the products you'd like to review")

## Capabilities

### New Capabilities
- `progressive-product-review`: Progressive disclosure behavior for product review comment fields — hidden until a rating is selected

### Modified Capabilities
- `star-rating-widget`: The `onRate` callback needs to drive comment field visibility, and must handle the unrate (rating → 0) case

## Impact

- **Templates**: `_product_review_item.html.twig` — comment field wrapper needs a targetable CSS class/ID and default `display: none`
- **JavaScript**: Inline script in `index.html.twig` — extend `onRate` callback to show/hide/clear comment fields
- **Translations**: `messages.en.yaml` — update `product_reviews_description` key
- **No backend changes**: Form types, controller, and validation are unaffected
