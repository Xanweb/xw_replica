import sys
import unittest
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent.parent))

import check_version  # noqa: E402

SRC = "protected $pkgVersion = '2.0.0';"


class CheckVersionTest(unittest.TestCase):
    def test_tag_ok(self):
        self.assertEqual(check_version.check("v2.0.0", SRC), [])

    def test_tag_without_v_fails(self):
        self.assertEqual(len(check_version.check("2.0.0", SRC)), 1)

    def test_tag_rc_fails(self):
        self.assertEqual(len(check_version.check("v2.0.0-rc1", SRC)), 1)

    def test_tag_short_fails(self):
        self.assertEqual(len(check_version.check("v2.0", SRC)), 1)

    def test_message(self):
        self.assertEqual(
            check_version.check("v2.0", SRC),
            ["tag v2.0 does not match pkgVersion 2.0.0 (expected v2.0.0)"])

    def test_double_quotes_parse(self):
        self.assertEqual(
            check_version.read_pkg_version('protected $pkgVersion = "2.0.0";'),
            "2.0.0")

    def test_missing_version_raises(self):
        with self.assertRaises(ValueError):
            check_version.read_pkg_version("<?php class C {}")


if __name__ == "__main__":
    unittest.main()
