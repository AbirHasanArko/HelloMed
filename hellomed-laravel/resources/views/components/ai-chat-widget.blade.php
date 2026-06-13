{{--
    HelloMed AI Health Assistant — Floating Chat Widget
    Included globally via layouts/app.blade.php before </body>
    Two modes: 'health' (symptom → doctor/article suggestions) and 'howto' (step-by-step site guide)
--}}
<style>
/* ── Widget FAB ──────────────────────────────────────── */
.ai-fab {
    position: fixed;
    bottom: 28px;
    right: 28px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
    color: white;
    border: none;
    cursor: pointer;
    display: grid;
    place-items: center;
    font-size: 26px;
    box-shadow: 0 4px 20px rgba(13,148,136,0.45);
    z-index: 9999;
    transition: transform 0.3s cubic-bezier(.34,1.56,.64,1), box-shadow 0.3s ease;
    animation: fabPulse 3s ease-in-out infinite;
}
.ai-fab:hover { transform: scale(1.12); box-shadow: 0 8px 32px rgba(13,148,136,0.55); }
.ai-fab.open  { transform: scale(1.05) rotate(45deg); animation: none; }
@keyframes fabPulse {
    0%,100% { box-shadow: 0 4px 20px rgba(13,148,136,0.45); }
    50%      { box-shadow: 0 4px 32px rgba(13,148,136,0.7), 0 0 0 8px rgba(13,148,136,0.12); }
}

/* ── Chat Panel ──────────────────────────────────────── */
.ai-panel {
    position: fixed;
    bottom: 100px;
    right: 28px;
    width: 400px;
    max-width: calc(100vw - 40px);
    height: 560px;
    max-height: calc(100vh - 120px);
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 24px;
    box-shadow: 0 24px 64px rgba(0,0,0,0.18);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    z-index: 9998;
    opacity: 0;
    transform: translateY(24px) scale(0.97);
    pointer-events: none;
    transition: opacity 0.25s ease, transform 0.3s cubic-bezier(.34,1.56,.64,1);
}
.ai-panel.open {
    opacity: 1;
    transform: translateY(0) scale(1);
    pointer-events: all;
}

