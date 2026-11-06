<?php
if (!defined('PH_ENTRY')) { http_response_code(404); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<base href="/">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Parent Portal | Sign In & Register | ParentingHub Pakistan</title>
<meta name="description" content="Sign in or create your ParentingHub account. Manage your orders, baby milestones, authentic warranty slips, and wishlist in one calm, verified sanctuary.">
<link rel="canonical" href="https://www.parentinghub.pk/login">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="theme-color" content="#b63866">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:locale" content="en_PK">
<meta property="og:site_name" content="ParentingHub Pakistan">
<meta property="og:title" content="Parent Portal | Sign In & Register | ParentingHub Pakistan">
<meta property="og:description" content="Sign in or create your ParentingHub account. Manage your orders, baby milestones, authentic warranty slips, and wishlist.">
<meta property="og:url" content="https://www.parentinghub.pk/login">
<meta property="og:image" content="https://www.parentinghub.pk/assets/visual/banner-bottles.jpg">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Parent Portal | Sign In & Register | ParentingHub Pakistan">
<meta name="twitter:description" content="Sign in or create your ParentingHub account. Manage your orders and baby milestones.">
<meta name="twitter:image" content="https://www.parentinghub.pk/assets/visual/banner-bottles.jpg">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="css/style.css?v=5.5-product-visual">
<!-- Favicons -->
<link rel="icon" type="image/png" href="favicon.png">
<link rel="icon" type="image/x-icon" href="favicon.ico">
<link rel="icon" type="image/png" sizes="64x64" href="assets/favicon-64.png">
<link rel="icon" type="image/png" sizes="192x192" href="icon-192.png">
<link rel="apple-touch-icon" sizes="180x180" href="assets/apple-touch-icon.png">
<link rel="shortcut icon" href="favicon.ico">

<style>
/* ==================================================================
   ParentingHub Premier Auth Stage — Luxury & Motion System
   ================================================================== */
:root {
  --ph-primary: #b63866;
  --ph-primary-light: #c94877;
  --ph-primary-dark: #8c254c;
  --ph-accent: #8e6fc4;
  --ph-bg-gradient: radial-gradient(ellipse at 12% 16%, rgba(182, 56, 102, 0.08) 0%, transparent 45%),
                    radial-gradient(ellipse at 88% 82%, rgba(142, 111, 196, 0.09) 0%, transparent 48%),
                    #FAF6F8;
}

body[data-page="login"] {
  background: var(--ph-bg-gradient);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.auth-page-wrap {
  flex: 1;
  padding: 48px 0 72px;
  display: flex;
  align-items: center;
  position: relative;
  overflow: hidden;
}

/* Ambient Floating Glow Orbs in Background */
.auth-ambient-glow {
  position: absolute;
  pointer-events: none;
  z-index: 0;
  border-radius: 50%;
  filter: blur(60px);
  opacity: 0.6;
}
.auth-glow-1 {
  width: 380px;
  height: 380px;
  background: radial-gradient(circle, rgba(224, 114, 159, 0.28) 0%, transparent 70%);
  top: -80px;
  left: -80px;
  animation: glowFloat 12s ease-in-out infinite alternate;
}
.auth-glow-2 {
  width: 440px;
  height: 440px;
  background: radial-gradient(circle, rgba(142, 111, 196, 0.24) 0%, transparent 70%);
  bottom: -100px;
  right: -80px;
  animation: glowFloat 15s ease-in-out infinite alternate-reverse;
}
@keyframes glowFloat {
  0% { transform: translate3d(0, 0, 0) scale(1); }
  50% { transform: translate3d(30px, 40px, 0) scale(1.08); }
  100% { transform: translate3d(-20px, 20px, 0) scale(0.95); }
}

/* Grid Layout */
.auth-grid {
  display: grid;
  grid-template-columns: 1.08fr 0.92fr;
  gap: 52px;
  align-items: center;
  max-width: 1080px;
  margin-inline: auto;
  position: relative;
  z-index: 1;
}

/* Left Visual Column */
.auth-visual-col {
  padding-right: 16px;
}

.auth-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #ffffff;
  color: var(--ph-primary);
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  padding: 6px 14px;
  border-radius: 999px;
  box-shadow: 0 2px 10px rgba(182, 56, 102, 0.08);
  border: 1px solid rgba(182, 56, 102, 0.12);
}
.auth-eyebrow-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: var(--ph-primary);
  box-shadow: 0 0 8px var(--ph-primary);
  animation: pulseDot 2s infinite;
}
@keyframes pulseDot {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.5; transform: scale(0.75); }
}

