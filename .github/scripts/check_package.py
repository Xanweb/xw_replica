"""Check the contents of an exported release zip."""
import pathlib
import sys
import zipfile

TOP = "xw_replica/"

MUST_SHIP_FILES = [
    "controller.php",
    "install.xml",
    "icon.png",
    "LICENSE",
    "README.md",
    "NOTICE",
    "CHANGELOG.md",
    "blocks/xw_quick_tabs/controller.php",
    "blocks/xw_simple_accordion/controller.php",
    "themes/xw_replica/page_theme.php",
    "themes/xw_replica/elements/header_top.php",
    "themes/xw_replica/css/main.less",
    "themes/xw_replica/css/common.css",
    "themes/xw_replica/css/bootstrap-modified.css",
    "themes/xw_replica/css/styles.xml",
    "themes/xw_replica/css/presets/defaults.less",
    "themes/xw_replica/css/presets/night-road.less",
    "vendor/xanweb/rp_v_item_list/js/v-item-list.js",
    "vendor/xanweb/rp_v_item_list/css/v-item-list.css",
    "vendor/xanweb/rp_html_helpers/css/dashboard/ios-toggle-button.css",
]

MUST_SHIP_NONEMPTY_DIRS = [
    "themes/xw_replica/css/build/",
    "src/Xanweb/Replica/",
] + ["vendor/xanweb/rp_%s/" % n for n in (
    "common", "helpers", "html_helpers", "js_localization", "module",
    "request", "v_item_list",
)]

# Step 2 will add content.xml and content_files here.
ALLOWED_TOP_LEVEL = {
    "controller.php", "install.xml", "icon.png", "LICENSE", "README.md",
    "NOTICE", "CHANGELOG.md", "blocks", "src", "themes", "vendor",
}

DEV_ONLY = [
    "build/", ".github/", ".gitattributes", ".gitignore", ".editorconfig",
    "CONTRIBUTING.md", "CODE_OF_CONDUCT.md", "SECURITY.md", "AGENTS.md", "CLAUDE.md",
]


def _is_dev_only(rel):
    for d in DEV_ONLY:
        if d.endswith("/"):
            if rel.startswith(d):
                return True
        elif rel == d:
            return True
    return False


def check(zip_path, extra_required=()):
    problems = []
    with zipfile.ZipFile(zip_path) as z:
        names = z.namelist()
        tops = set()
        for n in names:
            top = n.split("/", 1)[0]
            if top != TOP.rstrip("/"):
                tops.add(top)
        for top in sorted(tops):
            problems.append("bad top-level entry: %s" % top)

        files = {}
        for n in names:
            if n.endswith("/") or not n.startswith(TOP):
                continue
            files[n[len(TOP):]] = n

        for p in list(MUST_SHIP_FILES) + list(extra_required):
            if p not in files:
                problems.append("missing: %s" % p)
        for d in MUST_SHIP_NONEMPTY_DIRS:
            if not any(r.startswith(d) for r in files):
                problems.append("empty dir: %s" % d)
        unexpected = set()
        for rel in sorted(files):
            if _is_dev_only(rel):
                problems.append("dev-only file shipped: %s" % rel)
            else:
                top_name = rel.split("/", 1)[0]
                if top_name not in ALLOWED_TOP_LEVEL and top_name not in unexpected:
                    unexpected.add(top_name)
                    problems.append("unexpected top-level entry: %s" % top_name)
            if rel.lower().endswith(".php") and b"<?=" in z.read(files[rel]):
                problems.append("short echo tag in %s" % rel)
    return problems


def main(argv):
    extra = []
    zip_arg = None
    i = 0
    while i < len(argv):
        if argv[i] == "--require" and i + 1 < len(argv):
            extra.append(argv[i + 1])
            i += 2
        elif zip_arg is None and not argv[i].startswith("--"):
            zip_arg = argv[i]
            i += 1
        else:
            print("usage: check_package.py <zip> [--require <path>]...", file=sys.stderr)
            return 2
    if zip_arg is None:
        print("usage: check_package.py <zip> [--require <path>]...", file=sys.stderr)
        return 2
    problems = check(pathlib.Path(zip_arg), extra)
    for p in problems:
        print(p, file=sys.stderr)
    return 1 if problems else 0


if __name__ == "__main__":
    sys.exit(main(sys.argv[1:]))
