# Release checklist

## Manual acceptance

- [ ] Install the exported zip on a clean ConcreteCMS 9.3+ site.
- [ ] Render each page template.
- [ ] Switch presets and save a customizer change.
- [ ] Add and edit both blocks (Quick Tabs and Simple Accordion).
- [ ] Upgrade a populated site that runs the previous release.
- [ ] Extract the release zip (the git archive export), run the ConcreteCMS CLI packing command on that extracted package, and compare the result with `compare_zips.py` against the release zip (EOL-aware, no allowed differences).

## Release steps

1. Bump `pkgVersion` in `controller.php` and add the version section to `CHANGELOG.md`, through a pull request.
2. Go through the manual acceptance list above.
3. Tag the merge commit on `main` as `vX.Y.Z`.
4. The release workflow builds the zip and publishes the GitHub release. Check that it succeeds.
5. Upload the zip to the ConcreteCMS marketplace manually.
