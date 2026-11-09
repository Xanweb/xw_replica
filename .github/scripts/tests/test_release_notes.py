import sys
import unittest
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent.parent))

import release_notes  # noqa: E402

CL = """# Changelog

## [Unreleased]

## [2.1.0] - 2025-01-01

### Added

- Newer thing.

## [2.0.0] - 2024-06-03

### Added

First public release.
"""


class ReleaseNotesTest(unittest.TestCase):
    def test_section_extracted(self):
        self.assertEqual(release_notes.section(CL, "2.0.0"),
                         "### Added\n\nFirst public release.")
        self.assertEqual(release_notes.section(CL, "2.1.0"),
                         "### Added\n\n- Newer thing.")

    def test_missing_section_raises(self):
        with self.assertRaises(ValueError) as cm:
            release_notes.section(CL, "3.0.0")
        self.assertEqual(str(cm.exception), "no CHANGELOG section for 3.0.0")

    def test_empty_section_raises(self):
        with self.assertRaises(ValueError):
            release_notes.section(CL, "Unreleased")


if __name__ == "__main__":
    unittest.main()
