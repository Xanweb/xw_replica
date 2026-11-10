import sys
import tempfile
import unittest
import zipfile
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent.parent))

import check_package  # noqa: E402

TOP = "xw_replica/"


def complete_entries():
    entries = {}
    for p in check_package.MUST_SHIP_FILES:
        entries[TOP + p] = b"x"
    entries[TOP + "content.xml"] = b"<concrete5-cif/>"
    for d in check_package.MUST_SHIP_NONEMPTY_DIRS:
        entries[TOP + d + "file.txt"] = b"x"
    return entries


class ZipTestCase(unittest.TestCase):
    def setUp(self):
        self._tmp = tempfile.TemporaryDirectory()
        self.addCleanup(self._tmp.cleanup)

    def make_zip(self, entries):
        path = Path(self._tmp.name) / "t.zip"
        with zipfile.ZipFile(path, "w") as z:
            for name, data in entries.items():
                z.writestr(name, data)
        return path

    def problems(self, entries, **kw):
        return check_package.check(self.make_zip(entries), **kw)


class CheckPackageTest(ZipTestCase):

    def test_complete_package_ok(self):
        self.assertEqual(self.problems(complete_entries()), [])

    def test_missing_theme_css_build_fails(self):
        e = {k: v for k, v in complete_entries().items()
             if not k.startswith(TOP + "themes/xw_replica/css/build/")}
        self.assertIn("empty dir: themes/xw_replica/css/build/", self.problems(e))

    def test_missing_main_less_fails(self):
        e = complete_entries()
        del e[TOP + "themes/xw_replica/css/main.less"]
        self.assertIn("missing: themes/xw_replica/css/main.less", self.problems(e))

    def test_root_build_shipped_fails(self):
        e = complete_entries()
        e[TOP + "build/package.json"] = b"{}"
        self.assertIn("dev-only file shipped: build/package.json", self.problems(e))

    def test_agents_md_dev_only(self):
        for name in ("AGENTS.md", "CLAUDE.md"):
            e = complete_entries()
            e[TOP + name] = b"x"
            self.assertIn("dev-only file shipped: " + name, self.problems(e))

    def test_unexpected_top_level_entry_fails(self):
        e = complete_entries()
        e[TOP + "composer.json"] = b"{}"
        e[TOP + "stray/file.txt"] = b"x"
        got = self.problems(e)
        self.assertIn("unexpected top-level entry: composer.json", got)
        self.assertIn("unexpected top-level entry: stray", got)

    def test_theme_css_build_is_not_dev_only(self):
        e = complete_entries()
        e[TOP + "themes/xw_replica/css/build/x.less"] = b"x"
        self.assertEqual(self.problems(e), [])

    def test_second_top_level_fails(self):
        e = complete_entries()
        e["other/file.txt"] = b"x"
        self.assertIn("bad top-level entry: other", self.problems(e))

    def test_short_tag_in_block_template_php_fails(self):
        e = complete_entries()
        p = "themes/xw_replica/blocks/feature/templates/hover_description/view.php"
        e[TOP + p] = b"<div><?=h($x)?></div>"
        self.assertIn("short echo tag in " + p, self.problems(e))

    def test_short_tag_in_js_and_less_ignored(self):
        e = complete_entries()
        e[TOP + "themes/xw_replica/js/a.js"] = b"var s = '<?=';"
        e[TOP + "themes/xw_replica/css/a.less"] = b"/* <?= */"
        self.assertEqual(self.problems(e), [])

    def test_notice_and_changelog_required(self):
        for name in ("NOTICE", "CHANGELOG.md"):
            e = complete_entries()
            self.assertIn(TOP + name, e)
            del e[TOP + name]
            self.assertIn("missing: " + name, self.problems(e))

    def test_extra_required(self):
        self.assertIn("missing: EXTRA.txt",
                      self.problems(complete_entries(), extra_required=["EXTRA.txt"]))


def cx(refs=(), templates=()):
    body = "".join('<a href="{ccm:export:file:%s}"/>' % r for r in refs)
    pts = "".join('<pagetemplate handle="%s" name="N" icon="x" package="xw_replica"/>' % t
                  for t in templates)
    return ("<concrete5-cif><pagetemplates>%s</pagetemplates><pages>%s</pages>"
            "</concrete5-cif>" % (pts, body)).encode()


class CheckContentTest(ZipTestCase):
    def content_entries(self, xml, extra=None):
        e = complete_entries()
        e[TOP + "content.xml"] = xml
        for k, v in (extra or {}).items():
            e[TOP + k] = v
        return e

    def test_content_ok(self):
        e = self.content_entries(
            cx(["123456789012:a.png"], ["full"]),
            {"content_files/123456789012_a.png": b"x", "themes/xw_replica/full.php": b"x"})
        self.assertEqual(self.problems(e), [])

    def test_missing_content_file_fails(self):
        e = self.content_entries(cx(["123456789012:demo_logo.png"]))
        self.assertIn("missing content file for reference: 123456789012:demo_logo.png",
                      self.problems(e))

    def test_unused_content_file_fails(self):
        e = self.content_entries(
            cx(["123456789012:a.png"]),
            {"content_files/123456789012_a.png": b"x", "content_files/123456789012_b.png": b"x"})
        got = self.problems(e)
        self.assertIn("unused content file: content_files/123456789012_b.png", got)
        self.assertNotIn("unused content file: content_files/123456789012_a.png", got)

    def test_template_without_layout_fails(self):
        e = self.content_entries(cx(templates=["home"]))
        self.assertIn("page template without layout: home", self.problems(e))

    def test_nested_pagetemplate_ignored(self):
        xml = (b"<concrete5-cif><pagetemplates/><pagetypes><pagetype><pagetemplate "
               b'handle="nested"/></pagetype></pagetypes></concrete5-cif>')
        self.assertEqual(self.problems(self.content_entries(xml)), [])

    def test_content_xml_not_well_formed_fails(self):
        e = self.content_entries(b"<concrete5-cif><pages>")
        got = self.problems(e)
        self.assertEqual(len(got), 1)
        self.assertTrue(got[0].startswith("content.xml not well-formed:"))
        self.assertIn("line", got[0])

    def test_no_content_xml_skips_content_checks(self):
        e = complete_entries()
        del e[TOP + "content.xml"]
        e[TOP + "content_files/123456789012_a.png"] = b"x"
        self.assertEqual(self.problems(e), ["missing: content.xml"])

    def test_content_top_level_allowed(self):
        got = self.problems(self.content_entries(cx()))
        self.assertFalse([p for p in got if "unexpected top-level entry" in p])

    def test_unprefixed_reference_fails(self):
        e = self.content_entries(cx(["logo.png"]))
        self.assertIn("unprefixed content file reference: {ccm:export:file:logo.png}",
                      self.problems(e))

    def test_content_files_subfolder_fails(self):
        e = self.content_entries(cx(), {"content_files/sub/123456789012_a.png": b"x"})
        got = self.problems(e)
        self.assertIn("unexpected path in content_files: content_files/sub/123456789012_a.png", got)
        self.assertFalse([p for p in got if p.startswith("unused")])


if __name__ == "__main__":
    unittest.main()
