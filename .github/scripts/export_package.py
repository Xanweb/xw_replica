"""Export a git ref as the release zip (single top folder xw_replica/)."""
import pathlib
import subprocess
import sys


def export(ref, out, repo=None):
    repo = pathlib.Path.cwd() if repo is None else pathlib.Path(repo)
    out = pathlib.Path(out)
    out.parent.mkdir(parents=True, exist_ok=True)
    subprocess.run(
        ["git", "-C", str(repo), "archive", "--format=zip",
         "--prefix=xw_replica/", "-o", str(out.resolve()), ref],
        check=True,
    )
    return out


def main(argv):
    if len(argv) != 2:
        print("usage: export_package.py <ref> <out.zip>", file=sys.stderr)
        return 2
    try:
        export(argv[0], pathlib.Path(argv[1]))
    except subprocess.CalledProcessError as e:
        print("git archive failed: %s" % e, file=sys.stderr)
        return 1
    return 0


if __name__ == "__main__":
    sys.exit(main(sys.argv[1:]))
