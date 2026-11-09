"""Compare two release zips, ignoring CRLF/LF in text files."""
import pathlib
import sys
import zipfile


def _load(path):
    files = {}
    outside = []
    with zipfile.ZipFile(path) as z:
        for n in z.namelist():
            if n.endswith("/"):
                continue
            top, sep, rel = n.partition("/")
            if not sep:
                outside.append(n)
            elif rel:
                files[rel] = z.read(n)
    return files, outside


def compare(a, b, allowed=frozenset()):
    (fa, oa), (fb, ob) = _load(a), _load(b)
    problems = []
    for side, outside in (("A", oa), ("B", ob)):
        for n in sorted(outside):
            problems.append("outside top folder (%s): %s" % (side, n))
    for p in sorted(set(fa) | set(fb)):
        if p in allowed:
            continue
        if p not in fb:
            problems.append("only in A: %s" % p)
        elif p not in fa:
            problems.append("only in B: %s" % p)
        else:
            da, db = fa[p], fb[p]
            if b"\0" not in da and b"\0" not in db:
                da = da.replace(b"\r\n", b"\n")
                db = db.replace(b"\r\n", b"\n")
            if da != db:
                problems.append("differs: %s" % p)
    return problems


def main(argv):
    allowed = set()
    pos = []
    i = 0
    while i < len(argv):
        if argv[i] == "--allow" and i + 1 < len(argv):
            allowed.add(argv[i + 1])
            i += 2
        elif not argv[i].startswith("--"):
            pos.append(argv[i])
            i += 1
        else:
            pos = []
            break
    if len(pos) != 2:
        print("usage: compare_zips.py <a.zip> <b.zip> [--allow <path>]...", file=sys.stderr)
        return 2
    problems = compare(pathlib.Path(pos[0]), pathlib.Path(pos[1]), allowed)
    for p in problems:
        print(p, file=sys.stderr)
    return 1 if problems else 0


if __name__ == "__main__":
    sys.exit(main(sys.argv[1:]))
