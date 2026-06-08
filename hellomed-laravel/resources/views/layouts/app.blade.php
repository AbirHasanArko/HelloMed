<!doctype html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
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
            --nav-bg: rgba(240, 245, 244, 0.85);
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
            --nav-bg: rgba(13, 17, 23, 0.85);
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
            z-index: 100;
            transition: background 0.3s ease, border-color 0.3s ease;
        }
        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 14px 0;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            letter-spacing: 0.2px;
            text-decoration: none;
        }
        .brand-logo {
            width: 44px;
            height: 44px;
            border-radius: 14px;
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
            font-size: 11px;
            color: var(--muted);
            letter-spacing: 0;
        }
        .nav-links {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
        }
        .nav-links a, .pill, .ghost-button {
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
        .nav-links a:hover, .ghost-button:hover {
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

        /* Theme Toggle */
        .theme-toggle {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--muted);
            display: grid;
            place-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 18px;
            flex-shrink: 0;
        }
        .theme-toggle:hover {
            border-color: var(--primary);
            color: var(--primary);
            box-shadow: var(--shadow-glow);
        }
        .theme-toggle .icon-sun, .theme-toggle .icon-moon { transition: opacity 0.3s ease, transform 0.3s ease; }
        [data-theme="light"] .theme-toggle .icon-moon { display: none; }
        [data-theme="dark"] .theme-toggle .icon-sun { display: none; }

        /* Mobile nav toggle */
        .nav-toggle {
            display: none;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--muted);
            place-items: center;
            cursor: pointer;
            font-size: 20px;
        }

        /* ===== HERO ===== */
        .hero {
            padding: 64px 0 36px;
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 36px;
            align-items: center;
        }
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
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            aspect-ratio: 4/3;
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            display: grid;
            place-items: center;
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
        .hero-visual svg { width: 55%; height: auto; color: white; opacity: 0.9; }

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
        nav[aria-label="Pagination Navigation"] span,
        nav[aria-label="Pagination Navigation"] a {
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid var(--border);
            font-size: 13px;
            background: var(--surface);
            color: var(--text);
            transition: all 0.2s ease;
        }
        nav[aria-label="Pagination Navigation"] a:hover {
            border-color: var(--primary);
            color: var(--primary);
        }
        nav[aria-label="Pagination Navigation"] span[aria-current="page"] span {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: white;
            border-color: transparent;
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
            .hero { grid-template-columns: 1fr; padding: 36px 0 24px; }
            .hero-visual { max-height: 280px; }
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
    </style>
</head>
<body>
    <div class="nav">
        <div class="container nav-inner">
            <a class="brand" href="{{ route('home') }}">
                <span class="brand-logo">
                    <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="9" y="3" width="8" height="20" rx="2" fill="white"/>
                        <rect x="3" y="9" width="20" height="8" rx="2" fill="white"/>
                        <path d="M7 13 L10 10 L12 12.5 L15 8 L18 13" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none" opacity="0.7"/>
                    </svg>
                </span>
                <span class="brand-text">
                    <div style="font-size:16px;">HelloMed</div>
                    <small>Hospital & Care Platform</small>
                </span>
            </a>
            <div class="nav-links">
                <a href="{{ route('ambulance.create') }}" style="background: linear-gradient(135deg, #ef4444, #b91c1c); color: white; border:none; padding: 6px 14px; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4); margin-right: 8px;">
                    🚑 Ambulance
                </a>
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('departments.index') }}">Care</a>
                <a href="{{ route('qna.index') }}">Q&amp;A</a>
                <a href="{{ route('about') }}">About</a>
                <a href="{{ route('medicines.index') }}">Medicine shop</a>
                @auth
                    @if (auth()->user()->role === 'patient')
                        <a href="{{ route('patient.appointments') }}" class="ghost-button">My appointments</a>
                        <a href="{{ route('patient.records') }}" class="ghost-button">My records</a>
                        <a href="{{ route('patient.medicine-orders') }}" class="ghost-button">My medicine orders</a>
                    @endif
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="ghost-button">Backoffice</a>
                    @elseif (auth()->user()->isStaff())
                        <a href="{{ route('staff.dashboard') }}" class="ghost-button">Staff dashboard</a>
                    @elseif (auth()->user()->isDoctor())
                        <a href="{{ route('doctor.dashboard') }}" class="ghost-button">Doctor panel</a>
                    @elseif (auth()->user()->isPharmacist())
                        <a href="{{ route('pharmacist.dashboard') }}" class="ghost-button">Pharmacy</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button class="ghost-button" type="submit">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="ghost-button">Login</a>
                    <a href="{{ route('register') }}" class="button">Register</a>
                @endauth
                <a href="{{ route('contact') }}">Contact</a>
                <button class="theme-toggle" onclick="toggleTheme()" title="Toggle dark/light mode" aria-label="Toggle theme">
                    <span class="icon-sun">☀️</span>
                    <span class="icon-moon">🌙</span>
                </button>
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

        @yield('content')
    </main>

    <footer class="footer">
        <div class="container footer-inner">
            <div>
                <strong style="font-size: 16px;">HelloMed</strong>
                <p class="muted" style="font-size: 13px;">Online and offline hospital services with central appointment booking.</p>
            </div>
            <div class="muted" style="font-size: 13px;">Built with Laravel · © {{ date('Y') }} HelloMed</div>
        </div>
        <div class="dev-footer" style="border-top: 1px solid var(--border); margin-top: 24px; padding-top: 24px;">
            <div class="container dev-footer-inner">
                <span>Developed by <strong>Abir Hasan Arko</strong></span>
                <a href="mailto:abirhasanarko2004@gmail.com" title="Email">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    abirhasanarko2004@gmail.com
                </a>
                <a href="https://github.com/AbirHasanArko" target="_blank" rel="noopener" title="GitHub">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                    GitHub
                </a>
                <a href="https://www.linkedin.com/in/abirhasanarko/" target="_blank" rel="noopener" title="LinkedIn">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    LinkedIn
                </a>
            </div>
        </div>
        @stack('footer-extra')
    </footer>

    <script>
        // === Theme Toggle ===
        function toggleTheme() {
            const html = document.documentElement;
            const current = html.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
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
