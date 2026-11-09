import sys
import tempfile
import unittest
import zipfile
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent.parent))

import compare_zips  # noqa: E402


class CompareZipsTest(unittest.TestCase):
    def setUp(self):
        self._tmp = tempfile.TemporaryDirectory()
        self.addCleanup(self._tmp.cleanup)
        self.n = 0

    def make_zip(self, entries):
        self.n += 1
        path = Path(self._tmp.name) / ("z%d.zip" % self.n)
        with zipfile.ZipFile(path, "w") as z:
            for name, data in entries.items():
                z.writestr(name, data)
        return path

    def cmp(self, a, b, **kw):
        return compare_zips.compare(self.make_zip(a), self.make_zip(b), **kw)

    def test_identical_ok(self):
        e = {"xw_replica/a.txt": b"a\n", "xw_replica/b/c.php": b"<?php\n"}
        self.assertEqual(self.cmp(e, dict(e)), [])

    def test_crlf_text_equal(self):
        self.assertEqual(self.cmp({"xw_replica/a.txt": b"a\r\nb\r\n"},
                                  {"xw_replica/a.txt": b"a\nb\n"}), [])

    def test_binary_crlf_bytes_not_normalized(self):
        self.assertEqual(
            self.cmp({"xw_replica/icon.png": b"\x89PNG\0\r\n"},
                     {"xw_replica/icon.png": b"\x89PNG\0\n"}),
            ["differs: icon.png"])

    def test_allowed_skips(self):
        a = {"xw_replica/README.md": b"one", "xw_replica/NOTICE": b"n",
             "xw_replica/x": b"x"}
        b = {"xw_replica/README.md": b"two", "xw_replica/x": b"x"}
        self.assertEqual(self.cmp(a, b, allowed={"README.md", "NOTICE"}), [])

    def test_only_in_b(self):
        self.assertEqual(self.cmp({"xw_replica/a": b"a"},
                                  {"xw_replica/a": b"a", "xw_replica/b": b"b"}),
                         ["only in B: b"])

    def test_only_in_a_and_differs(self):
        self.assertEqual(
            sorted(self.cmp({"xw_replica/a": b"a", "xw_replica/c": b"1"},
                            {"xw_replica/c": b"2"})),
            ["differs: c", "only in A: a"])

    def test_dir_entries_ignored(self):
        a = {"xw_replica/": b"", "xw_replica/blocks/": b"",
             "xw_replica/blocks/f.php": b"f"}
        b = {"xw_replica/blocks/f.php": b"f"}
        self.assertEqual(self.cmp(a, b), [])

    def test_file_outside_top_folder_reported(self):
        a = {"xw_replica/a": b"a", "README.md": b"r"}
        b = {"xw_replica/a": b"a", "stray.txt": b"s"}
        self.assertEqual(
            sorted(self.cmp(a, b)),
            ["outside top folder (A): README.md",
             "outside top folder (B): stray.txt"])


if __name__ == "__main__":
    unittest.main()