.auth-hero-title {
  margin-top: 18px;
  font-size: clamp(2rem, 3.4vw, 2.65rem);
  line-height: 1.2;
  font-family: var(--f-display);
  color: var(--ink);
  letter-spacing: -0.02em;
}
.auth-hero-title em {
  color: var(--ph-primary);
  font-style: italic;
  font-weight: 600;
}

.auth-hero-desc {
  margin-top: 14px;
  max-width: 44ch;
  font-size: 15px;
  line-height: 1.6;
  color: var(--ink-2);
}

/* Trust Feature Badges */
.auth-trust-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
  margin-top: 26px;
}
.auth-trust-item {
  display: flex;
  align-items: center;
  gap: 14px;
  font-size: 14px;
  color: var(--ink);
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(8px);
  padding: 10px 16px;
  border-radius: 14px;
  border: 1px solid rgba(255, 255, 255, 0.85);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.auth-trust-item:hover {
  transform: translateX(4px);
  background: #ffffff;
  box-shadow: 0 4px 14px rgba(182, 56, 102, 0.08);
}
.auth-trust-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: linear-gradient(135deg, #fdf2f6, #fce4ee);
  color: var(--ph-primary);
  display: grid;
  place-items: center;
  flex-shrink: 0;
  box-shadow: inset 0 0 0 1px rgba(182, 56, 102, 0.15);
}
.auth-trust-icon svg {
  width: 18px;
  height: 18px;
}

/* Visual Frame */
.auth-visual-frame {
  border-radius: 22px;
  overflow: hidden;
  margin-top: 26px;
  box-shadow: 0 16px 36px -10px rgba(182, 56, 102, 0.15);
  border: 4px solid #ffffff;
  position: relative;
  max-height: 200px;
}
.auth-visual-frame img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.5s ease;
}
.auth-visual-frame:hover img {
  transform: scale(1.03);
}
.auth-visual-badge {
  position: absolute;
  bottom: 12px;
  left: 12px;
  background: rgba(23, 13, 28, 0.78);
  backdrop-filter: blur(10px);
  color: #ffffff;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 11.5px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 6px;
  border: 1px solid rgba(255, 255, 255, 0.15);
}
.auth-visual-badge svg {
  width: 14px;
  height: 14px;
  color: #ff9ec1;
}

/* ==================================================================
   Right Auth Card
   ================================================================== */
.auth-card {
  background: rgba(255, 255, 255, 0.96);
  backdrop-filter: blur(24px);
  border: 1px solid rgba(182, 56, 102, 0.12);
  border-radius: 26px;
  padding: 38px 34px;
  box-shadow: 0 20px 50px -12px rgba(182, 56, 102, 0.12),
              0 2px 10px rgba(0, 0, 0, 0.02);
  position: relative;
  transition: box-shadow 0.3s ease;
}

/* Sliding Tab Selector */
.auth-nav-pill-wrap {
  position: relative;
  background: #f7eff3;
  border-radius: 14px;
  padding: 4px;
  display: flex;
  margin-bottom: 22px;
  border: 1px solid #f0e1e7;
}
.auth-tab-btn {
  flex: 1;
  padding: 10px 16px;
  border-radius: 11px;
  border: none;
  background: transparent;
  font-family: inherit;
  font-size: 13.5px;
  font-weight: 700;
  color: #6a5b6f;
  cursor: pointer;
  transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 7px;
  position: relative;
  z-index: 1;
}
.auth-tab-btn svg {
  width: 16px;
  height: 16px;
  transition: transform 0.2s ease;
}
.auth-tab-btn.active {
  color: var(--ph-primary);
  background: #ffffff;
  box-shadow: 0 3px 12px rgba(182, 56, 102, 0.1);
}
.auth-tab-btn:hover:not(.active) {
  color: var(--ph-primary);
}


