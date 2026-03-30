## 1. Add Dependencies

- [x] 1.1 Run `composer require twig/markdown-extra league/commonmark`

## 2. Fix Translation Strings

- [x] 2.1 Update `store_review_intro` in `messages.en.yaml` to use `%store%` instead of hardcoded "The store" (e.g., `%store% has replied to your review.`)
- [x] 2.2 Update `product_review_intro` in `messages.en.yaml` to use `%store%` (e.g., `%store% has replied to your review of %name%.`)

## 3. Update Email Template

- [x] 3.1 Pass `channel.name` as the `%store%` parameter in the `trans()` calls for both intro translations
- [x] 3.2 Replace `{{ review.storeReply }}` with `{{ review.storeReply|markdown_to_html }}` to render markdown as HTML
- [x] 3.3 Remove the `<blockquote>` wrapper since `markdown_to_html` will produce its own HTML structure

## 4. Verify

- [x] 4.1 Test via the mail tester: send the store reply notification email and verify the store name appears in the intro and the markdown reply is rendered as HTML
