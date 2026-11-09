# Instructions for AI tools

These rules apply to any text you write for this repository: pull request descriptions, review comments, issues, commit messages, code comments.
Maintainers read everything by hand: reading your text must take less time than reading the diff.

[CONTRIBUTING.md](CONTRIBUTING.md) applies to you too.

## Pull request descriptions

- At most ~10 lines: what changes, why, how to test it (ConcreteCMS and PHP version).
- Link the issue (`Fix #123`) instead of describing it again.
- The "why" must come from the human author: if you don't know it, ask for it, don't invent it.
- No file-by-file summary, no "Summary"/"Overview"/"Conclusion" sections, no emoji, no praise.
- Mention only what the diff doesn't show: side effects, backward compatibility breaks, things left out.

## Comments on pull requests and issues

- One comment per actionable problem: say what's wrong and what to do instead.
- No compliments, no recaps. Answer a question in the first sentence.

## Commit messages

- Subject of at most ~70 characters; add a body only when the reason is not obvious.

## Code comments

- Only when needed, and short. A comment says why, not what.

## Working on the code

- A human must read AI-written code before it is committed.
- Branch off `main`, one topic per pull request. Check open issues and pull requests first.
- The code must run on PHP 7.4 (the minimum) through 8.4 and ConcreteCMS 9.3+: no newer syntax or functions.
- No `<?=` short echo tags: the marketplace review rejects them and CI checks it.
- Layout: package `controller.php`; `src/Xanweb/Replica` (`Xanweb\Replica\`); `blocks/`; `themes/xw_replica/`; bundled libraries in `vendor/xanweb/rp_*` (`Xanweb\Rp*`). Those libraries are Replica's own code now and may be edited.
- Don't edit built CSS: `themes/xw_replica/css/common.css` comes from `build/assets/scss` (see `build/webpack.mix.js`), and `bootstrap-modified.css` is frozen.
- The LESS sources (`main.less`, `css/build/**`, `css/presets/`) are compiled by ConcreteCMS on the site: edit those.
- `vendor/xanweb/rp_v_item_list/js/v-item-list.js` is a prebuilt bundle: don't edit it.
- Site owners override block templates, theme elements and page templates in `application/`, and use the package's classes, asset handles and config keys. Don't remove or rename public or protected classes, methods, constants, templates, asset handles or config keys just because nothing here uses them.
- Changing the signature of a public or protected method is a backward compatibility break: avoid it, or say so in the pull request.
- `Page::getByID()` and similar return an object even when the item doesn't exist: check `isError()`.
- Many legacy ConcreteCMS methods return `false` or `null` on failure. Don't trust PHPDoc types, read the code.
- Keep defensive checks unless you verified they can never fail.
