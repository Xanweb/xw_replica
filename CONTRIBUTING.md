# Contributing to Replica

Thanks for helping to improve Replica. This guide explains how to set up, what to check, and how changes get merged.

Using an AI tool? Point it to [AGENTS.md](AGENTS.md).

## Development setup

1. Set up a ConcreteCMS 9.3+ site (PHP 7.4 to 8.4).
2. Clone your fork into the site's `packages` folder:

   ```
   git clone https://github.com/<you>/xw_replica.git packages/xw_replica
   ```

3. Install the package from Dashboard, Extend ConcreteCMS.

## Where the CSS comes from

- `themes/xw_replica/css/common.css` is generated from `build/assets/scss`. Do not edit it by hand. Rebuild it with:

  ```
  cd build
  npm ci
  npm run production
  ```

  Use the Node version in `build/.nvmrc`. The build currently needs Node 20 with npm 10 (see build/.nvmrc). npm 11+ refuses the git dependency `bootstrap-tourist`, which `@concretecms/bedrock` pulls in.
  The `common.css` build check is not enforced yet; mention in your PR if you rebuilt it.
- `themes/xw_replica/css/bootstrap-modified.css` is frozen. Do not change it.
- `themes/xw_replica/css/main.less`, `themes/xw_replica/css/build/**` and `themes/xw_replica/css/presets/` are compiled by ConcreteCMS on the site, so edit the LESS files.

## No short echo tags

Do not use `<?=` in any PHP file. The ConcreteCMS marketplace review rejects it, and CI fails on it. Use `<?php echo ...; ?>` instead.

## Pull requests

1. Fork the repository and create a branch.
2. Open a pull request against `main`.
3. CI must pass.
4. A Xanweb maintainer reviews it.
5. Pull requests are merged with squash merge.

## Changelog

Maintainers curate `CHANGELOG.md`. For a user-visible change you may add a line under `Unreleased`.

## Running the checks locally

From the repository root (Python 3.8+):

```
python -m unittest discover -s .github/scripts/tests
python .github/scripts/export_package.py HEAD dist/candidate.zip
python .github/scripts/check_package.py dist/candidate.zip
```

The last two commands export the release zip and check its contents.

## Licensing

By contributing, you agree that your contributions are licensed under the MIT License of this project, and that Xanweb may reuse them in its other products, including Replica Pro.
