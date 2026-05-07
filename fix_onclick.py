import sys

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Fix onclick calls to be explicitly global
content = content.replace('onclick="startCvsScreening()"', 'onclick="window.startCvsScreening()"')
content = content.replace('onclick="submitCvsScreening()"', 'onclick="window.submitCvsScreening()"')
content = content.replace('onclick="setCvsValue(', 'onclick="window.setCvsValue(')

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
