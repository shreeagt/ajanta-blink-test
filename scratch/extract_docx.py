import zipfile
import xml.etree.ElementTree as ET

def get_docx_text(path):
    document = zipfile.ZipFile(path)
    xml_content = document.read('word/document.xml')
    document.close()
    tree = ET.fromstring(xml_content)
    
    # Namespaces
    ns = {'w': 'http://schemas.openxmlformats.org/wordprocessingml/2006/main'}
    
    text = []
    for paragraph in tree.findall('.//w:p', ns):
        p_text = []
        for run in paragraph.findall('.//w:t', ns):
            if run.text:
                p_text.append(run.text)
        if p_text:
            text.append("".join(p_text))
    return "\n".join(text)

print(get_docx_text('/Users/ravisir/Documents/GitHub/ajanta-blink-test/CVS-Q_questionnaire_regional Language.docx'))
