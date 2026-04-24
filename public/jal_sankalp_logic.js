// Dentist Map Logic
let rippleActive = false;

function initMapScreen() {
    // Initial State is now handled via inline CSS styling on the view
    const initialTotal = (window.pledgeStats && window.pledgeStats.total) ? window.pledgeStats.total.toLocaleString() : "5,240";
    document.getElementById('smile-counter-val').innerText = initialTotal;
    document.getElementById('smile-counter-val').style.color = "#004b8d";
    document.getElementById('counter-icon').style.color = "#00a8e1";

    // Reset Buttons
    document.getElementById('btn-become-pledge').style.display = 'block';
    document.getElementById('btn-download-cert').style.display = 'none';

    // Reset signature
    if (window.signaturePad) {
        window.signaturePad.clear();
    }
}

function openEngagement() {
    const overlay = document.getElementById('engagement-overlay');
    overlay.classList.add('show');

    // Initialize Signature Pad if not already done
    if (!window.signaturePad) {
        initSignaturePad();
    }
}

function closeEngagement() {
    const overlay = document.getElementById('engagement-overlay');
    overlay.classList.remove('show');
}

function initSignaturePad() {
    const canvas = document.getElementById('signature-pad');

    // Resize canvas
    function resizeCanvas() {
        // Adjust for device pixel ratio for smooth rendering
        const ratio = Math.max(window.devicePixelRatio || 1, 1);

        // Define internal canvas resolution based on display size
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
    }

    window.addEventListener("resize", resizeCanvas);
    resizeCanvas();

    let isDrawing = false;
    let ctx = canvas.getContext('2d');
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.strokeStyle = '#0277bd';

    // Helper to get touch/mouse pos relative to canvas
    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const clientX = e.clientX || (e.touches && e.touches[0].clientX);
        const clientY = e.clientY || (e.touches && e.touches[0].clientY);
        return {
            x: clientX - rect.left,
            y: clientY - rect.top
        };
    }

    let lastPos = null;

    function startDraw(e) {
        isDrawing = true;
        const pos = getPos(e);
        lastPos = pos;
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
    }

    function draw(e) {
        if (!isDrawing) return;

        const pos = getPos(e);

        // Simple smoothing: draw line from last pos
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();

        // Setup for next segment
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);

        lastPos = pos;
    }

    function stopDraw() {
        isDrawing = false;
    }

    canvas.addEventListener('mousedown', startDraw);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDraw);
    canvas.addEventListener('mouseout', stopDraw);

    canvas.addEventListener('touchstart', e => { e.preventDefault(); startDraw(e); }, { passive: false });
    canvas.addEventListener('touchmove', e => { e.preventDefault(); draw(e); }, { passive: false });
    canvas.addEventListener('touchend', stopDraw);
    canvas.addEventListener('touchcancel', stopDraw);

    // Provide a simple API
    window.signaturePad = {
        clear: function () {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        },
        isEmpty: function () {
            // Check if any pixels are colored
            const blank = document.createElement('canvas');
            blank.width = canvas.width;
            blank.height = canvas.height;
            return canvas.toDataURL() === blank.toDataURL();
        }
    };
}

function clearSignature() {
    if (window.signaturePad) {
        window.signaturePad.clear();
    }
}

