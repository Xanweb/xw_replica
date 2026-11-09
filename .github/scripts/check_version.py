"""Check that a release tag matches pkgVersion in controller.php."""
import re
import sys


def read_pkg_version(controller_text):
    m = re.search(r"\$pkgVersion\s*=\s*['\"]([^'\"]+)['\"]", controller_text)
    if not m:
        raise ValueError("pkgVersion not found")
    return m.group(1)


def check(tag, controller_text):
    version = read_pkg_version(controller_text)
    if tag != "v" + version:
        return ["tag %s does not match pkgVersion %s (expected v%s)" % (tag, version, version)]
    return []


def main(argv):
    if len(argv) != 2:
        print("usage: check_version.py <tag> <path/to/controller.php>", file=sys.stderr)
        return 2
    try:
        with open(argv[1], encoding="utf-8") as f:
            problems = check(argv[0], f.read())
    except (OSError, ValueError) as e:
        print(str(e), file=sys.stderr)
        return 1
    for p in problems:
        print(p, file=sys.stderr)
    return 1 if problems else 0


if __name__ == "__main__":
    sys.exit(main(sys.argv[1:]))
