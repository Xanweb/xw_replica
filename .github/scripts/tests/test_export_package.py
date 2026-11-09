import subprocess
import sys
import tempfile
import unittest
import zipfile
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent.parent))

import export_package  # noqa: E402


class ExportPackageTest(unittest.TestCase):
    def test_export_prefix_and_ignore(self):
        with tempfile.TemporaryDirectory() as tmp:
            repo = Path(tmp) / "repo"
            repo.mkdir()
            files = {
                "a.txt": "a\n",
                "build/x.txt": "x\n",
                "theme/css/build/y.less": "y\n",
                ".gitattributes": "/build export-ignore\n",
            }
            for name, text in files.items():
                p = repo / name
                p.parent.mkdir(parents=True, exist_ok=True)
                p.write_text(text)
            git = ["git", "-C", str(repo), "-c", "user.name=T", "-c", "user.email=t@example.com"]
            subprocess.run(git + ["init", "-q"], check=True)
            subprocess.run(git + ["add", "-A"], check=True)
            subprocess.run(git + ["commit", "-q", "-m", "init"], check=True)
            out = Path(tmp) / "out.zip"
            export_package.export("HEAD", out, repo)
            with zipfile.ZipFile(out) as z:
                names = {n for n in z.namelist() if not n.endswith("/")}
            self.assertEqual(names, {
                "xw_replica/a.txt",
                "xw_replica/theme/css/build/y.less",
                "xw_replica/.gitattributes",
            })


if __name__ == "__main__":
    unittest.main()