function submitPledge() {
    // Basic validation
    const p1 = document.getElementById('pl-1').checked;

    if (!p1) {
        alert("Please agree to the pledge");
        return;
    }

    if (!window.signaturePad || window.signaturePad.isEmpty()) {
        alert("Please provide your signature");
        return;
    }

    const pledges = [
        "I pledge to carry forward the mission of the “Crystal Clear Initiative” by actively advancing kidney stone care, promoting best practices, and helping build a healthier nation."
    ];

    // Save signature directly from the canvas element and store globally for the certificate to use
    const sigCanvas = document.getElementById('signature-pad');
    const signatureBase64 = sigCanvas ? sigCanvas.toDataURL('image/png') : '';
    window.lastSignatureBase64 = signatureBase64;

    pledges.push("SIGNATURE:" + signatureBase64);

    const fallbackName = document.getElementById('dr-name') ? document.getElementById('dr-name').value : '';
    const fallbackMobile = document.getElementById('dr-mobile') ? document.getElementById('dr-mobile').value : '';
    const fallbackEmail = document.getElementById('dr-email') ? document.getElementById('dr-email').value : '';
    const fallbackSpec = document.getElementById('dr-spec') ? document.getElementById('dr-spec').value : '';

    const formData = new FormData();
    formData.append('name', (typeof state !== 'undefined' && state.name) ? state.name : (fallbackName || 'Unknown'));
    formData.append('mobile', (typeof state !== 'undefined' && state.mobile) ? state.mobile : fallbackMobile);
    formData.append('email', (typeof state !== 'undefined' && state.email) ? state.email : (fallbackEmail || 'unknown@example.com'));
    formData.append('speciality', (typeof state !== 'undefined' && state.speciality) ? state.speciality : (fallbackSpec || 'Dentist'));
    formData.append('tips', JSON.stringify(pledges));


    formData.append('emp_code', sessionStorage.getItem('empCode') || '');

    const submitBtn = event ? event.target.closest('button') : null;
    const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';

    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating Certificate...';
    }

    // --- Generate Certificate Image on the fly to send to S3 ---
    const node = document.getElementById('cert_template');

    // Populate template first
    const docName = (typeof state !== 'undefined' && state.name) ? state.name : (fallbackName || 'Unknown');
    const docSpec = (typeof state !== 'undefined' && state.speciality) ? state.speciality : (fallbackSpec || 'Specialist');

    let finalDocName = docName.trim();
    if (finalDocName.toLowerCase().startsWith('dr.')) finalDocName = finalDocName.substring(3).trim();
    if (finalDocName.toLowerCase().startsWith('dr ')) finalDocName = finalDocName.substring(3).trim();

    document.getElementById('cert-doc-name').innerText = "Dr. " + (finalDocName || 'Unknown');
    document.getElementById('cert-doc-spec').innerText = docSpec;

    const shortId = Math.random().toString(36).substr(2, 8).toUpperCase();
    const today = new Date();
    document.getElementById('cert-uuid').innerText = "NDD-" + today.getFullYear() + "-" + shortId;
    document.getElementById('cert-date').innerText = today.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });

    const sigImg = document.getElementById('cert-signature');
    if (sigImg) sigImg.src = signatureBase64;

    // Mobile fix: bring to view temporarily (but invisible to user)
    const originalTop = node.style.top;
    const originalLeft = node.style.left;
    const originalZIndex = node.style.zIndex;

    // Position it at 0,0 so html2canvas can read it accurately, but make it invisible
    node.style.top = '0px';
    node.style.left = '0px';
    node.style.zIndex = '-9999';
    node.style.opacity = '0';
    node.style.pointerEvents = 'none';

    setTimeout(() => {
        const doSubmit = (certBase64) => {
            // Restore node
            node.style.top = originalTop;
            node.style.left = originalLeft;
            node.style.zIndex = originalZIndex;
            node.style.opacity = '1';

            const formData = new FormData();
            formData.append('name', (typeof state !== 'undefined' && state.name) ? state.name : (fallbackName || 'Unknown'));
            formData.append('mobile', (typeof state !== 'undefined' && state.mobile) ? state.mobile : fallbackMobile);
            formData.append('email', (typeof state !== 'undefined' && state.email) ? state.email : (fallbackEmail || 'unknown@example.com'));
            formData.append('speciality', docSpec);
            formData.append('tips', JSON.stringify(pledges));
            formData.append('emp_code', sessionStorage.getItem('empCode') || '');
            if (certBase64) {
                formData.append('certificate', certBase64); // Send the image!
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                fetch('/save', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken.getAttribute('content'), 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                }).then(res => res.json()).then(d => {
                    if (d.success) {
                        if (typeof state !== 'undefined') {
                            state.rank = d.rank + 5240;
                            state.total = state.rank;
                            if (d.data) {
                                state.record_id = d.data.id;
                                if (d.data.photo_path) state.photo_path = d.data.photo_path; // S3 URL
                            }
                        }
                        closeEngagement();
                        triggerRippleEffect();
                    } else {
                        alert("Error saving: " + (d.message || "Please check validation."));
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnHtml;
                        }
                    }
                }).catch(err => {
                    console.error(err);
                    alert("Connection error.");
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnHtml;
                    }
                });
            } else {
                alert("CSRF Token Missing.");
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                }
            }
        };

        // Mobile Fix (iOS/Safari specifically): Wrap html2canvas in a Promise race with a 2.5-second timeout
        // so if the canvas breaks or hangs, we still submit the pledge!
        Promise.race([
            html2canvas(node, {
                scale: 1, // lowered back to 1 for mobile stability, pdf is handled by server now
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff'
            }).then(canvas => canvas.toDataURL('image/png')),
            new Promise((_, reject) => setTimeout(() => reject(new Error('html2canvas timed out')), 2500))
        ])
            .then(certBase64 => {
                if (typeof state !== 'undefined') state.local_cert_b64 = certBase64;
                doSubmit(certBase64);
            })
            .catch(err => {
                console.warn("Certificate generation bypassed due to error or mobile timeout:", err);
                doSubmit(null); // Proceed without certificate image for S3 (it's optional)
            });

    }, 200);
}

