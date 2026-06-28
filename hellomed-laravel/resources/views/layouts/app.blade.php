<!doctype html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="HelloMed — Your complete digital hospital platform for appointments, prescriptions, and pharmacy.">
    <title>{{ config('app.name', 'HelloMed') }}@hasSection('title') | @yield('title')@endif</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 26 26'%3E%3Crect width='26' height='26' rx='6' fill='%230d9488'/%3E%3Crect x='9' y='4' width='8' height='18' rx='2' fill='white'/%3E%3Crect x='4' y='9' width='18' height='8' rx='2' fill='white'/%3E%3C/svg%3E">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        /* ===== DESIGN TOKENS ===== */
        :root, [data-theme="light"] {
            color-scheme: light;
            --bg: #f0f5f4;
            --bg-gradient: linear-gradient(180deg, #e8f4f2 0%, #f0f5f4 60%, #f8faf9 100%);
            --surface: #ffffff;
            --surface-raised: #ffffff;
            --surface-hover: #f7fafa;
            --text: #0f1729;
            --text-secondary: #1e3a3a;
            --muted: #586e75;
            --primary: #0d9488;
            --primary-strong: #0f766e;
            --primary-light: #99f6e4;
            --primary-glow: rgba(13, 148, 136, 0.2);
            --accent: #ccfbf1;
            --accent-strong: #5eead4;
            --border: #d1e0dd;
            --border-light: #e6efec;
            --gradient-start: #0d9488;
            --gradient-end: #10b981;
            --shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.04), 0 1px 2px rgba(15, 23, 42, 0.02);
            --shadow-md: 0 4px 16px rgba(15, 23, 42, 0.06), 0 2px 4px rgba(15, 23, 42, 0.03);
            --shadow-lg: 0 20px 50px rgba(15, 23, 42, 0.08), 0 8px 20px rgba(15, 23, 42, 0.04);
            --shadow-glow: 0 0 20px rgba(13, 148, 136, 0.15);
            --nav-bg: rgba(240, 245, 244, 0.96);
            --overlay-gradient: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.55) 100%);
            --notice-bg: #ecfdf5;
            --notice-text: #065f46;
            --notice-border: #a7f3d0;
            --error-bg: #fff1f2;
            --error-text: #9f1239;
            --error-border: #fecdd3;
            --input-bg: #ffffff;
            --badge-green-bg: #dcfce7;
            --badge-green-text: #166534;
            --badge-amber-bg: #fef3c7;
            --badge-amber-text: #92400e;
            --badge-red-bg: #fee2e2;
            --badge-red-text: #991b1b;
        }

        [data-theme="dark"] {
            color-scheme: dark;
            --bg: #0d1117;
            --bg-gradient: linear-gradient(180deg, #0d1117 0%, #111820 60%, #0d1117 100%);
            --surface: #161b22;
            --surface-raised: #1c2333;
            --surface-hover: #1f2937;
            --text: #e6edf3;
            --text-secondary: #c9d1d9;
            --muted: #8b949e;
            --primary: #2dd4bf;
            --primary-strong: #14b8a6;
            --primary-light: #042f2e;
            --primary-glow: rgba(45, 212, 191, 0.2);
            --accent: #042f2e;
            --accent-strong: #0d9488;
            --border: #30363d;
            --border-light: #21262d;
            --gradient-start: #0d9488;
            --gradient-end: #34d399;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.2);
            --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.3);
            --shadow-lg: 0 20px 50px rgba(0, 0, 0, 0.4);
            --shadow-glow: 0 0 20px rgba(45, 212, 191, 0.15);
            --nav-bg: rgba(13, 17, 23, 0.96);
            --overlay-gradient: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.75) 100%);
            --notice-bg: #042f2e;
            --notice-text: #5eead4;
            --notice-border: #115e59;
            --error-bg: #3b0f12;
            --error-text: #fca5a5;
            --error-border: #7f1d1d;
            --input-bg: #1c2333;
            --badge-green-bg: #052e16;
            --badge-green-text: #86efac;
            --badge-amber-bg: #451a03;
            --badge-amber-text: #fcd34d;
            --badge-red-bg: #450a0a;
            --badge-red-text: #fca5a5;
        }

        /* ===== RESET & BASE ===== */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-gradient);
            background-attachment: fixed;
            color: var(--text);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        a { color: inherit; text-decoration: none; }
        img { max-width: 100%; height: auto; }

        /* ===== LAYOUT ===== */
        .container { width: min(1160px, calc(100% - 32px)); margin: 0 auto; }

        /* ===== NAVIGATION ===== */
        .nav {
            position: sticky;
            top: 0;
            background: var(--nav-bg);
            backdrop-filter: blur(20px) saturate(1.6);
            -webkit-backdrop-filter: blur(20px) saturate(1.6);
            border-bottom: 1px solid var(--border-light);
            z-index: 500;
            transition: background 0.3s ease, border-color 0.3s ease;
        }
        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 0;
            height: 56px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            letter-spacing: 0.2px;
            text-decoration: none;
            flex-shrink: 0;
        }
        .brand-logo {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: white;
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            flex-shrink: 0;
        }
        .brand:hover .brand-logo {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(13, 148, 136, 0.4);
        }
        .brand-text small {
            display: block;
            font-weight: 400;
            font-size: 10px;
            color: var(--muted);
            letter-spacing: 0;
        }

        /* ===== GHOST BUTTON (used throughout app) ===== */
        .pill, .ghost-button {
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid var(--border);
            font-size: 13px;
            font-weight: 500;
            background: var(--surface);
            color: var(--text);
            transition: all 0.2s ease;
            cursor: pointer;
            white-space: nowrap;
        }
        .ghost-button:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--surface);
            box-shadow: var(--shadow-sm);
        }
        .button {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            border-radius: 999px;
            border: 1px solid transparent;
            font-size: 14px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(13, 148, 136, 0.3);
        }
        .button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(13, 148, 136, 0.4);
        }
        .button:active { transform: translateY(0); }

        /* ===== MEGA NAV ===== */
        .mega-nav {
            display: flex;
            align-items: center;
            gap: 0;
            height: 100%;
            flex: 1;
            justify-content: center;
        }
        .mega-nav-item {
            position: static;
            height: 100%;
            display: flex;
            align-items: center;
        }
        .mega-nav-trigger {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 0 13px;
            height: 100%;
            font-size: 13px;
            font-weight: 500;
            color: var(--text);
            text-decoration: none;
            background: none;
            border: none;
            cursor: pointer;
            white-space: nowrap;
            transition: color 0.2s ease;
            position: relative;
        }
        .mega-nav-trigger::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 13px;
            right: 13px;
            height: 2px;
            background: var(--primary);
            border-radius: 2px 2px 0 0;
            transform: scaleX(0);
            transition: transform 0.22s ease;
        }
        .mega-nav-item:hover .mega-nav-trigger,
        .mega-nav-item.open .mega-nav-trigger {
            color: var(--primary);
        }
        .mega-nav-item:hover .mega-nav-trigger::after,
        .mega-nav-item.open .mega-nav-trigger::after {
            transform: scaleX(1);
        }
        .mega-nav-trigger svg { flex-shrink: 0; }
        .mega-nav-trigger .chev {
            transition: transform 0.22s ease;
            opacity: 0.55;
        }
        .mega-nav-item:hover .mega-nav-trigger .chev,
        .mega-nav-item.open .mega-nav-trigger .chev {
            transform: rotate(180deg);
            opacity: 1;
        }
        /* Ambulance special */
        .mega-nav-trigger.nav-ambulance {
            background: linear-gradient(135deg, #ef4444, #b91c1c);
            color: white;
            border-radius: 999px;
            padding: 6px 14px;
            height: auto;
            margin: 0 6px;
            box-shadow: 0 2px 8px rgba(239,68,68,0.35);
            font-weight: 600;
            font-size: 13px;
        }
        .mega-nav-trigger.nav-ambulance:hover {
            background: linear-gradient(135deg, #dc2626, #991b1b);
            color: white;
            box-shadow: 0 4px 14px rgba(239,68,68,0.45);
            transform: translateY(-1px);
        }
        .mega-nav-trigger.nav-ambulance::after { display: none; }

        /* ===== DROPDOWN PANEL ===== */
        .mega-dropdown {
            position: fixed;
            top: 56px;
            left: 0;
            right: 0;
            background: var(--nav-bg);
            backdrop-filter: blur(28px) saturate(2);
            -webkit-backdrop-filter: blur(28px) saturate(2);
            border-bottom: 1px solid var(--border-light);
            box-shadow: 0 8px 40px rgba(0,0,0,0.10);
            z-index: 490;
            opacity: 0;
            pointer-events: none;
            transform: translateY(-8px);
            transition: opacity 0.22s ease, transform 0.22s ease;
        }
        .mega-nav-item:hover .mega-dropdown,
        .mega-nav-item.open .mega-dropdown {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }
        .mega-dropdown-inner {
            width: min(1160px, calc(100% - 32px));
            margin: 0 auto;
            padding: 22px 0;
        }
        .mega-dropdown-grid {
            display: grid;
            gap: 6px;
        }
        .mega-dropdown-grid.cols-4 { grid-template-columns: repeat(4, 1fr); }
        .mega-dropdown-grid.cols-3 { grid-template-columns: repeat(3, 1fr); }
        .mega-dropdown-grid.cols-2 { grid-template-columns: repeat(2, 1fr); }
        .mega-dropdown-grid.cols-1 { grid-template-columns: 1fr; }
        /* Multi-column sectioned layout */
        .mega-dropdown-sections {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0 24px;
        }
        .mega-dropdown-section-col { display: flex; flex-direction: column; gap: 2px; }
        .mega-dropdown-section-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--muted);
            padding: 6px 12px 4px;
            margin-top: 8px;
        }
        .mega-dropdown-section-title:first-child { margin-top: 0; }
        .mega-dropdown-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 10px;
            color: var(--text);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: background 0.15s ease, color 0.15s ease;
            white-space: nowrap;
        }
        .mega-dropdown-link:hover {
            background: var(--accent);
            color: var(--primary-strong);
        }
        [data-theme="dark"] .mega-dropdown-link:hover {
            background: var(--primary-light);
            color: var(--primary);
        }
        .mega-dropdown-link svg {
            flex-shrink: 0;
            opacity: 0.6;
            transition: opacity 0.15s ease;
        }
        .mega-dropdown-link:hover svg { opacity: 1; }
        .mega-dropdown-link-desc {
            font-size: 11px;
            color: var(--muted);
            font-weight: 400;
            line-height: 1.3;
            margin-top: 1px;
        }
        .mega-dropdown-link-text { display: flex; flex-direction: column; }
        /* Logout form inside dropdown */
        .mega-dropdown-link-form { display: contents; }
        .mega-dropdown-link-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 9px 12px;
            border-radius: 10px;
            background: none;
            border: none;
            color: var(--text);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            text-align: left;
            transition: background 0.15s ease, color 0.15s ease;
            font-family: inherit;
        }
        .mega-dropdown-link-btn:hover {
            background: #fee2e2;
            color: #991b1b;
        }
        [data-theme="dark"] .mega-dropdown-link-btn:hover {
            background: #450a0a;
            color: #fca5a5;
        }
        .mega-dropdown-link-btn svg { flex-shrink: 0; opacity: 0.6; transition: opacity 0.15s ease; }
        .mega-dropdown-link-btn:hover svg { opacity: 1; }
        /* Separator line in dropdown */
        .mega-dropdown-sep {
            height: 1px;
            background: var(--border-light);
            margin: 8px 12px;
        }

        /* ===== NAV UTILITIES (right side) ===== */
        .nav-utils {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }
        .nav-icon-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--muted);
            display: grid;
            place-items: center;
            cursor: pointer;
            transition: all 0.25s ease;
            flex-shrink: 0;
        }
        .nav-icon-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            box-shadow: var(--shadow-glow);
            background: var(--surface);
        }
        /* Mobile AI Chat Button */
        .mobile-ai-chat-btn {
            display: none;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: white;
            place-items: center;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            font-size: 18px;
            flex-shrink: 0;
        }
        .mobile-ai-chat-btn:active {
            transform: scale(0.95);
        }
        /* Theme Toggle */
        .theme-toggle {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--muted);
            display: grid;
            place-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }
        .theme-toggle:hover {
            border-color: var(--primary);
            color: var(--primary);
            box-shadow: var(--shadow-glow);
        }
        .theme-toggle .icon-sun, .theme-toggle .icon-moon { transition: opacity 0.3s ease, transform 0.3s ease; display: flex; align-items: center; }
        [data-theme="light"] .theme-toggle .icon-moon { display: none; }
        [data-theme="dark"] .theme-toggle .icon-sun { display: none; }

        /* ===== HAMBURGER (mobile only) ===== */
        .nav-hamburger {
            display: none;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text);
            place-items: center;
            cursor: pointer;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }
        .nav-hamburger:hover { border-color: var(--primary); color: var(--primary); }

        /* ===== MOBILE FULL-SCREEN NAV OVERLAY ===== */
        .mobile-nav-overlay {
            position: fixed;
            inset: 0;
            z-index: 600;
            display: flex;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.28s ease;
        }
        .mobile-nav-overlay.open {
            pointer-events: auto;
            opacity: 1;
        }
        .mobile-nav-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.45);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }
        .mobile-nav-panel {
            position: relative;
            width: min(340px, 92vw);
            height: 100%;
            background: var(--surface);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            transform: translateX(-100%);
            transition: transform 0.30s cubic-bezier(0.4,0,0.2,1);
            box-shadow: 4px 0 40px rgba(0,0,0,0.18);
        }
        .mobile-nav-overlay.open .mobile-nav-panel {
            transform: translateX(0);
        }
        .mobile-nav-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-light);
            flex-shrink: 0;
        }
        .mobile-nav-close {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--muted);
            display: grid;
            place-items: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .mobile-nav-close:hover { border-color: var(--primary); color: var(--primary); }
        .mobile-nav-body { flex: 1; padding: 8px 0 24px; overflow-y: auto; }
        /* Mobile direct links */
        .mobile-nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: var(--text);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.15s ease, color 0.15s ease;
        }
        .mobile-nav-link:hover { background: var(--surface-hover); color: var(--primary); }
        .mobile-nav-link svg { flex-shrink: 0; opacity: 0.55; }
        .mobile-nav-link:hover svg { opacity: 1; }
        /* Ambulance special */
        .mobile-nav-link.ambulance {
            background: linear-gradient(135deg,rgba(239,68,68,0.08),rgba(185,28,28,0.05));
            color: #dc2626;
            border-radius: 10px;
            margin: 4px 12px;
        }
        .mobile-nav-link.ambulance svg { opacity: 1; }
        /* Mobile section accordion */
        .mobile-nav-accordion {
            border: none;
            background: none;
        }
        .mobile-nav-acc-trigger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 12px 20px;
            background: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            text-align: left;
            transition: color 0.15s ease;
        }
        .mobile-nav-acc-trigger:hover { color: var(--primary); }
        .mobile-nav-acc-trigger-inner { display: flex; align-items: center; gap: 12px; }
        .mobile-nav-acc-trigger-inner svg { flex-shrink: 0; opacity: 0.55; }
        .mobile-nav-acc-chev {
            transition: transform 0.22s ease;
            opacity: 0.5;
            flex-shrink: 0;
        }
        .mobile-nav-accordion.open .mobile-nav-acc-chev { transform: rotate(180deg); opacity: 1; }
        .mobile-nav-accordion.open .mobile-nav-acc-trigger { color: var(--primary); }
        .mobile-nav-accordion.open .mobile-nav-acc-trigger svg { opacity: 1; }
        .mobile-nav-acc-body {
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.28s cubic-bezier(0.4,0,0.2,1);
        }
        .mobile-nav-accordion.open .mobile-nav-acc-body { max-height: 800px; }
        .mobile-nav-sub-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px 10px 44px;
            color: var(--muted);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: background 0.15s ease, color 0.15s ease;
        }
        .mobile-nav-sub-link:hover { background: var(--surface-hover); color: var(--primary); }
        .mobile-nav-sub-link svg { flex-shrink: 0; opacity: 0.5; }
        .mobile-nav-sub-link:hover svg { opacity: 1; }
        .mobile-nav-sub-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--muted);
            padding: 8px 20px 2px 44px;
            opacity: 0.7;
        }
        .mobile-nav-sub-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 10px 20px 10px 44px;
            background: none;
            border: none;
            color: var(--muted);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            text-align: left;
            font-family: inherit;
            transition: background 0.15s ease, color 0.15s ease;
        }
        .mobile-nav-sub-btn:hover { background: var(--surface-hover); color: #991b1b; }
        .mobile-nav-divider {
            height: 1px;
            background: var(--border-light);
            margin: 8px 0;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 960px) {
            .mega-nav { display: none; }
            .nav-hamburger { display: grid; }
            .mobile-ai-chat-btn { display: grid; }
        }
        @media (min-width: 961px) {
            .mobile-nav-overlay { display: none !important; }
        }

        /* ===== HERO ===== */
        .hero {
            padding: 64px 0 36px;
            display: grid;
            grid-template-columns: 1.4fr 0.6fr;
            grid-template-areas: 
                "text visual"
                "cards visual";
            gap: 24px 36px;
            align-items: stretch;
        }
        .hero-text { grid-area: text; }
        .hero-cards { grid-area: cards; }
        .hero h1 {
            font-size: clamp(2.2rem, 4vw, 3.8rem);
            line-height: 1.08;
            letter-spacing: -0.03em;
            margin-bottom: 16px;
            background: linear-gradient(135deg, var(--text) 0%, var(--primary-strong) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        [data-theme="dark"] .hero h1 {
            background: linear-gradient(135deg, var(--text) 0%, var(--primary) 100%);
            -webkit-background-clip: text;
            background-clip: text;
        }
        .hero-visual {
            grid-area: visual;
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            display: grid;
            place-items: center;
            height: 100%;
            min-height: 300px;
        }
        .hero-visual-pattern {
            position: absolute;
            inset: 0;
            opacity: 0.12;
            background-image:
                radial-gradient(circle at 20% 30%, white 1px, transparent 1px),
                radial-gradient(circle at 80% 70%, white 1px, transparent 1px),
                radial-gradient(circle at 50% 50%, white 2px, transparent 2px);
            background-size: 40px 40px, 60px 60px, 80px 80px;
        }

        /* ===== CARDS ===== */
        .card, .panel {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        .card { padding: 24px; }
        .panel { padding: 20px; }
        a.card:hover, .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }

        /* Photo Card — image header with overlay */
        .photo-card {
            padding: 0;
            display: flex;
            flex-direction: column;
        }
        .photo-card-img {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        }
        .photo-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        a.photo-card:hover .photo-card-img img {
            transform: scale(1.05);
        }
        .photo-card-img .photo-card-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60%;
            background: var(--overlay-gradient);
            pointer-events: none;
        }
        .photo-card-img .photo-card-badge {
            position: absolute;
            top: 12px;
            left: 12px;
        }
        .photo-card-body {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .photo-card-body p:last-child { margin-bottom: 0; }

        /* Fallback gradient header (no image) */
        .photo-card-placeholder {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            display: grid;
            place-items: center;
            position: relative;
        }
        .photo-card-placeholder svg {
            width: 48px;
            height: 48px;
            color: white;
            opacity: 0.6;
        }
        .photo-card-placeholder .photo-card-badge {
            position: absolute;
            top: 12px;
            left: 12px;
        }

        /* ===== TYPOGRAPHY ===== */
        h1, h2, h3, h4 { margin: 0 0 12px; line-height: 1.15; font-weight: 800; letter-spacing: -0.02em; }
        h1 { font-size: clamp(2rem, 3.5vw, 3rem); }
        h2 { font-size: clamp(1.5rem, 2.5vw, 2.2rem); }
        h3 { font-size: 1.15rem; }
        p { color: var(--muted); line-height: 1.7; margin: 0 0 14px; }

        /* ===== TAGS & BADGES ===== */
        .tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 999px;
            background: var(--accent);
            color: var(--primary-strong);
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }
        [data-theme="dark"] .tag {
            color: var(--primary);
        }
        .stock-badge {
            display: inline-flex;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }
        .stock-badge.in-stock { background: var(--badge-green-bg); color: var(--badge-green-text); }
        .stock-badge.low-stock { background: var(--badge-amber-bg); color: var(--badge-amber-text); }
        .stock-badge.out-of-stock { background: var(--badge-red-bg); color: var(--badge-red-text); }

        /* ===== GRIDS & SECTIONS ===== */
        .grid { display: grid; gap: 20px; }
        .grid.cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid.cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .grid.cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .section { padding: 24px 0 36px; }
        .pill-row, .meta-row { display: flex; flex-wrap: wrap; gap: 8px; }

        /* ===== STATS ===== */
        .stat {
            padding: 22px;
            border-radius: 18px;
            background: var(--surface);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            text-align: center;
        }
        .stat:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary);
        }
        .stat strong {
            display: block;
            font-size: 2rem;
            margin-bottom: 4px;
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ===== AVATAR ===== */
        .avatar-image {
            display: block;
            width: 110px;
            height: 110px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 12px;
            border: 3px solid transparent;
            background: linear-gradient(var(--surface), var(--surface)) padding-box,
                        linear-gradient(135deg, var(--gradient-start), var(--gradient-end)) border-box;
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.15);
        }
        .cover-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 16px;
            margin-bottom: 12px;
            border: 1px solid var(--border);
        }

        /* ===== LISTS ===== */
        .list { display: grid; gap: 10px; }
        .list-item {
            padding: 16px;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: var(--surface);
            transition: all 0.2s ease;
        }
        .list-item:hover { border-color: var(--border); background: var(--surface-hover); }
        a.list-item:hover { border-color: var(--primary); }
        .list-item h3, .list-item strong { margin-bottom: 4px; }

        /* ===== FOOTER ===== */
        .footer {
            margin-top: 40px;
            border-top: 1px solid var(--border);
            background: var(--surface);
            transition: background 0.3s ease, border-color 0.3s ease;
        }
        .footer-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 24px 0;
        }
        .footer p { margin-bottom: 0; }

        /* ===== FORMS ===== */
        .muted { color: var(--muted); }
        input, select, textarea {
            width: 100%;
            margin-top: 6px;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 12px;
            font: inherit;
            font-size: 14px;
            background: var(--input-bg);
            color: var(--text);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }
        label { display: block; font-weight: 600; margin-bottom: 14px; font-size: 14px; }
        textarea { min-height: 120px; resize: vertical; }
        input[type="checkbox"] { width: auto; margin-right: 8px; }

        /* ===== NOTICES ===== */
        .notice {
            padding: 14px 18px;
            border-radius: 14px;
            background: var(--notice-bg);
            color: var(--notice-text);
            border: 1px solid var(--notice-border);
            margin-bottom: 16px;
            font-weight: 500;
        }
        .error-box {
            padding: 14px 18px;
            border-radius: 14px;
            background: var(--error-bg);
            color: var(--error-text);
            border: 1px solid var(--error-border);
            margin-bottom: 16px;
        }

        /* ===== TABLES ===== */
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { text-align: left; padding: 14px; border-bottom: 1px solid var(--border); }
        .table th { font-size: 12px; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted); font-weight: 700; }
        .table tbody tr { transition: background 0.15s ease; }
        .table tbody tr:hover { background: var(--surface-hover); }

        /* ===== LINKS IN MAIN ===== */
        main a:not(.button):not(.ghost-button):not(.card):not(.photo-card):not(.list-item):not(.nav-links a) {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }
        main a:not(.button):not(.ghost-button):not(.card):not(.photo-card):not(.list-item):not(.nav-links a):hover {
            color: var(--primary-strong);
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        /* ===== PAGINATION ===== */
        nav[aria-label="Pagination Navigation"] {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        nav[aria-label="Pagination Navigation"] .sm\:hidden { display: none; }
        nav[aria-label="Pagination Navigation"] .sm\:flex { display: flex; align-items: center; justify-content: space-between; width: 100%; gap: 16px; flex-wrap: wrap; }
        @media (max-width: 640px) {
            nav[aria-label="Pagination Navigation"] .sm\:hidden { display: flex; justify-content: space-between; width: 100%; gap: 12px; }
            nav[aria-label="Pagination Navigation"] .sm\:flex { display: none; }
        }
        nav[aria-label="Pagination Navigation"] svg {
            width: 18px;
            height: 18px;
        }
        nav[aria-label="Pagination Navigation"] span.relative.inline-flex,
        nav[aria-label="Pagination Navigation"] span.inline-flex.rtl\:flex-row-reverse {
            display: inline-flex;
            gap: 6px;
            flex-wrap: wrap;
        }
        nav[aria-label="Pagination Navigation"] span[aria-disabled="true"] span,
        nav[aria-label="Pagination Navigation"] span[aria-disabled="true"],
        nav[aria-label="Pagination Navigation"] a {
            padding: 8px 14px;
            border-radius: 8px;
            border: 1px solid var(--border);
            font-size: 13px;
            font-weight: 500;
            background: var(--surface);
            color: var(--text);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        nav[aria-label="Pagination Navigation"] a:hover {
            border-color: var(--primary);
            color: var(--primary);
            box-shadow: var(--shadow-sm);
        }
        nav[aria-label="Pagination Navigation"] span[aria-current="page"] span {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: white;
            border-color: transparent;
            box-shadow: var(--shadow-sm);
        }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes shimmer {
            0% { background-position: -200% center; }
            100% { background-position: 200% center; }
        }

        .fade-in {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .fade-in-delay-1 { transition-delay: 0.1s; }
        .fade-in-delay-2 { transition-delay: 0.2s; }
        .fade-in-delay-3 { transition-delay: 0.3s; }
        .fade-in-delay-4 { transition-delay: 0.4s; }

        /* Reduce motion for accessibility */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
            .fade-in { opacity: 1; transform: none; }
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 960px) {
            .hero { 
                grid-template-columns: 1fr; 
                grid-template-areas: 
                    "text"
                    "visual"
                    "cards";
                padding: 36px 0 24px; 
            }
            .hero-visual { max-height: none; min-height: 380px; padding: 24px 0; overflow: visible; }
            .grid.cols-3, .grid.cols-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .nav-inner { flex-wrap: wrap; }
        }
        @media (max-width: 640px) {
            .grid.cols-2, .grid.cols-3, .grid.cols-4 { grid-template-columns: 1fr; }
            .nav-inner { gap: 10px; }
            .nav-links { gap: 4px; }
            .nav-links a, .ghost-button { padding: 6px 10px; font-size: 12px; }
            .footer-inner { flex-direction: column; align-items: flex-start; }
            .hero h1 { font-size: 1.8rem; }
            .stat strong { font-size: 1.5rem; }
        }

        /* ===== DEVELOPER FOOTER ===== */
        .dev-footer {
            border-top: 1px solid var(--border);
            padding: 20px 0;
            margin-top: 0;
        }
        .dev-footer-inner {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            font-size: 13px;
            color: var(--muted);
        }
        .dev-footer-inner a {
            color: var(--primary);
            font-weight: 600;
            transition: color 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .dev-footer-inner a:hover { color: var(--primary-strong); }
        .dev-footer-inner svg { width: 16px; height: 16px; }

        /* ===== MEDICINE ICON ===== */
        .medicine-icon-placeholder {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            display: grid;
            place-items: center;
        }
        .medicine-icon-placeholder svg { width: 56px; height: 56px; color: white; opacity: 0.5; }

        /* ===== ABOUT PAGE SPECIALS ===== */
        .about-hero {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            border-radius: 24px;
            padding: 48px;
            color: white;
            text-align: center;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }
        .about-hero h1 {
            background: none;
            -webkit-text-fill-color: white;
            font-size: clamp(1.8rem, 3vw, 2.8rem);
        }
        .about-hero p { color: rgba(255,255,255,0.85); }
        .about-hero-pattern {
            position: absolute;
            inset: 0;
            opacity: 0.08;
            background-image:
                radial-gradient(circle at 15% 25%, white 2px, transparent 2px),
                radial-gradient(circle at 85% 75%, white 2px, transparent 2px),
                radial-gradient(circle at 50% 50%, white 1px, transparent 1px);
            background-size: 50px 50px, 70px 70px, 30px 30px;
        }
        .feature-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--accent), var(--primary-light));
            display: grid;
            place-items: center;
            margin-bottom: 16px;
        }
        .feature-icon svg { width: 28px; height: 28px; color: var(--primary); }
        .photo-gallery { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .photo-gallery img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 16px;
            border: 1px solid var(--border);
            transition: transform 0.3s ease;
        }
        .photo-gallery img:hover { transform: scale(1.02); }
        @media (max-width: 640px) {
            .photo-gallery { grid-template-columns: 1fr; }
            .about-hero { padding: 32px 20px; }
        }

        /* ===== AUTH PAGES ===== */
        .auth-sidebar {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            border-radius: 20px;
            padding: 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .auth-sidebar h1 {
            background: none;
            -webkit-text-fill-color: white;
        }
        .auth-sidebar p { color: rgba(255,255,255,0.85); }
        .auth-sidebar .tag { background: rgba(255,255,255,0.2); color: white; }
        .auth-pattern {
            position: absolute;
            inset: 0;
            opacity: 0.08;
            background-image:
                radial-gradient(circle at 20% 30%, white 2px, transparent 2px),
                radial-gradient(circle at 80% 70%, white 2px, transparent 2px);
            background-size: 40px 40px, 60px 60px;
        }

        /* ===== QNA SPECIALS ===== */
        .question-count-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 10px;
            border-radius: 999px;
            background: var(--accent);
            color: var(--primary);
            font-size: 12px;
            font-weight: 700;
        }

        /* ===== PRICE DISPLAY ===== */
        .price { font-size: 1.2rem; font-weight: 800; color: var(--primary); }

        /* ===== NOTIFICATION PANEL ===== */
        .notification-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            width: 340px;
            max-height: 480px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            display: none;
            flex-direction: column;
            overflow: hidden;
            z-index: 200;
            margin-top: 12px;
            transform-origin: top right;
            animation: dropdownScale 0.2s ease forwards;
        }
        @keyframes dropdownScale {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .notification-dropdown.active { display: flex; }
        .notification-header {
            padding: 16px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--surface-hover);
        }
        .notification-header h3 { font-size: 14px; font-weight: 700; margin: 0; }
        .notification-header button {
            background: none; border: none; font-size: 12px; color: var(--primary); cursor: pointer; font-weight: 600;
        }
        .notification-header button:hover { color: var(--primary-strong); text-decoration: underline; }
        .notification-body {
            overflow-y: auto;
            flex-grow: 1;
            padding: 0;
        }
        .notification-item {
            display: flex;
            gap: 12px;
            padding: 16px;
            border-bottom: 1px solid var(--border-light);
            text-decoration: none;
            color: var(--text);
            transition: background 0.2s;
            cursor: pointer;
        }
        .notification-item:hover { background: var(--surface-hover); }
        .notification-item.unread { background: var(--notice-bg); }
        .notification-item.unread:hover { background: var(--notice-border); }
        .notification-indicator {
            width: 10px; height: 10px; border-radius: 50%; margin-top: 4px; flex-shrink: 0;
        }
        .notification-indicator.important { background: #ef4444; box-shadow: 0 0 8px rgba(239, 68, 68, 0.4); }
        .notification-indicator.moderate { background: #eab308; box-shadow: 0 0 8px rgba(234, 179, 8, 0.4); }
        .notification-indicator.normal { background: #22c55e; box-shadow: 0 0 8px rgba(34, 197, 94, 0.4); }
        .notification-content { flex-grow: 1; min-width: 0; }
        .notification-title { font-size: 13px; font-weight: 700; margin-bottom: 2px; }
        .notification-message { font-size: 12px; color: var(--text-secondary); line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .notification-time { font-size: 11px; color: var(--muted); margin-top: 4px; display: block; }
        .notification-empty { padding: 32px 16px; text-align: center; color: var(--muted); font-size: 13px; }
        
        .notification-toggle-wrapper { position: relative; display: flex; align-items: center; }
        .notification-toggle {
            background: none;
            border: 1px solid var(--border);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: grid;
            place-items: center;
            cursor: pointer;
            color: var(--text);
            position: relative;
            transition: all 0.2s ease;
        }
        .notification-toggle:hover { border-color: var(--primary); color: var(--primary); background: var(--surface-hover); }
        .notification-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #ef4444;
            color: white;
            font-size: 10px;
            font-weight: 800;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            border: 2px solid var(--nav-bg);
        }
    </style>
</head>
<body>

    {{-- ===== MAIN NAV ===== --}}

    <div class="nav">
        <div class="container nav-inner">
            <a class="brand" href="{{ route('home') }}">
                <span class="brand-logo">
                    <svg width="24" height="24" viewBox="0 0 26 26" fill="none">
                        <rect x="9" y="3" width="8" height="20" rx="2" fill="white"/>
                        <rect x="3" y="9" width="20" height="8" rx="2" fill="white"/>
                        <path d="M7 13 L10 10 L12 12.5 L15 8 L18 13" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none" opacity="0.7"/>
                    </svg>
                </span>
                <span class="brand-text">
                    <div style="font-size:15px;">HelloMed</div>
                    <small>Hospital &amp; Care Platform</small>
                </span>
            </a>

            {{-- Desktop Mega Nav --}}
            <nav class="mega-nav" aria-label="Main navigation">

                {{-- Ambulance --}}
                <div class="mega-nav-item">
                    <a href="{{ route('ambulance.create') }}" class="mega-nav-trigger nav-ambulance">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 4v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/><path d="M5 10h4M7 8v4"/></svg>
                        Ambulance
                    </a>
                </div>

                {{-- Home --}}
                <div class="mega-nav-item">
                    <a href="{{ route('home') }}" class="mega-nav-trigger">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        Home
                    </a>
                </div>

                {{-- Care --}}
                <div class="mega-nav-item">
                    <button class="mega-nav-trigger">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                        Care
                        <svg class="chev" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="mega-dropdown">
                        <div class="mega-dropdown-inner">
                            <div class="mega-dropdown-grid cols-4">
                                <a href="{{ route('departments.index') }}" class="mega-dropdown-link">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18M15 3v18"/></svg>
                                    <div class="mega-dropdown-link-text"><span>Visit Departments</span><span class="mega-dropdown-link-desc">Browse all hospital departments</span></div>
                                </a>
                                <a href="{{ route('doctors.index') }}" class="mega-dropdown-link">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    <div class="mega-dropdown-link-text"><span>Doctors for Appointments</span><span class="mega-dropdown-link-desc">Find &amp; book a specialist</span></div>
                                </a>
                                <a href="{{ route('available-tests.index') }}" class="mega-dropdown-link">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2v6l-2 3.5a1 1 0 0 0 .85 1.5h10.3A1 1 0 0 0 16 11.5L14 8V2"/><path d="M6 2h8M5 8h14"/></svg>
                                    <div class="mega-dropdown-link-text"><span>Diagnostics Services</span><span class="mega-dropdown-link-desc">Lab tests &amp; diagnostics</span></div>
                                </a>
                                <a href="{{ route('medicines.index') }}" class="mega-dropdown-link">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                                    <div class="mega-dropdown-link-text"><span>Medicine Shop</span><span class="mega-dropdown-link-desc">Order medicines online</span></div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Role-based Dashboard --}}
                @auth
                    @if(auth()->user()->isAdmin())
                        <div class="mega-nav-item">
                            <button class="mega-nav-trigger">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                                Admin Dashboard
                                <svg class="chev" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                            </button>
                            <div class="mega-dropdown">
                                <div class="mega-dropdown-inner">
                                    <div class="mega-dropdown-sections">
                                        <div class="mega-dropdown-section-col">
                                            <div class="mega-dropdown-section-title">Overview</div>
                                            <a href="{{ route('admin.dashboard') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg> Admin Panel</a>
                                            <a href="{{ route('admin.audit-logs.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg> Audit Log</a>
                                            <div class="mega-dropdown-section-title">Finances</div>
                                            <a href="{{ route('admin.payouts.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg> Payouts</a>
                                            <a href="{{ route('analytics.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg> Analytics</a>
                                        </div>
                                        <div class="mega-dropdown-section-col">
                                            <div class="mega-dropdown-section-title">Backoffice</div>
                                            <a href="{{ route('admin.departments.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18M15 3v18"/></svg> Departments</a>
                                            <a href="{{ route('admin.available-tests.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2v6l-2 3.5a1 1 0 0 0 .85 1.5h10.3A1 1 0 0 0 16 11.5L14 8V2"/><path d="M6 2h8"/></svg> Available Tests</a>
                                            <a href="{{ route('admin.medicines.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg> Pharmacy</a>
                                            <a href="{{ route('admin.ambulance.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 4v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg> Ambulance</a>
                                            <a href="{{ route('admin.doctors.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Doctors</a>
                                            <a href="{{ route('admin.staff.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg> Staff</a>
                                            <a href="{{ route('admin.pharmacists.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2v6l-2 3.5a1 1 0 0 0 .85 1.5h10.3A1 1 0 0 0 16 11.5L14 8V2"/><path d="M6 2h8"/></svg> Pharmacists</a>
                                        </div>
                                        <div class="mega-dropdown-section-col">
                                            <div class="mega-dropdown-section-title">Patient Management</div>
                                            <a href="{{ route('admin.patients.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Patients</a>
                                            <a href="{{ route('admin.appointments.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg> Appointments</a>
                                            <div class="mega-dropdown-section-title">Others</div>
                                            <a href="{{ route('admin.articles.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Articles</a>
                                            <a href="{{ route('admin.comments.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> Comments</a>
                                            <a href="{{ route('admin.qna.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg> Q&amp;A</a>
                                        </div>
                                        <div class="mega-dropdown-section-col">
                                            <div class="mega-dropdown-section-title">Account</div>
                                            <a href="{{ route('settings.profile') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg> Account Settings</a>
                                            <div class="mega-dropdown-sep"></div>
                                            <form method="POST" action="{{ route('logout') }}" class="mega-dropdown-link-form">@csrf
                                                <button type="submit" class="mega-dropdown-link-btn"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg> Logout</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @elseif(auth()->user()->isDoctor())
                        <div class="mega-nav-item">
                            <button class="mega-nav-trigger">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                Doctor Dashboard
                                <svg class="chev" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                            </button>
                            <div class="mega-dropdown">
                                <div class="mega-dropdown-inner">
                                    <div class="mega-dropdown-grid cols-2" style="max-width:520px;">
                                        <a href="{{ route('doctor.dashboard') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg><div class="mega-dropdown-link-text"><span>Doctor Panel</span><span class="mega-dropdown-link-desc">Dashboard overview &amp; schedule</span></div></a>
                                        <a href="{{ route('doctor.dashboard') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg><div class="mega-dropdown-link-text"><span>Appointments</span><span class="mega-dropdown-link-desc">View &amp; manage appointments</span></div></a>
                                        <a href="{{ route('doctor.articles.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg><div class="mega-dropdown-link-text"><span>My Articles</span><span class="mega-dropdown-link-desc">Published &amp; draft articles</span></div></a>
                                        <a href="{{ route('doctor.articles.create') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg><div class="mega-dropdown-link-text"><span>Write Article</span><span class="mega-dropdown-link-desc">Publish a new health article</span></div></a>
                                        <a href="{{ route('analytics.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg><div class="mega-dropdown-link-text"><span>Analytics</span><span class="mega-dropdown-link-desc">Performance &amp; payout stats</span></div></a>
                                        <a href="{{ route('settings.profile') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg><div class="mega-dropdown-link-text"><span>Account Settings</span></div></a>
                                    </div>
                                    <div class="mega-dropdown-sep"></div>
                                    <form method="POST" action="{{ route('logout') }}" class="mega-dropdown-link-form">@csrf
                                        <button type="submit" class="mega-dropdown-link-btn"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg> Logout</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @elseif(auth()->user()->isStaff())
                        <div class="mega-nav-item">
                            <button class="mega-nav-trigger">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                Staff Dashboard
                                <svg class="chev" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                            </button>
                            <div class="mega-dropdown">
                                <div class="mega-dropdown-inner">
                                    <div class="mega-dropdown-sections" style="grid-template-columns: repeat(3,1fr);">
                                        <div class="mega-dropdown-section-col">
                                            <div class="mega-dropdown-section-title">Operations</div>
                                            <a href="{{ route('staff.dashboard') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg> Staff Panel</a>
                                            <a href="{{ route('staff.ambulance.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 4v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg> Ambulance Dispatch</a>
                                            <a href="{{ route('admin.appointments.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg> Manage Appointments</a>
                                            <a href="{{ route('staff.offline-appointments.create') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="12" y1="14" x2="12" y2="20"/><line x1="9" y1="17" x2="15" y2="17"/></svg> Book Offline Appointment</a>
                                            <a href="{{ route('staff.diagnostic-services.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2v6l-2 3.5a1 1 0 0 0 .85 1.5h10.3A1 1 0 0 0 16 11.5L14 8V2"/><path d="M6 2h8"/></svg> Diagnostics Services</a>
                                        </div>
                                        <div class="mega-dropdown-section-col">
                                            <div class="mega-dropdown-section-title">Others</div>
                                            <a href="{{ route('staff.patients.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Patients</a>
                                            <a href="{{ route('admin.doctors.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg> Doctors</a>
                                            <a href="{{ route('staff.articles.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Articles</a>
                                            <a href="{{ route('staff.comments.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> Comments</a>
                                            <a href="{{ route('staff.qna.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg> Q&amp;A</a>
                                        </div>
                                        <div class="mega-dropdown-section-col">
                                            <div class="mega-dropdown-section-title">Account</div>
                                            <form method="POST" action="{{ route('logout') }}" class="mega-dropdown-link-form">@csrf
                                                <button type="submit" class="mega-dropdown-link-btn"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg> Logout</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @elseif(auth()->user()->isPharmacist())
                        <div class="mega-nav-item">
                            <button class="mega-nav-trigger">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2v6l-2 3.5a1 1 0 0 0 .85 1.5h10.3A1 1 0 0 0 16 11.5L14 8V2"/><path d="M6 2h8M5 8h14"/></svg>
                                Pharmacist Dashboard
                                <svg class="chev" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                            </button>
                            <div class="mega-dropdown">
                                <div class="mega-dropdown-inner">
                                    <div class="mega-dropdown-grid cols-2" style="max-width:480px;">
                                        <a href="{{ route('pharmacist.dashboard') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg><div class="mega-dropdown-link-text"><span>Pharmacist Panel</span><span class="mega-dropdown-link-desc">Dashboard &amp; inventory</span></div></a>
                                        <a href="{{ route('pharmacist.medicines.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2v6l-2 3.5a1 1 0 0 0 .85 1.5h10.3A1 1 0 0 0 16 11.5L14 8V2"/><path d="M6 2h8"/></svg><div class="mega-dropdown-link-text"><span>Manage Medicines</span><span class="mega-dropdown-link-desc">Stock, prices &amp; catalogue</span></div></a>
                                        <a href="{{ route('pharmacist.orders.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg><div class="mega-dropdown-link-text"><span>Manage Orders</span><span class="mega-dropdown-link-desc">Process &amp; track orders</span></div></a>
                                    </div>
                                    <div class="mega-dropdown-sep"></div>
                                    <form method="POST" action="{{ route('logout') }}" class="mega-dropdown-link-form">@csrf
                                        <button type="submit" class="mega-dropdown-link-btn"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg> Logout</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @else
                        {{-- Patient --}}
                        <div class="mega-nav-item">
                            <button class="mega-nav-trigger">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                My Dashboard
                                <svg class="chev" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                            </button>
                            <div class="mega-dropdown">
                                <div class="mega-dropdown-inner">
                                    <div class="mega-dropdown-grid cols-2" style="max-width:520px;">
                                        <a href="{{ route('patient.profile') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg><div class="mega-dropdown-link-text"><span>My Profile</span><span class="mega-dropdown-link-desc">Medical profile &amp; personal info</span></div></a>
                                        <a href="{{ route('patient.appointments') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg><div class="mega-dropdown-link-text"><span>My Appointments</span><span class="mega-dropdown-link-desc">Track &amp; manage appointments</span></div></a>
                                        <a href="{{ route('patient.records') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg><div class="mega-dropdown-link-text"><span>My Records</span><span class="mega-dropdown-link-desc">Lab results &amp; prescriptions</span></div></a>
                                        <a href="{{ route('patient.medicine-orders') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg><div class="mega-dropdown-link-text"><span>My Medicine Orders</span><span class="mega-dropdown-link-desc">Order history &amp; tracking</span></div></a>
                                        <a href="{{ route('settings.profile') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg><div class="mega-dropdown-link-text"><span>Account Settings</span></div></a>
                                    </div>
                                    <div class="mega-dropdown-sep"></div>
                                    <form method="POST" action="{{ route('logout') }}" class="mega-dropdown-link-form">@csrf
                                        <button type="submit" class="mega-dropdown-link-btn"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg> Logout</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @endauth

                {{-- Support --}}
                <div class="mega-nav-item">
                    <button class="mega-nav-trigger">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        Support
                        <svg class="chev" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="mega-dropdown">
                        <div class="mega-dropdown-inner">
                            <div class="mega-dropdown-grid cols-3" style="max-width:560px;">
                                <a href="{{ route('about') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><div class="mega-dropdown-link-text"><span>About</span><span class="mega-dropdown-link-desc">Our mission &amp; team</span></div></a>
                                <a href="{{ route('qna.index') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg><div class="mega-dropdown-link-text"><span>FAQ &amp; Q&amp;A</span><span class="mega-dropdown-link-desc">Community health questions</span></div></a>
                                <a href="{{ route('contact') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg><div class="mega-dropdown-link-text"><span>Contact</span><span class="mega-dropdown-link-desc">Get in touch with us</span></div></a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Register (guest only) --}}
                @guest
                <div class="mega-nav-item">
                    <button class="mega-nav-trigger">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                        Register
                        <svg class="chev" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="mega-dropdown">
                        <div class="mega-dropdown-inner">
                            <div class="mega-dropdown-grid cols-2" style="max-width:480px;">
                                <a href="{{ route('register') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg><div class="mega-dropdown-link-text"><span>Create a New Account</span><span class="mega-dropdown-link-desc">Register as a patient today</span></div></a>
                                <a href="{{ route('login') }}" class="mega-dropdown-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg><div class="mega-dropdown-link-text"><span>Log In</span><span class="mega-dropdown-link-desc">Sign in to your account</span></div></a>
                            </div>
                        </div>
                    </div>
                </div>
                @endguest

            </nav>

            {{-- Nav Utilities --}}
            <div class="nav-utils">
                @auth
                <div class="notification-toggle-wrapper" id="notificationWrapper">
                    <button class="notification-toggle" id="notificationToggle" aria-label="Notifications">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                        <div class="notification-badge" id="notificationBadge" style="display: none;">0</div>
                    </button>
                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-header">
                            <h3>Notifications</h3>
                            <button id="markAllReadBtn" style="display: none;">Mark all as read</button>
                        </div>
                        <div class="notification-body" id="notificationList">
                            <!-- Notifications loaded via JS -->
                        </div>
                    </div>
                </div>
                @endauth

                <button class="theme-toggle" onclick="toggleTheme()" title="Toggle dark/light mode" aria-label="Toggle theme">
                    <span class="icon-sun"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg></span>
                    <span class="icon-moon"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg></span>
                </button>

                {{-- Mobile AI Chat Toggle (Hidden on desktop) --}}
                <button class="mobile-ai-chat-btn" onclick="window.toggleAiChat && window.toggleAiChat()" title="Chat with AI" aria-label="Open AI chat">
                    🩺
                </button>

                <button class="nav-hamburger" id="navHamburger" aria-label="Open navigation menu" aria-expanded="false">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ===== MOBILE FULL-SCREEN NAV OVERLAY ===== --}}
    <div class="mobile-nav-overlay" id="mobileNavOverlay" aria-modal="true" role="dialog" aria-label="Navigation menu">

        <div class="mobile-nav-backdrop" id="mobileNavBackdrop"></div>
        <div class="mobile-nav-panel">
            <div class="mobile-nav-header">
                <a class="brand" href="{{ route('home') }}" style="gap:8px;">
                    <span class="brand-logo" style="width:32px;height:32px;border-radius:10px;">
                        <svg width="20" height="20" viewBox="0 0 26 26" fill="none"><rect x="9" y="3" width="8" height="20" rx="2" fill="white"/><rect x="3" y="9" width="20" height="8" rx="2" fill="white"/></svg>
                    </span>
                    <span style="font-size:14px;font-weight:800;">HelloMed</span>
                </a>
                <button class="mobile-nav-close" id="mobileNavClose" aria-label="Close navigation">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="mobile-nav-body">
                {{-- Ambulance --}}
                <a href="{{ route('ambulance.create') }}" class="mobile-nav-link ambulance">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 4v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/><path d="M5 10h4M7 8v4"/></svg>
                    Emergency Ambulance
                </a>
                {{-- Home --}}
                <a href="{{ route('home') }}" class="mobile-nav-link">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Home
                </a>
                <div class="mobile-nav-divider"></div>
                {{-- Care accordion --}}
                <div class="mobile-nav-accordion" id="mAcc-care">
                    <button class="mobile-nav-acc-trigger" onclick="toggleMobileAcc('mAcc-care')">
                        <span class="mobile-nav-acc-trigger-inner">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                            Care
                        </span>
                        <svg class="mobile-nav-acc-chev" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="mobile-nav-acc-body">
                        <a href="{{ route('departments.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18M3 9h6M15 3v18M15 9h6"/></svg> Visit Departments</a>
                        <a href="{{ route('doctors.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Doctors for Appointments</a>
                        <a href="{{ route('available-tests.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2v6l-2 3.5a1 1 0 0 0 .85 1.5h10.3A1 1 0 0 0 16 11.5L14 8V2"/><path d="M6 2h8M5 8h14"/></svg> Diagnostics Services</a>
                        <a href="{{ route('medicines.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.5 20H4a2 2 0 0 1-2-2V5c0-1.1.9-2 2-2h3.93a2 2 0 0 1 1.66.9l.82 1.2a2 2 0 0 0 1.66.9H20a2 2 0 0 1 2 2v3"/><circle cx="18" cy="18" r="3"/></svg> Medicine Shop</a>
                    </div>
                </div>

                {{-- Role-based dashboard accordion --}}
                @auth
                <div class="mobile-nav-divider"></div>
                @if(auth()->user()->isAdmin())
                <div class="mobile-nav-accordion" id="mAcc-dashboard">
                    <button class="mobile-nav-acc-trigger" onclick="toggleMobileAcc('mAcc-dashboard')">
                        <span class="mobile-nav-acc-trigger-inner"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg> Admin Dashboard</span>
                        <svg class="mobile-nav-acc-chev" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="mobile-nav-acc-body">
                        <a href="{{ route('admin.dashboard') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg> Admin Panel</a>
                        <div class="mobile-nav-sub-label">Finances</div>
                        <a href="{{ route('admin.payouts.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg> Payouts</a>
                        <a href="{{ route('analytics.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg> Analytics</a>
                        <div class="mobile-nav-sub-label">Backoffice</div>
                        <a href="{{ route('admin.departments.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/></svg> Departments</a>
                        <a href="{{ route('admin.available-tests.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2v6l-2 3.5a1 1 0 0 0 .85 1.5h10.3A1 1 0 0 0 16 11.5L14 8V2"/><path d="M6 2h8"/></svg> Available Tests</a>
                        <a href="{{ route('admin.medicines.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg> Pharmacy</a>
                        <a href="{{ route('admin.ambulance.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 4v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg> Ambulance</a>
                        <a href="{{ route('admin.doctors.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Doctors</a>
                        <a href="{{ route('admin.staff.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg> Staff</a>
                        <a href="{{ route('admin.pharmacists.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2v6l-2 3.5a1 1 0 0 0 .85 1.5h10.3A1 1 0 0 0 16 11.5L14 8V2"/><path d="M6 2h8"/></svg> Pharmacists</a>
                        <div class="mobile-nav-sub-label">Patient Management</div>
                        <a href="{{ route('admin.patients.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Patients</a>
                        <a href="{{ route('admin.appointments.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg> Appointments</a>
                        <div class="mobile-nav-sub-label">Others</div>
                        <a href="{{ route('admin.articles.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg> Articles</a>
                        <a href="{{ route('admin.comments.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> Comments</a>
                        <a href="{{ route('admin.qna.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg> Q&amp;A</a>
                        <a href="{{ route('admin.audit-logs.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg> Audit Log</a>
                        <div class="mobile-nav-sub-label">Account</div>
                        <a href="{{ route('settings.profile') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg> Account Settings</a>
                        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="mobile-nav-sub-btn"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg> Logout</button></form>
                    </div>
                </div>
                @elseif(auth()->user()->isDoctor())
                <div class="mobile-nav-accordion" id="mAcc-dashboard">
                    <button class="mobile-nav-acc-trigger" onclick="toggleMobileAcc('mAcc-dashboard')">
                        <span class="mobile-nav-acc-trigger-inner"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Doctor Dashboard</span>
                        <svg class="mobile-nav-acc-chev" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="mobile-nav-acc-body">
                        <a href="{{ route('doctor.dashboard') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg> Doctor Panel</a>
                        <a href="{{ route('doctor.dashboard') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg> Appointments</a>
                        <a href="{{ route('doctor.articles.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg> My Articles</a>
                        <a href="{{ route('doctor.articles.create') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg> Write Article</a>
                        <a href="{{ route('analytics.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg> Analytics</a>
                        <a href="{{ route('settings.profile') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg> Account Settings</a>
                        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="mobile-nav-sub-btn"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg> Logout</button></form>
                    </div>
                </div>
                @elseif(auth()->user()->isStaff())
                <div class="mobile-nav-accordion" id="mAcc-dashboard">
                    <button class="mobile-nav-acc-trigger" onclick="toggleMobileAcc('mAcc-dashboard')">
                        <span class="mobile-nav-acc-trigger-inner"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg> Staff Dashboard</span>
                        <svg class="mobile-nav-acc-chev" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="mobile-nav-acc-body">
                        <a href="{{ route('staff.dashboard') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg> Staff Panel</a>
                        <a href="{{ route('staff.ambulance.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 4v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg> Ambulance Dispatch</a>
                        <a href="{{ route('admin.appointments.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg> Manage Appointments</a>
                        <a href="{{ route('staff.offline-appointments.create') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/></svg> Book Offline Appointment</a>
                        <a href="{{ route('staff.diagnostic-services.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2v6l-2 3.5a1 1 0 0 0 .85 1.5h10.3A1 1 0 0 0 16 11.5L14 8V2"/><path d="M6 2h8"/></svg> Diagnostics Services</a>
                        <div class="mobile-nav-sub-label">Others</div>
                        <a href="{{ route('staff.patients.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Patients</a>
                        <a href="{{ route('admin.doctors.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg> Doctors</a>
                        <a href="{{ route('staff.articles.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg> Articles</a>
                        <a href="{{ route('staff.comments.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> Comments</a>
                        <a href="{{ route('staff.qna.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg> Q&amp;A</a>
                        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="mobile-nav-sub-btn"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg> Logout</button></form>
                    </div>
                </div>
                @elseif(auth()->user()->isPharmacist())
                <div class="mobile-nav-accordion" id="mAcc-dashboard">
                    <button class="mobile-nav-acc-trigger" onclick="toggleMobileAcc('mAcc-dashboard')">
                        <span class="mobile-nav-acc-trigger-inner"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2v6l-2 3.5a1 1 0 0 0 .85 1.5h10.3A1 1 0 0 0 16 11.5L14 8V2"/><path d="M6 2h8M5 8h14"/></svg> Pharmacist Dashboard</span>
                        <svg class="mobile-nav-acc-chev" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="mobile-nav-acc-body">
                        <a href="{{ route('pharmacist.dashboard') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg> Pharmacist Panel</a>
                        <a href="{{ route('pharmacist.medicines.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2v6l-2 3.5a1 1 0 0 0 .85 1.5h10.3A1 1 0 0 0 16 11.5L14 8V2"/><path d="M6 2h8"/></svg> Manage Medicines</a>
                        <a href="{{ route('pharmacist.orders.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg> Manage Orders</a>
                        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="mobile-nav-sub-btn"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg> Logout</button></form>
                    </div>
                </div>
                @else
                {{-- Patient --}}
                <div class="mobile-nav-accordion" id="mAcc-dashboard">
                    <button class="mobile-nav-acc-trigger" onclick="toggleMobileAcc('mAcc-dashboard')">
                        <span class="mobile-nav-acc-trigger-inner"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> My Dashboard</span>
                        <svg class="mobile-nav-acc-chev" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="mobile-nav-acc-body">
                        <a href="{{ route('patient.profile') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> My Profile</a>
                        <a href="{{ route('patient.appointments') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg> My Appointments</a>
                        <a href="{{ route('patient.records') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg> My Records</a>
                        <a href="{{ route('patient.medicine-orders') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg> My Medicine Orders</a>
                        <a href="{{ route('settings.profile') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg> Account Settings</a>
                        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="mobile-nav-sub-btn"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg> Logout</button></form>
                    </div>
                </div>
                @endif
                @endauth

                {{-- Support accordion --}}
                <div class="mobile-nav-divider"></div>
                <div class="mobile-nav-accordion" id="mAcc-support">
                    <button class="mobile-nav-acc-trigger" onclick="toggleMobileAcc('mAcc-support')">
                        <span class="mobile-nav-acc-trigger-inner"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg> Support</span>
                        <svg class="mobile-nav-acc-chev" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="mobile-nav-acc-body">
                        <a href="{{ route('about') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> About</a>
                        <a href="{{ route('qna.index') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> FAQ &amp; Q&amp;A</a>
                        <a href="{{ route('contact') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg> Contact</a>
                    </div>
                </div>

                {{-- Register accordion (guest only) --}}
                @guest
                <div class="mobile-nav-divider"></div>
                <div class="mobile-nav-accordion" id="mAcc-register">
                    <button class="mobile-nav-acc-trigger" onclick="toggleMobileAcc('mAcc-register')">
                        <span class="mobile-nav-acc-trigger-inner"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg> Register</span>
                        <svg class="mobile-nav-acc-chev" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="mobile-nav-acc-body">
                        <a href="{{ route('register') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg> Create a New Account</a>
                        <a href="{{ route('login') }}" class="mobile-nav-sub-link"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg> Log In</a>
                    </div>
                </div>
                @endguest
            </div>
        </div>
    </div>

    <main class="container">
        @if ($errors->any())
            <div class="error-box" style="margin-top: 20px;">
                <strong>There were validation errors:</strong>
                <ul style="margin:8px 0 0 16px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('status'))
            <div class="notice" style="margin-top: 20px;">{{ session('status') }}</div>
        @endif

        @auth
            @if(auth()->user()->role === 'patient' && (!auth()->user()->patientProfile || auth()->user()->patientProfile->isIncomplete()))
                <div class="notice" style="background-color: #fffbeb; color: #92400e; border-color: #fef3c7; margin: 20px 0;">
                    <strong>Action Required:</strong> Please complete your medical profile (date of birth, gender, height, weight, etc.) in your <a href="{{ route('patient.profile') }}" style="text-decoration: underline; color: inherit;">profile</a> for better care.
                </div>
            @endif
        @endauth

        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="container">

            {{-- ── Top grid ── --}}
            <div class="sf-grid">

                {{-- Brand column --}}
                <div class="sf-brand">
                    <a href="{{ route('home') }}" class="sf-logo">
                        <span class="sf-logo-mark">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                        </span>
                        <span class="sf-logo-text">HelloMed</span>
                    </a>
                    <p class="sf-tagline">Online &amp; offline hospital services with central appointment booking.</p>
                    <div class="sf-social">
                        <a href="https://github.com/AbirHasanArko" target="_blank" rel="noopener" aria-label="GitHub" class="sf-social-link">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                        </a>
                        <a href="https://www.linkedin.com/in/abirhasanarko/" target="_blank" rel="noopener" aria-label="LinkedIn" class="sf-social-link">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                        <a href="mailto:abirhasanarko2004@gmail.com" aria-label="Email" class="sf-social-link">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Care column (always visible) --}}
                <div class="sf-col">
                    <div class="sf-col-title">Care</div>
                    <a href="{{ route('departments.index') }}" class="sf-link">Departments</a>
                    <a href="{{ route('doctors.index') }}" class="sf-link">Find a Doctor</a>
                    <a href="{{ route('available-tests.index') }}" class="sf-link">Diagnostics &amp; Tests</a>
                    <a href="{{ route('medicines.index') }}" class="sf-link">Medicine Shop</a>
                    <a href="{{ route('ambulance.create') }}" class="sf-link sf-link-urgent">🚑 Request Ambulance</a>
                </div>

                {{-- Learn column (always visible) --}}
                <div class="sf-col">
                    <div class="sf-col-title">Learn</div>
                    <a href="{{ route('articles.index') }}" class="sf-link">Health Articles</a>
                    <a href="{{ route('qna.index') }}" class="sf-link">Q&amp;A Community</a>
                </div>

                {{-- Role-sensitive Dashboard column --}}
                <div class="sf-col">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <div class="sf-col-title">Admin Panel</div>
                            <a href="{{ route('admin.dashboard') }}" class="sf-link">Dashboard</a>
                            <a href="{{ route('admin.appointments.index') }}" class="sf-link">Appointments</a>
                            <a href="{{ route('admin.doctors.index') }}" class="sf-link">Doctors</a>
                            <a href="{{ route('admin.patients.index') }}" class="sf-link">Patients</a>
                            <a href="{{ route('admin.staff.index') }}" class="sf-link">Staff</a>
                            <a href="{{ route('admin.departments.index') }}" class="sf-link">Departments</a>
                            <a href="{{ route('admin.medicines.index') }}" class="sf-link">Pharmacy</a>
                            <a href="{{ route('admin.ambulance.index') }}" class="sf-link">Ambulance</a>
                            <a href="{{ route('admin.payouts.index') }}" class="sf-link">Payouts</a>
                            <a href="{{ route('analytics.index') }}" class="sf-link">Analytics</a>
                            <a href="{{ route('admin.audit-logs.index') }}" class="sf-link">Audit Log</a>
                            <a href="{{ route('admin.articles.index') }}" class="sf-link">Articles</a>
                            <a href="{{ route('admin.qna.index') }}" class="sf-link">Q&amp;A</a>
                            <a href="{{ route('settings.profile') }}" class="sf-link">Account Settings</a>

                        @elseif(auth()->user()->isDoctor())
                            <div class="sf-col-title">Doctor Panel</div>
                            <a href="{{ route('doctor.dashboard') }}" class="sf-link">Dashboard</a>
                            <a href="{{ route('doctor.articles.index') }}" class="sf-link">My Articles</a>
                            <a href="{{ route('doctor.articles.create') }}" class="sf-link">Write Article</a>
                            <a href="{{ route('analytics.index') }}" class="sf-link">Analytics</a>
                            <a href="{{ route('settings.profile') }}" class="sf-link">Account Settings</a>

                        @elseif(auth()->user()->isStaff())
                            <div class="sf-col-title">Staff Panel</div>
                            <a href="{{ route('staff.dashboard') }}" class="sf-link">Dashboard</a>
                            <a href="{{ route('admin.appointments.index') }}" class="sf-link">Appointments</a>
                            <a href="{{ route('staff.offline-appointments.create') }}" class="sf-link">Book Offline Appt.</a>
                            <a href="{{ route('staff.ambulance.index') }}" class="sf-link">Ambulance Dispatch</a>
                            <a href="{{ route('staff.diagnostic-services.index') }}" class="sf-link">Diagnostics</a>
                            <a href="{{ route('staff.patients.index') }}" class="sf-link">Patients</a>
                            <a href="{{ route('admin.doctors.index') }}" class="sf-link">Doctors</a>
                            <a href="{{ route('staff.articles.index') }}" class="sf-link">Articles</a>
                            <a href="{{ route('staff.qna.index') }}" class="sf-link">Q&amp;A</a>

                        @elseif(auth()->user()->isPharmacist())
                            <div class="sf-col-title">Pharmacist Panel</div>
                            <a href="{{ route('pharmacist.dashboard') }}" class="sf-link">Dashboard</a>
                            <a href="{{ route('pharmacist.medicines.index') }}" class="sf-link">Manage Medicines</a>
                            <a href="{{ route('pharmacist.orders.index') }}" class="sf-link">Manage Orders</a>
                            <a href="{{ route('settings.profile') }}" class="sf-link">Account Settings</a>

                        @else
                            {{-- Patient --}}
                            <div class="sf-col-title">My Dashboard</div>
                            <a href="{{ route('patient.profile') }}" class="sf-link">My Profile</a>
                            <a href="{{ route('patient.appointments') }}" class="sf-link">My Appointments</a>
                            <a href="{{ route('patient.records') }}" class="sf-link">My Records</a>
                            <a href="{{ route('patient.medicine-orders') }}" class="sf-link">Medicine Orders</a>
                            <a href="{{ route('settings.profile') }}" class="sf-link">Account Settings</a>
                        @endif
                    @else
                        <div class="sf-col-title">Account</div>
                        <a href="{{ route('login') }}" class="sf-link">Sign In</a>
                        <a href="{{ route('register') }}" class="sf-link">Create Account</a>
                    @endauth
                </div>

                {{-- Contact / Company column --}}
                <div class="sf-col">
                    <div class="sf-col-title">Company</div>
                    <a href="#" class="sf-link">About Us</a>
                    <a href="{{ route('contact') }}" class="sf-link">Contact Us</a>
                </div>

            </div>{{-- /sf-grid --}}

            {{-- ── Bottom bar ── --}}
            <div class="sf-bottom">
                <span class="sf-copy">© {{ date('Y') }} HelloMed · Built with Laravel</span>
                <span class="sf-dev">Developed by <strong>Abir Hasan Arko</strong></span>
            </div>

        </div>
        @stack('footer-extra')
    </footer>

    <style>
        /* ── Fat Footer ─────────────────────────────────────────── */
        .site-footer {
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 56px 0 0;
            margin-top: 0;
        }
        .site-footer .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }

        .sf-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
            gap: 40px 32px;
        }
        @media (max-width: 1024px) {
            .sf-grid { grid-template-columns: 1fr 1fr 1fr; }
            .sf-brand { grid-column: 1 / -1; }
        }
        @media (max-width: 640px) {
            .sf-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 420px) {
            .sf-grid { grid-template-columns: 1fr; }
        }

        /* Brand */
        .sf-brand { display: flex; flex-direction: column; gap: 14px; }
        .sf-logo { display: inline-flex; align-items: center; gap: 10px; text-decoration: none; color: var(--text); font-weight: 700; font-size: 1.1rem; width: fit-content; }
        .sf-logo-mark { width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end)); display: grid; place-items: center; color: #fff; flex-shrink: 0; }
        .sf-logo:hover .sf-logo-mark { opacity: .85; }
        .sf-tagline { font-size: 0.88rem; color: var(--muted); line-height: 1.6; max-width: 280px; margin: 0; }
        .sf-social { display: flex; gap: 10px; flex-wrap: wrap; }
        .sf-social-link { width: 34px; height: 34px; border-radius: 8px; background: var(--surface-hover); border: 1px solid var(--border); display: grid; place-items: center; color: var(--muted); text-decoration: none; transition: background .18s, color .18s, border-color .18s; }
        .sf-social-link svg { width: 15px; height: 15px; }
        .sf-social-link:hover { background: var(--primary); border-color: var(--primary); color: #fff; }

        /* Columns */
        .sf-col { display: flex; flex-direction: column; gap: 6px; }
        .sf-col-title { font-size: 0.7rem; font-weight: 700; letter-spacing: .09em; text-transform: uppercase; color: var(--muted); margin-bottom: 6px; margin-top: 2px; }
        .sf-link { font-size: 0.875rem; color: var(--text-secondary, var(--muted)); text-decoration: none; padding: 2px 0; transition: color .15s; cursor: pointer; }
        .sf-link:hover { color: var(--primary); }
        .sf-link-urgent { color: #ef4444 !important; font-weight: 600; }
        .sf-link-urgent:hover { color: #dc2626 !important; }
        .sf-link-muted { color: var(--muted); font-size: 0.82rem; cursor: default; }
        .sf-link-muted:hover { color: var(--muted); }

        /* Bottom bar */
        .sf-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            border-top: 1px solid var(--border);
            margin-top: 48px;
            padding: 20px 0 24px;
            font-size: 0.8rem;
            color: var(--muted);
        }
        .sf-dev strong { color: var(--text); }
    </style>


    {{-- AI Health Assistant Floating Widget --}}
    @include('components.ai-chat-widget')

    <script>
        // ===== MOBILE NAV =====
        (function() {
            const hamburger = document.getElementById('navHamburger');
            const overlay   = document.getElementById('mobileNavOverlay');
            const closeBtn  = document.getElementById('mobileNavClose');
            const backdrop  = document.getElementById('mobileNavBackdrop');

            function openNav() {
                overlay.classList.add('open');
                hamburger.setAttribute('aria-expanded', 'true');
                document.body.style.overflow = 'hidden';
            }
            function closeNav() {
                overlay.classList.remove('open');
                hamburger.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }

            if (hamburger) hamburger.addEventListener('click', openNav);
            if (closeBtn)  closeBtn.addEventListener('click', closeNav);
            if (backdrop)  backdrop.addEventListener('click', closeNav);

            // Close on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && overlay && overlay.classList.contains('open')) {
                    closeNav();
                }
            });
        })();

        function toggleMobileAcc(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.toggle('open');
        }

        function toggleTheme() {

            const current = document.documentElement.getAttribute('data-theme') || 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('hm-theme', next);
        }
        (function initTheme() {
            const saved = localStorage.getItem('hm-theme');
            if (saved) {
                document.documentElement.setAttribute('data-theme', saved);
            } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();

        @auth
        document.addEventListener('DOMContentLoaded', function() {
            const wrapper = document.getElementById('notificationWrapper');
            const toggle = document.getElementById('notificationToggle');
            const dropdown = document.getElementById('notificationDropdown');
            const badge = document.getElementById('notificationBadge');
            const list = document.getElementById('notificationList');
            const markAllBtn = document.getElementById('markAllReadBtn');
            let unreadCount = 0;

            toggle.addEventListener('click', () => {
                dropdown.classList.toggle('active');
            });

            document.addEventListener('click', (e) => {
                if (!wrapper.contains(e.target)) {
                    dropdown.classList.remove('active');
                }
            });

            function timeSince(dateStr) {
                const date = new Date(dateStr);
                const seconds = Math.floor((new Date() - date) / 1000);
                let interval = seconds / 31536000;
                if (interval > 1) return Math.floor(interval) + " years ago";
                interval = seconds / 2592000;
                if (interval > 1) return Math.floor(interval) + " months ago";
                interval = seconds / 86400;
                if (interval > 1) return Math.floor(interval) + " days ago";
                interval = seconds / 3600;
                if (interval > 1) return Math.floor(interval) + " hours ago";
                interval = seconds / 60;
                if (interval > 1) return Math.floor(interval) + " minutes ago";
                return Math.floor(seconds) + " seconds ago";
            }

            function renderNotifications(notifications) {
                if (notifications.length === 0) {
                    list.innerHTML = '<div class="notification-empty">No notifications yet.</div>';
                    markAllBtn.style.display = 'none';
                    return;
                }
                
                let html = '';
                notifications.forEach(n => {
                    const data = n.data;
                    const isUnread = n.read_at === null;
                    html += `
                        <div class="notification-item ${isUnread ? 'unread' : ''}" data-id="${n.id}" data-url="${data.action_url || ''}">
                            <div class="notification-indicator ${data.level || 'normal'}"></div>
                            <div class="notification-content">
                                <div class="notification-title">${data.title || 'Notification'}</div>
                                <div class="notification-message">${data.message || ''}</div>
                                <span class="notification-time">${timeSince(n.created_at)}</span>
                            </div>
                        </div>
                    `;
                });
                list.innerHTML = html;
                
                document.querySelectorAll('.notification-item').forEach(item => {
                    item.addEventListener('click', async function(e) {
                        const id = this.getAttribute('data-id');
                        const url = this.getAttribute('data-url');
                        if (this.classList.contains('unread')) {
                            await fetch(`/api/notifications/${id}/read`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                }
                            });
                        }
                        if (url) {
                            window.location.href = url;
                        }
                    });
                });
            }

            function fetchNotifications() {
                fetch('/api/notifications', {
                    headers: { 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    unreadCount = data.unread_count;
                    if (unreadCount > 0) {
                        badge.innerText = unreadCount > 99 ? '99+' : unreadCount;
                        badge.style.display = 'grid';
                        markAllBtn.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                        markAllBtn.style.display = 'none';
                    }
                    renderNotifications(data.notifications);
                })
                .catch(err => console.error(err));
            }

            markAllBtn.addEventListener('click', () => {
                fetch('/api/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(() => fetchNotifications());
            });

            // Initial fetch
            fetchNotifications();
            // Poll every 60 seconds
            setInterval(fetchNotifications, 60000);
        });
        @endauth

        // === Scroll Fade-In Observer ===
        document.addEventListener('DOMContentLoaded', function() {
            const els = document.querySelectorAll('.fade-in');
            if (!els.length) return;
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
            els.forEach(function(el) { observer.observe(el); });
        });
    </script>
</body>
</html>
