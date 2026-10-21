import sys
import re

tag = sys.argv[1].lstrip('v') if len(sys.argv) > 1 else ''

try:
    with open('CHANGELOG.md', 'r', encoding='utf-8') as f:
        content = f.read()

    # Match section starting with ## [tag] until the next ## [ or EOF
    pattern = r'## \[' + re.escape(tag) + r'\].*?\n(.*?)(?=\n## \[|\Z)'
    match = re.search(pattern, content, re.DOTALL)
    if match:
        notes = match.group(1).strip().strip('-').strip()
        with open('release_notes.md', 'w', encoding='utf-8') as out:
            out.write(notes + '\n')
    else:
        with open('release_notes.md', 'w', encoding='utf-8') as out:
            out.write('Release ' + tag + '\n')
except Exception as e:
    with open('release_notes.md', 'w', encoding='utf-8') as out:
        out.write('Release ' + tag + '\n')