function triggerRippleEffect() {
    if (rippleActive) return;
    rippleActive = true;

    // IMMEDIATELY hide the Take Pledge button so user can't click it again
    const becomePledgeBtn = document.getElementById('btn-become-pledge');
    if (becomePledgeBtn) {
        becomePledgeBtn.style.display = 'none';
        becomePledgeBtn.style.pointerEvents = 'none';
    }

    // Trigger fluid wave animation (Water filling up from bottom to top)
    const glowMap = document.getElementById('india-map-glow');
    if (glowMap) {
        glowMap.style.clipPath = 'inset(0 0 0 0)';
    }

    // Small delay before counter bumps
    setTimeout(() => {
        // Update counter
        const counterBox = document.getElementById('map-counter-box');
        const counterEl = document.getElementById('smile-counter-val');

        if (counterBox && counterEl) {
            // Quick color highlight instead of scale to avoid shift
            counterEl.style.color = "#00a8e1";
            counterEl.style.transition = "color 0.3s ease";
            const counterIcon = document.getElementById('counter-icon');
            if (counterIcon) counterIcon.style.color = "#00a8e1";

            setTimeout(() => {
                const displayRank = (typeof state !== 'undefined' && state.rank) ? state.rank.toLocaleString() : (window.pledgeStats ? window.pledgeStats.total.toLocaleString() : "5,241");
                counterEl.innerText = displayRank;
                counterEl.style.color = "#004b8d";
            }, 300);
        }

    }, 1000); // Trigger midway through the animation

    // Show Download Certificate Button After Animation
    setTimeout(() => {
        rippleActive = false;
        const dlCertBtn = document.getElementById('btn-download-cert');
        if (dlCertBtn) {
            dlCertBtn.style.display = 'block';
        }
    }, 2000);
}

