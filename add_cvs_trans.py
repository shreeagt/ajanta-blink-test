import sys

file_path = '/Users/ravisir/Documents/GitHub/ajanta-blink-test/resources/views/blink_test_app.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

hi_cvs = """                recorded_success: "आपकी स्क्रीनिंग सफलतापूर्वक दर्ज की गई है।",
                cvs_title: "CVS स्क्रीनिंग",
                symptom_assessment: "लक्षण मूल्यांकन",
                cvs_subtitle: "कृपया डिजिटल डिवाइस के उपयोग के दौरान अपने अनुभव के आधार पर निम्नलिखित लक्षणों को रेट करें।",
                current_cvs_score: "वर्तमान CVS स्कोर",
                complete_assessment: "मूल्यांकन पूरा करें",
                freq: "आवृत्ति",
                intens: "तीव्रता",
                never: "कभी नहीं",
                occas: "कभी-कभी",
                often: "अक्सर/हमेशा",
                moderate: "मध्यम",
                intense: "तीव्र\""""

or_cvs = """                recorded_success: "ଆପଣଙ୍କର ସ୍କ୍ରିନିଂ ସଫଳତାର ସହିତ ରେକର୍ଡ ହୋଇଛି |",
                cvs_title: "CVS ସ୍କ୍ରିନିଂ",
                symptom_assessment: "ଲକ୍ଷଣ ମୂଲ୍ୟାଙ୍କନ",
                cvs_subtitle: "ଦୟାକରି ଡିଜିଟାଲ୍ ଉପକରଣ ବ୍ୟବହାର ସମୟରେ ଆପଣଙ୍କର ଅଭିଜ୍ଞତା ଆଧାରରେ ନିମ୍ନଲିଖିତ ଲକ୍ଷଣଗୁଡ଼ିକୁ ମୂଲ୍ୟାଙ୍କନ କରନ୍ତୁ |",
                current_cvs_score: "ବର୍ତ୍ତମାନର CVS ସ୍କୋର",
                complete_assessment: "ମୂଲ୍ୟାଙ୍କନ ସମାପ୍ତ କରନ୍ତୁ",
                freq: "ଆବୃତ୍ତି",
                intens: "ତୀବ୍ରତା",
                never: "କଦାପି ନୁହେଁ",
                occas: "ମଝିରେ ମଝିରେ",
                often: "ସବୁବେଳେ / ପ୍ରାୟତଃ",
                moderate: "ମଧ୍ୟମ",
                intense: "ତୀବ୍ର\""""

content = content.replace('recorded_success: "आपकी स्क्रीनिंग सफलतापूर्वक दर्ज की गई है।",', hi_cvs)
content = content.replace('recorded_success: "ଆପଣଙ୍କର ସ୍କ୍ରିନିଂ ସଫଳତାର ସହିତ ରେକର୍ଡ ହୋଇଛି |",', or_cvs)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
