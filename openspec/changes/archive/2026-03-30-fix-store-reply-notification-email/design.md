## Context

The store reply notification email has two bugs: hardcoded "The store" instead of the actual store name, and markdown store reply rendered as plain text. The admin uses a `MarkdownTextareaType` for store reply input, but there's no server-side markdown converter.

## Goals / Non-Goals

**Goals:**
- Fix translation strings to use the store/channel name
- Add `league/commonmark` for server-side markdown-to-HTML conversion
- Create a Twig filter to convert markdown in email templates

**Non-Goals:**
- Replacing the admin-side JS markdown preview (that uses `marked`)
- Adding markdown support to other templates beyond the email

## Decisions

### 1. Use twig/markdown-extra with league/commonmark

**Decision**: Add `twig/markdown-extra` and `league/commonmark` as composer dependencies. The `twig/extra-bundle` (already likely present via Symfony) auto-registers the `markdown_to_html` Twig filter. No custom Twig extension needed.

**Rationale**: The official `twig/markdown-extra` package provides a `markdown_to_html` filter out of the box. It uses `league/commonmark` under the hood. This avoids creating a custom Twig extension — just `composer require` and use `{{ value|markdown_to_html }}` in templates.

**Alternative considered**: Custom `setono_sylius_review_markdown_to_html` Twig filter. Rejected because the official filter already exists and is well-maintained.

### 3. Fix intro text by adding `%store%` placeholder

**Decision**: Update the translation strings to use `%store%` for the channel/store name. For store reviews, simplify to `%store% has replied to your review.` since the review subject IS the store. For product reviews, use `%store% has replied to your review of %name%.` The template will pass `channel.name` as the `%store%` parameter.

**Rationale**: Avoids the awkward redundancy of "The store has replied to your review of Fashion Web Store" for store reviews.

## Risks / Trade-offs

- **[Risk] Markdown injection in emails** → `league/commonmark` escapes HTML by default, so user-supplied markdown won't inject scripts. The output is safe for email HTML.
