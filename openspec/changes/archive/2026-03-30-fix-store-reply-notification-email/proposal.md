## Why

The store reply notification email has two issues: (1) the intro text says "The store has replied..." with a hardcoded "The store" instead of using the actual store/channel name, and (2) the store reply content is written as markdown (the admin form uses a markdown textarea) but rendered as plain text in the email, so lists and formatting are lost.

## What Changes

- Update translation strings for `store_review_intro` and `product_review_intro` to use the store name instead of hardcoded "The store"
- Pass `channel` (already available) as a `%store%` placeholder in the email template
- Add `twig/markdown-extra` and `league/commonmark` as dependencies for the built-in `markdown_to_html` Twig filter
- Use the filter in the email template to render `review.storeReply` as HTML instead of raw text

## Capabilities

### New Capabilities
_(none — using the built-in `markdown_to_html` filter from `twig/markdown-extra`)_

### Modified Capabilities
- `store-reply-notification`: Updated translation strings and template to use store name and render markdown store reply as HTML

## Impact

- **New dependencies**: `twig/markdown-extra`, `league/commonmark` (composer require)
- **Modified files**: `store_reply_notification.html.twig`, `messages.en.yaml`