/* Input Fields */
.auth-input-group {
  margin-bottom: 16px;
  position: relative;
}
.auth-label {
  display: block;
  font-size: 12px;
  font-weight: 700;
  color: #382c3d;
  margin-bottom: 6px;
  letter-spacing: 0.02em;
}
.auth-input-wrap {
  position: relative;
  display: flex;
  align-items: center;
}
.auth-field-icon {
  position: absolute;
  left: 14px;
  color: #9c8d9f;
  pointer-events: none;
  width: 17px;
  height: 17px;
}
.auth-input, .auth-select {
  width: 100%;
  padding: 12px 16px 12px 42px;
  border: 1.5px solid #eae2e5;
  border-radius: 12px;
  outline: none;
  font-size: 14.5px;
  font-family: inherit;
  background: #fafafb;
  transition: border-color 0.18s ease, background-color 0.18s ease, box-shadow 0.18s ease;
  color: var(--ink);
}
.auth-select {
  padding-left: 14px;
  cursor: pointer;
  height: 47px;
}
.auth-input:focus, .auth-select:focus {
  border-color: var(--ph-primary);
  background: #ffffff;
  box-shadow: 0 0 0 3.5px rgba(182, 56, 102, 0.12);
}
.auth-input::placeholder {
  color: #aaa0ad;
}

/* Password Toggle */
.auth-pass-toggle {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #8a7e8d;
  cursor: pointer;
  padding: 5px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
  transition: color 0.15s ease;
}
.auth-pass-toggle:hover {
  color: var(--ph-primary);
}
.auth-pass-toggle svg {
  width: 18px;
  height: 18px;
}

/* Alert Notification */
.auth-msg {
  padding: 12px 16px;
  border-radius: 12px;
  font-size: 13.5px;
  margin-bottom: 16px;
  display: none;
  line-height: 1.45;
}
.auth-msg.error {
  display: block;
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
}
.auth-msg.success {
  display: block;
  background: #f0fdf4;
  color: #16a34a;
  border: 1px solid #bbf7d0;
}

/* Submit Action Button */
.auth-submit-btn {
  width: 100%;
  padding: 14px 20px;
  font-size: 15px;
  font-weight: 700;
  border-radius: 12px;
  border: none;
  background: linear-gradient(135deg, var(--ph-primary), var(--ph-primary-light));
  color: #ffffff;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  box-shadow: 0 8px 24px rgba(182, 56, 102, 0.28);
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  margin-top: 6px;
}
.auth-submit-btn:hover {
  background: linear-gradient(135deg, var(--ph-primary-dark), var(--ph-primary));
  box-shadow: 0 12px 28px rgba(182, 56, 102, 0.38);
  transform: translateY(-1.5px);
}
.auth-submit-btn:disabled {
  opacity: 0.6;
  pointer-events: none;
  transform: none;
}

/* Responsive Rules */
@media (max-width: 960px) {
  .auth-page-wrap {
    padding: 32px 0 56px;
  }
  .auth-grid {
    grid-template-columns: 1fr;
    gap: 28px;
  }
  .auth-visual-col {
    padding-right: 0;
    text-align: center;
  }
  .auth-hero-desc {
    margin-inline: auto;
  }
  .auth-trust-list {
    max-width: 500px;
    margin-inline: auto;
    text-align: left;
  }
  .auth-visual-frame {
    display: none;
  }
  .auth-card {
    padding: 28px 22px;
    border-radius: 22px;
  }
}

