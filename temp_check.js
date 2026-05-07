
    let state = {
        empCode: "dummy" || sessionStorage.getItem('empCode'),
        empName: "dummy" || sessionStorage.getItem('empName'),
        isLoggedIn: sessionStorage.getItem('isLoggedIn') === 'true',
        isPatientMode: "dummy"),
        historyPage: 0,
        lang: sessionStorage.getItem('lang') || null
    };

    function showToast(msg) {
        const t = document.getElementById('toast');
        t.innerText = msg;
        t.style.opacity = '1';
        t.style.transform = 'translateX(-50%) translateY(10px)';
        setTimeout(() => {
            t.style.opacity = '0';
            t.style.transform = 'translateX(-50%) translateY(0)';
        }, 3000);
    }

    function navigate(screenId) {
        document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
        const target = document.getElementById(screenId);
        if (target) target.classList.add('active');
        
        // Reset scroll position on shell
        const shell = document.querySelector('.app-shell');
        if (shell) shell.scrollTop = 0;
        
        const header = document.getElementById('app-header');
        const footer = document.getElementById('bottom-nav');
        
        const hideChrome = ['scr-login', 'scr-blink-test'];
        
        if (hideChrome.includes(screenId)) {
            if (header) header.style.display = 'none';
            if (footer) footer.style.display = 'none';
        } else {
            if (header) {
                header.style.display = 'flex';
                // Adjust header theme based on screen
                if (['scr-dashboard', 'scr-disclaimer'].includes(screenId)) {
                    header.style.background = 'transparent';
                    header.style.boxShadow = 'none';
                } else {
                    header.style.background = '#005eb8';
                    header.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
                }
            }
            const hideFooterOnly = ['scr-thank-you', 'scr-test-result', 'scr-cvs-result', 'scr-cvs-screening', 'scr-disclaimer'];
            if (hideFooterOnly.includes(screenId)) {
                if (footer) footer.style.display = 'none';
            } else {
                if (footer) footer.style.display = 'flex';
            }
            if(screenId === 'scr-dashboard') loadDashboard();
        }
        
        if (navItems.length) {
            navItems.forEach(btn => btn.classList.toggle('active', btn.id === 'nav-dash' && screenId === 'scr-dashboard'));
        }
    }

    function doLogin() {
        const id = document.getElementById('login-id').value.trim();
        const pass = document.getElementById('login-pass').value;
        if(!id || !pass) { showToast('Please enter credentials'); return; }

        fetch(""dummy"", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '"dummy"' },
            body: JSON.stringify({ emp_code: id, password: pass })
        }).then(res => res.json()).then(data => {
            if(data.success) {
                sessionStorage.setItem('isLoggedIn', 'true');
                sessionStorage.setItem('empCode', data.employee.emp_code);
                sessionStorage.setItem('empName', data.employee.name);
                state.empCode = data.employee.emp_code;
                state.empName = data.employee.name;
                state.isLoggedIn = true;
                navigate('scr-dashboard');
            } else {
                showToast('Invalid Code or Password');
            }
        });
    }

    function logout() {
        document.getElementById('logout-modal').style.display = 'flex';
    }

    function confirmLogout() {
        sessionStorage.clear();
        state.isLoggedIn = false;
        state.empCode = null;
        state.empName = null;
        navigate('scr-login');
        document.getElementById('logout-modal').style.display = 'none';
    }

    function closeLogout(e) {
        if(e.target.id === 'logout-modal') e.target.style.display = 'none';
    }

    let cache = {
        currentPage: 0,
        totalItems: 0
    };

    function loadDashboard() {
        // Re-sync state from storage to be safe
        if (!state.empCode) state.empCode = sessionStorage.getItem('empCode');
        if (!state.empName) state.empName = sessionStorage.getItem('empName');
        
        if(!state.empCode) {
            console.warn("No SO ID found for dashboard sync");
            return;
        }

        const offset = cache.currentPage * 10;
        const url = `"dummy"?so_id=${state.empCode}&offset=${offset}`;
        console.log("Syncing Dashboard:", url);
        
        if(state.empName) document.getElementById('dash-so-name').innerText = state.empName;

        fetch(url)
        .then(res => {
            if(!res.ok) throw new Error(`Server Error: ${res.status}`);
            return res.json();
        })
        .then(data => {
            console.log("Dashboard Data Received:", data);
            const oldToday = parseInt(document.getElementById('stat-today').innerText) || 0;
            const oldMonth = parseInt(document.getElementById('stat-month').innerText) || 0;
            const oldTotal = parseInt(document.getElementById('stat-visits').innerText) || 0;
            
            animateValue('stat-today', oldToday, data.today || 0, 1000);
            animateValue('stat-month', oldMonth, data.month || 0, 1000);
            animateValue('stat-visits', oldTotal, data.total || 0, 1000);
            
            // Goal Logic
            const goal = 10;
            const today = data.today || 0;
            const percent = Math.min(Math.round((today / goal) * 100), 100);
            document.getElementById('dash-percent').innerText = percent + '%';
            
            const motivationEl = document.getElementById('dash-motivation');
            if (today === 0) motivationEl.innerText = t("start_motivation");
            else if (today < goal) motivationEl.innerText = t("progress_motivation", { count: goal - today });
            else motivationEl.innerText = t("goal_motivation");

            cache.totalItems = data.total || 0;

            const list = document.getElementById('history-list');
            list.innerHTML = '';

            if (!data.history || data.history.length === 0) {
                list.innerHTML = `
                    <div style="text-align: center; padding: 50px 24px; background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%); border-radius: 32px; border: 2px dashed #cbd5e1; margin-top:10px; position: relative; overflow: hidden;">
                        <div style="position: absolute; top: -10px; right: -10px; width: 60px; height: 60px; background: rgba(0,94,184,0.05); border-radius: 50%;"></div>
                        <i class="fas fa-eye" style="font-size: 50px; color: var(--primary); opacity: 0.2; margin-bottom: 20px; display: block;"></i>
                        <h3 style="font-size: 18px; font-weight: 900; color: #1e293b; margin-bottom: 8px;">${t("no_screenings_title")}</h3>
                        <p style="font-size: 13px; color: #64748b; font-weight: 600; line-height: 1.5; margin-bottom: 25px;">${t("no_screenings_desc")}</p>
                        <button onclick="navigate('scr-disclaimer')" style="background: var(--primary-gradient); color: white; border: none; padding: 12px 24px; border-radius: 50px; font-weight: 800; font-size: 13px; box-shadow: 0 10px 20px rgba(0,94,184,0.2); display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fas fa-plus"></i> ${t("start_new_test")}
                        </button>
                    </div>
                `;
                return;
            }

            data.history.forEach((item) => {
                const row = document.createElement('div');
                row.className = 'history-item';
                
                // Show CVS button if test is missing symptom score
                const showCvsAction = !item.cvs_score;
                
                row.innerHTML = `
                    <div style="display:flex; align-items:center; gap:16px; flex: 1;">
                        <div style="width:44px; height:44px; background:var(--primary-light); border-radius:12px; display:flex; align-items:center; justify-content:center; color:var(--primary); font-size:18px; flex-shrink: 0;">
                            <i class="fas fa-file-medical-alt"></i>
                        </div>
                        <div class="history-info" style="overflow: hidden;">
                            <h4 style="margin: 0; font-size: 14px; font-weight: 800; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${t("blink_screening_label")}</h4>
                            <p style="margin: 2px 0 0; font-size: 12px; color: var(--text-sub); font-weight: 600;">${new Date(item.created_at).toLocaleDateString('en-GB', {day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit'})}</p>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:8px;">
                        ${showCvsAction ? `
                            <button onclick="window.startCvsForTest('${item.id}')" style="background: #fff7ed; color: #f97316; border: 1px solid #ffedd5; padding: 6px 10px; border-radius: 8px; font-size: 11px; font-weight: 800; display:flex; align-items:center; gap:4px;">
                                <i class="fas fa-clipboard-check"></i> CVS
                            </button>
                        ` : `
                            <div style="background: #f0fdf4; color: #16a34a; border: 1px solid #dcfce7; padding: 6px 10px; border-radius: 8px; font-size: 11px; font-weight: 800;">
                                <i class="fas fa-check"></i>
                            </div>
                        `}
                        <div class="history-badge" style="background:${item.blink_count < 10 ? 'var(--error)' : 'var(--success-light)'}; color:${item.blink_count < 10 ? 'white' : 'var(--success)'}; border:none; padding: 6px 12px; border-radius: 10px; font-weight: 800; font-size: 13px; display: flex; align-items: baseline; gap: 3px; flex-shrink: 0;">
                            ${item.blink_count} <span style="font-size:9px; opacity:0.8;">Bpm</span>
                        </div>
                    </div>
                `;
                list.appendChild(row);
            });

            document.getElementById('page-num').innerText = t("page_label", { count: cache.currentPage + 1 });
            document.getElementById('btn-prev').style.display = cache.currentPage > 0 ? 'block' : 'none';
            document.getElementById('btn-next').style.display = (offset + 10) < cache.totalItems ? 'block' : 'none';
        })
        .catch(err => {
            console.error("Dashboard Load Error:", err);
            showToast(`Sync Failed: ${err.message}`);
        });
    }

    function changePage(delta) {
        cache.currentPage += delta;
        loadDashboard();
    }

    function animateValue(id, start, end, duration) {
        const obj = document.getElementById(id);
        if(!obj) return;
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            obj.innerHTML = Math.floor(progress * (end - start) + start);
            if (progress < 1) window.requestAnimationFrame(step);
        };
        window.requestAnimationFrame(step);
    }

    let blinkCount = 0;
    let testTimer = 15;
    let testInterval;
    let camera;
    let faceMesh;
    let blinkThreshold = 0.23;
    let eyeClosed = false;

    function getEAR(landmarks, indices) {
        const dist = (pA, pB) => Math.hypot(pA.x - pB.x, pA.y - pB.y);
        return (dist(landmarks[indices[1]], landmarks[indices[5]]) + dist(landmarks[indices[2]], landmarks[indices[4]])) / (2.0 * dist(landmarks[indices[0]], landmarks[indices[3]]));
    }

    function onResults(results) {
        if (!results.multiFaceLandmarks || results.multiFaceLandmarks.length === 0) return;
        const landmarks = results.multiFaceLandmarks[0];
        const leftEAR = getEAR(landmarks, [362, 385, 387, 263, 373, 380]);
        const rightEAR = getEAR(landmarks, [33, 160, 158, 133, 153, 144]);
        const avgEAR = (leftEAR + rightEAR) / 2.0;
        
        if (avgEAR < blinkThreshold) {
            eyeClosed = true;
        } else if (eyeClosed) {
            blinkCount++;
            document.getElementById('live-blink-count').innerText = blinkCount;
            eyeClosed = false;
            
            // Physical Feedback: Vibration
            if (navigator.vibrate) navigator.vibrate(50);
            
            // Visual Feedback: Ripple
            const ripple = document.getElementById('blink-ripple');
            ripple.classList.remove('ripple-active');
            void ripple.offsetWidth; // Trigger reflow
            ripple.classList.add('ripple-active');
        }
    }

    window.startCvsForTest = function(testId) {
        state.lastBlinkTestId = testId;
        window.startCvsScreening();
    };

    function startBlinkTest() {
        navigate('scr-blink-test');
        blinkCount = 0; testTimer = 15; eyeClosed = false;
        document.getElementById('live-blink-count').innerText = "0";
        document.getElementById('test-timer').innerText = "15";
        document.getElementById('test-status-msg').innerText = t("init_camera");
        
        if(!faceMesh) {
            faceMesh = new FaceMesh({locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${file}`});
            faceMesh.setOptions({ maxNumFaces: 1, refineLandmarks: true, minDetectionConfidence: 0.5, minTrackingConfidence: 0.5 });
            faceMesh.onResults(onResults);
        }
        if(!camera) {
            camera = new Camera(document.getElementById('input_video'), {
                onFrame: async () => { await faceMesh.send({image: document.getElementById('input_video')}); },
                width: 640, height: 480
            });
        }
        camera.start().then(() => {
            document.getElementById('test-status-msg').innerText = "Test Started. Focus on the center.";
            document.getElementById('test-status-msg').style.color = "#10b981";
            testInterval = setInterval(() => {
                testTimer--;
                document.getElementById('test-timer').innerText = testTimer;
                if(testTimer <= 0) endBlinkTest();
            }, 1000);
        }).catch(err => {
            document.getElementById('test-status-msg').innerText = "Camera error: " + err.message;
            document.getElementById('test-status-msg').style.color = "var(--error)";
        });
    }

    
    function endBlinkTest() {
        clearInterval(testInterval);
        if(camera) camera.stop();
        
        const oneMinuteCount = blinkCount * 4;
        const finalCountEl = document.getElementById('final-blink-count');
        if(finalCountEl) finalCountEl.innerText = blinkCount;
        
        const scaledCountEl = document.getElementById('scaled-blink-count');
        if(scaledCountEl) scaledCountEl.innerText = oneMinuteCount;
        
        const reportDateEl = document.getElementById('report-date');
        if(reportDateEl) reportDateEl.innerText = new Date().toLocaleDateString('en-GB', {day:'2-digit', month:'short', year:'numeric'});
        
        const reportSoEl = document.getElementById('report-so-name');
        if(reportSoEl && state.empName) reportSoEl.innerText = state.empName;
        
        const tierBadge = document.getElementById('result-tier-badge');
        const statusEl = document.getElementById('result-status');
        const analysisEl = document.getElementById('result-analysis');
        
        const lang = state.lang || 'en';
        
        
        
        const blinkAnalysisSet = {
        "en": {
                "finish_close": "Finish & Close",
                "home_nav": "Home",
                "optimal": {
                        "tier": "Optimal",
                        "status": "Highly stable tear film",
                        "analysis": "Your blinking pattern indicates excellent moisture retention and tear film stability."
                },
                "excellent": {
                        "tier": "Excellent",
                        "status": "Very healthy moisture retention",
                        "analysis": "You have great tear stability and very healthy moisture levels."
                },
                "healthy": {
                        "tier": "Healthy",
                        "status": "Normal tear film function",
                        "analysis": "Your blink rate is within the ideal range for healthy adults."
                },
                "mild": {
                        "tier": "Mild",
                        "status": "Early moisture evaporation",
                        "analysis": "You may be starting to experience mild moisture loss or digital strain."
                },
                "moderate": {
                        "tier": "Moderate",
                        "status": "Lipid layer disruption",
                        "analysis": "Your blinking has increased as tears are evaporating faster than normal."
                },
                "high": {
                        "tier": "High Chance",
                        "status": "Signs of Screen-Blink Test",
                        "analysis": "Strong likelihood of dry eyes. We recommend taking regular breaks."
                },
                "severe": {
                        "tier": "Severe",
                        "status": "Highly unstable tear film",
                        "analysis": "Highly unstable tear film. Your eyes may be experiencing constant discomfort."
                }
        },
        "hi": {
                "finish_close": "पूर्ण करें और बंद करें",
                "home_nav": "मुख्य पृष्ठ",
                "optimal": {
                        "tier": "सर्वोत्तम",
                        "status": "अत्यधिक स्थिर अश्रु परत",
                        "analysis": "आपका पलक झपकने का तरीका उत्कृष्ट नमी और अश्रु परत स्थिरता का संकेत देता है।"
                },
                "excellent": {
                        "tier": "उत्कृष्ट",
                        "status": "बहुत स्वस्थ नमी प्रतिधारण",
                        "analysis": "आपकी आंखों में अश्रु स्थिरता और नमी का स्तर बहुत अच्छा है।"
                },
                "healthy": {
                        "tier": "स्वस्थ",
                        "status": "सामान्य अश्रु कार्य",
                        "analysis": "आपकी पलक झपकने की दर स्वस्थ वयस्कों के लिए आदर्श सीमा के भीतर है।"
                },
                "mild": {
                        "tier": "हल्का",
                        "status": "शुरुआती नमी का वाष्पीकरण",
                        "analysis": "आप हल्की नमी की कमी या डिजिटल तनाव का अनुभव करना शुरू कर सकते हैं।"
                },
                "moderate": {
                        "tier": "मध्यम",
                        "status": "लिपिड परत में व्यवधान",
                        "analysis": "आँसू सामान्य से तेजी से वाष्पित हो रहे हैं, इसलिए पलक झपकना बढ़ गया है।"
                },
                "high": {
                        "tier": "उच्च संभावना",
                        "status": "स्क्रीन-ड्राय आई के लक्षण",
                        "analysis": "आंखें सूखने की प्रबल संभावना है। हम नियमित ब्रेक लेने की सलाह देते हैं।"
                },
                "severe": {
                        "tier": "गंभीर",
                        "status": "अत्यधिक अस्थिर अश्रु परत",
                        "analysis": "अत्यधिक अस्थिर अश्रु परत। आपकी आँखों में लगातार परेशानी हो सकती है।"
                }
        },
        "mr": {
                "finish_close": "पूर्ण करा आणि बंद करा",
                "home_nav": "मुख्य पृष्ठ",
                "optimal": {
                        "tier": "सर्वोत्तम",
                        "status": "अत्यंत स्थिर अश्रू थर",
                        "analysis": "तुमच्या पापण्यांची हालचाल उत्कृष्ट ओलावा आणि अश्रू थराच्या स्थिरतेचे दर्शन घडवते."
                },
                "excellent": {
                        "tier": "उत्कृष्ट",
                        "status": "खूप चांगले ओलावा टिकवून ठेवणे",
                        "analysis": "तुमच्या डोळ्यांतील अश्रूंची स्थिरता आणि ओलावा पातळी खूप चांगली आहे."
                },
                "healthy": {
                        "tier": "निरोगी",
                        "status": "सामान्य अश्रू कार्य",
                        "analysis": "तुमच्या पापण्या झपकण्याचा वेग निरोगी प्रौढांसाठी आदर्श मर्यादेत आहे."
                },
                "mild": {
                        "tier": "सौम्य",
                        "status": "सुरुवातीचे ओलावा बाष्पीभवन",
                        "analysis": "तुम्हाला ओलावा कमी होण्याचा किंवा डिजिटल ताण जाणवण्यास सुरुवात होऊ शकते."
                },
                "moderate": {
                        "tier": "मध्यम",
                        "status": "लिपिड थरामध्ये व्यत्यय",
                        "analysis": "अश्रू नेहमीपेक्षा वेगाने सुकत असल्यामुळे पापण्या झपकण्याचे प्रमाण वाढले आहे."
                },
                "high": {
                        "tier": "उच्च शक्यता",
                        "status": "स्क्रीन-ड्राय आयची लक्षणे",
                        "analysis": "डोळे कोरडे होण्याची दाट शक्यता आहे. आम्ही नियमित विश्रांती घेण्याची शिफारस करतो."
                },
                "severe": {
                        "tier": "गंभीर",
                        "status": "अत्यंत अस्थिर अश्रू थर",
                        "analysis": "अत्यंत अस्थिर अश्रू थर. तुमच्या डोळ्यांना सतत त्रास जाणवू शकतो."
                }
        },
        "bn": {
                "finish_close": "শেষ করুন এবং বন্ধ করুন",
                "home_nav": "হোম",
                "optimal": {
                        "tier": "সেরা",
                        "status": "অত্যন্ত স্থিতিশীল টিয়ার ফিল্ম",
                        "analysis": "আপনার চোখের পলক ফেলার ধরণ চমৎকার আর্দ্রতা এবং টিয়ার ফিল্মের স্থিতিশীলতা নির্দেশ করে।"
                },
                "excellent": {
                        "tier": "চমৎকার",
                        "status": "খুব স্বাস্থ্যকর আর্দ্রতা ধারণ",
                        "analysis": "আপনার চোখের টিয়ার স্থিতিশীলতা এবং আর্দ্রতার মাত্রা খুব ভালো।"
                },
                "healthy": {
                        "tier": "সুস্থ",
                        "status": "স্বাভাবিক টিয়ার ফিল্ম ফাংশন",
                        "analysis": "আপনার চোখের পলক ফেলার হার সুস্থ প্রাপ্তবয়স্কদের আদর্শ সীমার মধ্যে রয়েছে।"
                },
                "mild": {
                        "tier": "সামান্য",
                        "status": "প্রাথমিক আর্দ্রতা বাষ্পীভবন",
                        "analysis": "আপনি সামান্য আর্দ্রতা হ্রাস বা ডিজিটাল চাপের সম্মুখীন হতে শুরু করতে পারেন।"
                },
                "moderate": {
                        "tier": "মাঝারি",
                        "status": "লিপিড লেয়ার ব্যাহত",
                        "analysis": "চোখের জল স্বাভাবিকের চেয়ে দ্রুত শুকিয়ে যাওয়ায় চোখের পলক ফেলা বেড়েছে।"
                },
                "high": {
                        "tier": "উচ্চ সম্ভাবনা",
                        "status": "স্ক্রিন-ড্রাই আই এর লক্ষণ",
                        "analysis": "চোখ শুকিয়ে যাওয়ার প্রবল সম্ভাবনা রয়েছে। আমরা নিয়মিত বিরতি নেওয়ার পরামর্শ দিই।"
                },
                "severe": {
                        "tier": "গুরুতর",
                        "status": "অত্যন্ত অস্থিতিশীল টিয়ার ফিল্ম",
                        "analysis": "অত্যন্ত অস্থিতিশীল টিয়ার ফিল্ম। আপনার চোখে ক্রমাগত অস্বস্তি হতে পারে।"
                }
        },
        "te": {
                "finish_close": "పూర్తి చేసి మూసివేయి",
                "home_nav": "హోమ్",
                "optimal": {
                        "tier": "అత్యుత్తమ",
                        "status": "చాలా స్థిరమైన కన్నీటి పొర",
                        "analysis": "మీ కనురెప్పల కదలిక అద్భుతమైన తేమ మరియు కన్నీటి పొర స్థిరత్వాన్ని సూచిస్తుంది."
                },
                "excellent": {
                        "tier": "అద్భుతమైన",
                        "status": "చాలా ఆరోగ్యకరమైన తేమ నిలుపుదల",
                        "analysis": "మీ కళ్ళలో కన్నీటి స్థిరత్వం మరియు తేమ స్థాయిలు చాలా బాగున్నాయి."
                },
                "healthy": {
                        "tier": "ఆరోగ్యకరమైన",
                        "status": "సాధారణ కన్నీటి పొర పనితీరు",
                        "analysis": "మీ కనురెప్పలు వేసే వేగం ఆరోగ్యకరమైన పెద్దలకు ఉండాల్సిన పరిమితిలోనే ఉంది."
                },
                "mild": {
                        "tier": "స్వల్ప",
                        "status": "ప్రారంభ తేమ ఆవిరి",
                        "analysis": "మీరు స్వల్పంగా తేమ తగ్గడం లేదా డిజిటల్ ఒత్తిడిని అనుభవించడం ప్రారంభించవచ్చు."
                },
                "moderate": {
                        "tier": "మితమైన",
                        "status": "లిపిడ్ పొరలో అంతరాయం",
                        "analysis": "కన్నీళ్లు సాధారణం కంటే వేగంగా ఆవిరైపోతుండటం వల్ల కనురెప్పలు వేయడం పెరిగింది."
                },
                "high": {
                        "tier": "అధిక అవకాశం",
                        "status": "స్క్రీన్-డ్రై ఐ లక్షణాలు",
                        "analysis": "కళ్ళు పొడిబారే అవకాశం ఎక్కువగా ఉంది. మేము క్రమం తప్పకుండా విరామం తీసుకోవాలని సూచిస్తున్నాము."
                },
                "severe": {
                        "tier": "తీవ్రమైన",
                        "status": "చాలా అస్థిరమైన కన్నీటి పొర",
                        "analysis": "చాలా అస్థిరమైన కన్నీటి పొర. మీ కళ్ళలో నిరంతరం అసౌకర్యం ఉండవచ్చు."
                }
        },
        "ta": {
                "finish_close": "முடித்து மூடவும்",
                "home_nav": "முகப்பு",
                "optimal": {
                        "tier": "சிறந்தது",
                        "status": "மிகவும் நிலையான கண்ணீர் படலம்",
                        "analysis": "உங்கள் கண் சிமிட்டும் முறை சிறந்த ஈரப்பதம் மற்றும் கண்ணீர் படலத்தின் நிலைத்தன்மையைக் குறிக்கிறது."
                },
                "excellent": {
                        "tier": "அருமை",
                        "status": "மிகவும் ஆரோக்கியமான ஈரப்பதம் தக்கவைப்பு",
                        "analysis": "உங்கள் கண்களில் கண்ணீர் நிலைத்தன்மை மற்றும் ஈரப்பதம் அளவு மிக நன்றாக உள்ளது."
                },
                "healthy": {
                        "tier": "ஆரோக்கியமானது",
                        "status": "சாதாரண கண்ணீர் படல செயல்பாடு",
                        "analysis": "உங்கள் கண் சிமிட்டும் வேகம் ஆரோக்கியமான பெரியவர்களுக்கு இருக்க வேண்டிய வரம்பிற்குள் உள்ளது."
                },
                "mild": {
                        "tier": "மிதமானது",
                        "status": "ஆரம்ப ஈரப்பதம் ஆவியாதல்",
                        "analysis": "நீங்கள் லேசான ஈரப்பதம் இழப்பு அல்லது டிஜிட்டல் அழுத்தத்தை அனுபவிக்க ஆரம்பிக்கலாம்."
                },
                "moderate": {
                        "tier": "நடுத்தரம்",
                        "status": "லிப்பிட் அடுக்கில் இடையூறு",
                        "analysis": "கண்ணீர் இயல்பை விட வேகமாக ஆவியாவதால் கண் சிமிட்டுவது அதிகரித்துள்ளது."
                },
                "high": {
                        "tier": "அதிக வாய்ப்பு",
                        "status": "ஸ்கிரீன்-ட்ரை ஐ அறிகுறிகள்",
                        "analysis": "கண்கள் வறட்சியடைய அதிக வாய்ப்பு உள்ளது. நாங்கள் வழக்கமான இடைவெளிகளை எடுக்க பரிந்துரைக்கிறோம்."
                },
                "severe": {
                        "tier": "கடுமையான",
                        "status": "மிகவும் நிலையற்ற கண்ணீர் படலம்",
                        "analysis": "மிகவும் நிலையற்ற கண்ணீர் படலம். உங்கள் கண்களில் தொடர்ச்சியான அசௌகரியம் இருக்கலாம்."
                }
        },
        "kn": {
                "finish_close": "ಪೂರ್ಣಗೊಳಿಸಿ ಮತ್ತು ಮುಚ್ಚಿ",
                "home_nav": "ಹೋಮ್",
                "optimal": {
                        "tier": "ಅತ್ಯುತ್ತಮ",
                        "status": "ಅತ್ಯಂತ ಸ್ಥಿರವಾದ ಕಣ್ಣೀರಿನ ಪದರ",
                        "analysis": "ನಿಮ್ಮ ಕಣ್ಣು ಮಿಟುಕಿಸುವ ವಿಧಾನವು ಅತ್ಯುತ್ತಮ ತೇವಾಂಶ ಮತ್ತು ಕಣ್ಣೀರಿನ ಪದರದ ಸ್ಥಿರತೆಯನ್ನು ಸೂಚಿಸುತ್ತದೆ."
                },
                "excellent": {
                        "tier": "ಉತ್ತಮ",
                        "status": "ಆರೋಗ್ಯಕರ ತೇವಾಂಶ ಉಳಿಸಿಕೊಳ್ಳುವಿಕೆ",
                        "analysis": "ನಿಮ್ಮ ಕಣ್ಣುಗಳಲ್ಲಿ ಕಣ್ಣೀರಿನ ಸ್ಥಿರತೆ ಮತ್ತು ತೇವಾಂಶದ ಮಟ್ಟವು ತುಂಬಾ ಚೆನ್ನಾಗಿದೆ."
                },
                "healthy": {
                        "tier": "ಆರೋಗ್ಯಕರ",
                        "status": "ಸಾಮಾನ್ಯ ಕಣ್ಣೀರಿನ ಪದರ ಕಾರ್ಯ",
                        "analysis": "ನಿಮ್ಮ ಕಣ್ಣು ಮಿಟುಕಿಸುವ ವೇಗವು ಆರೋಗ್ಯವಂತ ವಯಸ್ಕರಿಗೆ ಇರಬೇಕಾದ ಮಿತಿಯಲ್ಲಿದೆ."
                },
                "mild": {
                        "tier": "ಸೌಮ್ಯ",
                        "status": "ಆರಂಭಿಕ ತೇವಾಂಶ ಆವಿಯಾಗುವಿಕೆ",
                        "analysis": "ನೀವು ಸೌಮ್ಯ ತೇವಾಂಶದ ನಷ್ಟ ಅಥವಾ ಡಿಜಿಟಲ್ ಒತ್ತಡವನ್ನು ಅನುಭವಿಸಲು ಪ್ರಾರಂಭಿಸಬಹುದು."
                },
                "moderate": {
                        "tier": "ಮಧ್ಯಮ",
                        "status": "ಲಿಪಿಡ್ ಪದರದಲ್ಲಿ ಅಡಚಣೆ",
                        "analysis": "ಕಣ್ಣೀರು ಸಾಮಾನ್ಯಕ್ಕಿಂತ ವೇಗವಾಗಿ ಆವಿಯಾಗುತ್ತಿರುವುದರಿಂದ ಕಣ್ಣು ಮಿಟುಕಿಸುವುದು ಹೆಚ್ಚಾಗಿದೆ."
                },
                "high": {
                        "tier": "ಹೆಚ್ಚಿನ ಸಾಧ್ಯತೆ",
                        "status": "ಸ್ಕ್ರೀನ್-ಡ್ರೈ ಐ ಲಕ್ಷಣಗಳು",
                        "analysis": "ಕಣ್ಣುಗಳು ಒಣಗುವ ಹೆಚ್ಚಿನ ಸಾಧ್ಯತೆಯಿದೆ. ನಾವು ನಿಯಮಿತವಾಗಿ ವಿರಾಮ ತೆಗೆದುಕೊಳ್ಳಲು ಶಿಫಾರಸು ಮಾಡುತ್ತೇವೆ."
                },
                "severe": {
                        "tier": "ತೀವ್ರ",
                        "status": "ಅತ್ಯಂತ ಅಸ್ಥಿರವಾದ ಕಣ್ಣೀರಿನ ಪದರ",
                        "analysis": "ಅತ್ಯಂತ ಅಸ್ಥಿರವಾದ ಕಣ್ಣೀರಿನ ಪದರ. ನಿಮ್ಮ ಕಣ್ಣುಗಳಲ್ಲಿ ಸತತವಾಗಿ ಅಸ್ವಸ್ಥತೆ ಇರಬಹುದು."
                }
        },
        "gu": {
                "finish_close": "પૂર્ણ કરો અને બંધ કરો",
                "home_nav": "હોમ",
                "optimal": {
                        "tier": "શ્રેષ્ઠ",
                        "status": "અત્યંત સ્થિર અશ્રુ સ્તર",
                        "analysis": "તમારી પલક ઝપકાવવાની રીત ઉત્તમ ભેજ અને અશ્રુ સ્તરની સ્થિરતા દર્શાવે છે."
                },
                "excellent": {
                        "tier": "ઉત્કૃષ્ટ",
                        "status": "ખૂબ જ સ્વસ્થ ભેજ જાળવણી",
                        "analysis": "તમારી આંખોમાં અશ્રુ સ્થિરતા અને ભેજનું સ્તર ખૂબ જ સારું છે."
                },
                "healthy": {
                        "tier": "સ્વસ્થ",
                        "status": "સામાન્ય અશ્રુ કાર્ય",
                        "analysis": "તમારી પલક ઝપકાવવાનો દર સ્વસ્થ પુખ્ત વયના લોકો માટે આદર્શ મર્યાદામાં છે."
                },
                "mild": {
                        "tier": "હળવું",
                        "status": "શરૂઆતી ભેજનું બાષ્પીભવન",
                        "analysis": "તમે હળવા ભેજની કમી અથવા ડિજિટલ તણાવ અનુભવવાનું શરૂ કરી શકો છો."
                },
                "moderate": {
                        "tier": "મધ્યમ",
                        "status": "લિપિડ સ્તરમાં વિક્ષેપ",
                        "analysis": "આંસુ સામાન્ય કરતાં વધુ ઝડપથી સુકાઈ રહ્યા હોવાથી પલક ઝપકવાનું વધી ગયું છે."
                },
                "high": {
                        "tier": "ઉચ્ચ શક્યતા",
                        "status": "સ્ક્રીન-ડ્રાય આઈના લક્ષણો",
                        "analysis": "આંખો સુકાઈ જવાની પ્રબળ શક્યતા છે. અમે નિયમિત વિરામ લેવાની સલાહ આપીએ છીએ."
                },
                "severe": {
                        "tier": "ગંભીર",
                        "status": "અત્યંત અસ્થિર અશ્રુ સ્તર",
                        "analysis": "અત્યંત અસ્થિર અશ્રુ સ્તર. તમારી આંખોમાં સતત અસ્વસ્થતા હોઈ શકે છે."
                }
        },
        "ml": {
                "finish_close": "പൂർത്തിയാക്കി അടയ്ക്കുക",
                "home_nav": "ഹോം",
                "optimal": {
                        "tier": "അത്യുത്തമം",
                        "status": "വളരെ സ്ഥിരതയുള്ള കണ്ണുനീർ പാളി",
                        "analysis": "നിങ്ങളുടെ കണ്ണ് ചിമ്മുന്ന രീതി മികച്ച ഈർപ്പവും കണ്ണുനീർ പാളിയുടെ സ്ഥിരതയും സൂചിപ്പിക്കുന്നു."
                },
                "excellent": {
                        "tier": "മികച്ച",
                        "status": "ആരോഗ്യകരമായ ഈർപ്പം നിലനിർത്തൽ",
                        "analysis": "നിങ്ങളുടെ കണ്ണുകളിലെ കണ്ണുനീർ സ്ഥിരതയും ഈർപ്പവും വളരെ മികച്ചതാണ്."
                },
                "healthy": {
                        "tier": "ആരോഗ്യമുള്ള",
                        "status": "സാധാരണ കണ്ണുനീർ പാളി പ്രവർത്തനം",
                        "analysis": "നിങ്ങളുടെ കണ്ണ് ചിമ്മുന്ന നിരക്ക് ആരോഗ്യവാനായ ഒരാൾക്ക് ആവശ്യമായ പരിധിയിലാണ്."
                },
                "mild": {
                        "tier": "മിതമായ",
                        "status": "ഈർപ്പം ബാഷ്പീകരണം",
                        "analysis": "നിങ്ങൾക്ക് നേരിയ ഈർപ്പം നഷ്ടമോ ഡിജിറ്റൽ സ്ട്രെയിനോ അനുഭവപ്പെടാൻ തുടങ്ങിയേക്കാം."
                },
                "moderate": {
                        "tier": "ഇടത്തരം",
                        "status": "ലിപിഡ് പാളിയിലെ തടസ്സം",
                        "analysis": "കണ്ണുനീർ വേഗത്തിൽ വറ്റുന്നതിനാൽ കണ്ണ് ചിമ്മുന്നത് വർദ്ധിച്ചിട്ടുണ്ട്."
                },
                "high": {
                        "tier": "കൂടുതൽ സാധ്യത",
                        "status": "സ്ക്രീൻ-ഡ്രൈ ഐ ലക്ഷണങ്ങൾ",
                        "analysis": "കണ്ണ് വരളാൻ സാധ്യതയുണ്ട്. ഇടയ്ക്കിടെ വിശ്രമിക്കാൻ ഞങ്ങൾ ശുപാർശ ചെയ്യുന്നു."
                },
                "severe": {
                        "tier": "ഗുരുതരമായ",
                        "status": "അസ്ഥിരമായ കണ്ണുനീർ പാളി",
                        "analysis": "വളരെ അസ്ഥിരമായ കണ്ണുനീർ പാളി. നിങ്ങളുടെ കണ്ണുകളിൽ വിട്ടുമാറാത്ത അസ്വസ്ഥത ഉണ്ടായേക്കാം."
                }
        },
        "or": {
                "finish_close": "ସମାପ୍ତ କରନ୍ତୁ ଏବଂ ବନ୍ଦ କରନ୍ତୁ",
                "home_nav": "ହୋମ୍",
                "optimal": {
                        "tier": "ସର୍ବୋତ୍ତମ",
                        "status": "ଅତ୍ୟନ୍ତ ସ୍ଥିର ଲୁହ ସ୍ତର",
                        "analysis": "ଆପଣଙ୍କ ଆଖି ପତା ପକାଇବା ଶୈଳୀ ଉତ୍କୃଷ୍ଟ ଆର୍ଦ୍ରତା ଏବଂ ଲୁହ ସ୍ତରର ସ୍ଥିରତା ଦର୍ଶାଏ |"
                },
                "excellent": {
                        "tier": "ଉତ୍କୃଷ୍ଟ",
                        "status": "ଉତ୍ତମ ଆର୍ଦ୍ରତା ବଜାୟ ରଖିବା",
                        "analysis": "ଆପଣଙ୍କ ଆଖିରେ ଲୁହ ସ୍ଥିରତା ଏବଂ ଆର୍ଦ୍ରତା ସ୍ତର ବହୁତ ଭଲ ଅଛି |"
                },
                "healthy": {
                        "tier": "ସୁସ୍ଥ",
                        "status": "ସାଧାରଣ ଲୁହ କାର୍ଯ୍ୟ",
                        "analysis": "ଆପଣଙ୍କ ଆଖି ପତା ପକାଇବା ହାର ସୁସ୍ଥ ବୟସ୍କଙ୍କ ପାଇଁ ଆଦର୍ଶ ସୀମା ମଧ୍ୟରେ ଅଛି |"
                },
                "mild": {
                        "tier": "ସାମାନ୍ୟ",
                        "status": "ପ୍ରାଥମିକ ଆର୍ଦ୍ରତା ବାଷ୍ପୀଭବନ",
                        "analysis": "ଆପଣ ସାମାନ୍ୟ ଆର୍ଦ୍ରତା ହ୍ରାସ କିମ୍ବା ଡିଜିଟାଲ୍ ଚାପ ଅନୁଭବ କରିବା ଆରମ୍ଭ କରିପାରନ୍ତି |"
                },
                "moderate": {
                        "tier": "ମଧ୍ୟମ",
                        "status": "ଲିପିଡ୍ ସ୍ତରରେ ବାଧା",
                        "analysis": "ଲୁହ ସାଧାରଣ ଅପେକ୍ଷା ଶୀଘ୍ର ଶୁଖିଯାଉଥିବାରୁ ଆଖି ପତା ପକାଇବା ବୃଦ୍ଧି ପାଇଛି |"
                },
                "high": {
                        "tier": "ଉଚ୍ଚ ସମ୍ଭାବନା",
                        "status": "ସ୍କ୍ରିନ୍-ଡ୍ରାଏ ଆଇ ର ଲକ୍ଷଣ",
                        "analysis": "ଆଖି ଶୁଖିଯିବାର ପ୍ରବଳ ସମ୍ଭାବନା ଅଛି | ଆମେ ନିୟମିତ ବିରତି ନେବାକୁ ପରାମର୍ଶ ଦେଉଛୁ |"
                },
                "severe": {
                        "tier": "ଗୁରୁତର",
                        "status": "ଅତ୍ୟନ୍ତ ଅସ୍ଥିର ଲୁହ ସ୍ତର",
                        "analysis": "ଅତ୍ୟନ୍ତ ଅସ୍ଥିର ଲୁହ ସ୍ତର | ଆପଣଙ୍କ ଆଖିରେ କ୍ରମାଗତ ଅସୁବିଧା ହୋଇପାରେ |"
                }
        },
        "as": {
                "finish_close": "সমাপ্ত কৰি বন্ধ কৰক",
                "home_nav": "হোম",
                "optimal": {
                        "tier": "সর্বোত্তম",
                        "status": "অত্যন্ত স্থিতিশীল চকুলোৰ স্তৰ",
                        "analysis": "আপোনাৰ চকুৰ পতা পেলোৱাৰ ধৰণে উৎকৃষ্ট আৰ্দ্ৰতা আৰু চকুলোৰ স্তৰৰ স্থিতিশীলতা সূচায়।"
                },
                "excellent": {
                        "tier": "উৎকৃষ্ট",
                        "status": "সুস্থ আৰ্দ্ৰতা ধাৰণ",
                        "analysis": "আপোনাৰ চকুত চকুলোৰ স্থিৰতা আৰু আৰ্দ্ৰতাৰ মাত্ৰা বহুত ভাল।"
                },
                "healthy": {
                        "tier": "সুস্থ",
                        "status": "স্বাভাৱিক চকুলোৰ কাৰ্য",
                        "analysis": "আপোনাৰ চকুৰ পতা পেলোৱাৰ হাৰ এজন সুস্থ প্রাপ্তবয়স্কৰ বাবে আদর্শ সীমাৰ ভিতৰত।"
                },
                "mild": {
                        "tier": "সামান্য",
                        "status": "প্রাথমিক আৰ্দ্ৰতা বাষ্পীভৱন",
                        "analysis": "আপুনি সামান্য আৰ্দ্ৰতা হ্রাস বা ডিজিটেল চাপ অনুভৱ কৰিবলৈ আৰম্ভ কৰিব পাৰে।"
                },
                "moderate": {
                        "tier": "মধ্যমীয়া",
                        "status": "লিপিড স্তৰত ব্যাঘাত",
                        "analysis": "চকুলো স্বাভাৱিকতকৈ দ্রুতগতিত শুকাই যোৱাৰ বাবে চকুৰ পতা পেলোৱা বৃদ্ধি পাইছে।"
                },
                "high": {
                        "tier": "উচ্চ সম্ভাৱনা",
                        "status": "স্ক্ৰীণ-ব্লিংক টেষ্টৰ লক্ষণ",
                        "analysis": "চকু শুকাই যোৱাৰ প্ৰবল সম্ভাৱনা আছে। আমি নিয়মিত বিৰতি ল’বলৈ পৰামৰ্শ দিওঁ।"
                },
                "severe": {
                        "tier": "গুৰুতৰ",
                        "status": "অত্যন্ত অস্থিৰ চকুলোৰ স্তৰ",
                        "analysis": "অত্যন্ত অস্থিৰ চকুলোৰ স্তৰ। আপোনাৰ চকুত ক্রমাগত অসুবিধা হ’ব পাৰে।"
                }
        }
};




        let resObj = (blinkAnalysisSet[lang] || blinkAnalysisSet['hi'] || blinkAnalysisSet['en']).healthy;
        if (oneMinuteCount <= 6) resObj = (blinkAnalysisSet[lang] || blinkAnalysisSet['en']).optimal;
        else if (oneMinuteCount <= 10) resObj = (blinkAnalysisSet[lang] || blinkAnalysisSet['en']).excellent;
        else if (oneMinuteCount <= 13) resObj = (blinkAnalysisSet[lang] || blinkAnalysisSet['en']).healthy;
        else if (oneMinuteCount <= 16) resObj = (blinkAnalysisSet[lang] || blinkAnalysisSet['en']).mild;
        else if (oneMinuteCount <= 18) resObj = (blinkAnalysisSet[lang] || blinkAnalysisSet['en']).moderate;
        else if (oneMinuteCount <= 20) resObj = (blinkAnalysisSet[lang] || blinkAnalysisSet['en']).high;
        else resObj = (blinkAnalysisSet[lang] || blinkAnalysisSet['en']).severe;

        if (oneMinuteCount <= 10) { resObj.color = '#10b981'; resObj.bg = '#f0fdf4'; }
        else if (oneMinuteCount <= 13) { resObj.color = '#38bdf8'; resObj.bg = '#f0f9ff'; }
        else if (oneMinuteCount <= 16) { resObj.color = '#f59e0b'; resObj.bg = '#fffbeb'; }
        else if (oneMinuteCount <= 18) { resObj.color = '#f97316'; resObj.bg = '#fff7ed'; }
        else if (oneMinuteCount <= 20) { resObj.color = '#ef4444'; resObj.bg = '#fef2f2'; }
        else { resObj.color = '#991b1b'; resObj.bg = '#fef2f2'; }

        tierBadge.innerText = resObj.tier;
        tierBadge.style.color = resObj.color;
        tierBadge.style.background = resObj.bg;
        statusEl.innerText = resObj.status;
        statusEl.style.color = resObj.color;
        analysisEl.innerText = resObj.analysis;
        
        const indicator = document.getElementById('result-indicator');
        if(indicator) {
            let percent = ((oneMinuteCount - 3) / (20 - 3)) * 90 + 5;
            indicator.style.left = Math.min(95, Math.max(5, percent)) + '%';
        }
        
        state.blinkCount = oneMinuteCount;
        state.blinkTier = resObj.tier;
        state.blinkStatus = resObj.status;

        fetch(`"dummy"`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '"dummy"' },
            body: JSON.stringify({ emp_code: state.empCode, blink_count: oneMinuteCount })
        }).then(res => res.json()).then(data => {
            if(data.success) state.lastBlinkTestId = data.test.id;
        });

        navigate('scr-test-result');
    }

    function downloadCombinedPDF() {
        const element = document.getElementById('pdf-template');
        const blinkCount = state.blinkCount || '0';
        const cvsScore = state.cvsScore || '0';

        document.getElementById('pdf-blink-count').innerText = blinkCount;
        document.getElementById('pdf-date').innerText = new Date().toLocaleDateString('en-GB');
        document.getElementById('pdf-so-name').innerText = state.empName || state.empCode;
        document.getElementById('pdf-report-id').innerText = 'CERT-' + new Date().getTime().toString().substr(-6);
        
        const pdfTier = document.getElementById('pdf-tier-badge');
        pdfTier.innerText = state.blinkTier || '---';
        
        const pdfCvsScore = document.getElementById('pdf-cvs-score');
        if(pdfCvsScore) pdfCvsScore.innerText = cvsScore;
        
        const pdfCvsTier = document.getElementById('pdf-cvs-tier');
        if(pdfCvsTier) pdfCvsTier.innerText = state.cvsTier || '---';

        element.style.display = 'block'; 
        const opt = {
            margin: 10,
            filename: `Ajanta_Eye_Health_${state.empCode}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save().then(() => { element.style.display = 'none'; });
    }

    async function shareToWhatsApp() {
        const num = document.getElementById('whatsapp-num').value.trim();
        const msg = `🩺 *Ajanta Eye Health Screening Complete*\n\nBlink Rate: ${state.blinkCount}\nCVS Score: ${state.cvsScore}\n\nFacilitated by: ${state.empName || state.empCode}`;
        if (!num) return showToast("Please enter a WhatsApp number");
        window.open(`https://wa.me/${num}?text=${encodeURIComponent(msg)}`, '_blank');
    }

    function onTestFinish() {
        document.getElementById('ty-blink-score').innerText = state.blinkCount || '0';
        document.getElementById('ty-blink-status').innerText = state.blinkTier || '---';
        document.getElementById('ty-cvs-score').innerText = state.cvsScore || '0';
        document.getElementById('ty-cvs-status').innerText = state.cvsTier || '---';
        document.getElementById('thank-you-so-code').innerText = state.empCode;

        if (state.isPatientMode) navigate('scr-thank-you');
        else navigate('scr-dashboard');
    }

    const cvsSymptoms = [
        {
                "id": "burning",
                "en": "Burning",
                "hi": "जलन",
                "mr": "जळजळणे",
                "gu": "બળતરા",
                "or": "ପୋଡ଼ାଜଳା",
                "te": "మంట",
                "ta": "எரிச்சல்",
                "kn": "ಉರಿ",
                "ml": "കണ്ണെരിച്ചിൽ",
                "bn": "জ্বলুনি",
                "as": "চকু জ্বলা-পোৰা কৰা"
        },
        {
                "id": "itching",
                "en": "Itching",
                "hi": "खुजली",
                "mr": "खाज सुटणे",
                "gu": "ખંજવાળ",
                "or": "କୁଣ୍ଡାଇ ହେବା",
                "te": "దురద",
                "ta": "அரிப்பு",
                "kn": "ತುರಿಕೆ",
                "ml": "ചൊറിച്ചിൽ",
                "bn": "চুলকানি",
                "as": "চকু খজুওৱা"
        },
        {
                "id": "foreign_body",
                "en": "Feeling of foreign body",
                "hi": "आंख में कुछ होने का अहसास",
                "mr": "डोळ्यात काहीतरी गेल्यासारखे वाटणे",
                "gu": "આંખમાં કંઈક હોવાનો અહેસাস",
                "or": "ଆଖିରେ କିଛି ଥିବା ପରି ଅନୁଭବ",
                "te": "కంటిలో ఏదో ఉన్నట్లు అనిపించడం",
                "ta": "கண்ணில் ஏதோ இருப்பது போன்ற உணர்வு",
                "kn": "ಕಣ್ಣಿನಲ್ಲಿ ಏನೋ ಇರುವಿಕೆ ಅನಿಸಿಕೆ",
                "ml": "കണ്ണിൽ എന്തോ കരട് പോയതുപോലെ",
                "bn": "চোখে কিছু থাকার অনুভূতি",
                "as": "চকুত কিবা বাহিৰা বস্তু থকা যেন লগা"
        },
        {
                "id": "tearing",
                "en": "Excessive tearing",
                "hi": "आंखों से पानी आना",
                "mr": "डोळ्यातून पाणी येणे",
                "gu": "આંખમાંથી પાણી પડવું",
                "or": "ଆଖିରୁ ଲୁହ ବୋହିବା",
                "te": "కంటి నుండి నీరు కారడం",
                "ta": "கண்ணீர் வடிதல்",
                "kn": "ಕಣ್ಣೀರು ಬರುವುದು",
                "ml": "കണ്ണീർ ഒഴുക്ക്",
                "bn": "চোখ দিয়ে জল পড়া",
                "as": "চকুৰ পৰা পানী ওলোৱা"
        },
        {
                "id": "blinking",
                "en": "Excessive blinking",
                "hi": "पलकें झपकना",
                "mr": "पापण्या जास्त लवणे",
                "gu": "વારંવાર આંખ પલકાવવી",
                "or": "ବାରମ୍ବାର ଆଖି ପତା ପକାଇବା",
                "te": "కనురెప్పలు ఎక్కువగా వేయడం",
                "ta": "அடிக்கடி கண் சிமிட்டுதல்",
                "kn": "ಪದೇ ಪದೇ ಕಣ್ಣು ಮಿಟುಕಿಸುವುದು",
                "ml": "അമിതമായി কണ്ണ് ചിമ്മുന്നത്",
                "bn": "ঘনঘন পলক ফেলা",
                "as": "অতিৰিক্তভাৱে চকুৰ পতা লৰচৰ কৰা"
        },
        {
                "id": "redness",
                "en": "Eye redness",
                "hi": "आंखों में लालिमा",
                "mr": "डोळे लाल होणे",
                "gu": "આંખ લાલ થવી",
                "or": "ଆଖି ଲାଲ ହେବା",
                "te": "కళ్ళు ఎర్రబడటం",
                "ta": "கண் சிவத்தல்",
                "kn": "ಕಣ್ಣು ಕೆಂಪಾಗುವುದು",
                "ml": "കണ്ണ് ചുവപ്പ്",
                "bn": "চোখ লাল হওয়া",
                "as": "চকু ৰঙা পৰা"
        },
        {
                "id": "pain",
                "en": "Eye pain",
                "hi": "आंखों में दर्द",
                "mr": "डोळ्यात दुखणे",
                "gu": "આંખમાં દુખાવો",
                "or": "ଆଖିରେ ଯନ୍ତ୍ରଣା",
                "te": "కంటి నొప్పి",
                "ta": "கண் வலி",
                "kn": "ಕಣ್ಣಿನ ನೋವು",
                "ml": "കണ്ണ് വേദന",
                "bn": "চোখে ব্যথা",
                "as": "চকুৰ বিষ"
        },
        {
                "id": "heavy_eyelids",
                "en": "Heavy eyelids",
                "hi": "पलकों का भारीपन",
                "mr": "पापण्या जड होणे",
                "gu": "પોપચા ભારે લાગવા",
                "or": "ଆଖି ପତା ଭାରି ଲାગିବା",
                "te": "కనురెప్పలు బరువుగా అనిపించడం",
                "ta": "கண் இமைகள் கனமாக இருப்பது",
                "kn": "ಕಣ್ಣಿನ ರೆಪ್ಪೆಗಳು ಭಾರವೆನಿಸುವುದು",
                "ml": "കണ്പോളകൾക്ക് ഭാരം",
                "bn": "পলক ভারী হয়ে যাওয়া",
                "as": "চকুৰ পতা গধুৰ হোৱা"
        },
        {
                "id": "dryness",
                "en": "Dryness",
                "hi": "सूखापन",
                "mr": "कोरडेपणा",
                "gu": "સૂકાપણું",
                "or": "ଶୁଷ୍କତା",
                "te": "పొడిబారడం",
                "ta": "வறட்சி",
                "kn": "ಒಣಗಿದಂತಾಗುವುದು",
                "ml": "വരൾച്ച",
                "bn": "শুষ্কতা",
                "as": "চকু শুকাই যোৱা"
        },
        {
                "id": "blurred_vision",
                "en": "Blurred vision",
                "hi": "धुंधली दृष्टि",
                "mr": "अंधुक दिसणे",
                "gu": "ઝાંખી દ્રષ્ટિ",
                "or": "ଅସ୍ପଷ୍ଟ ଦୃଷ୍ଟି",
                "te": "మసక బారిన దృష్టి",
                "ta": "மங்கலான பார்வை",
                "kn": "ಮಸುಕಾದ ದೃಷ್ಟಿ",
                "ml": "മങ്ങിയ കാഴ്ച",
                "bn": "ঝাপসা দৃষ্টি",
                "as": "চকুৰে ধোঁৱাকোৱা দেখা"
        },
        {
                "id": "double_vision",
                "en": "Double vision",
                "hi": "दोहरा दिखाई देना",
                "mr": "दोन-दोन दिसणे",
                "gu": "બેવડી દ્રષ્ટિ",
                "or": "ଦୁଇଟି ଦେଖାଯିବା",
                "te": "రెండింతలుగా కనిపించడం",
                "ta": "இரட்டைப் பார்வை",
                "kn": "ಎರಡೆರಡು ಕಾಣಿಸುವುದು",
                "ml": "രണ്ടായി കാണുന്നത്",
                "bn": "সব কিছু দ্বিগুণ দেখা",
                "as": "এটা বস্তু দুটা দেখা"
        },
        {
                "id": "near_vision",
                "en": "Difficulty focusing for near vision",
                "hi": "पास की दृष्टि पर ध्यान केंद्रित करने में कठिनाई",
                "mr": "जवळचे पाहण्यात अडचण",
                "gu": "નજીકની દ્રષ્ટિ પર ધ્યાન કેન્દ્રિત કરવામાં મુશ્કેલી",
                "or": "ପାଖ ଜିନିଷ ଦେଖିବାରେ ଅସୁବିଧା",
                "te": "దగ్గర వస్తువులను చూడటంలో ఇబ్బంది",
                "ta": "அருகிலுள்ள பார்வையில் கவனம் செலுத்துவதில் சிரமம்",
                "kn": "ಹತ್ತಿರದ ದೃಷ್ಟಿ ಕೇಂದ್ರೀಕರಿಸಲು ತೊಂದರೆ",
                "ml": "സമീപ കാഴ്ചയ്ക്ക് ബുദ്ധിമുട്ട്",
                "bn": "কাছের জিনিসে ফোকাস করতে সমস্যা",
                "as": "ওচৰৰ বস্তুৰ ওপৰত দৃষ্টি কেন্দ্ৰীভূত কৰাত অসুবিধা"
        },
        {
                "id": "light_sensitivity",
                "en": "Increased sensitivity to light",
                "hi": "प्रकाश के प्रति संवेदनशीलता",
                "mr": "प्रकाशाचा त्रास होणे",
                "gu": "પ્રકાશ પ્રત્યે સંવેદનશીલતા",
                "or": "ଆଲୋକ প্ৰତି ସଂବେଦନଶୀଳତା",
                "te": "కాంతి పట్ల సున్నితత్వం",
                "ta": "ஒளி உணர்திறன் அதிகரிப்பு",
                "kn": "ಬೆಳಕಿನ ಕಡೆಗೆ ಸೂಕ್ಷ್ಮತೆ ಹೆಚ್ಚಾಗುವುದು",
                "ml": "വെളിച്ചത്തോടുള്ള അമിത പ്രതികരണം",
                "bn": "আলোতে অস্বস্তি",
                "as": "পোহৰৰ প্ৰতি সংবেদনশীলতা বৃদ্ধি"
        },
        {
                "id": "halos",
                "en": "Coloured halos around objects",
                "hi": "वस्तुओं के चारों ओर रंगीन घेरे",
                "mr": "वस्तूंभोवती रंगीत वलये",
                "gu": "વસ્તુઓની આસપાસ રંગીન વલયો",
                "or": "ବସ୍ତୁ ଚାରିପାଖରେ ରଙ୍ଗୀନ ବଳୟ",
                "te": "వస్తువుల చుట్టూ రంగుల వలయాలు",
                "ta": "பொருட்களைச் சுற்றி வண்ண வளையங்கள்",
                "kn": "ವಸ್ತುಗಳ ಸುತ್ತ ಬಣ್ಣದ ಪ್ರಭಾವಲಯಗಳು",
                "ml": "വസ്തുക്കൾക്ക് ചുറ്റും നിറമുള്ള വളയങ്ങൾ",
                "bn": "বস্তুর চারপাশে রঙিন বলয়",
                "as": "বস্তুৰ চাৰিওফালে ৰঙীন চক্ৰ দেখা"
        },
        {
                "id": "worsening",
                "en": "Feeling that sight is worsening",
                "hi": "दृष्टि खराब होने का अहसास",
                "mr": "दृष्टी कमी होत असल्याचे वाटणे",
                "gu": "દ્રષ્ટિ બગડી રહી હોવાનો અહેસাস",
                "or": "ଦୃଷ୍ଟିଶକ୍ତି ଖରାପ ହେବା ପରି ଅନୁଭব",
                "te": "చూపు మందగిస్తున్నట్లు అనిపించడం",
                "ta": "பார்வை மோசமடைவது போன்ற உணர்வு",
                "kn": "ದೃಷ್ಟಿ ಹದಗೆಡುತ್ತಿದೆ ಎಂಬ ಭಾವನೆ",
                "ml": "കാഴ്ചശക്തി കുറയുന്നതായുള്ള തോന്നൽ",
                "bn": "দৃষ্টিশক্তি কমে যাওয়ার অনুভূতি",
                "as": "দৃষ্টিশক্তি ক্ৰমান্বয়ে হ্ৰাস পোৱা যেন লগা"
        },
        {
                "id": "headache",
                "en": "Headache",
                "hi": "सिरदर्द",
                "mr": "डोकेदुखी",
                "gu": "માથાનો દુખાવો",
                "or": "ମୁଣ୍ଡବିନ୍ଧା",
                "te": "తలనొప్పి",
                "ta": "தலைவலி",
                "kn": "ತಲೆನೋವು",
                "ml": "തലവേദന",
                "bn": "মাথাব্যথা",
                "as": "মূৰৰ বিষ"
        }
];

    
    const translations = {
        "en": {
                "freq": "Frequency",
                "intens": "Intensity",
                "never": "Never",
                "occas": "Occasionally",
                "often": "Often",
                "moderate": "Moderate",
                "intense": "Intense",
                "complete_assessment": "Complete Assessment",
                "cvs_title": "CVS Screening",
                "symptom_assessment": "Symptom Assessment",
                "cvs_subtitle": "Please rate symptoms based on digital device use.",
                "current_cvs_score": "Current CVS Score",
                "screening_guide": "Screening Guide",
                "step1_title": "AI Blink Analysis",
                "step1_desc": "A 15-second AI scan to detect your natural blink rate and eye lubrication.",
                "step2_title": "CVS Symptom Check",
                "step2_desc": "Quick assessment to identify Computer Vision Syndrome and digital strain.",
                "step3_title": "Combined Certificate",
                "step3_desc": "Get a comprehensive medical-grade report with personalized eye care tips.",
                "privacy_title": "Privacy Guaranteed",
                "privacy_desc": "Video is processed locally on your device. No biometric data is stored.",
                "accept_proceed": "Start Assessment",
                "analyzing_blinks": "Analyzing Blinks...",
                "assessment_complete_title": "Assessment Complete",
                "assessment_complete_desc": "Your eye health screening has been securely recorded.",
                "download_cert": "Download Eye Care Certificate",
                "back_home": "Back to Home",
                "rep_id": "Representative ID",
                "next_cvs": "Next: CVS Screening",
                "skip_finish": "Skip & Finish",
                "language_label": "Language",
                "change_lang": "Change Language",
                "stare_center": "Please stare at the center",
                "live_count": "Live Count",
                "hello_rep": "Hello, Representative",
                "stat_today": "Today",
                "stat_month": "Month",
                "stat_total": "Total",
                "daily_progress": "Daily Progress",
                "recent_screenings": "Recent Screenings",
                "share_link": "Share Link",
                "prev": "Prev",
                "next": "Next",
                "stats_nav": "Stats",
                "dry_eye_nav": "Blink Test",
                "cvs_test_nav": "CVS Test",
                "start_motivation": "Start your first screening to ignite your daily goal!",
                "progress_motivation": "Great start! Only {count} more to reach your daily target.",
                "goal_motivation": "Goal achieved! You're making a real impact on eye health today.",
                "no_screenings_title": "No Screenings Yet",
                "no_screenings_desc": "Start your first AI blink test to see patient insights here.",
                "start_new_test": "Start New Test",
                "blink_screening_label": "Blink Test Screening",
                "blinks_count_label": "{count} Blinks",
                "page_label": "Page {count}",
                "init_camera": "Initializing Camera..."
        },
        "hi": {
                "freq": "आवृत्ति",
                "intens": "तीव्रता",
                "never": "कभी नहीं",
                "occas": "कभी-कभी",
                "often": "अक्सर/हमेशा",
                "moderate": "सामान्य",
                "intense": "तीव्र",
                "complete_assessment": "आकलन पूरा करें",
                "cvs_title": "सीवीएस स्क्रीनिंग",
                "symptom_assessment": "लक्षण आकलन",
                "cvs_subtitle": "डिजिटल डिवाइस उपयोग के आधार पर लक्षणों को रेट करें।",
                "current_cvs_score": "वर्तमान स्कोर",
                "screening_guide": "स्क्रीनिंग गाइड",
                "step1_title": "एआई ब्लिंक विश्लेषण",
                "step1_desc": "आपकी प्राकृतिक पलक झपकने की दर का पता लगाने के लिए 15 सेकंड का एआई स्कैन।",
                "step2_title": "सीवीएस लक्षण जांच",
                "step2_desc": "कंप्यूटर विजन सिंड्रोम और डिजिटल तनाव की पहचान करने के लिए त्वरित मूल्यांकन।",
                "step3_title": "संयुक्त प्रमाणपत्र",
                "step3_desc": "व्यक्तिगत आंखों की देखभाल के सुझावों के साथ एक विस्तृत रिपोर्ट प्राप्त करें।",
                "privacy_title": "गोपनीयता की गारंटी",
                "privacy_desc": "वीडियो आपके डिवाइस पर स्थानीय रूप से संसाधित होता है। कोई डेटा संग्रहीत नहीं किया जाता है।",
                "accept_proceed": "मूल्यांकन शुरू करें",
                "analyzing_blinks": "ब्लिंक विश्लेषण हो रहा है...",
                "assessment_complete_title": "मूल्यांकन पूर्ण",
                "assessment_complete_desc": "आपकी आंखों की जांच सुरक्षित रूप से रिकॉर्ड कर ली गई है।",
                "download_cert": "सर्टिफिकेट डाउनलोड करें",
                "back_home": "मुख्य पृष्ठ",
                "rep_id": "प्रतिनिधि आईडी",
                "next_cvs": "अगला: सीवीएस स्क्रीनिंग",
                "skip_finish": "छोड़ें और समाप्त करें",
                "language_label": "भाषा",
                "change_lang": "भाषा बदलें",
                "stare_center": "कृपया केंद्र में देखें",
                "live_count": "लाइव काउंट",
                "hello_rep": "नमस्ते, प्रतिनिधि",
                "stat_today": "आज",
                "stat_month": "महीना",
                "stat_total": "कुल",
                "daily_progress": "दैनिक प्रगति",
                "recent_screenings": "हालिया स्क्रीनिंग",
                "share_link": "लिंक साझा करें",
                "prev": "पिछला",
                "next": "अगला",
                "stats_nav": "आंकड़े",
                "dry_eye_nav": "ब्लिंक टेस्ट",
                "cvs_test_nav": "सीवीएस टेस्ट",
                "start_motivation": "अपना दैनिक लक्ष्य शुरू करने के लिए अपनी पहली स्क्रीनिंग शुरू करें!",
                "progress_motivation": "शानदार शुरुआत! अपने दैनिक लक्ष्य तक पहुँचने के लिए केवल {count} और शेष हैं।",
                "goal_motivation": "लक्ष्य प्राप्त हुआ! आज आप आंखों के स्वास्थ्य पर वास्तविक प्रभाव डाल रहे हैं।",
                "no_screenings_title": "अभी तक कोई स्क्रीनिंग नहीं",
                "no_screenings_desc": "मरीज की जानकारी देखने के लिए अपना पहला एआई ब्लिंक टेस्ट शुरू करें।",
                "start_new_test": "नया टेस्ट शुरू करें",
                "blink_screening_label": "ब्लिंक टेस्ट स्क्रीनिंग",
                "blinks_count_label": "{count} ब्लिंक",
                "page_label": "पृष्ठ {count}",
                "init_camera": "कैमरा शुरू हो रहा है..."
        },
        "as": {
                "freq": "সঘনাই দেখা দিয়ে (Frequency)",
                "intens": "তীব্ৰতা (Intensity)",
                "never": "কেতিয়াও নহয়",
                "occas": "মাজে মাজে",
                "often": "প্ৰায়েই বা সদায়",
                "moderate": "মধ্যমীয়া",
                "intense": "তীব্ৰ",
                "complete_assessment": "মূল্যায়ন সম্পূৰ্ণ কৰক",
                "cvs_title": "কম্পিউটাৰ ভিজন চিনড্ৰম প্ৰশ্নাৱলী (CVS-Q)",
                "symptom_assessment": "লক্ষণৰ মূল্যায়ন",
                "cvs_subtitle": "ডিজিটেল ডিভাইচ ব্যৱহাৰৰ ওপৰত ভিত্তি কৰি লক্ষণসমূহ মূল্যায়ন কৰক।",
                "current_cvs_score": "বৰ্তমানৰ চিভিএছ স্ক’ৰ",
                "screening_guide": "স্ক্ৰীনিং নিৰ্দেশিকা",
                "step1_title": "AI ব্লিংক বিশ্লেষণ",
                "step1_desc": "আপোনাৰ স্বাভাৱিক ব্লিংকৰ হাৰ ধৰা পেলাবলৈ ১৫ ছেকেণ্ডৰ AI স্কেন।",
                "step2_title": "CVS লক্ষণ পৰীক্ষা",
                "step2_desc": "ডিজিটেল চকুৰ চাপ চিনাক্ত কৰিবলৈ দ্ৰুত মূল্যায়ন।",
                "step3_title": "সংযুক্ত প্ৰমাণপত্ৰ",
                "step3_desc": "ব্যক্তিগত চকুৰ যতনৰ পৰামৰ্শৰ সৈতে এক প্ৰতিবেদন লাভ কৰক।",
                "privacy_title": "গোপনীয়তা নিশ্চিত",
                "privacy_desc": "ভিডিঅ’ আপোনাৰ ডিভাইচত স্থানীয়ভাৱে প্ৰক্ৰিয়াকৰণ কৰা হয়।",
                "accept_proceed": "মূল্যায়ন আৰম্ভ কৰক",
                "analyzing_blinks": "ব্লিংক বিশ্লেষণ কৰি থকা হৈছে...",
                "assessment_complete_title": "মূল্যায়ন সম্পূৰ্ণ হ’ল",
                "assessment_complete_desc": "আপোনাৰ চকুৰ স্বাস্থ্য পৰীক্ষা সুৰক্ষিতভাৱে ৰেকৰ্ড কৰা হৈছে।",
                "download_cert": "প্ৰমাণপত্ৰ ডাউনলোড কৰক",
                "back_home": "ঘৰলৈ উভতি যাওক",
                "rep_id": "প্ৰতিনিধিৰ পৰিচয়",
                "next_cvs": "পৰৱৰ্তী: CVS স্ক্ৰীনিং",
                "skip_finish": "এৰিব আৰু শেষ কৰক",
                "language_label": "ভাষা",
                "change_lang": "ভাষা পৰিৱৰ্তন কৰক",
                "stare_center": "অনুগ্ৰহ কৰি কেন্দ্ৰলৈ চাওক",
                "live_count": "লাইভ কাউণ্ট",
                "hello_rep": "নমস্কাৰ, প্ৰতিনিধি",
                "stat_today": "আজি",
                "stat_month": "মাহ",
                "stat_total": "মুঠ",
                "daily_progress": "দৈনিক অগ্ৰগতি",
                "recent_screenings": "শেহতীয়া স্ক্ৰীনিং",
                "share_link": "লিংক শ্বেয়াৰ কৰক",
                "prev": "পূৰ্বৱৰ্তী",
                "next": "পৰৱৰ্তী",
                "stats_nav": "পৰিসংখ্যা",
                "dry_eye_nav": "ব্লিংক টেষ্ট",
                "cvs_test_nav": "CVS টেষ্ট",
                "start_motivation": "আপোনাৰ দৈনিক লক্ষ্যত উপনীত হবলৈ প্ৰথম স্ক্ৰীনিং আৰম্ভ কৰক!",
                "progress_motivation": "সুন্দৰ আৰম্ভণি! আপোনাৰ লক্ষ্যত উপনীত হবলৈ কেৱল {count} টা বাকী আছে।",
                "goal_motivation": "লক্ষ্য অৰ্জন কৰা হ’ল! আজি আপুনি চকুৰ স্বাস্থ্যৰ ওপৰত এক প্ৰকৃত প্ৰভাৱ পেলাইছে।",
                "no_screenings_title": "এতিয়ালৈকে কোনো স্ক্ৰীনিং নাই",
                "no_screenings_desc": "ৰোগীৰ তথ্য চাবলৈ আপোনাৰ প্ৰথম AI ব্লিংক টেষ্ট আৰম্ভ কৰক।",
                "start_new_test": "নতুন পৰীক্ষা আৰম্ভ কৰক",
                "blink_screening_label": "ব্লিংক টেষ্ট স্ক্ৰীনিং",
                "blinks_count_label": "{count} টা ব্লিংক",
                "page_label": "পৃষ্ঠা {count}",
                "init_camera": "কেমেৰা সক্ৰিয় কৰা হৈছে..."
        },
        "mr": {
                "freq": "वारंवारता",
                "intens": "तीव्रता",
                "never": "कधीही नाही",
                "occas": "कधीकधी",
                "often": "नेहमी",
                "moderate": "मध्यम",
                "intense": "तीव्र",
                "complete_assessment": "मूल्यांकन पूर्ण करा",
                "cvs_title": "CVS स्क्रीनिंग",
                "symptom_assessment": "लक्षण मूल्यांकन",
                "cvs_subtitle": "डिजिटल उपकरणांच्या वापराच्या आधारे लक्षणांचे मूल्यांकन करा.",
                "current_cvs_score": "वर्तमान स्कोअर",
                "screening_guide": "स्क्रीनिंग मार्गदर्शिका",
                "step1_title": "AI ब्लिंक विश्लेषण",
                "step1_desc": "तुमच्या नैसर्गिक पापण्या झपकण्याचा दर शोधण्यासाठी १५ सेकंदांचे AI स्कॅन.",
                "step2_title": "CVS लक्षण तपासणी",
                "step2_desc": "डिजिटल डोळ्यांचा ताण ओळखण्यासाठी त्वरित मूल्यांकन.",
                "step3_title": "एकत्रित प्रमाणपत्र",
                "step3_desc": "डोळ्यांच्या काळजीच्या टिप्ससह सविस्तर अहवाल मिळवा.",
                "privacy_title": "गोपनीयतेची खात्री",
                "privacy_desc": "व्हिडिओ तुमच्या डिव्हाइसवर स्थानिक पातळीवर प्रक्रिया केला जातो.",
                "accept_proceed": "मूल्यांकन सुरू करा",
                "analyzing_blinks": "विश्लेषण करत आहे...",
                "assessment_complete_title": "मूल्यांकन पूर्ण",
                "assessment_complete_desc": "तुमच्या डोळ्यांची आरोग्य तपासणी यशस्वीरित्या रेकॉर्ड केली आहे.",
                "download_cert": "प्रमाणपत्र डाउनलोड करा",
                "back_home": "मुख्यपृष्ठ",
                "rep_id": "प्रतिनिधी आयडी",
                "next_cvs": "पुढील: CVS स्क्रीनिंग",
                "skip_finish": "वगळा आणि समाप्त करा",
                "language_label": "भाषा",
                "change_lang": "भाषा बदला",
                "stare_center": "कृपया मध्यभागी पहा",
                "live_count": "थेट गणना",
                "hello_rep": "नमस्कार, प्रतिनिधी",
                "stat_today": "आज",
                "stat_month": "महिना",
                "stat_total": "एकूण",
                "daily_progress": "दैनिक प्रगती",
                "recent_screenings": "अलीकडील स्क्रीनिंग",
                "share_link": "लिंक शेअर करा",
                "prev": "मागे",
                "next": "पुढे",
                "stats_nav": "आकडेवारी",
                "dry_eye_nav": "ब्लिंक टेस्ट",
                "cvs_test_nav": "CVS टेस्ट",
                "start_motivation": "तुमचे दैनंदिन ध्येय गाठण्यासाठी पहिले स्क्रीनिंग सुरू करा!",
                "progress_motivation": "उत्तम सुरुवात! तुमचे ध्येय गाठण्यासाठी फक्त {count} शिल्लक आहेत.",
                "goal_motivation": "ध्येय गाठले! आज तुम्ही डोळ्यांच्या आरोग्यावर वास्तविक प्रभाव टाकत आहात.",
                "no_screenings_title": "अद्याप कोणतेही स्क्रीनिंग नाही",
                "no_screenings_desc": "रुग्णाची माहिती पाहण्यासाठी तुमची पहिली AI ब्लिंक टेस्ट सुरू करा.",
                "start_new_test": "नवीन टेस्ट सुरू करा",
                "blink_screening_label": "ब्लिंक टेस्ट स्क्रीनिंग",
                "blinks_count_label": "{count} ब्लिंक्स",
                "page_label": "पृष्ठ {count}",
                "init_camera": "कॅमेरा सुरू होत आहे..."
        },
        "gu": {
                "freq": "આવૃત્તિ",
                "intens": "તીવ્રતા",
                "never": "ક્યારેય નહીં",
                "occas": "ક્યારેક",
                "often": "વારંવાર",
                "moderate": "મધ્યમ",
                "intense": "તીવ્ર",
                "complete_assessment": "મૂલ્યાંકન પૂર્ણ કરો",
                "cvs_title": "CVS સ્ક્રિનિંગ",
                "symptom_assessment": "લક્ષણ મૂલ્યાંકન",
                "cvs_subtitle": "ડિજિટલ ઉપકરણના ઉપયોગ પર આધારિત લક્ષણોને રેટ કરો.",
                "current_cvs_score": "વર્તમાન સ્કોર",
                "screening_guide": "સ્ક્રિનિંગ માર્ગદર્શિકા",
                "step1_title": "AI બ્લિંક વિશ્લેષણ",
                "step1_desc": "તમારા કુદરતી પલક ઝપકાવવાના દરને શોધવા માટે ૧૫ સેકન્ડનું AI સ્કેન.",
                "step2_title": "CVS લક્ષણ તપાસ",
                "step2_desc": "ડિજિટલ આંખના તાણને ઓળખવા માટે ઝડપી મૂલ્યાંકન.",
                "step3_title": "સંયુક્ત પ્રમાણપત્ર",
                "step3_desc": "આંખની સંભાળની ટિપ્સ સાથે વિગતવાર અહેવાલ મેળવો.",
                "privacy_title": "ગોપનીયતાની ખાતરી",
                "privacy_desc": "વિડિયો તમારા ઉપકરણ પર સ્થાનિક રીતે પ્રક્રિયા કરવામાં આવે છે.",
                "accept_proceed": "મૂલ્યાંકન શરૂ કરો",
                "analyzing_blinks": "વિશ્લેષણ કરી રહ્યા છીએ...",
                "assessment_complete_title": "મૂલ્યાંકન પૂર્ણ",
                "assessment_complete_desc": "તમારી આંખની તપાસ સફળતાપૂર્વક રેકોર્ડ કરવામાં આવી છે.",
                "download_cert": "પ્રમાણપત્ર ડાઉનલોડ કરો",
                "back_home": "મુખ્ય પૃષ્ઠ",
                "rep_id": "પ્રતિનિધિ ID",
                "next_cvs": "આગળ: CVS સ્ક્રિનિંગ",
                "skip_finish": "છોડો અને સમાપ્ત કરો",
                "language_label": "ભાષા",
                "change_lang": "ભાષા બદલો",
                "stare_center": "કૃપા કરીને કેન્દ્રમાં જુઓ",
                "live_count": "લાઇવ કાઉન્ટ",
                "hello_rep": "નમસ્તે, પ્રતિનિધિ",
                "stat_today": "આજે",
                "stat_month": "મહિનો",
                "stat_total": "કુલ",
                "daily_progress": "દૈનિક પ્રગતિ",
                "recent_screenings": "તાજેતરના સ્ક્રિનિંગ",
                "share_link": "લિંક શેર કરો",
                "prev": "પાછળ",
                "next": "આગળ",
                "stats_nav": "આંકડા",
                "dry_eye_nav": "બ્લિંક ટેસ્ટ",
                "cvs_test_nav": "CVS ટેસ્ટ",
                "start_motivation": "તમારો દૈનિક ધ્યેય શરૂ કરવા માટે તમારું પ્રથમ સ્ક્રિનિંગ શરૂ કરો!",
                "progress_motivation": "સરસ શરૂઆત! તમારા દૈનિક લક્ષ્ય સુધી પહોંચવા માટે માત્ર {count} બાકી છે.",
                "goal_motivation": "ધ્યેય પ્રાપ્ત થયો! આજે તમે આંખના સ્વાસ્થ્ય પર વાસ્તવિક પ્રભાવ પાડી રહ્યા છો.",
                "no_screenings_title": "હજુ સુધી કોઈ સ્ક્રિનિંગ નથી",
                "no_screenings_desc": "દર્દીની માહિતી જોવા માટે તમારી પ્રથમ AI બ્લિંક ટેસ્ટ શરૂ કરો.",
                "start_new_test": "નવી ટેસ્ટ શરૂ કરો",
                "blink_screening_label": "બ્લિંક ટેસ્ટ સ્ક્રિનિંગ",
                "blinks_count_label": "{count} બ્લિંક્સ",
                "page_label": "પૃષ્ઠ {count}",
                "init_camera": "કેમેરા શરૂ થઈ રહ્યો છે..."
        },
        "or": {
                "freq": "ବାରମ୍ବାରତା",
                "intens": "ତୀବ୍ରତା",
                "never": "କେବେ ନୁହେଁ",
                "occas": "ବେଳେବେଳେ",
                "often": "ସବୁବେଳେ",
                "moderate": "ମଧ୍ୟମ",
                "intense": "ତୀବ୍ର",
                "complete_assessment": "ମୂଲ୍ୟାଙ୍କନ ଶେଷ କରନ୍ତୁ",
                "cvs_title": "CVS ସ୍କ୍ରିନିଂ",
                "symptom_assessment": "ଲକ୍ଷଣ ମୂଲ୍ୟାଙ୍କନ",
                "cvs_subtitle": "ଡିଜିଟାଲ୍ ଡିଭାଇସ୍ ବ୍ୟବହାର ଆଧାରରେ ଲକ୍ଷଣଗୁଡ଼ିକର ମୂଲ୍ୟାଙ୍କନ କରନ୍ତୁ |",
                "current_cvs_score": "ସାମ୍ପ୍ରତିକ ସ୍କୋର",
                "screening_guide": "ସ୍କ୍ରିନିଂ ମାର୍ଗଦର୍ଶିକା",
                "step1_title": "AI ବ୍ଲିଙ୍କ୍ ବିଶ୍ଳେଷଣ",
                "step1_desc": "ଆପଣଙ୍କର ପ୍ରାକୃତିକ ପଲକ ହାର ଚିହ୍ନଟ କରିବାକୁ ୧୫ ସେକେଣ୍ଡର AI ସ୍କାନ |",
                "step2_title": "CVS ଲକ୍ଷଣ ଯାଞ୍ଚ",
                "step2_desc": "ଡିଜିଟାଲ୍ ଆଖି ଚାପ ଚିହ୍ନଟ କରିବାକୁ ଶୀଘ୍ର ମୂଲ୍ୟାଙ୍କନ |",
                "step3_title": "ମିଳିତ ପ୍ରମାଣପତ୍ର",
                "step3_desc": "ଆଖି ଯତ୍ନ ପରାମର୍ଶ ସହିତ ଏକ ବିସ୍ତୃତ ରିପୋର୍ଟ ପାଆନ୍ତୁ |",
                "privacy_title": "ଗୋପନୀୟତା ସୁନିଶ୍ଚିତ",
                "privacy_desc": "ଭିଡିଓ ଆପଣଙ୍କ ଡିଭାଇସରେ ସ୍ଥାନୀୟ ଭାବରେ ପ୍ରକ୍ରିୟାକରଣ ହୁଏ |",
                "accept_proceed": "ମୂଲ୍ୟାଙ୍କନ ଆରମ୍ଭ କରନ୍ତୁ",
                "analyzing_blinks": "ବିଶ୍ଳେଷଣ ଚାଲିଛି...",
                "assessment_complete_title": "ମୂଲ୍ୟାଙ୍କନ ସମାପ୍ତ",
                "assessment_complete_desc": "ଆପଣଙ୍କର ଆଖି ପରୀକ୍ଷା ସଫଳତାର ସହିତ ରେକର୍ଡ କରାଯାଇଛି |",
                "download_cert": "ପ୍ରମାଣପତ୍ର ଡାଉନଲୋଡ୍ କରନ୍ତୁ",
                "back_home": "ମୁଖ୍ୟ ପୃଷ୍ଠା",
                "rep_id": "ପ୍ରତିନିଧି ID",
                "next_cvs": "ପରବର୍ତ୍ତୀ: CVS ସ୍କ୍ରିନିଂ",
                "skip_finish": "ଛାଡିଦିঅନ୍ତୁ ଏବଂ ଶେଷ କରନ୍ତୁ",
                "language_label": "ଭାଷା",
                "change_lang": "ଭାଷା ପରିବର୍ତ୍ତନ କରନ୍ତୁ",
                "stare_center": "ଦୟାକରି କେନ୍ଦ୍ରକୁ ଚାହାଁନ୍ତୁ",
                "live_count": "ଲାଇଭ୍ ଗଣନା",
                "hello_rep": "ନମସ୍କାର, ପ୍ରତିନିଧି",
                "stat_today": "ଆଜି",
                "stat_month": "ମାସ",
                "stat_total": "ମୋଟ",
                "daily_progress": "ଦୈନିକ ପ୍ରଗତି",
                "recent_screenings": "ସାମ୍ପ୍ରତିକ ସ୍କ୍ରିନିଂ",
                "share_link": "ଲିଙ୍କ୍ ସେୟାର୍ କରନ୍ତୁ",
                "prev": "ପୂର୍ବବର୍ତ୍ତୀ",
                "next": "ପରବର୍ତ୍ତୀ",
                "stats_nav": "ପରିସଂଖ୍ୟାନ",
                "dry_eye_nav": "ବ୍ଲିଙ୍କ ଟେଷ୍ଟ",
                "cvs_test_nav": "CVS ଟେଷ୍ଟ",
                "start_motivation": "ଆପଣଙ୍କର ଦୈନିକ ଲକ୍ଷ୍ୟ ଆରମ୍ଭ କରିବାକୁ ଆପଣଙ୍କର ପ୍ରଥମ ସ୍କ୍ରିନିଂ ଆରମ୍ଭ କରନ୍ତୁ!",
                "progress_motivation": "ଉତ୍ତମ ଆରମ୍ଭ! ଆପଣଙ୍କର ଦୈନିକ ଲକ୍ଷ୍ୟରେ ପହଞ୍ଚିବା ପାଇଁ କେବଳ {count} ବାକି ଅଛି |",
                "goal_motivation": "ଲକ୍ଷ୍ୟ ହାସଲ ହେଲା! ଆଜି ଆପଣ ଆଖି ସ୍ୱାସ୍ଥ୍ୟ ଉପରେ ଏକ ପ୍ରକୃତ ପ୍ରଭାବ ପକାଉଛନ୍ତି |",
                "no_screenings_title": "ଏପର୍ଯ୍ୟନ୍ତ କୌଣସି ସ୍କ୍ରିନିଂ ହୋଇନାହିଁ",
                "no_screenings_desc": "ରୋଗୀର ତଥ୍ୟ ଦେଖିବାକୁ ଆପଣଙ୍କର ପ୍ରଥମ AI ବ୍ଲିଙ୍କ୍ ଟେଷ୍ଟ ଆରମ୍ଭ କରନ୍ତୁ |",
                "start_new_test": "ନୂତନ ପରୀକ୍ଷା ଆରମ୍ଭ କରନ୍ତୁ",
                "blink_screening_label": "ବ୍ଲିଙ୍କ୍ ଟେଷ୍ଟ ସ୍କ୍ରିନିଂ",
                "blinks_count_label": "{count} ବ୍ଲିଙ୍କ୍",
                "page_label": "ପୃଷ୍ଠା {count}",
                "init_camera": "କ୍ୟାମେରା ଆରମ୍ଭ ହେଉଛି..."
        },
        "te": {
                "freq": "ఫ్రీక్వెన్సీ",
                "intens": "తీవ్రత",
                "never": "ఎప్పుడూ కాదు",
                "occas": "అప్పుడప్పుడు",
                "often": "తరచుగా",
                "moderate": "మితమైన",
                "intense": "తీవ్రమైన",
                "complete_assessment": "మూల్యాంకనం పూర్తి చేయండి",
                "cvs_title": "CVS స్క్రీనింగ్",
                "symptom_assessment": "లక్షణ మూల్యాంకనం",
                "cvs_subtitle": "డిజిటల్ పరికరాల వినియోగం ఆధారంగా లక్షణాలను రేట్ చేయండి.",
                "current_cvs_score": "ప్రస్తుత స్కోరు",
                "screening_guide": "స్క్రీనింగ్ గైడ్",
                "step1_title": "AI బ్లింక్ విశ్లేషణ",
                "step1_desc": "మీ సహజమైన కనురెప్పల రేటును గుర్తించడానికి 15 సెకన్ల AI స్కాన్.",
                "step2_title": "CVS లక్షణ తనిఖీ",
                "step2_desc": "డిజిటల్ కంటి ఒత్తిడిని గుర్తించడానికి వేగవంతమైన మూల్యాంకనం.",
                "step3_title": "కంబైన్డ్ సర్టిఫికేట్",
                "step3_desc": "కంటి సంరక్షణ చిట్కాలతో కూడిన వివరణాత్మక నివేదికను పొందండి.",
                "privacy_title": "గోప్యత హామీ",
                "privacy_desc": "వీడియో మీ పరికరంలో స్థానికంగా ప్రాసెస్ చేయబడుతుంది.",
                "accept_proceed": "మూల్యాంకనాన్ని ప్రారంభించండి",
                "analyzing_blinks": "విశ్లేషిస్తోంది...",
                "assessment_complete_title": "మూల్యాంకనం పూర్తయింది",
                "assessment_complete_desc": "మీ కంటి ఆరోగ్య పరీక్ష విజయవంతంగా నమోదు చేయబడింది.",
                "download_cert": "సర్టిఫికేట్ డౌన్‌లోడ్ చేయండి",
                "back_home": "హోమ్‌కు తిరిగి వెళ్లండి",
                "rep_id": "ప్రతినిధి ID",
                "next_cvs": "తదుపరి: CVS స్క్రీనింగ్",
                "skip_finish": "వదిలేయండి & పూర్తి చేయండి",
                "language_label": "భాష",
                "change_lang": "భాషను మార్చండి",
                "stare_center": "దయచేసి మధ్యలో చూడండి",
                "live_count": "లైవ్ కౌంట్",
                "hello_rep": "నమస్కారం, ప్రతినిధి",
                "stat_today": "ఈరోజు",
                "stat_month": "నెల",
                "stat_total": "మొత్తం",
                "daily_progress": "రోజువారీ పురోగతి",
                "recent_screenings": "ఇటీవలి స్క్రీనింగ్లు",
                "share_link": "లింక్‌ను భాగస్వామ్యం చేయండి",
                "prev": "మునుపటి",
                "next": "తదుపరి",
                "stats_nav": "గణాంకాలు",
                "dry_eye_nav": "బ్లింక్ టెస్ట్",
                "cvs_test_nav": "CVS టెస్ట్",
                "start_motivation": "మీ రోజువారీ లక్ష్యాన్ని ప్రారంభించడానికి మీ మొదటి స్క్రీనింగ్‌ను ప్రారంభించండి!",
                "progress_motivation": "గొప్ప ప్రారంభం! మీ లక్ష్యాన్ని చేరుకోవడానికి కేవలం {count} మాత్రమే మిగిలి ఉన్నాయి.",
                "goal_motivation": "లక్ష్యం సాధించబడింది! ఈరోజు మీరు కంటి ఆరోగ్యంపై నిజమైన ప్రభావం చూపుతున్నారు.",
                "no_screenings_title": "ఇంకా స్క్రీనింగ్‌లు లేవు",
                "no_screenings_desc": "రోగి సమాచారాన్ని చూడటానికి మీ మొదటి AI బ్లింక్ టెస్ట్‌ను ప్రారంభించండి.",
                "start_new_test": "కొత్త టెస్ట్ ప్రారంభించండి",
                "blink_screening_label": "బ్లింక్ టెస్ట్ స్క్రీనింగ్",
                "blinks_count_label": "{count} బ్లింక్‌లు",
                "page_label": "పేజీ {count}",
                "init_camera": "కెమెరా ప్రారంభించబడుతోంది..."
        },
        "ta": {
                "freq": "அதிர்வெண்",
                "intens": "தீவிரம்",
                "never": "ஒருபோதும் இல்லை",
                "occas": "அவ்வப்போது",
                "often": "அடிக்கடி",
                "moderate": "மிதமானது",
                "intense": "தீவிரமானது",
                "complete_assessment": "மதிப்பீட்டை முடிக்கவும்",
                "cvs_title": "CVS ஸ்கிரீனிங்",
                "symptom_assessment": "அறிகுறி மதிப்பீடு",
                "cvs_subtitle": "டிஜிட்டல் சாதனப் பயன்பாட்டின் அடிப்படையில் அறிகுறிகளை மதிப்பிடவும்.",
                "current_cvs_score": "தற்போதைய மதிப்பெண்",
                "screening_guide": "ஸ்கிரீனிங் வழிகாட்டி",
                "step1_title": "AI கண் சிமிட்டல் பகுப்பாய்வு",
                "step1_desc": "உங்கள் இயல்பான கண் சிமிட்டல் வீதத்தைக் கண்டறிய 15 வினாடி AI ஸ்கேன்.",
                "step2_title": "CVS அறிகுறி சரிபார்ப்பு",
                "step2_desc": "டிஜிட்டல் கண் அழுத்தத்தைக் கண்டறிய விரைவான மதிப்பீடு.",
                "step3_title": "கூட்டுச் சான்றிதழ்",
                "step3_desc": "கண் பராமரிப்பு உதவிக்குறிப்புகளுடன் விரிவான அறிக்கையைப் பெறுங்கள்.",
                "privacy_title": "தனியுரிமை உறுதி",
                "privacy_desc": "வீடியோ உங்கள் சாதனத்தில் உள்ளூர் ரீதியாக செயலாக்கப்படுகிறது.",
                "accept_proceed": "மதிப்பீட்டைத் தொடங்கவும்",
                "analyzing_blinks": "பகுப்பாய்வு செய்கிறது...",
                "assessment_complete_title": "மதிப்பீடு முடிந்தது",
                "assessment_complete_desc": "உங்கள் கண் சுகாதார பரிசோதனை வெற்றிகரமாக பதிவு செய்யப்பட்டுள்ளது.",
                "download_cert": "சான்றிதழைப் பதிவிறக்கவும்",
                "back_home": "முகப்புக்குத் திரும்பு",
                "rep_id": "பிரதிநிதி ஐடி",
                "next_cvs": "அடுத்து: CVS ஸ்கிரீனிங்",
                "skip_finish": "தவிர் & முடி",
                "language_label": "மொழி",
                "change_lang": "மொழியை மாற்றவும்",
                "stare_center": "மையத்தைப் பார்க்கவும்",
                "live_count": "நேரடி எண்ணிக்கை",
                "hello_rep": "வணக்கம், பிரதிநிதி",
                "stat_today": "இன்று",
                "stat_month": "மாதம்",
                "stat_total": "மொத்தம்",
                "daily_progress": "தினசரி முன்னேற்றம்",
                "recent_screenings": "சமீபத்திய ஸ்கிரீனிங்",
                "share_link": "இணைப்பைப் பகிரவும்",
                "prev": "முந்தைய",
                "next": "அடுத்தது",
                "stats_nav": "புள்ளிவிவரங்கள்",
                "dry_eye_nav": "பிளிங்க் சோதனை",
                "cvs_test_nav": "CVS சோதனை",
                "start_motivation": "உங்கள் தினசரி இலக்கைத் தொடங்க உங்கள் முதல் ஸ்கிரீனிங்கைத் தொடங்குங்கள்!",
                "progress_motivation": "சிறந்த ஆரம்பம்! உங்கள் இலக்கை அடைய இன்னும் {count} மட்டுமே மீதமுள்ளது.",
                "goal_motivation": "இலக்கு எட்டப்பட்டது! இன்று நீங்கள் கண் ஆரோக்கியத்தில் உண்மையான தாக்கத்தை ஏற்படுத்துகிறீர்கள்.",
                "no_screenings_title": "இன்னும் ஸ்கிரீனிங் இல்லை",
                "no_screenings_desc": "நோயாளி தகவலைக் காண உங்கள் முதல் AI கண் சிமிட்டல் சோதனையைத் தொடங்குங்கள்.",
                "start_new_test": "புதிய சோதனையைத் தொடங்கவும்",
                "blink_screening_label": "கண் சிமிட்டல் சோதனை ஸ்கிரீனிங்",
                "blinks_count_label": "{count} கண் சிமிட்டல்கள்",
                "page_label": "பக்கம் {count}",
                "init_camera": "கேமரா தொடங்குகிறது..."
        },
        "kn": {
                "freq": "ಆವರ್ತನ",
                "intens": "ತೀವ್ರತೆ",
                "never": "ಎಂದಿಗೂ ಇಲ್ಲ",
                "occas": "ಅಪರೂಪಕ್ಕೆ",
                "often": "ಪದೇ ಪದೇ",
                "moderate": "ಮಧ್ಯಮ",
                "intense": "ತೀವ್ರ",
                "complete_assessment": "ಮೌಲ್ಯಮಾಪನ ಪೂರ್ಣಗೊಳಿಸಿ",
                "cvs_title": "CVS ಸ್ಕ್ರೀನಿಂಗ್",
                "symptom_assessment": "ಲಕ್ಷಣ ಮೌಲ್ಯಮಾಪನ",
                "cvs_subtitle": "ಡಿಜಿಟಲ್ ಸಾಧನ ಬಳಕೆಯ ಆಧಾರದ ಮೇಲೆ ಲಕ್ಷಣಗಳನ್ನು ರೇಟ್ ಮಾಡಿ.",
                "current_cvs_score": "ಪ್ರಸ್ತುತ ಸ್ಕೋರ್",
                "screening_guide": "ಸ್ಕ್ರೀನಿಂಗ್ ಮಾರ್ಗದರ್ಶಿ",
                "step1_title": "AI ಬ್ಲಿಂಕ್ ವಿಶ್ಲೇಷಣೆ",
                "step1_desc": "ನಿಮ್ಮ ನೈಸರ್ಗಿಕ ಕಣ್ಣು ಮಿಟುಕಿಸುವ ದರವನ್ನು ಪತ್ತೆಹಚ್ಚಲು 15 ಸೆಕೆಂಡುಗಳ AI ಸ್ಕ್ಯಾನ್.",
                "step2_title": "CVS ಲಕ್ಷಣ ಪರಿಶೀಲನೆ",
                "step2_desc": "ಡಿಜಿಟಲ್ ಕಣ್ಣಿನ ಒತ್ತಡವನ್ನು ಗುರುತಿಸಲು ತ್ವರಿತ ಮೌಲ್ಯಮಾಪನ.",
                "step3_title": "ಸಂಯೋಜಿತ ಪ್ರಮಾಣಪತ್ರ",
                "step3_desc": "ಕಣ್ಣಿನ ಆರೈಕೆ ಸಲಹೆಗಳೊಂದಿಗೆ ವಿವರವಾದ ವರದಿಯನ್ನು ಪಡೆಯಿರಿ.",
                "privacy_title": "ಗೌಪ್ಯತೆ ಖಾತರಿ",
                "privacy_desc": "ವೀಡಿಯೊ ನಿಮ್ಮ ಸಾಧನದಲ್ಲಿ ಸ್ಥಳೀಯವಾಗಿ ಪ್ರಕ್ರಿಯೆಗೊಳಿಸಲ್ಪಡುತ್ತದೆ.",
                "accept_proceed": "ಮೌಲ್ಯಮಾಪನ ಪ್ರಾರಂಭಿಸಿ",
                "analyzing_blinks": "ವಿಶ್ಲೇಷಿಸಲಾಗುತ್ತಿದೆ...",
                "assessment_complete_title": "ಮೌಲ್ಯಮಾಪನ ಪೂರ್ಣಗೊಂಡಿದೆ",
                "assessment_complete_desc": "ನಿಮ್ಮ ಕಣ್ಣಿನ ಆರೋಗ್ಯ ತಪಾಸಣೆಯನ್ನು ಯಶಸ್ವಿಯಾಗಿ ದಾಖಲಿಸಲಾಗಿದೆ.",
                "download_cert": "ಪ್ರಮಾಣಪತ್ರ ಡೌನ್‌ಲೋಡ್ ಮಾಡಿ",
                "back_home": "ಹೋಮ್‌ಗೆ ಹಿಂತಿರುಗಿ",
                "rep_id": "ಪ್ರತಿನಿಧಿ ID",
                "next_cvs": "ಮುಂದೆ: CVS ಸ್ಕ್ರೀನಿಂಗ್",
                "skip_finish": "ಬಿಟ್ಟುಬಿಡಿ ಮತ್ತು ಮುಗಿಸಿ",
                "language_label": "ಭಾಷೆ",
                "change_lang": "ಭಾಷೆ ಬದಲಾಯಿಸಿ",
                "stare_center": "ದಯವಿಟ್ಟು ಕೇಂದ್ರವನ್ನು ನೋಡಿ",
                "live_count": "ಲೈವ್ ಕೌಂಟ್",
                "hello_rep": "ನಮಸ್ಕಾರ, ಪ್ರತಿನಿಧಿ",
                "stat_today": "ಇಂದು",
                "stat_month": "ತಿಂಗಳು",
                "stat_total": "ಒಟ್ಟು",
                "daily_progress": "ದೈನಂದಿನ ಪ್ರಗತಿ",
                "recent_screenings": "ಇತ್ತೀಚಿನ ಸ್ಕ್ರೀನಿಂಗ್ಗಳು",
                "share_link": "ಲಿಂಕ್ ಹಂಚಿಕೊಳ್ಳಿ",
                "prev": "ಹಿಂದಿನ",
                "next": "ಮುಂದಿನ",
                "stats_nav": "ಅಂಕಿಅಂಶಗಳು",
                "dry_eye_nav": "ಬ್ಲಿಂಕ್ ಟೆಸ್ಟ್",
                "cvs_test_nav": "CVS ಟೆಸ್ಟ್",
                "start_motivation": "ನಿಮ್ಮ ದೈನಂದಿನ ಗುರಿಯನ್ನು ಪ್ರಾರಂಭಿಸಲು ನಿಮ್ಮ ಮೊದಲ ಸ್ಕ್ರೀನಿಂಗ್ ಪ್ರಾರಂಭಿಸಿ!",
                "progress_motivation": "ಉತ್ತಮ ಆರಂಭ! ನಿಮ್ಮ ಗುರಿಯನ್ನು ತಲುಪಲು ಕೇವಲ {count} ಬಾಕಿ ಇದೆ.",
                "goal_motivation": "ಗುರಿ ಸಾಧಿಸಲಾಗಿದೆ! ಇಂದು ನೀವು ಕಣ್ಣಿನ ಆರೋಗ್ಯದ ಮೇಲೆ ನೈಜ ಪ್ರಭಾವ ಬೀರುತ್ತಿದ್ದೀರಿ.",
                "no_screenings_title": "ಇನ್ನೂ ಯಾವುದೇ ಸ್ಕ್ರೀನಿಂಗ್ ಇಲ್ಲ",
                "no_screenings_desc": "ರೋಗಿಯ ಮಾಹಿತಿಯನ್ನು ನೋಡಲು ನಿಮ್ಮ ಮೊದಲ AI ಬ್ಲಿಂಕ್ ಟೆಸ್ಟ್ ಪ್ರಾರಂಭಿಸಿ.",
                "start_new_test": "ಹೊಸ ಟೆಸ್ಟ್ ಪ್ರಾರಂಭಿಸಿ",
                "blink_screening_label": "ಬ್ಲಿಂಕ್ ಟೆಸ್ಟ್ ಸ್ಕ್ರೀನಿಂಗ್",
                "blinks_count_label": "{count} ಬ್ಲಿಂಕ್‌ಗಳು",
                "page_label": "ಪುಟ {count}",
                "init_camera": "ಕ್ಯಾಮೆರಾ ಪ್ರಾರಂಭವಾಗುತ್ತಿದೆ..."
        },
        "ml": {
                "freq": "ആവൃത്തി",
                "intens": "തീവ്രത",
                "never": "ഒരിക്കലുമില്ല",
                "occas": "അപൂർവ്വമായി",
                "often": "പലപ്പോഴും",
                "moderate": "മിതമായ",
                "intense": "തീവ്രമായ",
                "complete_assessment": "മൂല്യനിർണ്ണയം പൂർത്തിയാക്കുക",
                "cvs_title": "CVS സ്ക്രീനിംഗ്",
                "symptom_assessment": "ലക്ഷണങ്ങളുടെ വിലയിരുത്തൽ",
                "cvs_subtitle": "ഡിജിറ്റൽ ഉപകരണങ്ങളുടെ ഉപയോഗത്തെ അടിസ്ഥാനമാക്കി ലക്ഷണങ്ങൾ വിലയിരുത്തുക.",
                "current_cvs_score": "നിലവിലെ സ്കോർ",
                "screening_guide": "സ്ക്രീനിംഗ് ഗൈഡ്",
                "step1_title": "AI ബ്ലിങ്ക് വിശകലനം",
                "step1_desc": "നിങ്ങളുടെ സ്വാഭാവിക കണ്ണ് ചിമ്മൽ നിരക്ക് കണ്ടെത്തുന്നതിന് 15 സെക്കൻഡ് AI സ്കാൻ.",
                "step2_title": "CVS ലക്ഷണ പരിശോധന",
                "step2_desc": "ഡിജിറ്റൽ കണ്ണിന്റെ ആയാസം തിരിച്ചറിയുന്നതിനുള്ള ദ്രുത വിലയിരുത്തൽ.",
                "step3_title": "സംയോജിത സർട്ടിഫിക്കറ്റ്",
                "step3_desc": "കണ്ണ് സംരക്ഷണ നുറുങ്ങുകൾ അടങ്ങിയ വിശദമായ റിപ്പോർട്ട് നേടുക.",
                "privacy_title": "സ്വകാര്യത ഉറപ്പ്",
                "privacy_desc": "വീഡിയോ നിങ്ങളുടെ ഉപകരണത്തിൽ പ്രാദേശികമായി പ്രോസസ്സ് ചെയ്യുന്നു.",
                "accept_proceed": "മൂല്യനിർണ്ണയം ആരംഭിക്കുക",
                "analyzing_blinks": "വിശകലനം ചെയ്യുന്നു...",
                "assessment_complete_title": "മൂല്യനിർണ്ണയം പൂർത്തിയായി",
                "assessment_complete_desc": "നിങ്ങളുടെ കണ്ണ് ആരോഗ്യ പരിശോധന വിജയകരമായി രേഖപ്പെടുത്തി.",
                "download_cert": "സർട്ടിഫിക്കറ്റ് ഡൗൺಲೋഡ് ചെയ്യുക",
                "back_home": "ഹോമിലേക്ക് മടങ്ങുക",
                "rep_id": "പ്രതിനിധി ഐഡി",
                "next_cvs": "അടുത്തത്: CVS സ്ക്രീനിംഗ്",
                "skip_finish": "ഒഴിവാക്കി പൂർത്തിയാക്കുക",
                "language_label": "ഭാഷ",
                "change_lang": "ഭാഷ മാറ്റുക",
                "stare_center": "ദയവായി മധ്യഭാഗത്തേക്ക് നോക്കുക",
                "live_count": "ലൈവ് കൗണ്ട്",
                "hello_rep": "നമസ്കാരം, പ്രതിനിധി",
                "stat_today": "ഇന്ന്",
                "stat_month": "മാസം",
                "stat_total": "ആകെ",
                "daily_progress": "ദൈനംദിന പുരോഗതി",
                "recent_screenings": "സമീപകാല സ്ക്രീനിംഗുകൾ",
                "share_link": "ലിങ്ക് പങ്കിടുക",
                "prev": "മുൻപത്തെ",
                "next": "അടുത്തത്",
                "stats_nav": "സ്ഥിതിവിവരക്കണക്കുകൾ",
                "dry_eye_nav": "ബ്ലിങ്ക് ടെസ്റ്റ്",
                "cvs_test_nav": "CVS ടെസ്റ്റ്",
                "start_motivation": "നിങ്ങളുടെ ദൈനംദിന ലക്ഷ്യം ആരംഭിക്കുന്നതിന് നിങ്ങളുടെ ആദ്യ സ്ക്രീനിംഗ് തുടങ്ങുക!",
                "progress_motivation": "മികച്ച തുടക്കം! നിങ്ങളുടെ ലക്ഷ്യത്തിലെത്താൻ ഇനി {count} എണ്ണം കൂടി മാത്രം.",
                "goal_motivation": "ലക്ഷ്യം കൈവരിച്ചു! ഇന്ന് നിങ്ങൾ കണ്ണ് ആരോഗ്യത്തിൽ വലിയ മാറ്റമുണ്ടാക്കുന്നു.",
                "no_screenings_title": "സ്ക്രീനിംഗുകൾ ഒന്നുമില്ല",
                "no_screenings_desc": "രോഗിയുടെ വിവരങ്ങൾ കാണാൻ നിങ്ങളുടെ ആദ്യ AI ബ്ലിങ്ക് ടെസ്റ്റ് ആരംഭിക്കുക.",
                "start_new_test": "പുതിയ ടെസ്റ്റ് തുടങ്ങുക",
                "blink_screening_label": "ബ്ലിങ്ക് ടെസ്റ്റ് സ്ക്രീനിംഗ്",
                "blinks_count_label": "{count} ബ്ലിങ്കുകൾ",
                "page_label": "പേജ് {count}",
                "init_camera": "ക്യാമറ ആരംഭിക്കുന്നു..."
        },
        "bn": {
                "freq": "ফ্রিকোয়েন্সি",
                "intens": "তীব্রতা",
                "never": "কখনও না",
                "occas": "মাঝে মাঝে",
                "often": "প্রায়ই",
                "moderate": "মাঝারি",
                "intense": "তীব্র",
                "complete_assessment": "মূল্যায়ন সম্পন্ন করুন",
                "cvs_title": "সিভিএস স্ক্রিনিং",
                "symptom_assessment": "উপসর্গ মূল্যায়ন",
                "cvs_subtitle": "ডিজিটাল ডিভাইস ব্যবহারের উপর ভিত্তি করে উপসর্গ রেট করুন।",
                "current_cvs_score": "বর্তমান স্কোর",
                "screening_guide": "স্ক্রিনিং গাইড",
                "step1_title": "এআই ব্লিংক বিশ্লেষণ",
                "step1_desc": "আপনার স্বাভাবিক ব্লিংকের হার সনাক্ত করতে ১৫ সেকেন্ডের এআই স্ক্যান।",
                "step2_title": "সিভিএস উপসর্গ পরীক্ষা",
                "step2_desc": "ডিজিটাল চোখের চাপ সনাক্ত করতে দ্রুত মূল্যায়ন।",
                "step3_title": "সম্মিলিত শংসাপত্র",
                "step3_desc": "চোখের যত্নের টিপস সহ একটি বিস্তারিত প্রতিবেদন পান।",
                "privacy_title": "গোপনীয়তা নিশ্চিত",
                "privacy_desc": "ভিডিও আপনার ডিভাইসে স্থানীয়ভাবে প্রক্রিয়া করা হয়।",
                "accept_proceed": "মূল্যায়ন শুরু করুন",
                "analyzing_blinks": "বিশ্লেষণ করা হচ্ছে...",
                "assessment_complete_title": "মূল্যায়ন সম্পন্ন",
                "assessment_complete_desc": "আপনার চোখের স্বাস্থ্য স্ক্রীনিং সফলভাবে রেকর্ড করা হয়েছে।",
                "download_cert": "শংসাপত্র ডাউনলোড করুন",
                "back_home": "হোমে ফিরে যান",
                "rep_id": "প্রতিনিধি আইডি",
                "next_cvs": "পরবর্তী: সিভিএস স্ক্রিনিং",
                "skip_finish": "এড়িয়ে যান এবং শেষ করুন",
                "language_label": "ভাষা",
                "change_lang": "ভাষা পরিবর্তন করুন",
                "stare_center": "অনুগ্রহ করে কেন্দ্রের দিকে তাকান",
                "live_count": "লাইভ কাউন্ট",
                "hello_rep": "নমস্কার, প্রতিনিধি",
                "stat_today": "আজ",
                "stat_month": "মাস",
                "stat_total": "মোট",
                "daily_progress": "দৈনিক অগ্রগতি",
                "recent_screenings": "সাম্প্রতিক স্ক্রিনিং",
                "share_link": "লিঙ্ক শেয়ার করুন",
                "prev": "আগের",
                "next": "পরের",
                "stats_nav": "পরিসংখ্যান",
                "dry_eye_nav": "ব্লিঙ্ক টেস্ট",
                "cvs_test_nav": "সিভিএস টেস্ট",
                "start_motivation": "আপনার দৈনিক লক্ষ্য শুরু করার জন্য আপনার প্রথম স্ক্রিনিং শুরু করুন!",
                "progress_motivation": "চমৎকার শুরু! আপনার লক্ষ্যে পৌঁছাতে কেবল {count}টি বাকি আছে।",
                "goal_motivation": "লক্ষ্য অর্জিত হয়েছে! আজ আপনি চোখের স্বাস্থ্যের ওপর এক প্রকৃত প্রভাব ফেলছেন।",
                "no_screenings_title": "এখনও পর্যন্ত কোন স্ক্রিনিং নেই",
                "no_screenings_desc": "রোগীর তথ্য দেখতে আপনার প্রথম এআই ব্লিংক টেস্ট শুরু করুন।",
                "start_new_test": "নতুন পরীক্ষা শুরু করুন",
                "blink_screening_label": "ব্লিংক টেস্ট স্ক্রিনিং",
                "blinks_count_label": "{count}টি ব্লিংক",
                "page_label": "পৃষ্ঠা {count}",
                "init_camera": "ক্যামেরা চালু হচ্ছে..."
        }
};


    function t(key, params = {}) {
        const trans = translations[state.lang] || translations['hi'] || translations['en'];
        let text = trans[key] || key;
        Object.keys(params).forEach(k => {
            text = text.replace(`{${k}}`, params[k]);
        });
        return text;
    }

    function updateTranslations() {
        const trans = translations[state.lang] || translations['hi'] || translations['en'];
        if(!trans) return;
        document.querySelectorAll('[data-t]').forEach(el => {
            const key = el.getAttribute('data-t');
            if(trans[key]) el.innerText = trans[key];
        });
    }

    window.openLanguageModal = () => { document.getElementById('language-modal').style.display = 'flex'; };
    window.closeLanguageModal = () => { document.getElementById('language-modal').style.display = 'none'; };

    window.selectLanguage = (lang) => {
        const wasInitial = !state.lang;
        state.lang = lang;
        sessionStorage.setItem('lang', lang);
        updateTranslations();
        closeLanguageModal();
        fetch(`"dummy"`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '"dummy"' },
            body: JSON.stringify({ emp_code: state.empCode, language: lang })
        });
        if (wasInitial) {
            if (state.isPatientMode) navigate('scr-disclaimer');
            else if (state.isLoggedIn) navigate('scr-dashboard');
            else navigate('scr-login');
        }
    };

    window.cvsScores = {};
    function renderCvsQuestions() {
        const container = document.getElementById('cvs-questions-container');
        if(!container) return;
        container.innerHTML = '';
        window.cvsScores = {}; 
        cvsSymptoms.forEach((s, index) => {
            const item = document.createElement('div');
            item.style.padding = '25px'; item.style.background = '#fff'; item.style.borderRadius = '28px';
            item.style.boxShadow = 'var(--shadow-sm)'; item.style.border = '1px solid #f1f5f9';
            item.innerHTML = `
                <div style="font-size: 16px; font-weight: 900; color: var(--text-main); margin-bottom: 15px; line-height: 1.4;">${index + 1}. ${s[state.lang] || s.en}</div>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <div>
                        <div style="font-size: 11px; font-weight: 800; color: var(--text-sub); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Frequency</div>
                        <div style="display: flex; gap: 8px;">
                            <button onclick="setCvsValue('${s.id}', 'freq', 0, this)" class="cvs-opt-btn" data-t="never">Never</button>
                            <button onclick="setCvsValue('${s.id}', 'freq', 1, this)" class="cvs-opt-btn" data-t="occas">Occasionally</button>
                            <button onclick="setCvsValue('${s.id}', 'freq', 2, this)" class="cvs-opt-btn" data-t="often">Often</button>
                        </div>
                    </div>
                    <div id="intens-${s.id}" style="display: none; flex-direction: column;">
                        <div style="font-size: 11px; font-weight: 800; color: var(--text-sub); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Intensity</div>
                        <div style="display: flex; gap: 8px;">
                            <button onclick="setCvsValue('${s.id}', 'intens', 1, this)" class="cvs-opt-btn" data-t="moderate">Moderate</button>
                            <button onclick="setCvsValue('${s.id}', 'intens', 2, this)" class="cvs-opt-btn" data-t="intense">Intense</button>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(item);
            window.cvsScores[s.id] = { freq: null, intens: null };
        });
        updateTranslations();
    }

    window.setCvsValue = (sId, type, val, btn) => {
        btn.parentElement.querySelectorAll('.cvs-opt-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        window.cvsScores[sId][type] = val;
        if (type === 'freq') {
            document.getElementById(`intens-${sId}`).style.display = (val === 0) ? 'none' : 'flex';
            if(val === 0) window.cvsScores[sId].intens = null;
        }
        calculateCvsScore();
    };

    function calculateCvsScore() {
        let total = 0; let answered = 0;
        Object.keys(window.cvsScores).forEach(id => {
            const s = window.cvsScores[id];
            if (s.freq === 0) answered++;
            else if (s.freq !== null && s.intens !== null) {
                answered++; total += (s.freq * s.intens);
            }
        });
        document.getElementById('cvs-running-score').innerText = total;
        return { total, answered };
    }

    window.startCvsScreening = () => { navigate('scr-cvs-screening'); renderCvsQuestions(); };

    window.submitCvsScreening = () => {
        const { total, answered } = calculateCvsScore();
        if (answered < cvsSymptoms.length) return showToast("Please answer all questions");
        showCvsResult(total);
    };

    function showCvsResult(score) {
        state.cvsScore = score;
        const resSets = {
            en: { 
                neg: 'Negative', mild: 'Mild CVS', mod: 'Moderate CVS', sev: 'Severe CVS',
                negTitle: 'Normal Findings', negDesc: 'Your symptom score is within the normal range. Continue following healthy digital habits.',
                mildTitle: 'Mild Strain Detected', mildDesc: 'You are showing early signs of digital eye strain. Consider more frequent breaks.',
                modTitle: 'Significant Eye Strain', modDesc: 'Your score indicates moderate digital strain. Using lubricating drops may help.',
                sevTitle: 'High Risk / Severe CVS', sevDesc: 'Severe digital strain detected. Please consult an eye specialist for a detailed checkup.'
            },
            hi: { 
                neg: 'नकारात्मक', mild: 'हल्का सीवीएस', mod: 'मध्यम सीवीएस', sev: 'गंभीर सीवीएस',
                negTitle: 'सामान्य निष्कर्ष', negDesc: 'आपका लक्षण स्कोर सामान्य सीमा के भीतर है। स्वस्थ डिजिटल आदतों का पालन जारी रखें।',
                mildTitle: 'हल्का तनाव पाया गया', mildDesc: 'आप डिजिटल आंखों के तनाव के शुरुआती लक्षण दिखा रहे हैं। अधिक बार ब्रेक लेने पर विचार करें।',
                modTitle: 'महत्वपूर्ण आंखों का तनाव', modDesc: 'आपका स्कोर मध्यम डिजिटल तनाव को इंगित करता है। लुब्रिकेटिंग ड्रॉप्स मदद कर सकते हैं।',
                sevTitle: 'उच्च जोखिम / गंभीर सीवीएस', sevDesc: 'गंभीर डिजिटल तनाव पाया गया। कृपया विस्तृत जांच के लिए नेत्र विशेषज्ञ से परामर्श लें।'
            },
            as: { 
                neg: 'নেতিবাচক', mild: 'সামান্য CVS', mod: 'মধ্যমীয়া CVS', sev: 'গম্ভীৰ CVS',
                negTitle: 'স্বাভাৱিক ফলাফল', negDesc: 'আপোনাৰ লক্ষণৰ স্ক’ৰ স্বাভাৱিক পৰিসৰৰ ভিতৰত আছে। স্বাস্থ্যকৰ ডিজিটেল অভ্যাস পালন কৰি থাকক।',
                mildTitle: 'সামান্য চাপ ধৰা পৰিছে', mildDesc: 'আপুনি ডিজিটেল চকুৰ চাপৰ প্ৰাৰম্ভিক লক্ষণ দেখুৱাইছে। মাজে মাজে বিৰতি লোৱাৰ কথা ভাবিব পাৰে।',
                modTitle: 'যথেষ্ট চকুৰ চাপ', modDesc: 'আপোনাৰ স্ক’ৰে মধ্যমীয়া ডিজিটেল চাপ সূচায়। লুব্ৰিকেটিং ড্ৰপছে সহায় কৰিব পাৰে।',
                sevTitle: 'উচ্চ বিপদাশংকা / গম্ভীৰ CVS', sevDesc: 'গম্ভীৰ ডিজিটেল চাপ ধৰা পৰিছে। অনুগ্ৰহ কৰি বিশেষজ্ঞৰ পৰামৰ্শ লওক।'
            },
            mr: { 
                neg: 'नकारात्मक', mild: 'सौम्य CVS', mod: 'मध्यम CVS', sev: 'गंभीर CVS',
                negTitle: 'सामान्य निष्कर्ष', negDesc: 'तुमचा स्कोअर सामान्य श्रेणीत आहे. निरोगी डिजिटल सवयींचे पालन चालू ठेवा.',
                mildTitle: 'सौम्य ताण आढळला', mildDesc: 'तुम्ही डिजिटल डोळ्यांच्या ताणाची सुरुवातीची लक्षणे दाखवत आहात. वारंवार विश्रांती घेण्याचा विचार करा.',
                modTitle: 'लक्षणीय डोळ्यांचा ताण', modDesc: 'तुमचा स्कोअर मध्यम डिजिटल ताण दर्शवतो. लुब्रिकेटिंग ड्रॉप्स मदत करू शकतात.',
                sevTitle: 'उच्च धोका / गंभीर CVS', sevDesc: 'गंभीर डिजिटल ताण आढळला. कृपया सविस्तर तपासणीसाठी तज्ज्ञांचा सल्ला घ्या.'
            },
            gu: { 
                neg: 'નકારાત્મક', mild: 'હળવું CVS', mod: 'મધ્યમ CVS', sev: 'ગંભીર CVS',
                negTitle: 'સામાન્ય તારણો', negDesc: 'તમારો સ્કોર સામાન્ય શ્રેણીમાં છે. તંદુરસ્ત ડિજિટલ ટેવોનું પાલન કરવાનું ચાલુ રાખો.',
                mildTitle: 'હળવો તણાવ જોવા મળ્યો', mildDesc: 'તમે ડિજિટલ આંખના તણાવના પ્રારંભિક લક્ષણો બતાવી રહ્યા છો. વારંવાર વિરામ લેવાનું વિચારો.',
                modTitle: 'નોંધપાત્ર આંખનો તણાવ', modDesc: 'તમારો સ્કોર મધ્યમ ડિજિટલ તણાવ સૂચવે છે. લ્યુબ્રિકેટિંગ ડ્રોપ્સ મદદ કરી શકે છે.',
                sevTitle: 'ઉચ્ચ જોખમ / ગંભીર CVS', sevDesc: 'ગંભીર ડિજિટલ તણાવ જોવા મળ્યો. કૃપા કરીને નિષ્ણાતની સલાહ લો.'
            },
            or: { 
                neg: 'ନକାରାତ୍ମକ', mild: 'ସାମାନ୍ୟ CVS', mod: 'ମଧ୍ୟମ CVS', sev: 'ଗୁରୁତର CVS',
                negTitle: 'ସାଧାରଣ ଫଳାଫଳ', negDesc: 'ଆପଣଙ୍କର ସ୍କୋର ସାଧାରଣ ସୀମା ମଧ୍ୟରେ ଅଛି | ସୁସ୍ଥ ଡିଜିଟାଲ୍ ଅଭ୍ୟାସ ଜାରି ରଖନ୍ତୁ |',
                mildTitle: 'ସାମାନ୍ୟ ଚାପ ଚିହ୍ନଟ', mildDesc: 'ଆପଣ ଡିଜିଟାଲ୍ ଆଖି ଚାପର ପ୍ରାରମ୍ଭିକ ଲକ୍ଷଣ ଦେଖାଉଛନ୍ତି | ଅଧିକ ବିରତି ନେବାକୁ ଚେଷ୍ଟା କରନ୍ତୁ |',
                modTitle: 'ଯଥେଷ୍ଟ ଆଖି ଚାପ', modDesc: 'ଆପଣଙ୍କର ସ୍କୋର ମଧ୍ୟମ ଡିଜିଟାଲ୍ ଚାପକୁ ସୂଚାଏ | ଲୁବ୍ରିକେଟିଂ ଡ୍ରପ୍ ସାହାଯ୍ୟ କରିପାରେ |',
                sevTitle: 'ଉଚ୍ଚ ବିପଦ / ଗୁରୁତର CVS', sevDesc: 'ଗୁରୁତର ଡିଜିଟାଲ୍ ଚାପ ଚିହ୍ନଟ ହୋଇଛି | ଦୟାକରି ବିଶେଷଜ୍ଞଙ୍କ ପରାମର୍ଶ ନିଅନ୍ତୁ |'
            },
            te: { 
                neg: 'ప్రతికూల', mild: 'తేలికపాటి CVS', mod: 'మితమైన CVS', sev: 'తీవ్రమైన CVS',
                negTitle: 'సాధారణ ఫలితాలు', negDesc: 'మీ లక్షణాల స్కోరు సాధారణ పరిధిలో ఉంది. ఆరోగ్యకరమైన డిజిటల్ అలవాట్లను కొనసాగించండి.',
                mildTitle: 'తేలికపాటి ఒత్తిడి గుర్తించబడింది', mildDesc: 'మీరు డిజిటల్ కంటి ఒత్తిడి యొక్క ప్రారంభ లక్షణాలను చూపిస్తున్నారు. తరచుగా విరామం తీసుకోండి.',
                modTitle: 'గణనీయమైన కంటి ఒత్తిడి', modDesc: 'మీ స్కోరు మధ్యస్థ డిజిటల్ ఒత్తిడిని సూచిస్తుంది. లూబ్రికేటింగ్ డ్రాప్స్ సహాయపడవచ్చు.',
                sevTitle: 'అధిక రిస్క్ / తీవ్రమైన CVS', sevDesc: 'తీవ్రమైన డిజిటల్ ఒత్తిడి గుర్తించబడింది. దయచేసి నిపుణుడిని సంప్రదించండి.'
            },
            ta: { 
                neg: 'எதிர்மறை', mild: 'மிதமான CVS', mod: 'நடுத்தர CVS', sev: 'கடுமையான CVS',
                negTitle: 'சாதாரண முடிவுகள்', negDesc: 'உங்கள் அறிகுறி மதிப்பெண் சாதாரண வரம்பிற்குள் உள்ளது. ஆரோக்கியமான டிஜிட்டல் பழக்கங்களைப் பின்பற்றுங்கள்.',
                mildTitle: 'லேசான கஷ்டம் கண்டறியப்பட்டது', mildDesc: 'நீங்கள் டிஜிட்டல் கண் அழுத்தத்தின் ஆரம்ப அறிகுறிகளைக் காட்டுகிறீர்கள். அடிக்கடி ஓய்வு எடுங்கள்.',
                modTitle: 'குறிப்பிடத்தக்க கண் அழுத்தம்', modDesc: 'உங்கள் மதிப்பெண் மிதமான டிஜிட்டல் அழுத்தத்தைக் குறிக்கிறது. கண் சொட்டு மருந்துகள் உதவலாம்.',
                sevTitle: 'அதிக ஆபத்து / கடுமையான CVS', sevDesc: 'கடுமையான டிஜிட்டல் அழுத்தம் கண்டறியப்பட்டது. தயவுசெய்து நிபுணரை அணுகவும்.'
            },
            kn: { 
                neg: 'ನಕಾರಾತ್ಮಕ', mild: 'ಸೌಮ್ಯ CVS', mod: 'ಮಧ್ಯಮ CVS', sev: 'ತೀವ್ರ CVS',
                negTitle: 'ಸಾಮಾನ್ಯ ಫಲಿತಾಂಶಗಳು', negDesc: 'ನಿಮ್ಮ ಲಕ್ಷಣಗಳ ಸ್ಕೋರ್ ಸಾಮಾನ್ಯ ವ್ಯಾಪ್ತಿಯಲ್ಲಿಯೇ ಇದೆ. ಆರೋಗ್ಯಕರ ಡಿಜಿಟಲ್ ಅಭ್ಯಾಸಗಳನ್ನು ಮುಂದುವರಿಸಿ.',
                mildTitle: 'ಸೌಮ್ಯ ಒತ್ತಡ ಪತ್ತೆಯಾಗಿದೆ', mildDesc: 'ನೀವು ಡಿಜಿಟಲ್ ಕಣ್ಣಿನ ಒತ್ತಡದ ಆರಂಭಿಕ ಲಕ್ಷಣಗಳನ್ನು ತೋರಿಸುತ್ತಿದ್ದೀರಿ. ಆಗಾಗ್ಗೆ ವಿರಾಮ ತೆಗೆದುಕೊಳ್ಳಿ.',
                modTitle: 'ಗಮನಾರ್ಹ ಕಣ್ಣಿನ ಒತ್ತಡ', modDesc: 'ನಿಮ್ಮ ಸ್ಕೋರ್ ಮಧ್ಯಮ ಡಿಜಿಟಲ್ ಒತ್ತಡವನ್ನು ಸೂಚಿಸುತ್ತದೆ. ಲೂಬ್ರಿಕೇಟಿಂಗ್ ಡ್ರಾಪ್ಸ್ ಸಹಾಯ ಮಾಡಬಹುದು.',
                sevTitle: 'ಹೆಚ್ಚಿನ ಅಪಾಯ / ತೀವ್ರ CVS', sevDesc: 'ತೀವ್ರ ಡಿಜಿಟல் ಒತ್ತಡ ಪತ್ತೆಯಾಗಿದೆ. ದಯವಿಟ್ಟು ತಜ್ಞರನ್ನು ಸಂಪರ್ಕಿಸಿ.'
            },
            ml: { 
                neg: 'നെഗറ്റീവ്', mild: 'മിതമായ CVS', mod: 'ഇടത്തരം CVS', sev: 'ഗുരുതരമായ CVS',
                negTitle: 'സാധാരണ ഫലങ്ങൾ', negDesc: 'നിങ്ങളുടെ സ്‌കോർ സാധാരണ പരിധിയിലാണ്. ആരോഗ്യകരമായ ഡിജിറ്റൽ ശീലങ്ങൾ തുടരുക.',
                mildTitle: 'നേരിയ സമ്മർദ്ദം കണ്ടെത്തി', mildDesc: 'നിങ്ങൾ ഡിജിറ്റൽ കണ്ണ് സമ്മർദ്ദത്തിന്റെ പ്രாரம்ഭ ലക്ഷണങ്ങൾ കാണിക്കുന്നു. ഇടവേળകൾ എടുക്കുക.',
                modTitle: 'കാര്യമായ കണ്ണ് സമ്മർദ്ദം', modDesc: 'നിങ്ങളുടെ സ്കോർ മിതമായ ഡിജിറ്റൽ സമ്മർദ്ദത്തെ സൂചിപ്പിക്കുന്നു. ലൂബ്രിക്കേറ്റിംഗ് ഡ്രോപ്പുകൾ സഹായിച്ചേക്കാം.',
                sevTitle: 'ഉയർന്ന അപകടസാധ്യത / ഗുരുതരമായ CVS', sevDesc: 'ഗുരുതരമായ ഡിജിറ്റൽ സമ്മർദ്ദം കണ്ടെത്തി. ദയവായി ഒരു വിദഗ്ദ്ധനെ കാണുക.'
            },
            bn: { 
                neg: 'নেতিবাচক', mild: 'সামান্য CVS', mod: 'মাঝারি CVS', sev: 'গুরুতর CVS',
                negTitle: 'সাধারণ ফলাফল', negDesc: 'আপনার লক্ষণের স্কোর সাধারণ সীমার মধ্যে রয়েছে। স্বাস্থ্যকর ডিজিটাল অভ্যাস পালন করুন।',
                mildTitle: 'সামান্য চাপ পাওয়া গেছে', mildDesc: 'আপনি ডিজিটাল চোখের চাপের প্রাথমিক লক্ষণ দেখাচ্ছেন। ঘন ঘন বিরতি নেওয়ার চেষ্টা করুন।',
                modTitle: 'উল্লেখযোগ্য চোখের চাপ', modDesc: 'আপনার স্কোর মাঝারি ডিজিটাল চাপ নির্দেশ করছে। লুব্রিকেটিং ড্রপস সাহায্য করতে পারে।',
                sevTitle: 'উচ্চ ঝুঁকি / গুরুতর সিভিএস', sevDesc: 'গুরুতর ডিজিটাল চাপ পাওয়া গেছে। অনুগ্রহ করে চক্ষু বিশেষজ্ঞের পরামর্শ নিন।'
            }
        };
        const lang = state.lang || 'en';
        const currentResSet = resSets[lang] || resSets['hi'] || resSets['en'];
        
        let tier, title, desc, percent;
        if (score < 6) {
            tier = currentResSet.neg; title = currentResSet.negTitle; desc = currentResSet.negDesc; percent = (score/6)*15;
        } else if (score <= 12) {
            tier = currentResSet.mild; title = currentResSet.mildTitle; desc = currentResSet.mildDesc; percent = 20 + ((score-6)/6)*25;
        } else if (score <= 20) {
            tier = currentResSet.mod; title = currentResSet.modTitle; desc = currentResSet.modDesc; percent = 50 + ((score-12)/8)*25;
        } else {
            tier = currentResSet.sev; title = currentResSet.sevTitle; desc = currentResSet.sevDesc; percent = 80 + ((score-20)/12)*20;
        }

        state.cvsTier = tier;
        document.getElementById('cvs-final-score').innerText = score;
        document.getElementById('cvs-tier-badge').innerText = tier;
        document.getElementById('cvs-status-title').innerText = title;
        document.getElementById('cvs-analysis-text').innerText = desc;
        document.getElementById('cvs-result-indicator').style.left = Math.min(95, percent) + '%';
        
        fetch(`"dummy"`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '"dummy"' },
            body: JSON.stringify({ emp_code: state.empCode, blink_test_id: state.lastBlinkTestId || null, symptom_data: window.cvsScores, total_score: score, has_cvs: score >= 6 })
        });
        navigate('scr-cvs-result');
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateTranslations();
        if (state.empName) {
            const display = document.getElementById('so-name-display');
            if (display) display.innerText = state.empName;
            const container = document.getElementById('so-facilitator');
            if (container) container.style.display = 'inline-block';
        }
        let target = state.isPatientMode ? 'scr-disclaimer' : (state.isLoggedIn ? 'scr-dashboard' : 'scr-login');
        
        // Final fallback to ensure something shows
        if (!target) target = 'scr-login';
        
        navigate(target);
        
        // Ensure language modal doesn't block content unnecessarily
        if (!state.lang) {
            setTimeout(openLanguageModal, 500);
        }

        // Splash screen dismissal
        setTimeout(() => {
            const splash = document.getElementById('splash-screen');
            if(splash) {
                splash.style.transition = 'opacity 0.8s ease-out';
                splash.style.opacity = '0';
                setTimeout(() => splash.remove(), 800);
            }
        }, 1500);
    });

    window.closeLightbox = function() {
        document.getElementById('lightbox').style.display = 'none';
        document.getElementById('lightbox').innerHTML = '<div class="lightbox-close" onclick="closeLightbox()">&times;</div>';
    };
