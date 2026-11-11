"""Check the contents of an exported release zip."""
import pathlib
import re
import sys
import xml.etree.ElementTree as ET
import zipfile

TOP = "xw_replica/"

MUST_SHIP_FILES = [
    "controller.php",
    "install.xml",
    "content.xml",
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

ALLOWED_TOP_LEVEL = {
    "controller.php", "install.xml", "icon.png", "LICENSE", "README.md",
    "NOTICE", "CHANGELOG.md", "blocks", "src", "themes", "vendor",
    "content.xml", "content_files",
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


FILE_REF = re.compile(r"\{ccm:export:file:([0-9]{12}):([^}]+)\}")
ANY_FILE_REF = re.compile(r"\{ccm:export:file:[^}]*\}")


def check_content(files):
    """Check content.xml against content_files/ and the theme layouts.

    files maps package paths (relative to xw_replica/) to their bytes.
    """
    try:
        root = ET.fromstring(files["content.xml"])
    except ET.ParseError as exc:
        return ["content.xml not well-formed: %s" % exc]
    problems = []
    text = files["content.xml"].decode("utf-8", "replace")

    refs = []
    for token in ANY_FILE_REF.findall(text):
        m = FILE_REF.fullmatch(token)
        if m:
            ref = (m.group(1), m.group(2))
            if ref not in refs:
                refs.append(ref)
        else:
            problems.append("unprefixed content file reference: %s" % token)

    prefix = "content_files/"
    direct = set()
    for rel in sorted(files):
        if rel.startswith(prefix):
            if "/" in rel[len(prefix):]:
                problems.append("unexpected path in content_files: %s" % rel)
            else:
                direct.add(rel[len(prefix):])
    used = set()
    for pre, name in refs:
        fname = "%s_%s" % (pre, name)
        if fname in direct:
            used.add(fname)
        else:
            problems.append("missing content file for reference: %s:%s" % (pre, name))
    for fname in sorted(direct - used):
        problems.append("unused content file: %s%s" % (prefix, fname))

    templates = root.find("pagetemplates")
    if templates is not None:
        for pt in templates.findall("pagetemplate"):
            handle = pt.get("handle")
            if handle and "themes/xw_replica/%s.php" % handle not in files:
                problems.append("page template without layout: %s" % handle)

    pagetypes = root.find("pagetypes")
    if pagetypes is not None:
        for pt in pagetypes.findall("pagetype"):
            handle = pt.get("handle")
            pts = pt.find("pagetemplates")
            default = pts.get("default") if pts is not None else None
            if not default:
                problems.append("page type without default template: %s" % handle)
            if pts is not None and pts.get("type") == "custom":
                allowed = [c.get("handle") for c in pts.findall("pagetemplate")]
                if not allowed:
                    problems.append("page type has no allowed templates: %s" % handle)
                elif default and default not in allowed:
                    problems.append("page type default template not allowed: %s" % handle)
            if default and "themes/xw_replica/%s.php" % default not in files:
                problems.append("page type default template without layout: %s" % handle)
    return problems


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
        if "content.xml" in files:
            problems.extend(check_content({r: z.read(n) for r, n in files.items()}))
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
