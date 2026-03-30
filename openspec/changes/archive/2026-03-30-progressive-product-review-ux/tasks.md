## 1. Template Changes

- [x] 1.1 Wrap the comment field in `_product_review_item.html.twig` in a `<div>` with a predictable ID and `style="display: none"`
- [x] 1.2 Add `data-comment-target` attribute to each product review `.star.rating` div pointing to the comment wrapper ID

## 2. JavaScript Changes

- [x] 2.1 Add `clearable: true` to the `.rating()` initialization options in `index.html.twig`
- [x] 2.2 Extend the `onRate` callback to show the comment wrapper when `value > 0` and hide + clear it when `value === 0`
- [x] 2.3 Handle the unrate case: uncheck all radio buttons when `value === 0`

## 3. Translation Changes

- [x] 3.1 Update `product_reviews_description` in `messages.en.yaml` to signal optionality

## 4. Verification

- [x] 4.1 Test in browser: new review page shows hidden comment fields, rating a product reveals comment, clearing rating hides and clears comment
- [x] 4.2 Test in browser: editing an existing review shows comment fields for pre-rated products on page load
