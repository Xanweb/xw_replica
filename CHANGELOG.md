# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/).

## [Unreleased]

## [2.1.0] - 2026-11-13

### Added

- Full content swap: installing Replica can replace the site's content with sample pages, page types and images.
- Support link to GitHub Issues in the package description in the dashboard.

### Internal

- Removed unused `css/main.css`; the theme loads `main.less`.
- CI checks the sample content: file references, page templates, page type defaults and parallax layouts.
- CI moved to Node 24 actions and publishes the package zip on every run.

## [2.0.0] - 2024-06-03

### Added

First public release of the source code. Package files are identical to the 2.0.0 marketplace release, apart from line endings (LF).