/* === v23: Clean, form-only auth experience === */
.auth-ambient-glow,.auth-visual-col{display:none!important}
.auth-page-wrap{
  padding:clamp(22px,4vw,52px) 0 64px!important;
  align-items:flex-start!important;
  background:linear-gradient(180deg,#fff 0%,#fff8fb 100%);
  overflow:visible!important;
}
.auth-grid{
  display:grid!important;
  grid-template-columns:minmax(0,500px)!important;
  justify-content:center!important;
  gap:0!important;
  max-width:500px!important;
  width:100%!important;
}
.auth-card{
  width:100%;
  padding:26px!important;
  border-radius:24px!important;
  border:1px solid #f0dce5!important;
  background:#fff!important;
  box-shadow:0 18px 50px rgba(87,45,64,.10)!important;
}
.auth-nav-pill-wrap{
  margin-bottom:22px!important;
  padding:5px!important;
  border-radius:14px!important;
  background:#fff4f8!important;
  border:1px solid #f1d8e3!important;
}
.auth-tab-btn{
  min-height:44px!important;
  border-radius:11px!important;
  font-size:14px!important;
  font-weight:700!important;
}
.auth-tab-btn.active{
  background:#fff!important;
  color:var(--ph-primary)!important;
  box-shadow:0 4px 14px rgba(182,56,102,.10)!important;
}
.auth-input-group{margin-bottom:16px!important}
.auth-label{font-size:12.5px!important;margin-bottom:7px!important}
.auth-input,.auth-select{
  min-height:48px!important;
  border-radius:12px!important;
  background:#fff!important;
  border:1px solid #e8dbe1!important;
}
.auth-submit-btn{
  min-height:50px!important;
  border-radius:13px!important;
  box-shadow:none!important;
}
.auth-submit-btn:hover{transform:none!important;box-shadow:0 8px 20px rgba(182,56,102,.18)!important}
.auth-card > p.tiny{margin-top:16px!important;font-size:10.8px!important;color:#8a7e88!important}
@media(max-width:600px){
  body[data-page="login"]{background:#fff8fb!important}
  .auth-page-wrap{padding:14px 0 34px!important}
  .auth-page-wrap .container{padding-inline:12px!important;width:100%!important}
  .auth-grid{max-width:none!important}
  .auth-card{padding:18px 14px 20px!important;border-radius:18px!important;box-shadow:0 8px 24px rgba(87,45,64,.07)!important}
  .auth-nav-pill-wrap{margin-bottom:18px!important}
  .auth-tab-btn{font-size:13px!important;padding-inline:8px!important}
  .auth-tab-btn svg{width:16px!important;height:16px!important}
  .auth-input-group[style*="grid-template-columns"]{grid-template-columns:1fr!important;gap:12px!important}
  .auth-input,.auth-select{font-size:16px!important}
  .auth-submit-btn{font-size:14px!important}
  #chrome-bottom{margin-top:0!important}
}

</style>
</head>
<body data-page="login">

<!-- Site Navigation Chrome -->
<div id="chrome-top"></div>

<main class="auth-page-wrap">
  <!-- Glowing Ambient Accents -->
  <div class="auth-ambient-glow auth-glow-1" aria-hidden="true"></div>
  <div class="auth-ambient-glow auth-glow-2" aria-hidden="true"></div>

  <div class="container">
    <div class="auth-grid">

      <!-- ================= LEFT VISUAL / BRAND COLUMN ================= -->
      <div class="auth-visual-col">
        <span class="auth-eyebrow">
          <span class="auth-eyebrow-dot"></span>
          Official Parent Sanctuary
        </span>

        <h1 class="auth-hero-title">
          Your Little Space,<br>
          <em>Warm, Verified &amp; Calm.</em>
        </h1>

        <p class="auth-hero-desc">
          Sign in or register to track your authentic orders, save genuine baby care essentials to your wishlist, and enjoy personalized care tailored for Pakistani families.
        </p>

        <div class="auth-trust-list">
          <div class="auth-trust-item">
            <span class="auth-trust-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </span>
            <span><b>100% Genuine Authorized Products</b> (Philips Avent, Tommee Tippee, Babyono)</span>
          </div>

          <div class="auth-trust-item">
            <span class="auth-trust-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </span>
            <span><b>Private &amp; Encrypted Security</b> — Your family details are strictly confidential</span>
          </div>

          <div class="auth-trust-item">
            <span class="auth-trust-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </span>
            <span><b>Nationwide Express Delivery</b> with email &amp; SMS live tracking</span>
          </div>
        </div>

        <div class="auth-visual-frame">
          <img src="assets/img/learn.jpg" alt="Parent and baby smiling calmly">
          <div class="auth-visual-badge">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            <span>Verified Authentic · Pakistan's #1 Baby Care Hub</span>
          </div>
        </div>
      </div>

      <!-- ================= RIGHT AUTHENTICATION CARD ================= -->
      <div class="auth-card">
        <!-- Interactive Pill Switcher -->
        <div class="auth-nav-pill-wrap" id="authNavWrap">
          <button class="auth-tab-btn active" id="tabLoginBtn" type="button" onclick="switchAuthTab('login')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
            Sign In
          </button>
          <button class="auth-tab-btn" id="tabRegisterBtn" type="button" onclick="switchAuthTab('register')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
            Create Account
          </button>
        </div>

        <!-- Notification Alert Box -->
        <div id="authAlert" class="auth-msg"></div>

        <!-- ================= FORM 1: SIGN IN ================= -->
        <form id="loginForm" onsubmit="handleLoginSubmit(event)">
          <div class="auth-input-group">
            <label class="auth-label" for="loginEmail">Email Address or Admin ID</label>
            <div class="auth-input-wrap">
              <svg class="auth-field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
              <input required type="text" inputmode="email" id="loginEmail" placeholder="e.g. fatima@example.com or admin" class="auth-input" autocomplete="username email">
            </div>
          </div>

          <div class="auth-input-group">
            <label class="auth-label" for="loginPassword">Password</label>
            <div class="auth-input-wrap">
              <svg class="auth-field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              <input required type="password" id="loginPassword" placeholder="••••••••" class="auth-input" autocomplete="current-password">
              <button type="button" class="auth-pass-toggle" onclick="togglePass('loginPassword', this)" title="Show/Hide Password" aria-label="Toggle password visibility">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
          </div>

          <div class="row" style="justify-content:space-between;align-items:center;margin:8px 0 22px">
            <label class="tiny muted row gap-s" style="cursor:pointer;display:flex;align-items:center">
              <input type="checkbox" id="keepSignedIn" checked style="accent-color:var(--ph-primary);width:15px;height:15px">
              <span>Keep me signed in</span>
            </label>
            <a class="tiny" href="javascript:void(0)" onclick="switchAuthTab('forgot')" style="color:var(--ph-primary);font-weight:700;text-decoration:none">Forgot password?</a>
          </div>

          <button type="submit" id="loginSubmitBtn" class="auth-submit-btn">
            <span>Sign In to My Space</span>
            <span aria-hidden="true">&rarr;</span>
          </button>
        </form>

        <!-- ================= FORM 2: CREATE ACCOUNT ================= -->
        <form id="registerForm" onsubmit="handleRegisterSubmit(event)" style="display:none">
          <div class="auth-input-group">
            <label class="auth-label" for="regName">Full Name</label>
            <div class="auth-input-wrap">
              <svg class="auth-field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              <input required type="text" id="regName" placeholder="e.g. Sana Khan / Ali Raza" class="auth-input" autocomplete="name">
            </div>
          </div>

          <div class="auth-input-group">
            <label class="auth-label" for="regEmail">Email Address</label>
            <div class="auth-input-wrap">
              <svg class="auth-field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
              <input required type="email" id="regEmail" placeholder="sana@example.com" class="auth-input" autocomplete="email">
            </div>
          </div>

          <div class="auth-input-group">
            <label class="auth-label" for="regPhone">Mobile Number (Courier Updates)</label>
            <div class="auth-input-wrap">
              <svg class="auth-field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
              <input type="tel" id="regPhone" placeholder="03XX XXXXXXX" class="auth-input" inputmode="tel">
            </div>
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px" class="auth-input-group">
            <div>
              <label class="auth-label" for="regCity">City</label>
              <select id="regCity" class="auth-select">
                <option value="Karachi">Karachi</option>
                <option value="Lahore" selected>Lahore</option>
                <option value="Islamabad">Islamabad</option>
                <option value="Rawalpindi">Rawalpindi</option>
                <option value="Faisalabad">Faisalabad</option>
                <option value="Multan">Multan</option>
                <option value="Peshawar">Peshawar</option>
                <option value="Quetta">Quetta</option>
                <option value="Other">Other City</option>
              </select>
            </div>
            <div>
              <label class="auth-label" for="regStage">Parenting Stage</label>
              <select id="regStage" class="auth-select">
                <option value="Expecting mom">Expecting Mom</option>
                <option value="Newborn (0-3 months)" selected>Newborn (0-3m)</option>
                <option value="Infant (4-12 months)">Infant (4-12m)</option>
                <option value="Toddler (1-3 years)">Toddler (1-3y)</option>
              </select>
            </div>
          </div>

          <div class="auth-input-group">
            <label class="auth-label" for="regPassword">Create Password</label>
            <div class="auth-input-wrap">
              <svg class="auth-field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              <input required minlength="8" type="password" id="regPassword" placeholder="Minimum 8 characters" class="auth-input" autocomplete="new-password">
              <button type="button" class="auth-pass-toggle" onclick="togglePass('regPassword', this)" title="Show/Hide Password" aria-label="Toggle password visibility">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
          </div>

          <button type="submit" id="regSubmitBtn" class="auth-submit-btn">
            <span>Create Free Account</span>
            <span aria-hidden="true">&rarr;</span>
          </button>
        </form>

        <!-- ================= FORM 3: FORGOT PASSWORD ================= -->
        <form id="forgotForm" onsubmit="handleForgotSubmit(event)" style="display:none">
          <div style="margin-bottom:18px">
            <h3 style="margin:0 0 6px;font-size:19px;font-family:var(--f-display)">Reset Your Password</h3>
            <p class="tiny muted" style="margin:0;line-height:1.4">Enter your registered email address to receive password reset instructions.</p>
          </div>
          <div class="auth-input-group">
            <label class="auth-label" for="forgotEmail">Registered Email</label>
            <div class="auth-input-wrap">
              <svg class="auth-field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
              <input required type="email" id="forgotEmail" placeholder="you@example.com" class="auth-input" autocomplete="email">
            </div>
          </div>
          <button type="submit" id="forgotSubmitBtn" class="auth-submit-btn">
            <span>Send Reset Instructions</span>
            <span aria-hidden="true">&rarr;</span>
          </button>
          <div style="text-align:center;margin-top:16px">
            <a class="tiny muted" href="javascript:void(0)" onclick="switchAuthTab('login')" style="font-weight:700;color:var(--ph-primary);text-decoration:none">&larr; Back to Sign In</a>
          </div>
        </form>

        <!-- ================= FORM 4: SET NEW PASSWORD ================= -->
        <form id="resetForm" onsubmit="handleResetSubmit(event)" style="display:none">
          <div style="margin-bottom:18px">
            <h3 style="margin:0 0 6px;font-size:19px;font-family:var(--f-display)">Set New Password</h3>
            <p class="tiny muted" style="margin:0;line-height:1.4">Enter your reset token and your new secure password.</p>
          </div>
          <div class="auth-input-group">
            <label class="auth-label" for="resetTokenInput">Reset Token</label>
            <div class="auth-input-wrap">
              <input required type="text" id="resetTokenInput" placeholder="Enter token received" class="auth-input" style="padding-left:14px">
            </div>
          </div>
          <div class="auth-input-group">
            <label class="auth-label" for="resetNewPass">New Password</label>
            <div class="auth-input-wrap">
              <input required minlength="8" type="password" id="resetNewPass" placeholder="Minimum 8 characters" class="auth-input" style="padding-left:14px">
            </div>
          </div>
          <div class="auth-input-group">
            <label class="auth-label" for="resetConfirmPass">Confirm New Password</label>
            <div class="auth-input-wrap">
              <input required minlength="8" type="password" id="resetConfirmPass" placeholder="Repeat new password" class="auth-input" style="padding-left:14px">
            </div>
          </div>
          <button type="submit" id="resetSubmitBtn" class="auth-submit-btn">
            <span>Update Password</span>
            <span aria-hidden="true">&rarr;</span>
          </button>
          <div style="text-align:center;margin-top:16px">
            <a class="tiny muted" href="javascript:void(0)" onclick="switchAuthTab('login')" style="font-weight:700;color:var(--ph-primary);text-decoration:none">&larr; Back to Sign In</a>
          </div>
        </form>

        <p class="tiny muted" style="margin-top:22px;text-align:center;line-height:1.5;font-size:11.5px">
          By signing in or creating an account, you agree to ParentingHub's <a href="/terms" style="color:var(--ph-primary);text-decoration:underline">Terms</a> &amp; <a href="/privacy" style="color:var(--ph-primary);text-decoration:underline">Privacy Policy</a>. Safe and authentic shopping guaranteed.
        </p>
      </div>

    </div>
  </div>
</main>

<!-- Site Footer Chrome -->
<div id="chrome-bottom"></div>

<!-- Application Core & Data Scripts -->
<script src="js/data.js?v=5.5-product-visual"></script>
<script src="js/app.js?v=5.5-product-visual"></script>

<script>
// Check query parameters (e.g. redirect or oauth error)
(function initAuthPage() {
  const params = new URLSearchParams(window.location.search);
  const oauthErr = params.get("oauth_error");
  if (oauthErr) {
    showAuthMsg(decodeURIComponent(oauthErr), true);
  }

  if (typeof getCurrentUser === "function") {
    const user = getCurrentUser();
    if (user && !oauthErr) {
      const target = params.get("redirect") || "/profile";
      window.location.replace(target);
    }
  }
})();

function togglePass(inputId, btn) {
  const input = document.getElementById(inputId);
  if (!input) return;
  const isPass = input.type === "password";
  input.type = isPass ? "text" : "password";
  btn.setAttribute("aria-label", isPass ? "Hide password" : "Show password");
  btn.innerHTML = isPass
    ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>'
    : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
}

function switchAuthTab(mode) {
  const alertBox = document.getElementById("authAlert");
  if (alertBox) { alertBox.className = "auth-msg"; alertBox.style.display = "none"; }

  const tabLogin = document.getElementById("tabLoginBtn");
  const tabReg = document.getElementById("tabRegisterBtn");
  const formLogin = document.getElementById("loginForm");
  const formReg = document.getElementById("registerForm");
  const formForgot = document.getElementById("forgotForm");
  const formReset = document.getElementById("resetForm");
  const authNavWrap = document.getElementById("authNavWrap");

  if (formLogin) formLogin.style.display = "none";
  if (formReg) formReg.style.display = "none";
  if (formForgot) formForgot.style.display = "none";
  if (formReset) formReset.style.display = "none";

  if (mode === "register") {
    if (tabLogin) tabLogin.classList.remove("active");
    if (tabReg) tabReg.classList.add("active");
    if (authNavWrap) authNavWrap.style.display = "flex";
    if (formReg) formReg.style.display = "block";
  } else if (mode === "forgot") {
    if (authNavWrap) authNavWrap.style.display = "none";
    if (formForgot) formForgot.style.display = "block";
  } else if (mode === "reset") {
    if (authNavWrap) authNavWrap.style.display = "none";
    if (formReset) formReset.style.display = "block";
  } else {
    if (tabReg) tabReg.classList.remove("active");
    if (tabLogin) tabLogin.classList.add("active");
    if (authNavWrap) authNavWrap.style.display = "flex";
    if (formLogin) formLogin.style.display = "block";
  }
}

function showAuthMsg(msg, isError = false) {
  const alertBox = document.getElementById("authAlert");
  if (!alertBox) return;
  alertBox.style.display = "block";
  alertBox.className = "auth-msg " + (isError ? "error" : "success");
  alertBox.textContent = msg;
}


async function handleLoginSubmit(e) {
  e.preventDefault();
  let email = document.getElementById("loginEmail").value.trim();
  const password = document.getElementById("loginPassword").value;
  const btn = document.getElementById("loginSubmitBtn");

  // Support 'admin' shortcut for quick admin access
  if (email.toLowerCase() === "admin") {
    email = "admin@parentinghub.pk";
    document.getElementById("loginEmail").value = email;
  }

  if (!email || !password) {
    showAuthMsg("Please provide your email and password.", true);
    return;
  }

  btn.disabled = true;
  btn.innerHTML = "<span>Signing in...</span>";

  let loginSucceeded = false;

  try {
    const res = await fetch("/api/auth/login", {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-Requested-With": "XMLHttpRequest" },
      body: JSON.stringify({ email, password })
    });
    const data = await res.json().catch(() => null);

    if (data && data.success && data.user) {
      loginSucceeded = true;
      if (typeof setCurrentUser === "function") setCurrentUser(data.user);
      const isAdmin = !!(data.isAdmin || data.user.role === 'admin' || data.user.email === 'admin@parentinghub.pk');
      if (isAdmin) {
        localStorage.setItem("ph_admin_logged_in", "true");
      }
      showAuthMsg(isAdmin ? "Admin credentials verified! Opening Admin Panel..." : "Signed in successfully! Opening your space...", false);
      btn.innerHTML = "<span>Redirecting...</span>";
      if (typeof toast === "function") toast(isAdmin ? "Welcome to Admin Portal" : "Welcome back to your Parent Space");
      setTimeout(() => {
        const queryTarget = new URLSearchParams(window.location.search).get("redirect");
        const target = queryTarget || data.redirect || (isAdmin ? "/admin-panel" : "/profile");
        window.location.href = target;
      }, 400);
      return;
    } else {
      showAuthMsg((data && (data.message || data.error)) ? (data.message || data.error) : "Invalid email or password. Please verify your details or register an account.", true);
    }
  } catch (err) {
    showAuthMsg("Unable to reach authentication service. Please check your connection and try again.", true);
  } finally {
    if (!loginSucceeded) {
      btn.disabled = false;
      btn.innerHTML = '<span>Sign In to My Space</span> <span aria-hidden="true">&rarr;</span>';
    }
  }
}

async function handleRegisterSubmit(e) {
  e.preventDefault();
  const name = document.getElementById("regName").value.trim();
  const email = document.getElementById("regEmail").value.trim();
  const phone = document.getElementById("regPhone").value.trim();
  const city = document.getElementById("regCity").value;
  const stage = document.getElementById("regStage").value;
  const password = document.getElementById("regPassword").value;
  const btn = document.getElementById("regSubmitBtn");

  if (!name || !email || !password) {
    showAuthMsg("Please fill in all required fields.", true);
    return;
  }
  if (password.length < 8) {
    showAuthMsg("Password must be at least 8 characters.", true);
    return;
  }

  btn.disabled = true;
  btn.innerHTML = "<span>Creating your account...</span>";

  let regSucceeded = false;

  try {
    const res = await fetch("/api/auth/register", {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-Requested-With": "XMLHttpRequest" },
      body: JSON.stringify({ name, email, phone, city, password, stage })
    });
    const data = await res.json().catch(() => null);

    if (data && data.success && data.user) {
      regSucceeded = true;
      if (typeof setCurrentUser === "function") setCurrentUser(data.user);
      showAuthMsg("Account created successfully! Opening your space...", false);
      btn.innerHTML = "<span>Redirecting...</span>";
      if (typeof toast === "function") toast("Welcome to ParentingHub Pakistan");
      setTimeout(() => {
        const target = new URLSearchParams(window.location.search).get("redirect") || "/profile";
        window.location.href = target;
      }, 400);
    } else {
      showAuthMsg((data && data.message) ? data.message : "Account registration failed. Please check your details.", true);
    }
  } catch (err) {
    showAuthMsg("Unable to connect to the registration server. Please try again.", true);
  } finally {
    if (!regSucceeded) {
      btn.disabled = false;
      btn.innerHTML = '<span>Create Free Account</span> <span aria-hidden="true">&rarr;</span>';
    }
  }
}

async function handleForgotSubmit(e) {
  e.preventDefault();
  const email = document.getElementById("forgotEmail").value.trim();
  const btn = document.getElementById("forgotSubmitBtn");
  if (!email) {
    showAuthMsg("Please enter your registered email address.", true);
    return;
  }

  btn.disabled = true;
  btn.innerHTML = "<span>Sending request...</span>";

  try {
    const res = await fetch("/api/auth/forgot-password", {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-Requested-With": "XMLHttpRequest" },
      body: JSON.stringify({ email })
    });
    const data = await res.json().catch(() => null);

    if (data && data.success) {
      showAuthMsg(data.message || "If an account exists for this email, password reset instructions have been sent.", false);
    } else {
      showAuthMsg((data && data.message) ? data.message : "Unable to process password reset. Please try again.", true);
    }
  } catch (err) {
    showAuthMsg("Unable to connect to the authentication server. Please check your connection.", true);
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<span>Send Reset Instructions</span> <span aria-hidden="true">&rarr;</span>';
  }
}

async function handleResetSubmit(e) {
  e.preventDefault();
  const token = document.getElementById("resetTokenInput").value.trim();
  const newPassword = document.getElementById("resetNewPass").value;
  const confirmPassword = document.getElementById("resetConfirmPass").value;
  const btn = document.getElementById("resetSubmitBtn");

  if (!token) {
    showAuthMsg("Please enter your reset token.", true);
    return;
  }
  if (!newPassword || newPassword.length < 8) {
    showAuthMsg("New password must be at least 8 characters.", true);
    return;
  }
  if (newPassword !== confirmPassword) {
    showAuthMsg("Passwords do not match.", true);
    return;
  }

  btn.disabled = true;
  btn.innerHTML = "<span>Updating password...</span>";

  try {
    const res = await fetch("/api/auth/reset-password", {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-Requested-With": "XMLHttpRequest" },
      body: JSON.stringify({ token, newPassword })
    });
    const data = await res.json().catch(() => null);

    if (data && data.success) {
      showAuthMsg(data.message || "Password updated successfully! Please sign in with your new password.", false);
      if (typeof toast === "function") toast("Password updated successfully! Please sign in");
      setTimeout(() => {
        switchAuthTab("login");
      }, 1200);
    } else {
      showAuthMsg((data && data.message) ? data.message : "Password reset failed. The token may be expired or invalid.", true);
    }
  } catch (err) {
    showAuthMsg("Unable to connect to the server. Please try again.", true);
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<span>Update Password</span> <span aria-hidden="true">&rarr;</span>';
  }
}

// Auto open register or reset if url hash/query is set
const urlParams = new URLSearchParams(window.location.search);
if (window.location.hash === "#register") {
  switchAuthTab("register");
} else if (urlParams.get("token") || urlParams.get("reset_token") || window.location.hash === "#reset") {
  switchAuthTab("reset");
  const tok = urlParams.get("token") || urlParams.get("reset_token");
  if (tok) {
    const tokInput = document.getElementById("resetTokenInput");
    if (tokInput) tokInput.value = tok;
  }
}
</script>
</body>
</html>
