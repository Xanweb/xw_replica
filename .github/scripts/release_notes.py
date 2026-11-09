"""Print the CHANGELOG section for a version."""
import sys


def section(changelog_text, version):
    heading = "## [%s]" % version
    body = None
    for line in changelog_text.splitlines():
        if body is None:
            if line.startswith(heading):
                body = []
        elif line.startswith("## ["):
            break
        else:
            body.append(line)
    text = "\n".join(body).strip() if body is not None else ""
    if not text:
        raise ValueError("no CHANGELOG section for %s" % version)
    return text


def main(argv):
    if len(argv) != 2:
        print("usage: release_notes.py <version> <CHANGELOG.md>", file=sys.stderr)
        return 2
    try:
        with open(argv[1], encoding="utf-8") as f:
            out = section(f.read(), argv[0])
    except (OSError, ValueError) as e:
        print(str(e), file=sys.stderr)
        return 1
    print(out)
    return 0


if __name__ == "__main__":
    sys.exit(main(sys.argv[1:]))