function generateCertificatePreview() {
    navigate('scr-certificate');
    const container = document.getElementById('cert-preview-container');

    const isHistory = (typeof state !== 'undefined' && state.record_id);

    const docName = (typeof state !== 'undefined' && state.name) ? state.name : 'Unknown';
    const docSpec = (typeof state !== 'undefined' && state.speciality) ? state.speciality : 'Specialist';



    const templateNode = document.getElementById('cert_template');
    if (!templateNode) {
        container.innerHTML = '<div style="padding:40px; text-align:center; color:red;">Template not found. Please try again.</div>';
        return;
    }

    // Clone the template
    const clone = templateNode.cloneNode(true);
    clone.id = 'cert-preview-clone';

    // Populate data inside the clone
    let finalDocName = docName.trim();
    if (finalDocName.toLowerCase().startsWith('dr.')) finalDocName = finalDocName.substring(3).trim();
    if (finalDocName.toLowerCase().startsWith('dr ')) finalDocName = finalDocName.substring(3).trim();

    const nameEl = clone.querySelector('#cert-doc-name');
    const specEl = clone.querySelector('#cert-doc-spec');
    if (nameEl) nameEl.innerText = "Dr. " + (finalDocName || 'Unknown');
    if (specEl) specEl.innerText = docSpec;

    const uuidEl = clone.querySelector('#cert-uuid');
    const dateEl = clone.querySelector('#cert-date');
    const shortId = isHistory ? state.record_id : Math.random().toString(36).substr(2, 8).toUpperCase();
    const today = isHistory ? new Date(state.created_at || Date.now()) : new Date();

    if (uuidEl) uuidEl.innerText = "CCI-" + today.getFullYear() + "-" + (isHistory ? ("000" + state.record_id).slice(-4) : shortId);
    if (dateEl) dateEl.innerText = today.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });

    const sigImg = clone.querySelector('#cert-signature');
    if (sigImg) {
        if (window.lastSignatureBase64) {
            sigImg.src = window.lastSignatureBase64;
            sigImg.style.display = 'inline-block';
        } else {
            sigImg.src = '';
            sigImg.style.display = 'none';
        }
    }

    // The certificate is 600x800. Scale it to fit the container width.
    const containerWidth = container.offsetWidth || 340;
    const certNativeW = 600;
    const certNativeH = 800;
    const scale = containerWidth / certNativeW;
    const scaledH = certNativeH * scale;

    // Reset any fixed/offscreen positioning on the clone
    clone.style.position = 'static';
    clone.style.top = 'auto';
    clone.style.left = 'auto';
    clone.style.width = certNativeW + 'px';
    clone.style.height = certNativeH + 'px';
    clone.style.transform = `scale(${scale})`;
    clone.style.transformOrigin = 'top left';
    clone.style.flexShrink = '0';

    // Wrapper div that clips the scaled content to the correct visible size
    const wrapper = document.createElement('div');
    wrapper.style.width = containerWidth + 'px';
    wrapper.style.height = scaledH + 'px';
    wrapper.style.overflow = 'hidden';
    wrapper.style.flexShrink = '0';
    wrapper.appendChild(clone);

    container.style.height = scaledH + 'px';
    container.style.overflow = 'hidden';
    container.style.display = 'block';
    container.style.borderRadius = '12px';
    container.style.boxShadow = '0 10px 25px rgba(0,0,0,0.1)';
    container.style.backgroundColor = '#ffffff';

    container.innerHTML = '';
    container.appendChild(wrapper);
}

function downloadFinalPDF() {
    if (typeof state === 'undefined' || !state.total) { // Use total (rank/id) to fetch the PDF
        alert("Certificate record not found. Please try again.");
        return;
    }

    const btn = document.getElementById('btn-final-download');
    if (btn) {
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating PDF...';
        btn.style.pointerEvents = 'none';

        // Direct browser to the backend PDF download route using the DB ID (which we stored in state.rank/state.total from the API response)
        // Note: state.total is currently set to state.rank in doSubmit. We need the actual ID if rank is offset.
        // Actually the API response gives us the full $poster object. Wait, `d.data.id` is the actual ID.
        // Let's rely on state.record_id if it exists, otherwise prompt error.
        window.location.href = `/download-jal-certificate/${state.record_id}`;

        setTimeout(() => {
            btn.innerHTML = '<i class="fas fa-check"></i> Downloaded!';
            btn.style.background = '#4caf50';
            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.style.background = '';
                btn.style.pointerEvents = 'auto';
            }, 3000);
        }, 1500);
    }
}

function showThankYouScreen() {
    navigate('scr-thanks');
}

// Ensure navigate function exists or handle it
if (typeof navigate === 'undefined') {
    window.navigate = function (screenId) {
        document.querySelectorAll('.screen').forEach(s => s.style.display = 'none');
        const target = document.getElementById(screenId);
        if (target) {
            target.style.display = 'flex';
            // Scroll to top
            window.scrollTo(0, 0);
        }
    };
}

function showThankYouScreen() {
    const rank = (typeof state !== 'undefined' && state.rank) ? state.rank : (window.pledgeStats ? window.pledgeStats.total : 5240);
    const total = (typeof state !== 'undefined' && state.total) ? state.total : (window.pledgeStats ? window.pledgeStats.total : 5240);

    document.getElementById('thanks-rank').innerText = "#" + rank.toLocaleString();
    document.getElementById('thanks-total-count').innerText = total.toLocaleString() + "+";

    navigate('scr-thanks');
}
