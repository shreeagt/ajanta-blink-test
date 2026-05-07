import re
import json

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

def extract_obj(pattern):
    match = re.search(pattern, content, re.DOTALL)
    if match:
        return match.group(1)
    return None

translations_str = extract_obj(r'const translations = (\{.*?\});')
blink_analysis_str = extract_obj(r'const blinkAnalysisSet = (\{.*?\});')
res_sets_str = extract_obj(r'const resSets = (\{.*?\});')

# This is a bit tricky because they are JS objects, not perfect JSON.
# But I can try to parse them using a JS parser if I had one, or just use regex to clean them up.
# Since I'm in Python, I'll use a simple approach to turn them into something I can use.

def js_to_dict(js_str):
    if not js_str: return {}
    # Remove comments
    js_str = re.sub(r'//.*', '', js_str)
    # Wrap keys in quotes if they aren't
    js_str = re.sub(r'(\w+):', r'"\1":', js_str)
    # Remove trailing commas
    js_str = re.sub(r',\s*\}', '}', js_str)
    js_str = re.sub(r',\s*\]', ']', js_str)
    try:
        return json.loads(js_str)
    except Exception as e:
        # If it fails, I'll just print it for manual review
        # print(f"Error parsing: {e}")
        return js_str

# I'll just write a script that generates the Seeder PHP code directly by processing the text.
def generate_seeder_entries():
    entries = []
    
    # Process translations
    # Find all "lang": { ... }
    lang_matches = re.finditer(r'"?(\w+)"?:\s*\{(.*?)\}\s*(?=,|$|})', translations_str, re.DOTALL)
    for lm in lang_matches:
        lang = lm.group(1)
        body = lm.group(2)
        # Find all "key": "value"
        key_matches = re.finditer(r'"?(\w+)"?:\s*["\'](.*?)["\']', body)
        for km in key_matches:
            entries.append((lang, 'ui', km.group(1), km.group(2)))

    # Process blinkAnalysisSet
    # Structure: lang: { tier: { tier: '...', status: '...', analysis: '...' }, ... }
    lang_matches = re.finditer(r'"?(\w+)"?:\s*\{(.*?)\}\s*(?=,|$|})', blink_analysis_str, re.DOTALL)
    for lm in lang_matches:
        lang = lm.group(1)
        body = lm.group(2)
        # Find tiers
        tier_matches = re.finditer(r'"?(\w+)"?:\s*\{(.*?)\}', body, re.DOTALL)
        for tm in tier_matches:
            tier_name = tm.group(1)
            tier_body = tm.group(2)
            # Find tier, status, analysis
            field_matches = re.finditer(r'"?(\w+)"?:\s*["\'](.*?)["\']', tier_body)
            for fm in field_matches:
                entries.append((lang, 'analysis', f"{tier_name}_{fm.group(1)}", fm.group(2)))

    # Process resSets
    lang_matches = re.finditer(r'"?(\w+)"?:\s*\{(.*?)\}\s*(?=,|$|})', res_sets_str, re.DOTALL)
    for lm in lang_matches:
        lang = lm.group(1)
        body = lm.group(2)
        key_matches = re.finditer(r'"?(\w+)"?:\s*["\'](.*?)["\']', body)
        for km in key_matches:
            entries.append((lang, 'cvs_result', km.group(1), km.group(2)))

    return entries

all_entries = generate_seeder_entries()

# Write to a file for seeder
with open('seeder_data.php', 'w', encoding='utf-8') as f:
    f.write("<?php\n\n$data = [\n")
    for e in all_entries:
        # Escape single quotes in value
        val = e[3].replace("'", "\\'")
        f.write(f"    ['{e[0]}', '{e[1]}', '{e[2]}', '{val}'],\n")
    f.write("];\n")