/* Panel header */
.ai-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 16px 18px;
    background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
    color: white;
    flex-shrink: 0;
}
.ai-header-avatar {
    width: 36px; height: 36px;
    border-radius: 50%;
    background: rgba(255,255,255,0.25);
    display: grid; place-items: center;
    font-size: 18px; flex-shrink: 0;
}
.ai-header-info { flex: 1; min-width: 0; }
.ai-header-info strong { display: block; font-size: 14px; font-weight: 700; }
.ai-header-info span   { font-size: 11px; opacity: 0.85; }
.ai-header-status { width: 8px; height: 8px; border-radius: 50%; background: #4ade80; flex-shrink: 0; }
.ai-header-status.offline { background: #f87171; }
.ai-header-close {
    background: none; border: none; color: rgba(255,255,255,0.8);
    cursor: pointer; font-size: 20px; line-height: 1; padding: 2px 4px;
    border-radius: 6px; transition: background 0.2s;
}
.ai-header-close:hover { background: rgba(255,255,255,0.2); color: white; }

/* Disclaimer bar */
.ai-disclaimer {
    padding: 6px 14px;
    background: #fffbeb;
    border-bottom: 1px solid #fef3c7;
    font-size: 11px;
    color: #92400e;
    text-align: center;
    flex-shrink: 0;
}
[data-theme="dark"] .ai-disclaimer {
    background: #1c1a0a; color: #fcd34d; border-color: #3d3200;
}

/* Messages area */
.ai-messages {
    flex: 1;
    overflow-y: auto;
    padding: 14px 14px 8px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    scroll-behavior: smooth;
}
.ai-messages::-webkit-scrollbar { width: 4px; }
.ai-messages::-webkit-scrollbar-track { background: transparent; }
.ai-messages::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

/* Bubbles */
.ai-bubble {
    display: flex;
    gap: 8px;
    align-items: flex-end;
    animation: bubbleIn 0.25s ease;
}
@keyframes bubbleIn {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}
.ai-bubble.user { flex-direction: row-reverse; }
.ai-bubble-avatar {
    width: 28px; height: 28px; border-radius: 50%;
    background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
    display: grid; place-items: center; font-size: 14px;
    flex-shrink: 0; margin-bottom: 2px;
}
.ai-bubble-text {
    max-width: 80%;
    padding: 10px 13px;
    border-radius: 16px;
    font-size: 13.5px;
    line-height: 1.5;
    color: var(--text);
}
.ai-bubble.bot  .ai-bubble-text {
    background: var(--accent);
    border-bottom-left-radius: 4px;
    border: 1px solid var(--border-light);
}
.ai-bubble.user .ai-bubble-text {
    background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
    color: white;
    border-bottom-right-radius: 4px;
}

/* Typing indicator */
.ai-typing { display: flex; gap: 4px; align-items: center; padding: 4px 0; }
.ai-typing span {
    width: 7px; height: 7px; border-radius: 50%;
    background: var(--primary); opacity: 0.6;
    animation: typingBounce 1.2s ease-in-out infinite;
}
.ai-typing span:nth-child(2) { animation-delay: 0.2s; }
.ai-typing span:nth-child(3) { animation-delay: 0.4s; }
@keyframes typingBounce {
    0%,80%,100% { transform: translateY(0); opacity: 0.4; }
    40%          { transform: translateY(-5px); opacity: 1; }
}

/* Urgency banner */
.ai-urgency {
    margin: 0 14px;
    padding: 8px 12px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 600;
    flex-shrink: 0;
    display: none;
}
.ai-urgency.high     { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; display: block; }
.ai-urgency.moderate { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; display: block; }
[data-theme="dark"] .ai-urgency.high     { background: #3b0f12; color: #fca5a5; border-color: #7f1d1d; }
[data-theme="dark"] .ai-urgency.moderate { background: #451a03; color: #fcd34d; border-color: #78350f; }

/* Doctor cards */
.ai-doctor-card {
    background: var(--surface-raised);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 12px;
    display: flex;
    gap: 10px;
    align-items: center;
    transition: all 0.2s ease;
    text-decoration: none;
    color: inherit;
}
.ai-doctor-card:hover {
    border-color: var(--primary);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}
.ai-doctor-avatar {
    width: 46px; height: 46px; border-radius: 50%;
    object-fit: cover; flex-shrink: 0;
    background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
    display: grid; place-items: center; color: white; font-size: 18px;
    border: 2px solid var(--border);
}
.ai-doctor-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
.ai-doctor-info { flex: 1; min-width: 0; }
.ai-doctor-info strong { display: block; font-size: 13px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.ai-doctor-info span   { font-size: 11px; color: var(--muted); }
.ai-doctor-book {
    font-size: 11px; font-weight: 700;
    padding: 5px 10px; border-radius: 999px;
    background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
    color: white; white-space: nowrap; flex-shrink: 0;
    border: none; cursor: pointer; text-decoration: none;
}

/* Article cards */
.ai-article-card {
    background: var(--surface-raised);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 10px 12px;
    display: flex; gap: 10px; align-items: center;
    text-decoration: none; color: inherit;
    transition: all 0.2s ease;
}
.ai-article-card:hover { border-color: var(--primary); transform: translateY(-1px); }
.ai-article-thumb {
    width: 44px; height: 44px; border-radius: 8px;
    object-fit: cover; flex-shrink: 0;
    background: var(--accent);
    display: grid; place-items: center; color: var(--primary); font-size: 20px;
}
.ai-article-thumb img { width: 100%; height: 100%; object-fit: cover; border-radius: 8px; }
.ai-article-info { flex: 1; min-width: 0; }
.ai-article-info strong { display: block; font-size: 12px; font-weight: 700; line-height: 1.3; }
.ai-article-info span   { font-size: 11px; color: var(--muted); }

/* Test cards */
.ai-test-card {
    background: var(--surface-raised);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 10px 12px;
    display: flex; gap: 10px; align-items: center;
    text-decoration: none; color: inherit;
    transition: all 0.2s ease;
}
.ai-test-card:hover { border-color: var(--primary); transform: translateY(-1px); }
.ai-test-icon {
    width: 40px; height: 40px; border-radius: 10px;
    background: var(--accent); display: grid; place-items: center;
    font-size: 20px; flex-shrink: 0;
}
.ai-test-info { flex: 1; min-width: 0; }
.ai-test-info strong { display: block; font-size: 12px; font-weight: 700; }
.ai-test-info span   { font-size: 11px; color: var(--muted); }

/* Navigation step cards (howto mode) */
.ai-steps { display: flex; flex-direction: column; gap: 6px; margin-top: 4px; }
.ai-step {
    display: flex; gap: 8px; align-items: flex-start;
    background: var(--surface-raised);
    border: 1px solid var(--border-light);
    border-radius: 10px;
    padding: 8px 10px;
    font-size: 12.5px;
    animation: bubbleIn 0.2s ease both;
}
.ai-step-num {
    width: 20px; height: 20px; border-radius: 50%;
    background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
    color: white; font-size: 10px; font-weight: 800;
    display: grid; place-items: center; flex-shrink: 0; margin-top: 1px;
}
.ai-step-body { flex: 1; }
.ai-step-body p { margin: 0 0 4px; color: var(--text); font-size: 12.5px; line-height: 1.45; }
.ai-step-link {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 11px; font-weight: 700;
    color: white;
    background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
    padding: 3px 9px; border-radius: 999px;
    text-decoration: none; transition: opacity 0.2s;
}
.ai-step-link:hover { opacity: 0.85; }

/* Quick-action chips */
.ai-chips { display: flex; flex-wrap: wrap; gap: 6px; padding: 6px 0; }
.ai-chip {
    padding: 5px 11px; border-radius: 999px;
    border: 1px solid var(--border);
    background: var(--surface-hover);
    font-size: 12px; cursor: pointer;
    color: var(--text);
    transition: all 0.2s ease;
    white-space: nowrap;
}
.ai-chip:hover { border-color: var(--primary); color: var(--primary); background: var(--accent); }

/* Follow-up chip */
.ai-followup {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 12px; border-radius: 999px;
    border: 1.5px dashed var(--primary);
    background: transparent;
    font-size: 12px; cursor: pointer; color: var(--primary);
    transition: all 0.2s ease; margin-top: 4px;
}
.ai-followup:hover { background: var(--accent); }

/* Input area */
.ai-input-area {
    padding: 10px 12px 12px;
    border-top: 1px solid var(--border-light);
    display: flex; gap: 8px; align-items: flex-end;
    flex-shrink: 0;
}
.ai-textarea {
    flex: 1; min-height: 38px; max-height: 100px;
    padding: 9px 12px; border-radius: 12px;
    border: 1px solid var(--border); background: var(--input-bg);
    color: var(--text); font-family: inherit; font-size: 13.5px;
    resize: none; line-height: 1.4; outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.ai-textarea:focus { border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-glow); }
.ai-textarea::placeholder { color: var(--muted); }
.ai-send {
    width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
    background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
    border: none; color: white; cursor: pointer; font-size: 17px;
    display: grid; place-items: center;
    transition: opacity 0.2s, transform 0.15s;
}
.ai-send:disabled { opacity: 0.5; cursor: not-allowed; }
.ai-send:not(:disabled):hover { transform: scale(1.07); }

/* Cards container */
.ai-cards { display: flex; flex-direction: column; gap: 7px; margin-top: 2px; }

/* Section label */
.ai-section-label {
    font-size: 10px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.06em; color: var(--muted); margin: 4px 0 2px;
}

/* Mobile */
@media (max-width: 480px) {
    .ai-panel { right: 12px; left: 12px; width: auto; bottom: 88px; border-radius: 20px; }
    .ai-fab   { bottom: 20px; right: 20px; }
}
</style>

{{-- FAB Button --}}
<button class="ai-fab" id="aiFab" title="Chat with AI Health Assistant" aria-label="Open AI chat">
    🩺
</button>

{{-- Chat Panel --}}
<div class="ai-panel" id="aiPanel" role="dialog" aria-label="AI Health Assistant">

    {{-- Header --}}
    <div class="ai-header">
        <div class="ai-header-avatar">🤖</div>
        <div class="ai-header-info">
            <strong>HelloMed AI Assistant</strong>
            <span id="aiStatusLabel">Checking availability…</span>
        </div>
        <div class="ai-header-status offline" id="aiStatusDot"></div>
        <button class="ai-header-close" id="aiClose" aria-label="Close chat">✕</button>
    </div>

    {{-- Disclaimer --}}
    <div class="ai-disclaimer">
        ⚠️ Not a doctor. For guidance only — always consult a qualified professional.
    </div>

    {{-- Urgency banner --}}
    <div class="ai-urgency" id="aiUrgency"></div>

    {{-- Messages --}}
    <div class="ai-messages" id="aiMessages">
        {{-- Welcome message injected by JS --}}
    </div>

    {{-- Input --}}
    <div class="ai-input-area">
        <textarea
            class="ai-textarea"
            id="aiInput"
            placeholder="Describe symptoms or ask 'How do I…?'"
            rows="1"
            aria-label="Message input"
        ></textarea>
        <button class="ai-send" id="aiSend" aria-label="Send message" title="Send (Enter)">➤</button>
    </div>
</div>

<script>
(function () {
    /* ── State ─────────────────────────────────────────────────── */
    const SESSION_KEY  = 'hm_ai_session';
    const HISTORY_KEY  = 'hm_ai_history';
    const OPEN_KEY     = 'hm_ai_open';
    const CSRF         = document.querySelector('meta[name="csrf-token"]')?.content || '';

    let sessionId   = localStorage.getItem(SESSION_KEY) || crypto.randomUUID();
    let history     = JSON.parse(localStorage.getItem(HISTORY_KEY) || '[]');
    let isGenerating = false;
    let ollamaOnline = false;

    localStorage.setItem(SESSION_KEY, sessionId);

    /* ── Elements ─────────────────────────────────────────────── */
    const fab       = document.getElementById('aiFab');
    const panel     = document.getElementById('aiPanel');
    const closeBtn  = document.getElementById('aiClose');
    const messages  = document.getElementById('aiMessages');
    const input     = document.getElementById('aiInput');
    const sendBtn   = document.getElementById('aiSend');
    const statusDot = document.getElementById('aiStatusDot');
    const statusLbl = document.getElementById('aiStatusLabel');
    const urgency   = document.getElementById('aiUrgency');

    /* ── Open / close ─────────────────────────────────────────── */
    function openPanel() {
        panel.classList.add('open');
        fab.classList.add('open');
        fab.innerHTML = '✕';
        localStorage.setItem(OPEN_KEY, '1');
        input.focus();
        scrollToBottom();
    }
    function closePanel() {
        panel.classList.remove('open');
        fab.classList.remove('open');
        fab.innerHTML = '🩺';
        localStorage.removeItem(OPEN_KEY);
    }
    fab.addEventListener('click', () => panel.classList.contains('open') ? closePanel() : openPanel());
    closeBtn.addEventListener('click', closePanel);

    // Restore open state
    if (localStorage.getItem(OPEN_KEY)) openPanel();

    /* ── Status check ─────────────────────────────────────────── */
    function checkStatus() {
        fetch('/api/ai/chat/status')
            .then(r => r.json())
            .then(data => {
                ollamaOnline = data.available;
                if (ollamaOnline) {
                    statusDot.classList.remove('offline');
                    statusLbl.textContent = `Online · ${data.model}`;
                } else {
                    statusDot.classList.add('offline');
                    statusLbl.textContent = 'AI offline — install Ollama';
                }
            })
            .catch(() => {
                statusDot.classList.add('offline');
                statusLbl.textContent = 'Status unknown';
            });
    }
    checkStatus();
    setInterval(checkStatus, 120_000);

    /* ── Welcome screen ───────────────────────────────────────── */
    function renderWelcome() {
        const chips = [
            // Health chips
            { label: '❤️ Heart & Chest',   text: 'I have chest pain and breathlessness' },
            { label: '🦴 Bones & Joints',  text: 'I have joint pain and stiffness' },
            { label: '🦷 Dental',           text: 'I have a toothache' },
            { label: '🧠 Mental Health',    text: 'I have been feeling very anxious' },
            // How-to chips
            { label: '📅 Book appointment', text: 'How do I book an appointment?' },
            { label: '💊 Buy medicines',    text: 'How do I buy medicines?' },
            { label: '📋 My prescriptions', text: 'How do I see my prescriptions?' },
            { label: '🚑 Call ambulance',   text: 'How do I request an ambulance?' },
        ];
        const chipsHtml = chips.map(c =>
            `<button class="ai-chip" data-text="${c.text}">${c.label}</button>`
        ).join('');

        addBotMessage(
            `Hello! I'm your HelloMed AI assistant. I can:<br>
            <b>🩺 Health</b> — help you find doctors and articles for your symptoms<br>
            <b>🗺️ Guide</b> — answer "How do I…?" questions about the site<br><br>
            What can I help you with today?`,
            `<div class="ai-chips">${chipsHtml}</div>`,
            false
        );
        // Bind chip clicks
        messages.querySelectorAll('.ai-chip').forEach(btn => {
            btn.addEventListener('click', () => {
                input.value = btn.dataset.text;
                sendMessage();
            });
        });
    }

    /* ── Message rendering ─────────────────────────────────────── */
    function scrollToBottom() {
        requestAnimationFrame(() => { messages.scrollTop = messages.scrollHeight; });
    }

    function addUserMessage(text) {
        const el = document.createElement('div');
        el.className = 'ai-bubble user';
        el.innerHTML = `
            <div class="ai-bubble-avatar" style="background:var(--border); font-size:16px;">👤</div>
            <div class="ai-bubble-text">${escHtml(text)}</div>`;
        messages.appendChild(el);
        scrollToBottom();
    }

    function addBotMessage(text, extraHtml = '', withFeedback = true) {
        const el = document.createElement('div');
        el.className = 'ai-bubble bot';
        el.innerHTML = `
            <div class="ai-bubble-avatar">🤖</div>
            <div>
                <div class="ai-bubble-text">${text}</div>
                ${extraHtml}
                ${withFeedback ? '<div class="ai-feedback-row" style="margin-top:4px; display:flex; gap:6px;"><button class="ai-chip" data-fb="helpful" style="font-size:11px; padding:3px 9px;">👍 Helpful</button><button class="ai-chip" data-fb="not_helpful" style="font-size:11px; padding:3px 9px;">👎 Not helpful</button></div>' : ''}
            </div>`;
        messages.appendChild(el);

        if (withFeedback) {
            el.querySelectorAll('[data-fb]').forEach(btn => {
                btn.addEventListener('click', function () {
                    submitFeedback(this.dataset.fb);
                    this.closest('.ai-feedback-row').innerHTML = '<span style="font-size:11px; color:var(--muted);">Thanks for your feedback!</span>';
                });
            });
        }
        scrollToBottom();
        return el;
    }

    function addTypingIndicator() {
        const el = document.createElement('div');
        el.className = 'ai-bubble bot';
        el.id = 'aiTyping';
        el.innerHTML = `
            <div class="ai-bubble-avatar">🤖</div>
            <div class="ai-bubble-text">
                <div class="ai-typing"><span></span><span></span><span></span></div>
            </div>`;
        messages.appendChild(el);
        scrollToBottom();
    }
    function removeTypingIndicator() {
        document.getElementById('aiTyping')?.remove();
    }

    /* ── Render structured response ───────────────────────────── */
    function renderResponse(data) {
        // Urgency banner
        urgency.className = 'ai-urgency';
        if (data.urgency === 'high') {
            urgency.className = 'ai-urgency high';
            urgency.innerHTML = `🔴 This sounds urgent! Please call emergency services or <a href="/ambulance" style="text-decoration:underline; color:inherit;">request an ambulance</a> immediately.`;
        } else if (data.urgency === 'moderate') {
            urgency.className = 'ai-urgency moderate';
            urgency.innerHTML = `🟡 These symptoms may need attention soon. Please consult a doctor.`;
        }

        let extraHtml = '';

        if (data.intent === 'howto' && data.navigation_steps?.length) {
            // Step-by-step guide
            extraHtml += '<div class="ai-steps">';
            data.navigation_steps.forEach(s => {
                const linkHtml = s.link
                    ? `<a href="${escHtml(s.link)}" class="ai-step-link">${escHtml(s.link_text || 'Go →')}</a>`
                    : '';
                extraHtml += `
                    <div class="ai-step">
                        <div class="ai-step-num">${s.step}</div>
                        <div class="ai-step-body">
                            <p>${escHtml(s.instruction)}</p>
                            ${linkHtml}
                        </div>
                    </div>`;
            });
            extraHtml += '</div>';
        } else {
            // Doctor cards
            if (data.doctors?.length) {
                extraHtml += `<div class="ai-section-label">Suggested Doctors</div><div class="ai-cards">`;
                data.doctors.slice(0, 3).forEach(d => {
                    const photo = d.photo_url
                        ? `<img src="${escHtml(d.photo_url)}" alt="${escHtml(d.name)}" loading="lazy">`
                        : '👨‍⚕️';
                    const fee = d.online_available && d.online_fee
                        ? `Online ৳${d.online_fee}`
                        : (d.offline_fee ? `Offline ৳${d.offline_fee}` : '');
                    extraHtml += `
                        <a href="${escHtml(d.profile_url)}" class="ai-doctor-card">
                            <div class="ai-doctor-avatar">${photo}</div>
                            <div class="ai-doctor-info">
                                <strong>${escHtml(d.name)}</strong>
                                <span>${escHtml(d.specialty || '')}${d.department ? ' · ' + escHtml(d.department) : ''}</span><br>
                                <span>${fee}</span>
                            </div>
                            <a href="${escHtml(d.booking_url)}" class="ai-doctor-book" onclick="event.stopPropagation()">Book →</a>
                        </a>`;
                });
                extraHtml += '</div>';
            }

            // Article cards
            if (data.articles?.length) {
                extraHtml += `<div class="ai-section-label">Related Articles</div><div class="ai-cards">`;
                data.articles.slice(0, 2).forEach(a => {
                    const thumb = a.cover_image
                        ? `<img src="${escHtml(a.cover_image)}" alt="" loading="lazy">`
                        : '📄';
                    extraHtml += `
                        <a href="${escHtml(a.url)}" class="ai-article-card" target="_blank" rel="noopener">
                            <div class="ai-article-thumb">${thumb}</div>
                            <div class="ai-article-info">
                                <strong>${escHtml(a.title)}</strong>
                                <span>${escHtml((a.excerpt || '').slice(0, 70))}…</span>
                            </div>
                        </a>`;
                });
                extraHtml += '</div>';
            }

            // Diagnostic test cards
            if (data.tests?.length) {
                extraHtml += `<div class="ai-section-label">Diagnostic Tests</div><div class="ai-cards">`;
                data.tests.slice(0, 2).forEach(t => {
                    extraHtml += `
                        <a href="${escHtml(t.url)}" class="ai-test-card">
                            <div class="ai-test-icon">🔬</div>
                            <div class="ai-test-info">
                                <strong>${escHtml(t.name)}</strong>
                                <span>${t.fee ? '৳' + t.fee : ''} ${t.location ? '· ' + escHtml(t.location) : ''}</span>
                            </div>
                        </a>`;
                });
                extraHtml += '</div>';
            }
        }

        // Follow-up question chip
        if (data.follow_up) {
            extraHtml += `<button class="ai-followup" data-text="${escAttr(data.follow_up)}">💬 ${escHtml(data.follow_up)}</button>`;
        }

        addBotMessage(data.message || '(no response)', extraHtml);

        // Bind follow-up click
        messages.querySelector('.ai-followup:last-of-type')?.addEventListener('click', function () {
            input.value = this.dataset.text;
            sendMessage();
        });
    }

    /* ── Send message ─────────────────────────────────────────── */
    function sendMessage() {
        const text = input.value.trim();
        if (!text || isGenerating) return;

        input.value = '';
        autoResize();
        isGenerating = true;
        sendBtn.disabled = true;
        urgency.className = 'ai-urgency';

        addUserMessage(text);
        addTypingIndicator();

        // Update local history
        history.push({ role: 'user', content: text });
        if (history.length > 12) history = history.slice(-12);

        fetch('/api/ai/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify({
                message: text,
                history: history.slice(0, -1), // exclude the one we just added
            }),
        })
        .then(r => r.json())
        .then(data => {
            removeTypingIndicator();
            renderResponse(data);
            if (data.message) {
                history.push({ role: 'assistant', content: data.message });
            }
            localStorage.setItem(HISTORY_KEY, JSON.stringify(history.slice(-12)));
        })
        .catch(() => {
            removeTypingIndicator();
            addBotMessage('Sorry, something went wrong. Please check that Laravel is running and try again.', '', false);
        })
        .finally(() => {
            isGenerating  = false;
            sendBtn.disabled = false;
            input.focus();
        });
    }

    /* ── Feedback ─────────────────────────────────────────────── */
    function submitFeedback(rating) {
        fetch('/api/ai/chat/feedback', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify({ session_id: sessionId, rating }),
        }).catch(() => {});
    }

    /* ── Input events ─────────────────────────────────────────── */
    input.addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
    });
    sendBtn.addEventListener('click', sendMessage);

    function autoResize() {
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 100) + 'px';
    }
    input.addEventListener('input', autoResize);

    /* ── Helpers ──────────────────────────────────────────────── */
    function escHtml(str) {
        return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function escAttr(str) {
        return String(str ?? '').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }

    /* ── Boot ─────────────────────────────────────────────────── */
    renderWelcome();
})();
</script>
