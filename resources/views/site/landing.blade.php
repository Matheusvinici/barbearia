<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Barber Control Pro — O Sistema Nº 1 para Barbearias</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/studio-barber.css') }}">
<style>
:root {
    --bg: #0d0d12;
    --bg-elevated: #14141b;
    --card: rgba(20, 20, 27, 0.65);
    --card-solid: #16161e;
    --border: rgba(255, 255, 255, 0.06);
    --border-strong: rgba(255, 255, 255, 0.11);
    --text: #f4f4f6;
    --text-muted: #8a8a94;
    --text-faint: #55555f;
    --accent: #f5b544;
    --accent-hover: #ffc554;
    --accent-soft: #e89538;
    --accent-glow: rgba(245, 181, 68, 0.16);
    --success: #4ade80;
    --success-bg: rgba(74, 222, 128, 0.12);
    --danger: #f87171;
    --danger-bg: rgba(248, 113, 113, 0.12);
    --info: #60a5fa;
    --info-bg: rgba(96, 165, 250, 0.12);
    --purple: #c084fc;
    --purple-bg: rgba(192, 132, 252, 0.12);
    --r-sm: 10px; --r-md: 14px; --r-lg: 18px; --r-xl: 22px; --r-2xl: 28px;
}
[data-bs-theme="light"] {
    --bg: #f6f6f8;
    --bg-elevated: #ffffff;
    --card: rgba(255, 255, 255, 0.75);
    --card-solid: #ffffff;
    --border: rgba(0, 0, 0, 0.07);
    --border-strong: rgba(0, 0, 0, 0.12);
    --text: #14141a;
    --text-muted: #6b6b75;
    --text-faint: #a0a0aa;
    --accent: #c47a06;
    --accent-hover: #d97706;
    --accent-soft: #b45309;
    --accent-glow: rgba(217, 119, 6, 0.12);
    --success: #16a34a;
    --success-bg: rgba(22, 163, 74, 0.1);
    --danger: #dc2626;
    --danger-bg: rgba(220, 38, 38, 0.1);
    --info: #2563eb;
    --info-bg: rgba(37, 99, 235, 0.1);
    --purple: #9333ea;
    --purple-bg: rgba(147, 51, 234, 0.1);
}
* { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; }
body {
    font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
    background: var(--bg);
    color: var(--text);
    -webkit-font-smoothing: antialiased;
    letter-spacing: -0.01em;
    overflow-x: hidden;
    transition: background-color 300ms ease, color 300ms ease;
}
body::before {
    content: '';
    position: fixed; inset: 0;
    background:
      radial-gradient(900px 600px at 85% -10%, var(--accent-glow), transparent 60%),
      radial-gradient(700px 500px at 0% 110%, rgba(96, 165, 250, 0.05), transparent 60%);
    pointer-events: none; z-index: 0;
}
.icon { width: 22px; height: 22px; display: inline-flex; flex-shrink: 0; }
.icon-sm { width: 18px; height: 18px; }
.icon-xs { width: 15px; height: 15px; }
.navbar-c {
    position: fixed; top: 0; left: 0; right: 0;
    padding: 18px 0;
    z-index: 1000;
    transition: all 300ms ease;
    background: transparent;
}
.navbar-c.scrolled {
    background: rgba(13, 13, 18, 0.8);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid var(--border);
    padding: 12px 0;
}
[data-bs-theme="light"] .navbar-c.scrolled {
    background: rgba(255, 255, 255, 0.85);
}
.nav-container {
    max-width: 1200px; margin: 0 auto;
    padding: 0 24px;
    display: flex; align-items: center; justify-content: space-between;
}
.brand-logo {
    display: flex; align-items: center; gap: 10px;
    text-decoration: none; color: var(--text);
    font-weight: 800; font-size: 18px; letter-spacing: -0.02em;
}
.brand-mark {
    width: 38px; height: 38px; border-radius: 11px;
    background: linear-gradient(135deg, var(--accent), var(--accent-soft));
    display: grid; place-items: center; color: #0d0d12;
    box-shadow: 0 8px 22px -8px var(--accent-glow);
}
.brand-logo span { color: var(--text-muted); font-weight: 500; }
.nav-links { display: flex; align-items: center; gap: 32px; }
.nav-link-c {
    color: var(--text-muted); text-decoration: none;
    font-size: 14.5px; font-weight: 500;
    transition: color 150ms;
}
.nav-link-c:hover { color: var(--text); }
.nav-actions { display: flex; align-items: center; gap: 12px; }
.btn-ghost-c {
    height: 42px; padding: 0 18px;
    border-radius: 11px;
    background: transparent; color: var(--text);
    border: 1px solid var(--border-strong);
    font-weight: 600; font-size: 14px;
    display: inline-flex; align-items: center; gap: 8px;
    cursor: pointer; transition: all 180ms;
    text-decoration: none;
}
.btn-ghost-c:hover { border-color: var(--accent); background: var(--accent-glow); }
.btn-primary-c {
    height: 42px; padding: 0 20px;
    border-radius: 11px;
    background: var(--accent); color: #0d0d12;
    border: none; font-weight: 700; font-size: 14px;
    display: inline-flex; align-items: center; gap: 8px;
    cursor: pointer; transition: all 180ms;
    box-shadow: 0 8px 22px -8px var(--accent-glow);
    text-decoration: none;
}
.btn-primary-c:hover { background: var(--accent-hover); transform: translateY(-1px); }
.theme-toggle-nav {
    width: 42px; height: 42px; border-radius: 11px;
    background: transparent; color: var(--text-muted);
    border: 1px solid var(--border-strong);
    display: grid; place-items: center;
    cursor: pointer; transition: all 180ms;
}
.theme-toggle-nav:hover { color: var(--accent); border-color: var(--accent); background: var(--accent-glow); }
.container-c { max-width: 1200px; margin: 0 auto; padding: 0 24px; position: relative; z-index: 1; }
.hero { padding: 160px 0 80px; position: relative; }
.hero-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; }
.hero-badge {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 6px 14px; border-radius: 999px;
    background: var(--accent-glow); color: var(--accent);
    border: 1px solid rgba(245, 181, 68, 0.2);
    font-size: 12.5px; font-weight: 700;
    margin-bottom: 24px;
}
.hero-title {
    font-size: 56px; font-weight: 800;
    letter-spacing: -0.04em; line-height: 1.05;
    margin-bottom: 24px;
}
.hero-title span { color: var(--accent); }
.hero-subtitle {
    font-size: 17px; color: var(--text-muted);
    line-height: 1.6; margin-bottom: 36px;
    max-width: 500px;
}
.hero-cta { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 32px; }
.hero-cta .btn-primary-c { height: 52px; padding: 0 28px; font-size: 15px; }
.hero-cta .btn-ghost-c { height: 52px; padding: 0 24px; font-size: 15px; }
.hero-social { display: flex; align-items: center; gap: 16px; font-size: 13px; color: var(--text-muted); }
.avatars { display: flex; }
.avatar-sm { width: 32px; height: 32px; border-radius: 50%; border: 2px solid var(--bg); margin-left: -10px; background-size: cover; background-position: center; }
.avatar-sm:first-child { margin-left: 0; }
.stars { display: flex; gap: 2px; color: var(--accent); }
.mockup-wrap { position: relative; perspective: 1000px; }
.mockup {
    background: var(--bg-elevated);
    border: 1px solid var(--border-strong);
    border-radius: var(--r-xl);
    padding: 16px;
    box-shadow: 0 30px 60px -15px rgba(0,0,0,0.5), 0 0 0 1px var(--border);
    transform: rotateY(-5deg) rotateX(2deg);
    transition: transform 400ms;
}
.mockup:hover { transform: rotateY(0) rotateX(0); }
.mockup-header { display: flex; gap: 6px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border); }
.mockup-dot { width: 10px; height: 10px; border-radius: 50%; background: var(--border-strong); }
.mockup-dot:nth-child(1) { background: #f87171; }
.mockup-dot:nth-child(2) { background: #fbbf24; }
.mockup-dot:nth-child(3) { background: #4ade80; }
.mockup-body { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.mockup-card { background: var(--card-solid); border: 1px solid var(--border); border-radius: var(--r-md); padding: 14px; }
.mockup-card-title { font-size: 10px; color: var(--text-faint); font-weight: 700; text-transform: uppercase; margin-bottom: 8px; }
.mockup-card-val { font-size: 20px; font-weight: 800; letter-spacing: -0.02em; }
.mockup-card-val.amber { color: var(--accent); }
.mockup-card-val.green { color: var(--success); }
.mockup-chart { grid-column: 1 / -1; height: 120px; position: relative; padding: 14px; }
.mockup-chart svg { width: 100%; height: 100%; }
.mockup-list { grid-column: 1 / -1; display: flex; flex-direction: column; gap: 8px; }
.mockup-list-item { display: flex; align-items: center; gap: 10px; padding: 10px; background: var(--card-solid); border: 1px solid var(--border); border-radius: 10px; }
.mockup-av { width: 28px; height: 28px; border-radius: 50%; background: var(--accent); flex-shrink: 0; }
.mockup-line { flex: 1; height: 8px; background: var(--border); border-radius: 4px; }
.mockup-line.half { max-width: 60%; }
.mockup-badge { width: 40px; height: 20px; background: var(--success-bg); border-radius: 6px; }
.logos-strip { padding: 40px 0; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); background: var(--bg-elevated); }
.logos-grid { display: flex; justify-content: space-around; align-items: center; flex-wrap: wrap; gap: 24px; }
.logo-item { font-size: 18px; font-weight: 800; color: var(--text-faint); letter-spacing: -0.02em; opacity: 0.6; transition: opacity 150ms; }
.logo-item:hover { opacity: 1; }
section { padding: 100px 0; position: relative; z-index: 1; }
.section-header { text-align: center; max-width: 700px; margin: 0 auto 60px; }
.section-tag { display: inline-block; font-size: 12.5px; font-weight: 700; color: var(--accent); background: var(--accent-glow); padding: 5px 12px; border-radius: 999px; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 16px; }
.section-title { font-size: 40px; font-weight: 800; letter-spacing: -0.035em; line-height: 1.15; margin-bottom: 16px; }
.section-subtitle { font-size: 16px; color: var(--text-muted); line-height: 1.6; }
.bento-grid { display: grid; grid-template-columns: repeat(3, 1fr); grid-auto-rows: minmax(200px, auto); gap: 20px; }
.bento-card { background: var(--card); backdrop-filter: blur(20px); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 28px; position: relative; overflow: hidden; transition: all 250ms; }
.bento-card:hover { border-color: var(--border-strong); transform: translateY(-4px); }
.bento-card.span-2 { grid-column: span 2; }
.bento-card.span-2-rows { grid-row: span 2; }
.bento-ic { width: 48px; height: 48px; border-radius: 13px; display: grid; place-items: center; margin-bottom: 20px; background: var(--accent-glow); color: var(--accent); }
.bento-card.green .bento-ic { background: var(--success-bg); color: var(--success); }
.bento-card.blue .bento-ic { background: var(--info-bg); color: var(--info); }
.bento-card.purple .bento-ic { background: var(--purple-bg); color: var(--purple); }
.bento-title { font-size: 19px; font-weight: 700; margin-bottom: 8px; letter-spacing: -0.02em; }
.bento-desc { font-size: 14px; color: var(--text-muted); line-height: 1.5; }
.bento-visual { position: absolute; bottom: 20px; right: 20px; width: 140px; height: 100px; opacity: 0.8; }
.steps-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.step-card { text-align: center; padding: 32px; background: var(--card); border: 1px solid var(--border); border-radius: var(--r-lg); position: relative; }
.step-num { width: 48px; height: 48px; border-radius: 14px; background: linear-gradient(135deg, var(--accent), var(--accent-soft)); color: #0d0d12; font-weight: 800; font-size: 20px; display: grid; place-items: center; margin: 0 auto 20px; box-shadow: 0 8px 20px -6px var(--accent-glow); }
.step-title { font-size: 18px; font-weight: 700; margin-bottom: 10px; }
.step-desc { font-size: 14px; color: var(--text-muted); line-height: 1.5; }
.testimonial-card { background: var(--card); backdrop-filter: blur(20px); border: 1px solid var(--border); border-radius: var(--r-2xl); padding: 48px; max-width: 900px; margin: 0 auto; text-align: center; position: relative; overflow: hidden; }
.testimonial-card::before { content: '"'; position: absolute; top: -20px; left: 20px; font-size: 180px; color: var(--accent); opacity: 0.1; font-family: serif; line-height: 1; }
.testimonial-text { font-size: 24px; font-weight: 600; line-height: 1.4; letter-spacing: -0.02em; margin-bottom: 32px; }
.testimonial-author { display: flex; align-items: center; justify-content: center; gap: 14px; }
.author-av { width: 52px; height: 52px; border-radius: 50%; background: linear-gradient(135deg, #f87171, #f5b544); display: grid; place-items: center; font-weight: 700; color: white; }
.author-info { text-align: left; }
.author-name { font-size: 15px; font-weight: 700; }
.author-role { font-size: 13px; color: var(--text-muted); }
.pricing-toggle { display: flex; justify-content: center; margin-bottom: 40px; }
.toggle-wrap { display: flex; align-items: center; gap: 12px; background: var(--card-solid); border: 1px solid var(--border-strong); padding: 6px; border-radius: 12px; }
.toggle-btn { padding: 10px 20px; border-radius: 8px; background: transparent; border: none; color: var(--text-muted); font-weight: 600; font-size: 14px; cursor: pointer; font-family: inherit; transition: all 180ms; }
.toggle-btn.active { background: var(--accent); color: #0d0d12; }
.save-badge { font-size: 11px; font-weight: 700; color: var(--success); background: var(--success-bg); padding: 3px 8px; border-radius: 6px; margin-left: 8px; }
.pricing-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; max-width: 800px; margin: 0 auto; }
.price-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--r-xl); padding: 36px; transition: all 250ms; position: relative; }
.price-card:hover { border-color: var(--border-strong); transform: translateY(-4px); }
.price-card.featured { border-color: var(--accent); box-shadow: 0 20px 50px -15px var(--accent-glow); }
.price-tag-popular { position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: var(--accent); color: #0d0d12; padding: 5px 14px; border-radius: 999px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
.price-name { font-size: 18px; font-weight: 700; margin-bottom: 8px; }
.price-desc { font-size: 13.5px; color: var(--text-muted); margin-bottom: 24px; }
.price-val { display: flex; align-items: baseline; gap: 4px; margin-bottom: 6px; }
.price-cur { font-size: 20px; font-weight: 600; color: var(--text-muted); }
.price-num { font-size: 44px; font-weight: 800; letter-spacing: -0.03em; }
.price-period { font-size: 14px; color: var(--text-muted); font-weight: 500; }
.price-sub { font-size: 12.5px; color: var(--text-faint); margin-bottom: 28px; }
.price-features { list-style: none; margin-bottom: 28px; }
.price-features li { display: flex; align-items: center; gap: 10px; font-size: 14px; color: var(--text-muted); padding: 8px 0; border-bottom: 1px solid var(--border); }
.price-features li:last-child { border-bottom: none; }
.price-features li svg { color: var(--success); flex-shrink: 0; }
.price-cta { width: 100%; height: 48px; border-radius: 12px; border: 1px solid var(--border-strong); background: transparent; color: var(--text); font-weight: 700; font-size: 15px; cursor: pointer; transition: all 180ms; font-family: inherit; display: flex; align-items: center; justify-content: center; gap: 8px; }
.price-cta:hover { border-color: var(--accent); background: var(--accent-glow); }
.price-card.featured .price-cta { background: var(--accent); color: #0d0d12; border-color: var(--accent); }
.price-card.featured .price-cta:hover { background: var(--accent-hover); }
.faq-container { max-width: 760px; margin: 0 auto; }
.faq-item { border-bottom: 1px solid var(--border); }
.faq-header { display: flex; align-items: center; justify-content: space-between; padding: 22px 0; cursor: pointer; transition: color 150ms; }
.faq-header:hover .faq-q { color: var(--accent); }
.faq-q { font-size: 17px; font-weight: 600; flex: 1; transition: color 150ms; }
.faq-icon { width: 32px; height: 32px; border-radius: 8px; background: var(--border); color: var(--text-muted); display: grid; place-items: center; transition: all 250ms; }
.faq-item.open .faq-icon { background: var(--accent-glow); color: var(--accent); transform: rotate(180deg); }
.faq-a { max-height: 0; overflow: hidden; transition: max-height 300ms ease, padding 300ms ease; color: var(--text-muted); font-size: 15px; line-height: 1.6; }
.faq-item.open .faq-a { max-height: 200px; padding-bottom: 22px; }
.cta-final { background: linear-gradient(135deg, var(--bg-elevated), var(--bg)); border: 1px solid var(--border-strong); border-radius: var(--r-2xl); padding: 60px; text-align: center; position: relative; overflow: hidden; }
.cta-final::after { content: ''; position: absolute; top: -50%; left: 50%; transform: translateX(-50%); width: 600px; height: 600px; border-radius: 50%; background: radial-gradient(circle, var(--accent-glow), transparent 60%); pointer-events: none; }
.cta-title { font-size: 36px; font-weight: 800; letter-spacing: -0.03em; margin-bottom: 12px; position: relative; z-index: 1; }
.cta-sub { font-size: 16px; color: var(--text-muted); margin-bottom: 32px; position: relative; z-index: 1; }
.cta-btn { height: 56px; padding: 0 36px; font-size: 16px; position: relative; z-index: 1; }
.newsletter-form { display: flex; gap: 12px; max-width: 440px; margin: 24px auto 0; position: relative; z-index: 1; }
.newsletter-form .form-input { flex: 1; height: 52px; padding: 0 18px; border-radius: 12px; border: 1px solid var(--border-strong); background: var(--card-solid); color: var(--text); font-family: inherit; font-size: 15px; }
.newsletter-form .form-input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 4px var(--accent-glow); }
.newsletter-form .btn-primary-c { height: 52px; padding: 0 24px; font-size: 15px; }
footer { border-top: 1px solid var(--border); padding: 60px 0 30px; margin-top: 80px; }
.footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 40px; margin-bottom: 40px; }
.footer-brand p { font-size: 14px; color: var(--text-muted); margin-top: 16px; max-width: 300px; line-height: 1.6; }
.footer-col h4 { font-size: 14px; font-weight: 700; margin-bottom: 16px; }
.footer-col a { display: block; font-size: 14px; color: var(--text-muted); text-decoration: none; padding: 6px 0; transition: color 150ms; }
.footer-col a:hover { color: var(--accent); }
.footer-bottom { border-top: 1px solid var(--border); padding-top: 24px; display: flex; justify-content: space-between; align-items: center; font-size: 13px; color: var(--text-faint); flex-wrap: wrap; gap: 16px; }
.barbearia-card-c {
    background: var(--card); backdrop-filter: blur(20px);
    border: 1px solid var(--border); border-radius: var(--r-lg);
    padding: 28px; text-align: center;
    transition: all 250ms;
}
.barbearia-card-c:hover { border-color: var(--border-strong); transform: translateY(-4px); }
.barbearia-card-c img { max-width: 100%; height: 60px; object-fit: contain; margin-bottom: 16px; }
.barbearia-card-c h5 { font-size: 17px; font-weight: 700; margin-bottom: 6px; }
.barbearia-card-c p { font-size: 13px; color: var(--text-muted); margin-bottom: 16px; }
.barbearia-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
@keyframes pulse-dot { 0% { box-shadow: 0 0 0 0 currentColor; } 70% { box-shadow: 0 0 0 5px transparent; } 100% { box-shadow: 0 0 0 0 transparent; } }
.reveal { opacity: 0; transform: translateY(30px); transition: all 600ms cubic-bezier(0.16, 1, 0.3, 1); }
.reveal.visible { opacity: 1; transform: translateY(0); }
@media (max-width: 992px) {
    .hero-grid { grid-template-columns: 1fr; gap: 40px; }
    .hero-title { font-size: 40px; }
    .mockup-wrap { max-width: 500px; margin: 0 auto; }
    .bento-grid { grid-template-columns: 1fr; }
    .bento-card.span-2 { grid-column: span 1; }
    .steps-grid { grid-template-columns: 1fr; }
    .pricing-grid { grid-template-columns: 1fr; }
    .footer-grid { grid-template-columns: 1fr 1fr; }
    .nav-links { display: none; }
    .barbearia-grid { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 576px) {
    .hero { padding: 120px 0 60px; }
    .hero-title { font-size: 32px; }
    .section-title { font-size: 28px; }
    .testimonial-text { font-size: 18px; }
    .cta-final { padding: 40px 24px; }
    .cta-title { font-size: 24px; }
    .footer-grid { grid-template-columns: 1fr; }
    .nav-actions .btn-ghost-c { display: none; }
    .barbearia-grid { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <defs>
    <symbol id="i-scissor" viewBox="0 0 24 24" fill="none"><circle cx="6" cy="6" r="3" stroke="currentColor" stroke-width="1.6"/><circle cx="6" cy="18" r="3" stroke="currentColor" stroke-width="1.6"/><path d="M8.12 7.88L20 18M8.12 16.12L20 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-calendar" viewBox="0 0 24 24" fill="none"><path d="M8 2v3M16 2v3M3.5 9.09h17M22 19c0 .75-.21 1.46-.58 2.06a3.42 3.42 0 0 1-2.91 1.64H5.49C3.26 22.7 1.7 21.07 1.7 19V8.06c0-2.13 1.56-3.79 3.79-3.79h13.02c2.13 0 3.79 1.66 3.79 3.79V16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-wallet" viewBox="0 0 24 24" fill="none"><path d="M22 12v3.5c0 1.5-.5 2.5-2 2.5h-2v-3.25c0-.41-.34-.75-.75-.75h-3.5c-.41 0-.75.34-.75.75V18H4c-1.5 0-2-1-2-2.5V8.5C2 7 2.5 6 4 6h16c1.5 0 2 1 2 2.5V12z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M2 9h4M2 13h4M14 9.5V7c0-1.5 1-2.5 2.5-2.5h2.05c.31 0 .57.25.62.55.06.4.07.81.07 1.2 0 1.55-.43 2.95-1.13 4.13-.21.35-.59.55-.99.55H14z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-chart" viewBox="0 0 24 24" fill="none"><path d="M3 22h18M5.6 18V9M10.6 18V5M15.6 18v-7M20.6 18V8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-user-tag" viewBox="0 0 24 24" fill="none"><path d="M13 20.5H6.5c-1.5 0-2.5-1-2.5-2.5 0-3.5 3-5.5 6-5.5.83 0 1.63.13 2.36.37" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><circle cx="10" cy="6.5" r="3.5" stroke="currentColor" stroke-width="1.6"/><path d="M17.13 17.92l2.32 2.32c.21.21.55.21.76 0l1.55-1.55c.21-.21.21-.55 0-.76l-2.32-2.32a.54.54 0 0 1-.16-.38v-2.18c0-.29-.24-.53-.53-.53h-2.18a.54.54 0 0 1-.38-.16L14 9.95c-.18-.18-.49-.18-.67 0l-1.55 1.55c-.18.18-.18.49 0 .67l1.65 1.65c.1.1.16.24.16.38v2.18c0 .29.24.53.53.53h2.18c.14 0 .28.06.38.16z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-check" viewBox="0 0 24 24" fill="none"><path d="M5 12.5l4.5 4.5L19 7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-arrow-right" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 18l6-6-6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-chevron-down" viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-zap" viewBox="0 0 24 24" fill="none"><path d="M13 2L4 14h7l-1 8 9-12h-7l1-8z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-smartphone" viewBox="0 0 24 24" fill="none"><rect x="7" y="2" width="10" height="20" rx="3" stroke="currentColor" stroke-width="1.6"/><path d="M11 18h2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
    <symbol id="i-star" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3 7h7l-5.5 4.5L18 21l-6-4-6 4 1.5-7.5L2 9h7l3-7z"/></symbol>
    <symbol id="i-whatsapp" viewBox="0 0 24 24" fill="none"><path d="M3 21l1.9-5.7A8.5 8.5 0 1 1 12 20.5a8.4 8.4 0 0 1-4.5-1.3L3 21z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M9 9.5c0 3 2.5 5.5 5.5 5.5.6 0 1-.5 1-1l-.2-1.2-1.8.4-.8-.8c-.5-.5-1-1.3-1.3-1.8l.4-1.8L11 8.5c0-.6-.5-1-1-1" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-instagram" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="5" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.6"/><circle cx="17" cy="7" r="1" fill="currentColor"/></symbol>
    <symbol id="i-sun" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.6"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
    <symbol id="i-moon" viewBox="0 0 24 24" fill="none"><path d="M3.27 12.31c.43 4.6 4.34 8.21 8.95 8.41 3.16.13 5.97-1.18 7.86-3.34.62-.71.27-1.32-.69-1.21-.55.06-1.11.04-1.69-.06-3.58-.6-6.32-3.45-6.65-7.06-.12-1.34.07-2.62.5-3.79.34-.92-.31-1.39-1.22-1.04-4.21 1.61-7.04 5.71-6.69 10.09z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-mail" viewBox="0 0 24 24" fill="none"><rect x="2" y="4.5" width="20" height="15" rx="3" stroke="currentColor" stroke-width="1.6"/><path d="M3 6l9 7 9-7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-building" viewBox="0 0 24 24" fill="none"><rect x="2" y="3" width="20" height="19" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M9 7h2M9 11h2M9 15h2M13 7h2M13 11h2M13 15h2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
  </defs>
</svg>

<nav class="navbar-c" id="navbar">
  <div class="nav-container">
    <a href="#" class="brand-logo">
      <div class="brand-mark"><svg class="icon" style="width:20px;height:20px"><use href="#i-scissor"/></svg></div>
      Barber Control <span>Pro</span>
    </a>
    <div class="nav-links">
      <a href="#features" class="nav-link-c">Funcionalidades</a>
      <a href="#how" class="nav-link-c">Como Funciona</a>
      <a href="#pricing" class="nav-link-c">Planos</a>
      <a href="#barbearias" class="nav-link-c">Barbearias</a>
      <a href="#faq" class="nav-link-c">FAQ</a>
    </div>
    <div class="nav-actions">
      <button class="theme-toggle-nav" id="themeToggle" title="Alternar tema">
        <svg class="icon"><use href="#i-sun"/></svg>
      </button>
      <a href="#cta" class="btn-primary-c">Teste Grátis</a>
    </div>
  </div>
</nav>

<section class="hero">
  <div class="container-c">
    <div class="hero-grid">
      <div class="hero-content">
        <span class="hero-badge">
          <svg class="icon icon-xs"><use href="#i-zap"/></svg>
          A plataforma completa para sua barbearia
        </span>
        <h1 class="hero-title">A plataforma completa para <span>dominar sua barbearia</span></h1>
        <p class="hero-subtitle">Automatize agendamentos, controle o financeiro, gerencie sua equipe e fidelize clientes. Tudo em um só lugar, com a identidade da sua marca.</p>
        <div class="hero-cta">
          <a href="#pricing" class="btn-primary-c">
            Começar 14 dias grátis
            <svg class="icon icon-sm"><use href="#i-arrow-right"/></svg>
          </a>
          <a href="#how" class="btn-ghost-c">Ver como funciona</a>
        </div>
        <div class="hero-social">
          <div class="avatars">
            <div class="avatar-sm" style="background:linear-gradient(135deg,#f5b544,#e89538)"></div>
            <div class="avatar-sm" style="background:linear-gradient(135deg,#60a5fa,#3b82f6)"></div>
            <div class="avatar-sm" style="background:linear-gradient(135deg,#4ade80,#22c55e)"></div>
            <div class="avatar-sm" style="background:linear-gradient(135deg,#f472b6,#ec4899)"></div>
          </div>
          <div>
            <div class="stars">
              <svg class="icon-sm"><use href="#i-star"/></svg>
              <svg class="icon-sm"><use href="#i-star"/></svg>
              <svg class="icon-sm"><use href="#i-star"/></svg>
              <svg class="icon-sm"><use href="#i-star"/></svg>
              <svg class="icon-sm"><use href="#i-star"/></svg>
            </div>
            <span>+1.200 barbearias já usam</span>
          </div>
        </div>
      </div>
      <div class="mockup-wrap">
        <div class="mockup">
          <div class="mockup-header">
            <div class="mockup-dot"></div>
            <div class="mockup-dot"></div>
            <div class="mockup-dot"></div>
          </div>
          <div class="mockup-body">
            <div class="mockup-card">
              <div class="mockup-card-title">Faturamento Hoje</div>
              <div class="mockup-card-val amber">R$ 1.240</div>
            </div>
            <div class="mockup-card">
              <div class="mockup-card-title">Agendamentos</div>
              <div class="mockup-card-val">32</div>
            </div>
            <div class="mockup-card mockup-chart">
              <div class="mockup-card-title">Receita Semanal</div>
              <svg viewBox="0 0 300 80" preserveAspectRatio="none">
                <defs><linearGradient id="m-grad" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="var(--accent)" stop-opacity="0.5"/><stop offset="100%" stop-color="var(--accent)" stop-opacity="0"/></linearGradient></defs>
                <path d="M0,60 L50,50 L100,55 L150,30 L200,35 L250,15 L300,20 L300,80 L0,80 Z" fill="url(#m-grad)"/>
                <path d="M0,60 L50,50 L100,55 L150,30 L200,35 L250,15 L300,20" fill="none" stroke="var(--accent)" stroke-width="2.5" stroke-linecap="round"/>
              </svg>
            </div>
            <div class="mockup-list">
              <div class="mockup-list-item">
                <div class="mockup-av" style="background:linear-gradient(135deg,#f5b544,#e89538)"></div>
                <div class="mockup-line"></div>
                <div class="mockup-line half"></div>
                <div class="mockup-badge"></div>
              </div>
              <div class="mockup-list-item">
                <div class="mockup-av" style="background:linear-gradient(135deg,#60a5fa,#3b82f6)"></div>
                <div class="mockup-line"></div>
                <div class="mockup-line half"></div>
                <div class="mockup-badge" style="background:var(--info-bg)"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="logos-strip">
  <div class="container-c">
    <div class="logos-grid">
      <div class="logo-item">BARBEARIA VIP</div>
      <div class="logo-item">CORTE & CIA</div>
      <div class="logo-item">STUDIO HAIR</div>
      <div class="logo-item">NAVALHA DE OURO</div>
      <div class="logo-item">CLUBE DO CORTE</div>
    </div>
  </div>
</div>

<section id="features">
  <div class="container-c">
    <div class="section-header reveal">
      <span class="section-tag">Funcionalidades</span>
      <h2 class="section-title">Tudo que você precisa para crescer</h2>
      <p class="section-subtitle">Esqueça as planilhas e cadernos. Automatize sua operação e tenha tempo livre para focar no que realmente importa: seus clientes.</p>
    </div>
    <div class="bento-grid">
      <div class="bento-card span-2 reveal">
        <div class="bento-ic"><svg class="icon"><use href="#i-calendar"/></svg></div>
        <h3 class="bento-title">Agenda Inteligente 24/7</h3>
        <p class="bento-desc">Seus clientes marcam, remarcam e cancelam sozinhos pelo app ou site. O sistema envia lembretes automáticos via WhatsApp, reduzindo faltas em até 70%.</p>
        <div class="bento-visual">
          <svg viewBox="0 0 140 100">
            <rect x="10" y="10" width="120" height="80" rx="8" fill="none" stroke="var(--border-strong)"/>
            <rect x="20" y="20" width="40" height="10" rx="3" fill="var(--accent)" opacity="0.5"/>
            <rect x="20" y="40" width="100" height="6" rx="3" fill="var(--border)"/>
            <rect x="20" y="52" width="80" height="6" rx="3" fill="var(--border)"/>
            <rect x="20" y="64" width="90" height="6" rx="3" fill="var(--border)"/>
            <circle cx="110" cy="70" r="15" fill="var(--success-bg)"/>
            <path d="M105 70 L109 74 L115 66" stroke="var(--success)" stroke-width="2" fill="none" stroke-linecap="round"/>
          </svg>
        </div>
      </div>
      <div class="bento-card reveal">
        <div class="bento-ic"><svg class="icon"><use href="#i-wallet"/></svg></div>
        <h3 class="bento-title">Financeiro Automático</h3>
        <p class="bento-desc">Controle caixa, comissões e despesas. Relatórios prontos para o contador.</p>
      </div>
      <div class="bento-card green reveal">
        <div class="bento-ic"><svg class="icon"><use href="#i-whatsapp"/></svg></div>
        <h3 class="bento-title">WhatsApp Integrado</h3>
        <p class="bento-desc">Confirmações e lembretes automáticos. Pare de perder tempo com mensagens manuais.</p>
      </div>
      <div class="bento-card blue span-2 reveal">
        <div class="bento-ic"><svg class="icon"><use href="#i-smartphone"/></svg></div>
        <h3 class="bento-title">App Próprio com sua Marca</h3>
        <p class="bento-desc">Ofereça aos seus clientes um app exclusivo da sua barbearia. Fidelização extrema com programa de pontos, histórico de cortes e pagamentos via Pix.</p>
        <div class="bento-visual">
          <svg viewBox="0 0 140 100">
            <rect x="45" y="10" width="50" height="80" rx="10" fill="none" stroke="var(--info-bg)"/>
            <rect x="50" y="18" width="40" height="60" rx="4" fill="var(--info-bg)"/>
            <circle cx="70" cy="85" r="2" fill="var(--info-bg)"/>
            <rect x="55" y="24" width="30" height="4" rx="2" fill="var(--info)" opacity="0.5"/>
            <rect x="55" y="32" width="20" height="4" rx="2" fill="var(--info)" opacity="0.3"/>
          </svg>
        </div>
      </div>
      <div class="bento-card purple reveal">
        <div class="bento-ic"><svg class="icon"><use href="#i-user-tag"/></svg></div>
        <h3 class="bento-title">Gestão de Equipe</h3>
        <p class="bento-desc">Cada barbeiro tem seu painel. Controle comissões e horários com facilidade.</p>
      </div>
      <div class="bento-card reveal">
        <div class="bento-ic"><svg class="icon"><use href="#i-chart"/></svg></div>
        <h3 class="bento-title">Relatórios em Tempo Real</h3>
        <p class="bento-desc">Saiba quanto faturou hoje, qual serviço vende mais e quem é seu melhor barbeiro.</p>
      </div>
    </div>
  </div>
</section>

<section id="how" style="background: var(--bg-elevated); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
  <div class="container-c">
    <div class="section-header reveal">
      <span class="section-tag">Como Funciona</span>
      <h2 class="section-title">Comece em menos de 10 minutos</h2>
      <p class="section-subtitle">Sem instalações complicadas. Sem taxas de setup. Configure uma vez e veja sua barbearia decolar.</p>
    </div>
    <div class="steps-grid">
      <div class="step-card reveal">
        <div class="step-num">1</div>
        <h3 class="step-title">Crie sua conta</h3>
        <p class="step-desc">Faça o cadastro gratuito. Sem cartão de crédito necessário. Em 2 minutos seu painel estará pronto.</p>
      </div>
      <div class="step-card reveal">
        <div class="step-num">2</div>
        <h3 class="step-title">Configure seus serviços</h3>
        <p class="step-desc">Adicione seus barbeiros, preços e horários de funcionamento. Personalize com a identidade da sua barbearia.</p>
      </div>
      <div class="step-card reveal">
        <div class="step-num">3</div>
        <h3 class="step-title">Comece a agendar</h3>
        <p class="step-desc">Divulgue seu link de agendamento. Clientes marcam sozinhos e você só acompanha o caixa crescer.</p>
      </div>
    </div>
  </div>
</section>

<section>
  <div class="container-c">
    <div class="testimonial-card reveal">
      <p class="testimonial-text">"Reduzi as faltas em 80% no primeiro mês com os lembretes automáticos. O controle financeiro me poupa 5 horas por semana. Hoje não consigo imaginar a barbearia sem o Barber Control Pro."</p>
      <div class="testimonial-author">
        <div class="author-av">JS</div>
        <div class="author-info">
          <div class="author-name">João Souza</div>
          <div class="author-role">Dono da Barbearia Navalha de Ouro</div>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="barbearias">
  <div class="container-c">
    <div class="section-header reveal">
      <span class="section-tag">Barbearias</span>
      <h2 class="section-title">Barbearias Cadastradas</h2>
      <p class="section-subtitle">Conheça as barbearias que já confiam no Barber Control Pro.</p>
    </div>
    @if($barbearias->count())
    <div class="barbearia-grid">
      @foreach($barbearias as $b)
      <div class="barbearia-card-c reveal">
        <img src="{{ $b->logo_url }}" alt="{{ $b->nome }}">
        <h5>{{ $b->nome }}</h5>
        <p>{{ $b->cidade }}{{ $b->bairro ? ' - '.$b->bairro : '' }}</p>
        <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;">
          <a href="{{ route('tenant.site.agendar', $b->slug) }}" class="btn-primary-c" style="height:36px;padding:0 14px;font-size:12.5px;">Agendar</a>
          <a href="{{ route('tenant.login', $b->slug) }}" class="btn-ghost-c" style="height:36px;padding:0 14px;font-size:12.5px;">Admin</a>
        </div>
      </div>
      @endforeach
    </div>
    @else
    <p style="text-align:center;color:var(--text-muted);font-size:15px;">Nenhuma barbearia cadastrada ainda.</p>
    @endif
  </div>
</section>

<section id="pricing" style="background: var(--bg-elevated); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
  <div class="container-c">
    <div class="section-header reveal">
      <span class="section-tag">Planos</span>
      <h2 class="section-title">Preços que cabem no seu bolso</h2>
      <p class="section-subtitle">Sem taxas escondidas. Cancele quando quiser. Comece com 14 dias grátis.</p>
    </div>
    <div class="pricing-toggle reveal">
      <div class="toggle-wrap">
        <button class="toggle-btn active" data-period="monthly">Mensal</button>
        <button class="toggle-btn" data-period="yearly">Anual <span class="save-badge">-20%</span></button>
      </div>
    </div>
    <div class="pricing-grid">
      <div class="price-card reveal">
        <div class="price-name">Essencial</div>
        <div class="price-desc">Para barbearias independentes começando a organizar a casa.</div>
        <div class="price-val">
          <span class="price-cur">R$</span>
          <span class="price-num" data-monthly="79" data-yearly="63">79</span>
          <span class="price-period">/mês</span>
        </div>
        <div class="price-sub">Cobrança mensal recorrente</div>
        <ul class="price-features">
          <li><svg class="icon-sm"><use href="#i-check"/></svg> Até 2 barbeiros</li>
          <li><svg class="icon-sm"><use href="#i-check"/></svg> Agenda online 24/7</li>
          <li><svg class="icon-sm"><use href="#i-check"/></svg> Lembretes WhatsApp</li>
          <li><svg class="icon-sm"><use href="#i-check"/></svg> Controle financeiro básico</li>
          <li><svg class="icon-sm"><use href="#i-check"/></svg> Suporte por e-mail</li>
        </ul>
        <button class="price-cta">Começar Teste Grátis</button>
      </div>
      <div class="price-card featured reveal">
        <div class="price-tag-popular">Mais Popular</div>
        <div class="price-name">Pro</div>
        <div class="price-desc">Para barbearias em crescimento que querem dominar o mercado.</div>
        <div class="price-val">
          <span class="price-cur">R$</span>
          <span class="price-num" data-monthly="149" data-yearly="119">149</span>
          <span class="price-period">/mês</span>
        </div>
        <div class="price-sub">Cobrança mensal recorrente</div>
        <ul class="price-features">
          <li><svg class="icon-sm"><use href="#i-check"/></svg> Barbeiros ilimitados</li>
          <li><svg class="icon-sm"><use href="#i-check"/></svg> App próprio com sua marca</li>
          <li><svg class="icon-sm"><use href="#i-check"/></svg> Integração Mercado Pago (Pix)</li>
          <li><svg class="icon-sm"><use href="#i-check"/></svg> Relatórios avançados</li>
          <li><svg class="icon-sm"><use href="#i-check"/></svg> Programa de fidelidade</li>
          <li><svg class="icon-sm"><use href="#i-check"/></svg> Suporte prioritário (WhatsApp)</li>
        </ul>
        <button class="price-cta">Começar Teste Grátis</button>
      </div>
    </div>
  </div>
</section>

<section id="faq">
  <div class="container-c">
    <div class="section-header reveal">
      <span class="section-tag">Dúvidas</span>
      <h2 class="section-title">Perguntas Frequentes</h2>
      <p class="section-subtitle">Tudo o que você precisa saber antes de começar.</p>
    </div>
    <div class="faq-container reveal">
      <div class="faq-item open">
        <div class="faq-header"><span class="faq-q">Preciso instalar algum programa?</span><span class="faq-icon"><svg class="icon icon-sm"><use href="#i-chevron-down"/></svg></span></div>
        <div class="faq-a">Não! O Barber Control Pro é 100% online. Você acessa de qualquer computador, tablet ou celular pelo navegador. Seus clientes também não precisam instalar nada para agendar.</div>
      </div>
      <div class="faq-item">
        <div class="faq-header"><span class="faq-q">Como funciona o teste grátis?</span><span class="faq-icon"><svg class="icon icon-sm"><use href="#i-chevron-down"/></svg></span></div>
        <div class="faq-a">Você tem 14 dias para usar todos os recursos do plano Pro sem pagar nada. Não pedimos cartão de crédito no cadastro. Se gostar, escolhe um plano; se não, é só não fazer nada.</div>
      </div>
      <div class="faq-item">
        <div class="faq-header"><span class="faq-q">Posso cancelar quando quiser?</span><span class="faq-icon"><svg class="icon icon-sm"><use href="#i-chevron-down"/></svg></span></div>
        <div class="faq-a">Sim, sem multas nem fidelidade. Você pode cancelar com 1 clique no painel. O acesso continua válido até o final do período já pago.</div>
      </div>
      <div class="faq-item">
        <div class="faq-header"><span class="faq-q">Meus dados estão seguros?</span><span class="faq-icon"><svg class="icon icon-sm"><use href="#i-chevron-down"/></svg></span></div>
        <div class="faq-a">Absolutamente. Usamos criptografia de ponta a ponta e seguimos todas as diretrizes da LGPD. Seus dados de clientes e financeiros são seus e nunca serão compartilhados.</div>
      </div>
    </div>
  </div>
</section>

<section id="cta">
  <div class="container-c">
    <div class="cta-final reveal">
      <h2 class="cta-title">Pronto para revolucionar sua barbearia?</h2>
      <p class="cta-sub">Junte-se a +1.200 barbearias que já automatizaram seu negócio. Comece seu teste grátis hoje.</p>
      <form method="POST" action="{{ route('landing') }}" class="newsletter-form">
        @csrf
        <input type="email" name="email" class="form-input" placeholder="Seu melhor e-mail" required>
        <button type="submit" class="btn-primary-c">Quero Testar</button>
      </form>
    </div>
  </div>
</section>

<footer>
  <div class="container-c">
    <div class="footer-grid">
      <div class="footer-brand">
        <a href="#" class="brand-logo">
          <div class="brand-mark"><svg class="icon" style="width:20px;height:20px"><use href="#i-scissor"/></svg></div>
          Barber Control <span>Pro</span>
        </a>
        <p>O sistema de gestão completo para barbearias modernas. Agende, controle e cresça.</p>
      </div>
      <div class="footer-col">
        <h4>Produto</h4>
        <a href="#features">Funcionalidades</a>
        <a href="#pricing">Planos</a>
        <a href="#">App do Cliente</a>
        <a href="#">Integrações</a>
      </div>
      <div class="footer-col">
        <h4>Empresa</h4>
        <a href="#">Sobre nós</a>
        <a href="#">Blog</a>
        <a href="#">Contato</a>
        <a href="#">Seja um parceiro</a>
      </div>
      <div class="footer-col">
        <h4>Suporte</h4>
        <a href="#faq">Central de Ajuda</a>
        <a href="#">Status do Sistema</a>
        <a href="#">Termos de Uso</a>
        <a href="#">Privacidade (LGPD)</a>
      </div>
    </div>
    <div class="footer-bottom">
      <span>&copy; {{ date('Y') }} Barber Control Pro. Todos os direitos reservados.</span>
      <div style="display:flex; gap:16px;">
        <a href="#" style="color:var(--text-faint);"><svg class="icon"><use href="#i-instagram"/></svg></a>
        <a href="#" style="color:var(--text-faint);"><svg class="icon"><use href="#i-whatsapp"/></svg></a>
      </div>
    </div>
  </div>
</footer>

<script>
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 30);
});
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) entry.target.classList.add('visible');
    });
}, { threshold: 0.1 });
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
document.querySelectorAll('.faq-header').forEach(header => {
    header.addEventListener('click', () => {
        const item = header.parentElement;
        const isOpen = item.classList.contains('open');
        document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('open'));
        if (!isOpen) item.classList.add('open');
    });
});
document.querySelectorAll('.toggle-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const period = btn.dataset.period;
        document.querySelectorAll('.price-num').forEach(num => {
            const target = parseInt(num.dataset[period]);
            let current = parseInt(num.textContent);
            let step = (target - current) / 10;
            let i = 0;
            const interval = setInterval(() => {
                current += step;
                i++;
                if (i >= 10) { num.textContent = target; clearInterval(interval); }
                else { num.textContent = Math.round(current); }
            }, 20);
        });
    });
});
const themeToggle = document.getElementById('themeToggle');
const html = document.documentElement;
if (localStorage.getItem('theme') === 'light') {
    html.setAttribute('data-bs-theme', 'light');
    themeToggle.innerHTML = '<svg class="icon"><use href="#i-moon"/></svg>';
}
themeToggle.addEventListener('click', () => {
    const isDark = html.getAttribute('data-bs-theme') === 'dark';
    const newTheme = isDark ? 'light' : 'dark';
    html.setAttribute('data-bs-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    themeToggle.innerHTML = isDark ? '<svg class="icon"><use href="#i-moon"/></svg>' : '<svg class="icon"><use href="#i-sun"/></svg>';
});
</script>
</body>
</html>
