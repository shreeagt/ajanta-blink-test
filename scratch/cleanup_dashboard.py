import sys
import re

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Remove duplicate cvs-opt-btn style blocks
# Keep only the first occurrence in the main style block
cvs_style_pattern = r'\.cvs-opt-btn\s*\{[^}]*\}\s*\.cvs-opt-btn\.active\s*\{[^}]*\}'
matches = list(re.finditer(cvs_style_pattern, content))
if len(matches) > 1:
    # Keep the first one, remove the others
    for match in reversed(matches[1:]):
        content = content[:match.start()] + content[match.end():]

# Remove redundant style tags that are empty or only contain comments
content = re.sub(r'<style>\s*</style>', '', content)

# Fix dashboard spacing
content = re.sub(
    r'margin: 0 -24px 30px; padding: 40px 24px 70px;',
    'margin: 0 -24px 60px; padding: 50px 24px 80px;',
    content
)
content = re.sub(
    r'margin: 50px 0 30px;',
    'margin: 0 0 30px;',
    content
)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
