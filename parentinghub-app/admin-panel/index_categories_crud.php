<?php
if (!defined('PH_ENTRY')) { http_response_code(404); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<base href="/">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Admin Portal | ParentingHub.pk (CMS & Store Manager)</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="favicon.png">
<link rel="icon" type="image/x-icon" href="favicon.ico">
<link rel="icon" type="image/png" sizes="64x64" href="assets/favicon-64.png">
<link rel="icon" type="image/png" sizes="192x192" href="icon-192.png">
<link rel="apple-touch-icon" sizes="180x180" href="assets/apple-touch-icon.png">
<link rel="shortcut icon" href="favicon.ico">
<style>
:root {
  --primary: #bd3d6b;
  --primary-hover: #a12f58;
  --primary-light: #fdf2f6;
  --primary-border: #f9d2df;
  --slate-900: #0f172a;
  --slate-800: #1e293b;
  --slate-700: #334155;
  --slate-600: #475569;
  --slate-500: #64748b;
  --slate-400: #94a3b8;
  --slate-200: #e2e8f0;
  --slate-100: #f1f5f9;
  --slate-50: #f8fafc;
  --success: #16a34a;
  --success-bg: #dcfce7;
  --warning: #d97706;
  --warning-bg: #fef3c7;
  --danger: #dc2626;
  --danger-bg: #fee2e2;
  --info: #0284c7;
  --info-bg: #e0f2fe;
  --radius: 12px;
  --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
  --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.08), 0 2px 4px -2px rgba(0,0,0,0.05);
  --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -4px rgba(0,0,0,0.04);
}

* { box-sizing: border-box; margin: 0; padding: 0; }
body {
  font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
  background-color: var(--slate-50);
  color: var(--slate-800);
  min-height: 100vh;
  display: flex;
}

/* ================= LOGIN SCREEN ================= */
#loginScreen {
  position: fixed;
  inset: 0;
  background: linear-gradient(135deg, #fff5f8 0%, #f1f5f9 100%);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}
.login-card {
  background: #ffffff;
  border: 1px solid var(--slate-200);
  border-radius: 20px;
  padding: 40px;
  width: 100%;
  max-width: 440px;
  box-shadow: var(--shadow-lg);
  text-align: center;
}
.admin-alert {
  padding: 10px 14px;
  border-radius: 10px;
  font-size: 13px;
  margin-bottom: 16px;
  display: none;
  text-align: left;
  line-height: 1.4;
}
.admin-alert.error {
  display: block;
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
}
.admin-alert.success {
  display: block;
  background: #f0fdf4;
  color: #16a34a;
  border: 1px solid #bbf7d0;
}
.pass-input-wrap {
  position: relative;
  display: flex;
  align-items: center;
}
.pass-toggle-btn {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: var(--slate-400);
  cursor: pointer;
  padding: 4px;
  display: flex;
  align-items: center;
}
.pass-toggle-btn:hover {
  color: var(--primary);
}
.login-logo {
  max-height: 48px;
  margin-bottom: 20px;
}
.login-card h2 {
  font-size: 24px;
  font-weight: 700;
  color: var(--slate-900);
  margin-bottom: 8px;
}
.login-card p {
  color: var(--slate-500);
  font-size: 14px;
  margin-bottom: 24px;
}
.form-group {
  text-align: left;
  margin-bottom: 18px;
}
.form-group label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: var(--slate-700);
  margin-bottom: 6px;
}
.form-input, .form-textarea, .form-select {
  width: 100%;
  padding: 11px 14px;
  border: 1px solid var(--slate-200);
  border-radius: 10px;
  font-size: 14px;
  font-family: inherit;
  outline: none;
  transition: all 0.2s;
  background: #ffffff;
}
.form-textarea {
  min-height: 100px;
  resize: vertical;
}
.form-input:focus, .form-textarea:focus, .form-select:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(189, 61, 107, 0.15);
}
.btn-primary {
  width: 100%;
  padding: 12px 18px;
  background: var(--primary);
  color: #fff;
  border: none;
  border-radius: 10px;
  font-size: 14.5px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}
.btn-primary:hover {
  background: var(--primary-hover);
}
.login-credentials-hint {
  margin-top: 20px;
  padding: 12px;
  background: var(--primary-light);
  border: 1px dashed var(--primary-border);
  border-radius: 10px;
  font-size: 12px;
  color: var(--primary);
  line-height: 1.5;
  text-align: left;
  cursor: pointer;
}

/* ================= LAYOUT ================= */
#adminApp {
  display: flex;
  width: 100%;
  min-height: 100vh;
}

/* Sidebar */
.sidebar {
  width: 270px;
  background: #ffffff;
  border-right: 1px solid var(--slate-200);
  display: flex;
  flex-direction: column;
  position: sticky;
  top: 0;
  height: 100vh;
  z-index: 100;
  transition: transform 0.3s ease;
}
.sidebar-header {
  padding: 20px;
  border-bottom: 1px solid var(--slate-200);
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.sidebar-header img {
  height: 36px;
}
.sidebar-badge {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  background: var(--primary-light);
  color: var(--primary);
  padding: 3px 8px;
  border-radius: 6px;
  letter-spacing: 0.5px;
}
.sidebar-nav {
  padding: 16px 10px;
  flex: 1;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 3px;
}
.nav-section-label {
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  color: var(--slate-400);
  padding: 12px 14px 4px;
  letter-spacing: 0.6px;
}
.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 14px;
  border-radius: 10px;
  color: var(--slate-600);
  font-size: 13.5px;
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.2s;
}
.nav-item:hover {
  background: var(--slate-100);
  color: var(--slate-900);
}
.nav-item.active {
  background: var(--primary-light);
  color: var(--primary);
}
.nav-item .icon {
  font-size: 17px;
  width: 22px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.sidebar-footer {
  padding: 16px;
  border-top: 1px solid var(--slate-200);
  background: var(--slate-50);
}
.admin-user-pill {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
}
.admin-avatar {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: var(--primary);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 13px;
}
.admin-info {
  font-size: 13px;
  line-height: 1.3;
}
.admin-info b { display: block; color: var(--slate-900); }
.admin-info span { font-size: 11px; color: var(--slate-500); }
.btn-logout {
  width: 100%;
  padding: 8px;
  background: #ffffff;
  border: 1px solid var(--slate-200);
  border-radius: 8px;
  color: var(--danger);
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}
.btn-logout:hover {
  background: var(--danger-bg);
  border-color: var(--danger);
}

/* Main Content Area */
.main-wrapper {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
}
.topbar {
  height: 68px;
  background: #ffffff;
  border-bottom: 1px solid var(--slate-200);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 32px;
  position: sticky;
  top: 0;
  z-index: 90;
}
.topbar-left {
  display: flex;
  align-items: center;
  gap: 16px;
}
.topbar-title {
  font-size: 20px;
  font-weight: 700;
  color: var(--slate-900);
}
.topbar-right {
  display: flex;
  align-items: center;
  gap: 12px;
}
.btn-save-global {
  padding: 9px 18px;
  background: var(--primary);
  color: #ffffff;
  border: none;
  border-radius: 8px;
  font-size: 13.5px;
  font-weight: 700;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  box-shadow: 0 2px 4px rgba(189, 61, 107, 0.2);
}
.btn-save-global:hover { background: var(--primary-hover); }
.btn-storefront {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 14px;
  background: #ffffff;
  border: 1px solid var(--slate-200);
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  color: var(--slate-700);
  text-decoration: none;
  transition: all 0.2s;
}
.btn-storefront:hover {
  border-color: var(--primary);
  color: var(--primary);
}
.hamburger {
  display: none;
  background: none;
  border: none;
  font-size: 24px;
  color: var(--slate-700);
  cursor: pointer;
}

/* Content Area */
.content-body {
  padding: 28px 32px;
  flex: 1;
}
.tab-content {
  display: none;
}
.tab-content.active {
  display: block;
}

/* Metrics Cards Grid */
.metrics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
  gap: 18px;
  margin-bottom: 24px;
}
.metric-card {
  background: #ffffff;
  border: 1px solid var(--slate-200);
  border-radius: 16px;
  padding: 20px;
  box-shadow: var(--shadow-sm);
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
}
.metric-title {
  font-size: 12.5px;
  font-weight: 600;
  color: var(--slate-500);
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.metric-value {
  font-size: 24px;
  font-weight: 800;
  color: var(--slate-900);
  line-height: 1.1;
}
.metric-sub {
  font-size: 12px;
  color: var(--slate-500);
  margin-top: 6px;
}
.metric-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
}
.icon-rose { background: var(--primary-light); color: var(--primary); }
.icon-green { background: var(--success-bg); color: var(--success); }
.icon-blue { background: var(--info-bg); color: var(--info); }
.icon-amber { background: var(--warning-bg); color: var(--warning); }

/* Panels & Cards */
.panel {
  background: #ffffff;
  border: 1px solid var(--slate-200);
  border-radius: 16px;
  padding: 24px;
  box-shadow: var(--shadow-sm);
  margin-bottom: 24px;
}
.panel-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
  flex-wrap: wrap;
  gap: 14px;
}
.panel-head h3 {
  font-size: 18px;
  font-weight: 700;
  color: var(--slate-900);
}
.filter-bar {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}
.search-box {
  position: relative;
  min-width: 240px;
}
.search-box input {
  width: 100%;
  padding: 8px 12px 8px 34px;
  border: 1px solid var(--slate-200);
  border-radius: 8px;
  font-size: 13.5px;
  outline: none;
}
.search-box input:focus { border-color: var(--primary); }
.search-box .search-icon {
  position: absolute;
  left: 11px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--slate-400);
  font-size: 14px;
}

/* Status tabs */
.status-pills {
  display: flex;
  gap: 8px;
  overflow-x: auto;
  padding-bottom: 8px;
  margin-bottom: 18px;
}
.status-pill {
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
  background: var(--slate-100);
  color: var(--slate-600);
  border: none;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.2s;
}
.status-pill.active {
  background: var(--primary);
  color: #ffffff;
}

/* Data Table */
.table-responsive {
  width: 100%;
  overflow-x: auto;
}
table.data-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
  font-size: 13.5px;
}
table.data-table th {
  padding: 12px 14px;
  background: var(--slate-50);
  color: var(--slate-500);
  font-weight: 600;
  border-bottom: 1px solid var(--slate-200);
  white-space: nowrap;
}
table.data-table td {
  padding: 13px 14px;
  border-bottom: 1px solid var(--slate-200);
  vertical-align: middle;
}
table.data-table tr:hover td {
  background: var(--slate-50);
}

/* Badges */
.badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  white-space: nowrap;
}
.badge-pending { background: var(--warning-bg); color: var(--warning); }
.badge-confirmed { background: var(--info-bg); color: var(--info); }
.badge-packed { background: #ede9fe; color: #7c3aed; }
.badge-shipped { background: #dbeafe; color: #1d4ed8; }
.badge-delivered { background: var(--success-bg); color: var(--success); }
.badge-cancelled { background: var(--danger-bg); color: var(--danger); }
.badge-cod { background: #fef3c7; color: #b45309; }
.badge-bank { background: #e0e7ff; color: #4338ca; }

/* Action Buttons */
.btn-action {
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 12.5px;
  font-weight: 600;
  border: 1px solid var(--slate-200);
  background: #ffffff;
  cursor: pointer;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  text-decoration: none;
}
.btn-action:hover { background: var(--slate-100); }
.btn-action.btn-wa { color: #15803d; border-color: #86efac; background: #f0fdf4; }
.btn-action.btn-wa:hover { background: #dcfce7; }
.btn-action.btn-del { color: var(--danger); border-color: #fca5a5; background: #fef2f2; }
.btn-action.btn-del:hover { background: var(--danger-bg); }

/* Modal Windows */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(4px);
  z-index: 1000;
  display: none;
  align-items: center;
  justify-content: center;
  padding: 20px;
}
.modal-overlay.active { display: flex; }
.modal-box {
  background: #ffffff;
  border-radius: 20px;
  width: 100%;
  max-width: 820px;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  box-shadow: var(--shadow-lg);
  overflow: hidden;
  animation: modalIn 0.25s ease-out;
}
@keyframes modalIn {
  from { transform: scale(0.96); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
.modal-header {
  padding: 18px 24px;
  border-bottom: 1px solid var(--slate-200);
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.modal-header h3 { font-size: 18px; font-weight: 700; color: var(--slate-900); }
.modal-close {
  background: none;
  border: none;
  font-size: 22px;
  color: var(--slate-400);
  cursor: pointer;
}
.modal-body {
  padding: 24px;
  overflow-y: auto;
  flex: 1;
}
.modal-footer {
  padding: 16px 24px;
  border-top: 1px solid var(--slate-200);
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  background: var(--slate-50);
}

/* Grid Forms */
.two-col-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}
@media (max-width: 768px) {
  .two-col-grid { grid-template-columns: 1fr; }
}

/* Card / Slider Item */
.cms-card {
  border: 1px solid var(--slate-200);
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 14px;
  background: #ffffff;
}

/* Toast */
#adminToast {
  position: fixed;
  bottom: 24px;
  right: 24px;
  background: var(--slate-900);
  color: #ffffff;
  padding: 12px 20px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  box-shadow: var(--shadow-lg);
  z-index: 99999;
  display: none;
  animation: slideToast 0.3s ease-out;
}
@keyframes slideToast {
  from { transform: translateY(20px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

/* Product Gallery & Video Manager */
.media-manager-box {
  background: var(--slate-50);
  border: 1px solid var(--slate-200);
  border-radius: 12px;
  padding: 14px 16px;
  margin-top: 8px;
}
.gallery-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(95px, 1fr));
  gap: 12px;
  margin-top: 10px;
}
.gallery-thumb-card {
  position: relative;
  aspect-ratio: 1;
  border-radius: 10px;
  overflow: hidden;
  border: 2px solid var(--slate-200);
  background: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}
.gallery-thumb-card.is-cover {
  border-color: var(--primary);
  box-shadow: 0 0 0 2px rgba(189, 61, 107, 0.25);
}
.gallery-thumb-card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.gallery-thumb-actions {
  position: absolute;
  inset: 0;
  background: rgba(15, 23, 42, 0.72);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 4px;
  opacity: 0;
  transition: opacity 0.2s;
  padding: 6px;
}
.gallery-thumb-card:hover .gallery-thumb-actions {
  opacity: 1;
}
.btn-thumb-action {
  font-size: 11px;
  padding: 4px 6px;
  border-radius: 6px;
  border: none;
  cursor: pointer;
  font-weight: 600;
  width: 96%;
  text-align: center;
}
.btn-thumb-cover {
  background: var(--primary);
  color: #fff;
}
.btn-thumb-cover:hover {
  background: var(--primary-hover);
}
.btn-thumb-del {
  background: var(--danger);
  color: #fff;
}
.btn-thumb-del:hover {
  background: #b91c1c;
}
.cover-badge-tag {
  position: absolute;
  top: 4px;
  left: 4px;
  background: var(--primary);
  color: #fff;
  font-size: 9px;
  font-weight: 700;
  padding: 2px 6px;
  border-radius: 4px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  pointer-events: none;
  z-index: 2;
}
.video-preview-wrapper {
  margin-top: 10px;
  border-radius: 10px;
  overflow: hidden;
  background: #0f172a;
  min-height: 160px;
  max-height: 280px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.video-preview-wrapper iframe, .video-preview-wrapper video {
  width: 100%;
  height: 240px;
  border: none;
  display: block;
}
</style>
</head>
<body>

<!-- ================= LOGIN SCREEN ================= -->
<div id="loginScreen">
  <div class="login-card">
    <img src="logo.png" alt="ParentingHub.pk" class="login-logo">
    <h2>Store & CMS Manager</h2>
    <p>Sign in to manage orders, products, homepage & site content.</p>
    <div id="adminLoginAlert" class="admin-alert"></div>
    <form id="adminLoginForm" onsubmit="handleAdminLogin(event)">
      <div class="form-group">
        <label for="loginEmail">Admin Email</label>
        <input type="email" id="loginEmail" class="form-input" required placeholder="admin@parentinghub.pk" value="admin@parentinghub.pk" autocomplete="email">
      </div>
      <div class="form-group">
        <label for="loginPassword">Password</label>
        <div class="pass-input-wrap">
          <input type="password" id="loginPassword" class="form-input" required placeholder="Enter admin password" autocomplete="current-password">
          <button type="button" class="pass-toggle-btn" onclick="toggleAdminPass()" title="Show/Hide Password" aria-label="Toggle password visibility">
            <svg style="width:18px;height:18px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
      </div>
      <button type="submit" class="btn-primary" id="loginBtn">🔐 Secure Sign In</button>
      <div style="margin-top:14px;text-align:center">
        <a href="index.html" style="font-size:12.5px;color:var(--slate-500);text-decoration:none">&larr; Return to Storefront</a>
      </div>
    </form>
  </div>
</div>

<!-- ================= MAIN ADMIN APP ================= -->
<div id="adminApp" style="display:none">
  <!-- Sidebar -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <img src="logo.png" alt="ParentingHub.pk">
      <span class="sidebar-badge">CMS</span>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-section-label">Store Operations</div>
      <a class="nav-item active" onclick="switchTab('dashboard')">
        <span class="icon">📊</span> Dashboard
      </a>
      <a class="nav-item" onclick="switchTab('orders')">
        <span class="icon">🛍️</span> Orders
        <span id="navOrderCount" style="margin-left:auto;font-size:11px;background:var(--primary);color:#fff;padding:2px 7px;border-radius:10px">0</span>
      </a>
      <a class="nav-item" onclick="switchTab('products')">
        <span class="icon">📦</span> Products & Stock
      </a>
      <a class="nav-item" onclick="switchTab('categories')">
        <span class="icon">🗂️</span> Categories
      </a>

      <div class="nav-section-label">Website & CMS (Theme)</div>
      <a class="nav-item" onclick="switchTab('theme')">
        <span class="icon">🎨</span> Homepage Editor
      </a>
      <a class="nav-item" onclick="switchTab('articles')">
        <span class="icon">✍️</span> Learn / Guides
      </a>
      <a class="nav-item" onclick="switchTab('faqs')">
        <span class="icon">❓</span> FAQs & Help
      </a>
      <a class="nav-item" onclick="switchTab('coupons')">
        <span class="icon">🏷️</span> Coupons & Offers
      </a>
      <a class="nav-item" onclick="switchTab('community')">
        <span class="icon">💬</span> Community & Reels
      </a>

      <div class="nav-section-label">Support & Config</div>
      <a class="nav-item" onclick="switchTab('inquiries')">
        <span class="icon">✉️</span> Customer Inquiries
      </a>
      <a class="nav-item" onclick="switchTab('settings')">
        <span class="icon">⚙️</span> Store Settings
      </a>
    </nav>
    <div class="sidebar-footer">
      <div class="admin-user-pill">
        <div class="admin-avatar">A</div>
        <div class="admin-info">
          <b>Administrator</b>
          <span>ParentingHub.pk</span>
        </div>
      </div>
      <button class="btn-logout" onclick="handleLogout()">Sign Out</button>
    </div>
  </aside>

  <!-- Main Content Wrapper -->
  <div class="main-wrapper">
    <!-- Topbar -->
    <header class="topbar">
      <div class="topbar-left">
        <button class="hamburger" onclick="toggleSidebar()">☰</button>
        <h1 class="topbar-title" id="pageTitle">Dashboard Overview</h1>
      </div>
      <div class="topbar-right">
        <button class="btn-save-global" onclick="syncAllToLive()">
          💾 Save to Live Website
        </button>
        <a href="shop.html" target="_blank" class="btn-storefront">
          🏪 View Storefront ↗
        </a>
      </div>
    </header>

    <!-- Content Body -->
    <main class="content-body">

      <!-- ================= 1. DASHBOARD TAB ================= -->
      <section id="tab-dashboard" class="tab-content active">
        <div class="metrics-grid">
          <div class="metric-card">
            <div>
              <div class="metric-title">Total Revenue</div>
              <div class="metric-value" id="kpiRevenue">Rs 0</div>
              <div class="metric-sub" id="kpiAvgOrder">Avg: Rs 0 / order</div>
            </div>
            <div class="metric-icon icon-rose">💰</div>
          </div>
          <div class="metric-card">
            <div>
              <div class="metric-title">Total Orders</div>
              <div class="metric-value" id="kpiOrders">0</div>
              <div class="metric-sub" id="kpiOrdersBreakdown">0 Pending · 0 Confirmed</div>
            </div>
            <div class="metric-icon icon-green">📦</div>
          </div>
          <div class="metric-card">
            <div>
              <div class="metric-title">Catalog Inventory</div>
              <div class="metric-value" id="kpiProducts">0</div>
              <div class="metric-sub">Loaded from live database</div>
            </div>
            <div class="metric-icon icon-blue">🏷️</div>
          </div>
          <div class="metric-card">
            <div>
              <div class="metric-title">Customer Support</div>
              <div class="metric-value" id="kpiInquiries">0</div>
              <div class="metric-sub">Active inquiries</div>
            </div>
            <div class="metric-icon icon-amber">💬</div>
          </div>
        </div>

        <div class="panel">
          <div class="panel-head">
            <h3>Recent Orders</h3>
            <button class="btn-action" onclick="switchTab('orders')">View All Orders →</button>
          </div>
          <div class="table-responsive">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Order ID</th>
                  <th>Customer</th>
                  <th>Payment</th>
                  <th>Total</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="recentOrdersBody">
                <tr><td colspan="6" style="text-align:center;color:var(--slate-400);padding:30px">No orders placed yet. Orders will appear here in real time.</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ================= 2. ORDERS TAB ================= -->
      <section id="tab-orders" class="tab-content">
        <div class="panel">
          <div class="panel-head">
            <h3>Order Management</h3>
            <div class="filter-bar">
              <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" id="orderSearch" placeholder="Search Order ID, name, phone..." oninput="renderOrders()">
              </div>
              <button class="btn-action" onclick="exportOrdersCsv()">📥 Export CSV</button>
            </div>
          </div>

          <div class="status-pills">
            <button class="status-pill active" onclick="filterOrderStatus('all', this)">All Orders (<span id="countAll">0</span>)</button>
            <button class="status-pill" onclick="filterOrderStatus('Confirmed', this)">Confirmed (<span id="countConfirmed">0</span>)</button>
            <button class="status-pill" onclick="filterOrderStatus('Pending Payment', this)">Pending (<span id="countPending">0</span>)</button>
            <button class="status-pill" onclick="filterOrderStatus('Packed', this)">Packed (<span id="countPacked">0</span>)</button>
            <button class="status-pill" onclick="filterOrderStatus('Shipped', this)">Shipped (<span id="countShipped">0</span>)</button>
            <button class="status-pill" onclick="filterOrderStatus('Delivered', this)">Delivered (<span id="countDelivered">0</span>)</button>
            <button class="status-pill" onclick="filterOrderStatus('Cancelled', this)">Cancelled (<span id="countCancelled">0</span>)</button>
          </div>

          <div class="table-responsive">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Order #</th>
                  <th>Date</th>
                  <th>Customer</th>
                  <th>City</th>
                  <th>Items</th>
                  <th>Payment</th>
                  <th>Total</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="ordersTableBody"></tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ================= 3. PRODUCTS TAB ================= -->
      <section id="tab-products" class="tab-content">
        <div class="panel">
          <div class="panel-head">
            <div>
              <h3>Product Catalog</h3>
              <span style="font-size:13px;color:var(--slate-500)" id="productCatalogStats">Loading database products…</span>
            </div>
            <div class="filter-bar">
              <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" id="productSearch" placeholder="Search by title, SKU..." oninput="productPage=1;renderProducts()">
              </div>
              <select class="form-select" id="brandFilter" style="width:auto" onchange="productPage=1;renderProducts()"><option value="all" selected>All Brands</option></select>
              <select class="form-select" id="catFilter" style="width:auto" onchange="productPage=1;renderProducts()">
                <option value="all">All Categories</option>
              </select>
              <button class="btn-primary" style="width:auto;padding:8px 16px" onclick="openAddProductModal()">＋ Add Product</button>
              <button class="btn-action" style="padding:8px 14px;border-color:var(--primary);color:var(--primary);font-weight:700;background:#fff" onclick="syncAllToLive()" title="Force save & sync catalog changes to live server">💾 Sync to Server</button>
            </div>
          </div>

          <div class="table-responsive">
            <table class="data-table">
              <thead>
                <tr>
                  <th style="width:55px">Image</th>
                  <th>Title & SKU</th>
                  <th>Brand</th>
                  <th>Category</th>
                  <th>Price (PKR)</th>
                  <th>Stock</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="productsTableBody"></tbody>
            </table>
          </div>
          <div id="productsPagination" style="display:flex;align-items:center;justify-content:center;gap:8px;flex-wrap:wrap;padding:16px 12px 4px"></div>
        </div>
      </section>

      <!-- ================= 4. CATEGORIES TAB ================= -->
      <section id="tab-categories" class="tab-content">
        <div class="panel">
          <div class="panel-head">
            <h3>Store Categories</h3>
            <button class="btn-primary" style="width:auto;padding:8px 16px" onclick="openAddCategoryModal()">＋ Add Category</button>
          </div>
          <div class="table-responsive">
            <table class="data-table">
              <thead>
                <tr>
                  <th style="width:60px">Image</th>
                  <th>Category Name</th>
                  <th>Handle / Slug</th>
                  <th>Description</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="categoriesTableBody"></tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ================= 5. THEME / HOMEPAGE EDITOR ================= -->
      <section id="tab-theme" class="tab-content">
        <div class="panel">
          <div class="panel-head">
            <div>
              <h3>🎨 Homepage & Theme Customizer</h3>
              <p style="font-size:13px;color:var(--slate-500)">Control the exact headlines, announcement text, slider slides & banners on your live storefront.</p>
            </div>
            <button class="btn-primary" style="width:auto;padding:8px 20px" onclick="saveThemeCustomizer()">💾 Save Theme</button>
          </div>

          <!-- Announcement Bar -->
          <div class="cms-card">
            <h4 style="margin-bottom:12px;font-size:15px;color:var(--primary)">1. Top Announcement Bar</h4>
            <div class="two-col-grid">
              <div class="form-group">
                <label>Announcement Bar Text</label>
                <input type="text" id="themeAnnouncement" class="form-input" placeholder="e.g. Fixed PKR 200 delivery per order">
              </div>
              <div class="form-group">
                <label>Banner Link URL</label>
                <input type="text" id="themeAnnouncementLink" class="form-input" placeholder="shop.html">
              </div>
            </div>
          </div>

          <!-- Hero Section -->
          <div class="cms-card">
            <h4 style="margin-bottom:12px;font-size:15px;color:var(--primary)">2. Main Hero Section</h4>
            <div class="two-col-grid">
              <div class="form-group">
                <label>Main Headline</label>
                <input type="text" id="themeHeroHeadline" class="form-input" placeholder="Their little world.<br><em>Your whole heart.</em>">
              </div>
              <div class="form-group">
                <label>Subheadline / Intro Text</label>
                <textarea id="themeHeroLede" class="form-textarea" style="min-height:45px" placeholder="Shop essentials, learn what comes next, and find parents walking the same road — all in one warm Pakistani space."></textarea>
              </div>
            </div>
            <div class="two-col-grid">
              <div class="form-group">
                <label>Primary Button Text</label>
                <input type="text" id="themeHeroBtnText" class="form-input" placeholder="Explore the shop">
              </div>
              <div class="form-group">
                <label>Primary Button Link</label>
                <input type="text" id="themeHeroBtnLink" class="form-input" placeholder="shop.html">
              </div>
            </div>
          </div>

          <!-- Hero Slider Slides -->
          <div class="cms-card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
              <h4 style="font-size:15px;color:var(--primary)">3. Hero Carousel Slides</h4>
              <button class="btn-action" onclick="openAddSlideModal()">＋ Add Slide</button>
            </div>
            <div id="slidesListContainer"></div>
          </div>
        </div>
      </section>

      <!-- ================= 6. ARTICLES / GUIDES CMS ================= -->
      <section id="tab-articles" class="tab-content">
        <div class="panel">
          <div class="panel-head">
            <div>
              <h3>✍️ Learn / Parenting Guides (Database)</h3>
              <p style="font-size:13px;color:var(--slate-500)">Manage database-backed Learn guides. Changes publish directly to the website.</p>
            </div>
            <button class="btn-primary" style="width:auto;padding:8px 18px" onclick="openAddArticleModal()">＋ Add Guide</button>
          </div>
          <div class="table-responsive">
            <table class="data-table">
              <thead>
                <tr>
                  <th style="width:55px">Cover</th>
                  <th>Article Title</th>
                  <th>Category</th>
                  <th>Author</th>
                  <th>Read Time</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="articlesTableBody"></tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ================= 7. FAQS TAB ================= -->
      <section id="tab-faqs" class="tab-content">
        <div class="panel">
          <div class="panel-head">
            <h3>❓ Frequently Asked Questions</h3>
            <button class="btn-primary" style="width:auto;padding:8px 18px" onclick="openAddFaqModal()">＋ Add FAQ</button>
          </div>
          <div id="faqsListContainer"></div>
        </div>
      </section>

      <!-- ================= 8. COUPONS TAB ================= -->
      <section id="tab-coupons" class="tab-content">
        <div class="panel">
          <div class="panel-head">
            <h3>🏷️ Discount Coupons & Promo Codes</h3>
            <button class="btn-primary" style="width:auto;padding:8px 18px" onclick="openAddCouponModal()">＋ Create Coupon</button>
          </div>
          <div class="table-responsive">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Coupon Code</th>
                  <th>Type</th>
                  <th>Discount</th>
                  <th>Min Order</th>
                  <th>Description</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="couponsTableBody"></tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ================= COMMUNITY CMS TAB ================= -->
      <section id="tab-community" class="tab-content">
        <div class="panel">
          <div class="panel-head">
            <div>
              <h3>💬 Community Questions / Discussions</h3>
              <p style="font-size:13px;color:var(--slate-500)">Everything shown on the Community feed is loaded from the database.</p>
            </div>
            <button class="btn-action" onclick="resetAdminQuestionForm()">＋ New Question</button>
          </div>
          <input type="hidden" id="commPostEditId">
          <div class="two-col-grid">
            <div class="form-group"><label>Author</label><input id="commPostAuthor" class="form-input" value="ParentingHub"></div>
            <div class="form-group"><label>Community Key</label><input id="commPostCommunity" class="form-input" value="feeding" placeholder="feeding, newborn, health..."></div>
            <div class="form-group"><label>Flair</label><input id="commPostFlair" class="form-input" value="Question"></div>
            <div class="form-group"><label>Votes (automatic)</label><input id="commPostVotes" class="form-input" type="number" value="0" readonly><small style="color:var(--slate-500)">Calculated from signed-in user votes; not editable.</small></div>
          </div>
          <div class="form-group"><label>Question / Discussion Title</label><input id="commPostTitle" class="form-input"></div>
          <div class="form-group"><label>Body</label><textarea id="commPostBody" class="form-textarea" style="min-height:90px"></textarea></div>
          <div style="display:flex;gap:10px;justify-content:flex-end;margin-bottom:22px"><button class="btn-action" onclick="resetAdminQuestionForm()">Clear</button><button class="btn-primary" style="width:auto;padding:9px 20px" onclick="saveAdminQuestion()">💾 Save Question</button></div>
          <div class="table-responsive"><table class="data-table"><thead><tr><th>Title</th><th>Community</th><th>Author</th><th>Votes</th><th>Answers</th><th>Actions</th></tr></thead><tbody id="communityQuestionsBody"></tbody></table></div>
        </div>

        <div class="panel">
          <div class="panel-head"><div><h3>🎬 Reels CRUD</h3><p style="font-size:13px;color:var(--slate-500)">Reels page reads these records directly from the database.</p></div><button class="btn-action" onclick="resetAdminReelForm()">＋ New Reel</button></div>
          <input type="hidden" id="commReelEditId">
          <div class="two-col-grid">
            <div class="form-group"><label>Title</label><input id="commReelTitle" class="form-input"></div>
            <div class="form-group"><label>Tag</label><input id="commReelTag" class="form-input" value="Care"></div>
            <div class="form-group"><label>Author</label><input id="commReelAuthor" class="form-input" value="@parentinghub.pk"></div>
            <div class="form-group"><label>Status</label><select id="commReelStatus" class="form-select"><option value="published">Published</option><option value="draft">Draft</option></select></div>
            <div class="form-group">
              <label>Thumbnail</label>
              <input id="commReelThumb" class="form-input" placeholder="/assets/uploads/thumbnail.jpg or image URL">
              <input type="file" id="commReelThumbFile" accept="image/jpeg,image/png,image/webp,image/gif" style="display:none" onchange="handleCommunityReelUpload(event,'thumbnail')">
              <div style="display:flex;gap:8px;margin-top:7px;align-items:center;flex-wrap:wrap">
                <button type="button" class="btn-action" onclick="document.getElementById('commReelThumbFile').click()">🖼 Upload Thumbnail from PC</button>
                <span id="commReelThumbStatus" style="font-size:11.5px;color:var(--slate-500)"></span>
              </div>
            </div>
            <div class="form-group">
              <label>Reel / Video Media</label>
              <input id="commReelMedia" class="form-input" placeholder="/assets/uploads/reel.mp4 or video URL">
              <input type="file" id="commReelMediaFile" accept="video/mp4,video/webm,video/quicktime,.mp4,.webm,.mov" style="display:none" onchange="handleCommunityReelUpload(event,'media')">
              <div style="display:flex;gap:8px;margin-top:7px;align-items:center;flex-wrap:wrap">
                <button type="button" class="btn-action" onclick="document.getElementById('commReelMediaFile').click()">🎬 Upload Video from PC</button>
                <span id="commReelMediaStatus" style="font-size:11.5px;color:var(--slate-500)"></span>
              </div>
            </div>
            <div class="form-group"><label>Views (legacy/internal)</label><input id="commReelViews" class="form-input" type="number" value="0" readonly><small style="color:var(--slate-500)">Not shown publicly until real view tracking is enabled.</small></div>
            <div class="form-group"><label>Likes (legacy/internal)</label><input id="commReelLikes" class="form-input" type="number" value="0" readonly><small style="color:var(--slate-500)">Not shown publicly until real like tracking is enabled.</small></div>
            <div class="form-group"><label>Sort Order</label><input id="commReelSort" class="form-input" type="number" value="0"></div>
          </div>
          <div class="form-group"><label>Caption</label><textarea id="commReelCaption" class="form-textarea" style="min-height:70px"></textarea></div>
          <div style="display:flex;gap:10px;justify-content:flex-end;margin-bottom:22px"><button class="btn-action" onclick="resetAdminReelForm()">Clear</button><button class="btn-primary" style="width:auto;padding:9px 20px" onclick="saveAdminReel()">💾 Save Reel</button></div>
          <div class="table-responsive"><table class="data-table"><thead><tr><th>Preview</th><th>Title</th><th>Tag</th><th>Author</th><th>Views</th><th>Actions</th></tr></thead><tbody id="communityReelsBody"></tbody></table></div>
        </div>

        <div class="panel">
          <div class="panel-head"><div><h3>✅ Answers Moderation</h3><p style="font-size:13px;color:var(--slate-500)">Edit, verify, mark best answer, or delete community replies.</p></div></div>
          <div class="table-responsive"><table class="data-table"><thead><tr><th>Answer</th><th>Author</th><th>Votes</th><th>Best</th><th>Verified</th><th>Actions</th></tr></thead><tbody id="communityAnswersBody"></tbody></table></div>
        </div>

        <div class="panel">
          <div class="panel-head"><div><h3>🔖 Saved Community Items</h3><p style="font-size:13px;color:var(--slate-500)">Account-backed saved questions, answers and reels.</p></div><button class="btn-action" onclick="resetAdminSavedForm()">＋ Add Saved Record</button></div>
          <input type="hidden" id="commSavedEditId">
          <div class="two-col-grid">
            <div class="form-group"><label>User ID</label><input id="commSavedUser" class="form-input" type="number" min="1"></div>
            <div class="form-group"><label>Item Type</label><select id="commSavedType" class="form-select"><option value="post">Question/Post</option><option value="answer">Answer</option><option value="reel">Reel</option></select></div>
            <div class="form-group"><label>Item ID</label><input id="commSavedItem" class="form-input" placeholder="post-... / ans-... / r1"></div>
          </div>
          <div style="display:flex;gap:10px;justify-content:flex-end;margin-bottom:22px"><button class="btn-action" onclick="resetAdminSavedForm()">Clear</button><button class="btn-primary" style="width:auto;padding:9px 20px" onclick="saveAdminSaved()">💾 Save Record</button></div>
          <div class="table-responsive"><table class="data-table"><thead><tr><th>User</th><th>Type</th><th>Item ID</th><th>Saved At</th><th>Actions</th></tr></thead><tbody id="communitySavedBody"></tbody></table></div>
        </div>
      </section>

      <!-- ================= 9. INQUIRIES TAB ================= -->
      <section id="tab-inquiries" class="tab-content">
        <div class="panel">
          <div class="panel-head">
            <h3>Customer Inquiries & Support Messages</h3>
          </div>
          <div class="table-responsive">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Inquiry ID</th>
                  <th>Date</th>
                  <th>Customer</th>
                  <th>Contact</th>
                  <th>Topic</th>
                  <th>Message</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="inquiriesTableBody"></tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ================= 10. SETTINGS TAB ================= -->
      <section id="tab-settings" class="tab-content">
        <div class="panel">
          <div class="panel-head">
            <div>
              <h3>⚙️ Store Settings & Payment Configuration</h3>
              <p style="font-size:13px;color:var(--slate-500)">Update store contact, bank transfer info, and the fixed delivery policy.</p>
            </div>
            <button class="btn-primary" style="width:auto;padding:9px 20px" onclick="saveStoreSettings()">💾 Save Settings</button>
          </div>

          <h4 style="font-size:15px;color:var(--primary);margin-bottom:12px">Store Details</h4>
          <div class="two-col-grid">
            <div class="form-group">
              <label>Store Name</label>
              <input type="text" id="cfgSiteName" class="form-input" value="ParentingHub">
            </div>
            <div class="form-group">
              <label>Tagline</label>
              <input type="text" id="cfgTagline" class="form-input" value="Shop • Learn • Connect • Grow">
            </div>
            <div class="form-group">
              <label>Support Email</label>
              <input type="email" id="cfgEmail" class="form-input" value="support@parentinghub.pk">
            </div>
            <div class="form-group">
              <label>WhatsApp Support Number</label>
              <input type="text" id="cfgWhatsapp" class="form-input" value="923293007360">
            </div>
          </div>

          <h4 style="font-size:15px;color:var(--primary);margin:20px 0 12px">Shipping & Delivery</h4>
          <div class="two-col-grid">
            <div class="form-group">
              <label>Standard Delivery Fee (PKR per order)</label>
              <input type="number" id="cfgShippingFee" class="form-input" value="200" readonly disabled>
              <small class="muted">Official policy: fixed PKR 200 on every non-empty order.</small>
            </div>
            <div class="form-group">
              <label>Delivery Pricing Policy</label>
              <input type="text" class="form-input" value="Fixed per order — no threshold or weight calculation" readonly disabled>
                            <small class="muted">Product weight may be stored as catalog/logistics data; the customer delivery fee remains fixed at PKR 200.</small>
            </div>
          </div>

          <h4 style="font-size:15px;color:var(--primary);margin:20px 0 12px">Direct Bank Deposit Details</h4>
          <div class="two-col-grid">
            <div class="form-group">
              <label>Bank Name</label>
              <input type="text" id="cfgBankName" class="form-input" value="Faysal Bank">
            </div>
            <div class="form-group">
              <label>Account Title</label>
              <input type="text" id="cfgAccountTitle" class="form-input" value="SHAGUFTA KANWAL SHEERAZI">
            </div>
            <div class="form-group">
              <label>IBAN Number</label>
              <input type="text" id="cfgIban" class="form-input" value="PK23FAYS3207575000006005">
            </div>
            <div class="form-group">
              <label>Branch Details</label>
              <input type="text" id="cfgBranch" class="form-input" value="IBB SOLDIER BAZAR, KARACHI">
            </div>
          </div>

          <h4 style="font-size:15px;color:var(--primary);margin:20px 0 12px">Policies & Legal</h4>
          <div class="form-group">
            <label>Shipping Policy</label>
            <textarea id="cfgShippingPolicy" class="form-textarea"></textarea>
          </div>
          <div class="form-group">
            <label>Return & Exchange Policy</label>
            <textarea id="cfgReturnPolicy" class="form-textarea"></textarea>
          </div>
        </div>
      </section>

    </main>
  </div>
</div>

<!-- ================= ORDER DETAILS MODAL ================= -->
<div class="modal-overlay" id="orderModal">
  <div class="modal-box">
    <div class="modal-header">
      <h3 id="modalOrderTitle">Order Details</h3>
      <button class="modal-close" onclick="closeOrderModal()">✕</button>
    </div>
    <div class="modal-body" id="modalOrderBody"></div>
    <div class="modal-footer">
      <button class="btn-action" onclick="closeOrderModal()">Close</button>
    </div>
  </div>
</div>

<!-- ================= PRODUCT EDIT / ADD MODAL ================= -->
<div class="modal-overlay" id="productModal">
  <div class="modal-box">
    <div class="modal-header">
      <div>
        <h3 id="modalProductTitle" style="margin:0;font-size:18px">Product Editor</h3>
        <span style="font-size:12px;color:var(--slate-500)">Manage title, pricing, multi-image gallery, product videos, and stock</span>
      </div>
      <button class="modal-close" onclick="closeProductModal()">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="editProductId">
      <div class="form-group">
        <label>Product Title</label>
        <input type="text" id="editTitle" class="form-input" required placeholder="e.g. Kidilo K903 High Chair">
      </div>
      <div class="two-col-grid">
        <div class="form-group">
          <label>Brand</label>
          <select id="editBrand" class="form-select"></select>
        </div>
        <div class="form-group">
          <label>Category</label>
          <select id="editCategory" class="form-select"></select>
        </div>
      </div>
      <div class="two-col-grid">
        <div class="form-group">
          <label>SKU / Model Code</label>
          <input type="text" id="editSku" class="form-input" placeholder="e.g. KDL-K903">
        </div>
        <div class="form-group">
          <label>Price (PKR)</label>
          <input type="number" id="editPrice" class="form-input" required placeholder="e.g. 15999">
        </div>
      </div>
      <div class="form-group">
        <label>Product Weight (KG) — catalog/logistics only</label>
        <input type="number" id="editWeightKg" class="form-input" min="0.01" step="0.01" placeholder="e.g. 0.75">
      </div>
      <div class="two-col-grid">
        <div class="form-group">
          <label>Compare At / Old Price (PKR)</label>
          <input type="number" id="editOldPrice" class="form-input" placeholder="e.g. 18500 (optional discount)">
        </div>
        <div class="form-group">
          <label>Available Color Variants</label>
          <input type="text" id="editColors" class="form-input" placeholder="e.g. Grey, Khaki, Pink, Navy Blue (comma separated)">
        </div>
      </div>

      <!-- ================= MULTI-PHOTO GALLERY MANAGER ================= -->
      <div class="media-manager-box">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
          <div>
            <b style="font-size:14px;color:var(--slate-900)">📸 Product Photos & Gallery</b>
            <span style="font-size:12px;color:var(--slate-500);margin-left:6px" id="modalGalleryCount">(0 photos)</span>
          </div>
          <div style="display:flex;gap:8px">
            <input type="file" id="modalGalleryUploadInput" accept="image/*" multiple style="display:none" onchange="handleGalleryFilesSelected(event)">
            <button type="button" class="btn-action" style="font-size:12px;padding:6px 14px;background:#fff;border-color:var(--primary);color:var(--primary);font-weight:700" onclick="document.getElementById('modalGalleryUploadInput').click()">
              ➕ Upload Multiple Photos
            </button>
          </div>
        </div>
        <p style="font-size:12px;color:var(--slate-500);margin:0 0 10px 0">Add high-resolution product photos. Hover over any photo to set it as Primary Cover or delete it.</p>
        
        <div style="display:flex;gap:8px;margin-bottom:10px">
          <input type="text" id="modalAddImageUrl" class="form-input" placeholder="Paste image URL (https://... or assets/products/...)" style="font-size:12.5px;padding:6px 10px">
          <button type="button" class="btn-action" style="font-size:12.5px;padding:6px 14px;white-space:nowrap" onclick="addGalleryImageUrl()">+ Add URL</button>
        </div>

        <input type="hidden" id="editImage">
        <div class="gallery-grid" id="modalGalleryGrid"></div>
      </div>

      <!-- ================= PRODUCT VIDEO MANAGER ================= -->
      <div class="media-manager-box" style="margin-top:16px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
          <div>
            <b style="font-size:14px;color:var(--slate-900)">🎥 Product Video</b>
            <span style="font-size:12px;color:var(--slate-500);margin-left:6px">(YouTube Link or MP4 Clip)</span>
          </div>
          <div style="display:flex;gap:8px">
            <input type="file" id="modalVideoUploadInput" accept="video/mp4,video/webm,video/quicktime,.mp4,.webm,.mov" style="display:none" onchange="handleVideoFileSelected(event)">
            <button type="button" class="btn-action" style="font-size:12px;padding:6px 14px;background:#fff;border-color:var(--slate-300)" onclick="document.getElementById('modalVideoUploadInput').click()">
              📹 Upload Video File
            </button>
            <button type="button" class="btn-action btn-del" style="font-size:12px;padding:6px 10px" onclick="removeProductVideo()" title="Remove video">
              ✕
            </button>
          </div>
        </div>

        <div style="display:flex;gap:8px;margin-bottom:10px">
          <input type="text" id="editVideo" class="form-input" placeholder="Paste YouTube link (e.g. https://youtu.be/... or https://www.youtube.com/watch?v=...) or direct MP4 URL" oninput="updateVideoPreview()" style="font-size:12.5px;padding:6px 10px">
        </div>

        <div id="modalVideoPreviewBox" class="video-preview-wrapper">
          <div style="color:var(--slate-400);font-size:12.5px;padding:24px;text-align:center">No video attached yet. Enter a YouTube URL or upload an MP4 clip to showcase this product.</div>
        </div>
      </div>

      <div class="form-group" style="margin-top:16px">
        <label>Description & Features</label>
        <textarea id="editDesc" class="form-textarea" style="min-height:90px" placeholder="Describe product features, age group, safety certifications, material, dimensions..."></textarea>
      </div>
      <div class="form-group">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
          <input type="checkbox" id="editInStock" checked style="width:18px;height:18px">
          <b>Product is currently in stock & available for purchase</b>
        </label>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-action" onclick="closeProductModal()">Cancel</button>
      <button class="btn-primary" style="width:auto;padding:8px 22px;font-size:13.5px" onclick="saveProductModal()">💾 Save Product Changes</button>
    </div>
  </div>
</div>

<!-- ================= ARTICLE EDIT / ADD MODAL ================= -->
<div class="modal-overlay" id="articleModal">
  <div class="modal-box">
    <div class="modal-header">
      <h3 id="modalArticleTitle">Guide Editor</h3>
      <button class="modal-close" onclick="closeArticleModal()">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="editArtId">
      <div class="form-group">
        <label>Guide Title</label>
        <input type="text" id="editArtTitle" class="form-input" required>
      </div>
      <div class="two-col-grid">
        <div class="form-group">
          <label>Category</label>
          <input type="text" id="editArtCat" class="form-input" placeholder="Newborn, Feeding, etc.">
        </div>
        <div class="form-group">
          <label>Author</label>
          <input type="text" id="editArtAuthor" class="form-input" value="ParentingHub Editorial">
        </div>
      </div>
      <div class="two-col-grid">
        <div class="form-group">
          <label>Published Date</label>
          <input type="text" id="editArtDate" class="form-input" placeholder="28 Oct 2026">
        </div>
        <div class="form-group">
          <label>Read Time</label>
          <input type="text" id="editArtRead" class="form-input" value="4 min">
        </div>
      </div>
      <div class="form-group">
        <label>Cover Image URL</label>
        <input type="text" id="editArtImage" class="form-input" value="art/art1.jpg">
        <div style="display:flex;gap:8px;align-items:center;margin-top:8px;flex-wrap:wrap">
          <input type="file" id="editArtImageFile" accept="image/jpeg,image/png,image/webp,image/gif" class="form-input" style="max-width:320px" onchange="uploadLearnCoverFromPc(this.files[0])">
          <span id="editArtUploadStatus" style="font-size:12px;color:var(--slate-500)">Optional: upload cover from computer</span>
        </div>
      </div>
      <div class="form-group">
        <label>Excerpt / Summary</label>
        <textarea id="editArtExcerpt" class="form-textarea" style="min-height:60px"></textarea>
      </div>
      <div class="form-group">
        <label>Full Content (Paragraphs separated by new lines)</label>
        <textarea id="editArtBody" class="form-textarea" style="min-height:140px"></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-action" onclick="closeArticleModal()">Cancel</button>
      <button class="btn-primary" style="width:auto;padding:8px 18px" onclick="saveArticleModal()">Save Guide</button>
    </div>
  </div>
</div>

<!-- ================= SLIDE MODAL ================= -->
<div class="modal-overlay" id="slideModal">
  <div class="modal-box">
    <div class="modal-header">
      <h3>Slider Slide</h3>
      <button class="modal-close" onclick="closeSlideModal()">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="editSlideIdx">
      <div class="form-group">
        <label>Tag / Eyebrow</label>
        <input type="text" id="editSlideTag" class="form-input" placeholder="e.g. Feeding edit">
      </div>
      <div class="form-group">
        <label>Slide Title</label>
        <input type="text" id="editSlideTitle" class="form-input" placeholder="e.g. Little things. Lots of love.">
      </div>
      <div class="form-group">
        <label>Description</label>
        <textarea id="editSlideDesc" class="form-textarea" style="min-height:60px"></textarea>
      </div>
      <div class="two-col-grid">
        <div class="form-group">
          <label>Button Text</label>
          <input type="text" id="editSlideBtnText" class="form-input" value="Shop feeding ↗">
        </div>
        <div class="form-group">
          <label>Link</label>
          <input type="text" id="editSlideLink" class="form-input" value="shop.html">
        </div>
      </div>
      <div class="form-group">
        <label>Slide Image URL</label>
        <input type="text" id="editSlideImage" class="form-input" value="assets/img/slider1.jpg">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-action" onclick="closeSlideModal()">Cancel</button>
      <button class="btn-primary" style="width:auto;padding:8px 18px" onclick="saveSlideModal()">Save Slide</button>
    </div>
  </div>
</div>

<!-- ================= FAQ MODAL ================= -->
<div class="modal-overlay" id="faqModal">
  <div class="modal-box">
    <div class="modal-header">
      <h3 id="modalFaqTitle">FAQ Editor</h3>
      <button class="modal-close" onclick="closeFaqModal()">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="editFaqId">
      <div class="form-group">
        <label>Question</label>
        <input type="text" id="editFaqQ" class="form-input" required>
      </div>
      <div class="form-group">
        <label>Answer</label>
        <textarea id="editFaqA" class="form-textarea" required></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-action" onclick="closeFaqModal()">Cancel</button>
      <button class="btn-primary" style="width:auto;padding:8px 18px" onclick="saveFaqModal()">Save FAQ</button>
    </div>
  </div>
</div>

<!-- ================= COUPON MODAL ================= -->
<div class="modal-overlay" id="couponModal">
  <div class="modal-box">
    <div class="modal-header">
      <h3>Coupon Editor</h3>
      <button class="modal-close" onclick="closeCouponModal()">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label>Coupon Code</label>
        <input type="text" id="editCouponCode" class="form-input" style="text-transform:uppercase" placeholder="e.g. WELCOME10">
      </div>
      <div class="two-col-grid">
        <div class="form-group">
          <label>Discount Type</label>
          <select id="editCouponType" class="form-select">
            <option value="percent">Percentage (%)</option>
            <option value="fixed">Fixed Amount (PKR)</option>
          </select>
        </div>
        <div class="form-group">
          <label>Discount Value</label>
          <input type="number" id="editCouponVal" class="form-input" placeholder="10 or 200">
        </div>
      </div>
      <div class="form-group">
        <label>Minimum Order Spend (PKR)</label>
        <input type="number" id="editCouponMin" class="form-input" value="1000">
      </div>
      <div class="form-group">
        <label>Description</label>
        <input type="text" id="editCouponDesc" class="form-input" placeholder="10% off your order">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-action" onclick="closeCouponModal()">Cancel</button>
      <button class="btn-primary" style="width:auto;padding:8px 18px" onclick="saveCouponModal()">Save Coupon</button>
    </div>
  </div>
</div>

<!-- Toast -->
<div id="adminToast"></div>

<script src="js/data.js"></script>
<script>
// ================= STATE MANAGEMENT =================
let allOrders = [];
let allInquiries = [];
let catalogData = null;
let currentOrderFilter = 'all';
let productPage = 1;
const PRODUCTS_PER_PAGE = 50;
let communityAdminData = { posts: [], answers: [], reels: [], saved: [] };
let communityAdminLoaded = false;

document.addEventListener("DOMContentLoaded", async () => {
  let isAdmin = false;
  try {
    const meRes = await fetch("/api/auth/me", { headers: { "X-Requested-With": "XMLHttpRequest" } });
    const meData = await meRes.json().catch(() => null);
    isAdmin = !!(meData && meData.authenticated && meData.isAdmin);
  } catch(e) {}

  if (isAdmin) localStorage.setItem("ph_admin_logged_in", "true");
  else localStorage.removeItem("ph_admin_logged_in");
  checkAuth(isAdmin);
  if (!isAdmin) return;

  await loadCatalog();
  await loadOrders();
  await loadInquiries();
  initCategorySelects();
  initBrandSelects();
  populateThemeForm();
  populateSettingsForm();
  updateDashboardKpis();
});

function toast(msg) {
  const t = document.getElementById("adminToast");
  t.textContent = msg;
  t.style.display = "block";
  setTimeout(() => { t.style.display = "none"; }, 3000);
}


// ================= COMMUNITY / REELS DATABASE CMS =================
function adminEsc(v) {
  return String(v ?? '').replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[s]));
}
async function communityAdminApi(url, options = {}) {
  const res = await fetch(url, {
    credentials:'same-origin',
    ...options,
    headers:{
      'Accept':'application/json',
      'X-Requested-With':'XMLHttpRequest',
      ...(options.body && !(options.body instanceof FormData) ? {'Content-Type':'application/json'} : {}),
      ...(options.headers||{})
    }
  });
  const data = await res.json().catch(()=>({success:false,message:'Invalid server response'}));
  if (!res.ok || data.success === false) throw new Error(data.message || `Request failed (${res.status})`);
  return data;
}
async function loadCommunityAdmin(force = false) {
  if (communityAdminLoaded && !force) { renderCommunityAdminAll(); return; }
  try {
    const [p,a,r,s] = await Promise.all([
      communityAdminApi('/api/admin/community/posts'),
      communityAdminApi('/api/admin/community/answers'),
      communityAdminApi('/api/admin/community/reels'),
      communityAdminApi('/api/admin/community/saved')
    ]);
    communityAdminData = { posts:p.posts||[], answers:a.answers||[], reels:r.reels||[], saved:s.items||[] };
    communityAdminLoaded = true;
    renderCommunityAdminAll();
  } catch(err) { toast('Community CMS: ' + err.message); }
}
function renderCommunityAdminAll(){ renderAdminQuestions(); renderAdminReels(); renderAdminAnswers(); renderAdminSaved(); }
function renderAdminQuestions(){
  const box=document.getElementById('communityQuestionsBody'); if(!box)return;
  box.innerHTML=communityAdminData.posts.length?communityAdminData.posts.map(p=>`<tr><td><b>${adminEsc(p.title)}</b><div style="font-size:11px;color:var(--slate-500);margin-top:3px">${adminEsc((p.body||'').slice(0,90))}</div></td><td>${adminEsc(p.community)}</td><td>${adminEsc(p.au)}</td><td>${Number(p.votes||0)}</td><td>${Number(p.comments||0)}</td><td><div style="display:flex;gap:6px"><button class="btn-action" onclick="editAdminQuestion('${adminEsc(p.id)}')">Edit</button><button class="btn-action btn-del" onclick="deleteAdminQuestion('${adminEsc(p.id)}')">Delete</button></div></td></tr>`).join(''):'<tr><td colspan="6">No database questions found.</td></tr>';
}
function resetAdminQuestionForm(){['commPostEditId','commPostTitle','commPostBody'].forEach(id=>document.getElementById(id).value='');document.getElementById('commPostAuthor').value='ParentingHub';document.getElementById('commPostCommunity').value='feeding';document.getElementById('commPostFlair').value='Question';document.getElementById('commPostVotes').value=0;}
function editAdminQuestion(id){const p=communityAdminData.posts.find(x=>x.id===id);if(!p)return;document.getElementById('commPostEditId').value=p.id;document.getElementById('commPostAuthor').value=p.au||'';document.getElementById('commPostCommunity').value=p.community||'';document.getElementById('commPostFlair').value=p.flair||'';document.getElementById('commPostVotes').value=p.votes||0;document.getElementById('commPostTitle').value=p.title||'';document.getElementById('commPostBody').value=p.body||'';document.getElementById('commPostTitle').scrollIntoView({behavior:'smooth',block:'center'});}
async function saveAdminQuestion(){const id=document.getElementById('commPostEditId').value.trim();const body={authorName:document.getElementById('commPostAuthor').value.trim(),community:document.getElementById('commPostCommunity').value.trim(),flair:document.getElementById('commPostFlair').value.trim(),title:document.getElementById('commPostTitle').value.trim(),body:document.getElementById('commPostBody').value.trim()};if(!body.title||!body.body){toast('Question title and body are required.');return;}try{if(id){body._action='update';body.id=id;}await communityAdminApi('/api/admin/community/posts',{method:'POST',body:JSON.stringify(body)});toast('Community question saved.');resetAdminQuestionForm();communityAdminLoaded=false;await loadCommunityAdmin(true);}catch(err){toast(err.message);}}
async function deleteAdminQuestion(id){if(!confirm('Delete this question and its answers?'))return;try{await communityAdminApi('/api/admin/community/posts',{method:'POST',body:JSON.stringify({_action:'delete',id})});toast('Question deleted.');communityAdminLoaded=false;await loadCommunityAdmin(true);}catch(err){toast(err.message);}}


async function uploadAdminFileFromPc(file, statusEl) {
  if (!file) throw new Error('No file selected.');
  const maxBytes = 80 * 1024 * 1024;
  if (file.size > maxBytes) throw new Error('File is larger than 80MB. Compress the video or use a hosted video URL.');
  if (statusEl) statusEl.textContent = 'Uploading…';
  const fd = new FormData();
  fd.append('file', file, file.name);
  const res = await fetch('/api/upload', {
    method: 'POST',
    credentials: 'same-origin',
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    body: fd
  });
  const data = await res.json().catch(()=>({success:false,message:'Invalid upload response'}));
  if (!res.ok || !data.success || !data.url) throw new Error(data.message || `Upload failed (${res.status})`);
  if (statusEl) statusEl.textContent = '✓ Uploaded';
  return data.url;
}

async function handleCommunityReelUpload(event, kind) {
  const file = event.target.files && event.target.files[0];
  if (!file) return;
  const isThumb = kind === 'thumbnail';
  const status = document.getElementById(isThumb ? 'commReelThumbStatus' : 'commReelMediaStatus');
  const target = document.getElementById(isThumb ? 'commReelThumb' : 'commReelMedia');
  try {
    const url = await uploadAdminFileFromPc(file, status);
    target.value = url;
    toast(isThumb ? 'Thumbnail uploaded from computer.' : 'Reel/video uploaded from computer.');
  } catch (err) {
    if (status) status.textContent = 'Upload failed';
    toast(err.message || 'Upload failed.');
  } finally {
    event.target.value = '';
  }
}

function renderAdminReels(){const box=document.getElementById('communityReelsBody');if(!box)return;box.innerHTML=communityAdminData.reels.length?communityAdminData.reels.map(r=>`<tr><td><img src="/${adminEsc(String(r.img||'').replace(/^\//,''))}" alt="" style="width:52px;height:70px;object-fit:cover;border-radius:8px;background:#eee"></td><td><b>${adminEsc(r.title)}</b></td><td>${adminEsc(r.tag)}</td><td>${adminEsc(r.by)}</td><td>${Number(r.views||0).toLocaleString()}</td><td><div style="display:flex;gap:6px"><button class="btn-action" onclick="editAdminReel('${adminEsc(r.id)}')">Edit</button><button class="btn-action btn-del" onclick="deleteAdminReel('${adminEsc(r.id)}')">Delete</button></div></td></tr>`).join(''):'<tr><td colspan="6">No reels found.</td></tr>';}
function resetAdminReelForm(){['commReelEditId','commReelTitle','commReelCaption','commReelThumb','commReelMedia'].forEach(id=>document.getElementById(id).value='');document.getElementById('commReelTag').value='Care';document.getElementById('commReelAuthor').value='@parentinghub.pk';document.getElementById('commReelStatus').value='published';document.getElementById('commReelViews').value=0;document.getElementById('commReelLikes').value=0;document.getElementById('commReelSort').value=0;['commReelThumbStatus','commReelMediaStatus'].forEach(id=>{const el=document.getElementById(id);if(el)el.textContent='';});}
function editAdminReel(id){const r=communityAdminData.reels.find(x=>x.id===id);if(!r)return;document.getElementById('commReelEditId').value=r.id;document.getElementById('commReelTitle').value=r.title||'';document.getElementById('commReelTag').value=r.tag||'';document.getElementById('commReelAuthor').value=r.by||'';document.getElementById('commReelCaption').value=r.caption||'';document.getElementById('commReelThumb').value=r.img||'';document.getElementById('commReelMedia').value=r.media||'';document.getElementById('commReelViews').value=r.views||0;document.getElementById('commReelLikes').value=r.likes||0;document.getElementById('commReelSort').value=r.sortOrder||0;document.getElementById('commReelStatus').value=r.status||'published';document.getElementById('commReelTitle').scrollIntoView({behavior:'smooth',block:'center'});}
async function saveAdminReel(){const id=document.getElementById('commReelEditId').value.trim();const body={title:document.getElementById('commReelTitle').value.trim(),tag:document.getElementById('commReelTag').value.trim(),authorName:document.getElementById('commReelAuthor').value.trim(),caption:document.getElementById('commReelCaption').value.trim(),thumbnail:document.getElementById('commReelThumb').value.trim(),media:document.getElementById('commReelMedia').value.trim(),sortOrder:Number(document.getElementById('commReelSort').value||0),status:document.getElementById('commReelStatus').value};if(!body.title||!body.media){toast('Reel title and video/media are required.');return;}if(!body.thumbnail){body.thumbnail='/art/placeholder.jpg';}try{if(id){body._action='update';body.id=id;}await communityAdminApi('/api/admin/community/reels',{method:'POST',body:JSON.stringify(body)});toast('Reel saved.');resetAdminReelForm();communityAdminLoaded=false;await loadCommunityAdmin(true);}catch(err){toast(err.message);}}
async function deleteAdminReel(id){if(!confirm('Delete this reel?'))return;try{await communityAdminApi('/api/admin/community/reels',{method:'POST',body:JSON.stringify({_action:'delete',id})});toast('Reel deleted.');communityAdminLoaded=false;await loadCommunityAdmin(true);}catch(err){toast(err.message);}}

function renderAdminAnswers(){const box=document.getElementById('communityAnswersBody');if(!box)return;box.innerHTML=communityAdminData.answers.length?communityAdminData.answers.map(a=>`<tr><td style="max-width:420px">${adminEsc((a.text||'').slice(0,180))}</td><td>${adminEsc(a.au)}</td><td>${Number(a.votes||0)}</td><td>${a.best?'✅':'—'}</td><td>${a.verified?'✅':'—'}</td><td><div style="display:flex;gap:5px;flex-wrap:wrap"><button class="btn-action" onclick="editAdminAnswer('${adminEsc(a.id)}')">Edit</button><button class="btn-action" onclick="toggleAdminAnswer('${adminEsc(a.id)}','best')">Best</button><button class="btn-action" onclick="toggleAdminAnswer('${adminEsc(a.id)}','verified')">Verify</button><button class="btn-action btn-del" onclick="deleteAdminAnswer('${adminEsc(a.id)}')">Delete</button></div></td></tr>`).join(''):'<tr><td colspan="6">No answers found.</td></tr>';}
async function editAdminAnswer(id){const a=communityAdminData.answers.find(x=>x.id===id);if(!a)return;const author=prompt('Author name',a.au||'');if(author===null)return;const text=prompt('Answer text',a.text||'');if(text===null||!text.trim())return;try{await communityAdminApi('/api/admin/community/answers',{method:'POST',body:JSON.stringify({_action:'update',id,authorName:author,text,votes:a.votes,best:a.best,verified:a.verified})});communityAdminLoaded=false;await loadCommunityAdmin(true);toast('Answer updated.');}catch(err){toast(err.message);}}
async function toggleAdminAnswer(id,field){const a=communityAdminData.answers.find(x=>x.id===id);if(!a)return;const body={authorName:a.au,text:a.text,votes:a.votes,best:a.best,verified:a.verified};body[field]=!body[field];try{body._action='update';body.id=id;await communityAdminApi('/api/admin/community/answers',{method:'POST',body:JSON.stringify(body)});communityAdminLoaded=false;await loadCommunityAdmin(true);}catch(err){toast(err.message);}}
async function deleteAdminAnswer(id){if(!confirm('Delete this answer?'))return;try{await communityAdminApi('/api/admin/community/answers',{method:'POST',body:JSON.stringify({_action:'delete',id})});communityAdminLoaded=false;await loadCommunityAdmin(true);toast('Answer deleted.');}catch(err){toast(err.message);}}

function renderAdminSaved(){const box=document.getElementById('communitySavedBody');if(!box)return;box.innerHTML=communityAdminData.saved.length?communityAdminData.saved.map(s=>`<tr><td><b>${adminEsc(s.userName||('User #'+s.userId))}</b><div style="font-size:11px;color:var(--slate-500)">${adminEsc(s.email||'')}</div></td><td>${adminEsc(s.itemType)}</td><td><code>${adminEsc(s.itemId)}</code></td><td>${adminEsc(s.createdAt)}</td><td><div style="display:flex;gap:6px"><button class="btn-action" onclick="editAdminSaved(${Number(s.id)})">Edit</button><button class="btn-action btn-del" onclick="deleteAdminSaved(${Number(s.id)})">Delete</button></div></td></tr>`).join(''):'<tr><td colspan="5">No saved community records.</td></tr>';}
function resetAdminSavedForm(){document.getElementById('commSavedEditId').value='';document.getElementById('commSavedUser').value='';document.getElementById('commSavedType').value='post';document.getElementById('commSavedItem').value='';}
function editAdminSaved(id){const s=communityAdminData.saved.find(x=>Number(x.id)===Number(id));if(!s)return;document.getElementById('commSavedEditId').value=s.id;document.getElementById('commSavedUser').value=s.userId;document.getElementById('commSavedType').value=s.itemType;document.getElementById('commSavedItem').value=s.itemId;document.getElementById('commSavedUser').scrollIntoView({behavior:'smooth',block:'center'});}
async function saveAdminSaved(){const id=document.getElementById('commSavedEditId').value;const body={userId:Number(document.getElementById('commSavedUser').value||0),itemType:document.getElementById('commSavedType').value,itemId:document.getElementById('commSavedItem').value.trim()};if(!body.userId||!body.itemId){toast('User ID and Item ID are required.');return;}try{if(id){body._action='update';body.id=Number(id);}await communityAdminApi('/api/admin/community/saved',{method:'POST',body:JSON.stringify(body)});resetAdminSavedForm();communityAdminLoaded=false;await loadCommunityAdmin(true);toast('Saved record updated.');}catch(err){toast(err.message);}}
async function deleteAdminSaved(id){if(!confirm('Delete this saved record?'))return;try{await communityAdminApi('/api/admin/community/saved',{method:'POST',body:JSON.stringify({_action:'delete',id:Number(id)})});communityAdminLoaded=false;await loadCommunityAdmin(true);toast('Saved record deleted.');}catch(err){toast(err.message);}}

// Authentication
function checkAuth(isAuth = false) {
  if (isAuth) {
    document.getElementById("loginScreen").style.display = "none";
    document.getElementById("adminApp").style.display = "flex";
  } else {
    document.getElementById("loginScreen").style.display = "flex";
    document.getElementById("adminApp").style.display = "none";
  }
}

function showAdminLoginAlert(msg, isError = true) {
  const box = document.getElementById("adminLoginAlert");
  if (!box) return;
  box.className = "admin-alert " + (isError ? "error" : "success");
  box.textContent = msg;
}

function toggleAdminPass() {
  const p = document.getElementById("loginPassword");
  if (!p) return;
  p.type = p.type === "password" ? "text" : "password";
}

async function handleAdminLogin(e) {
  e.preventDefault();
  const alertBox = document.getElementById("adminLoginAlert");
  if (alertBox) alertBox.style.display = "none";

  const email = document.getElementById("loginEmail").value.trim();
  const password = document.getElementById("loginPassword").value.trim();
  const btn = document.getElementById("loginBtn");

  if (!email || !password) {
    showAdminLoginAlert("Please enter both email and password.", true);
    return;
  }

  btn.disabled = true;
  btn.textContent = "Verifying credentials...";

  try {
    const res = await fetch("/api/auth/admin-login", {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-Requested-With": "XMLHttpRequest" },
      body: JSON.stringify({ email, password })
    });
    const data = await res.json().catch(() => null);
    if (data && data.success) {
      localStorage.setItem("ph_admin_logged_in", "true");
      checkAuth(true);
      toast("Welcome to ParentingHub Admin Portal");
      loadOrders();
    } else {
      showAdminLoginAlert((data && data.message) ? data.message : "Invalid administrator credentials.", true);
    }
  } catch (err) {
    showAdminLoginAlert("Unable to reach authentication service. Please check server status.", true);
  } finally {
    btn.disabled = false;
    btn.textContent = "🔐 Secure Sign In";
  }
}

async function handleLogout() {
  try {
    await fetch('/api/auth/logout', { method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'} });
  } catch(e) {}
  localStorage.removeItem("ph_admin_logged_in");
  communityAdminLoaded = false;
  checkAuth(false);
  toast("Signed out successfully.");
}

function toggleSidebar() {
  document.getElementById("sidebar").classList.toggle("open");
}

function switchTab(tabId) {
  document.querySelectorAll(".tab-content").forEach(el => el.classList.remove("active"));
  document.querySelectorAll(".nav-item").forEach(el => el.classList.remove("active"));

  const targetTab = document.getElementById("tab-" + tabId);
  if (targetTab) targetTab.classList.add("active");

  const titleMap = {
    dashboard: "Dashboard Overview",
    orders: "Order Management",
    products: "Products & Stock",
    categories: "Categories & Collections",
    theme: "Homepage & Theme Customizer",
    articles: "Guides & Articles (CMS)",
    faqs: "Frequently Asked Questions",
    coupons: "Discounts & Promo Codes",
    community: "Community, Reels & Saved Items",
    inquiries: "Customer Inquiries",
    settings: "Store Settings"
  };
  document.getElementById("pageTitle").textContent = titleMap[tabId] || "Dashboard";

  // Mark nav item active
  document.querySelectorAll(".nav-item").forEach(item => {
    if (item.getAttribute("onclick") && item.getAttribute("onclick").includes(tabId)) {
      item.classList.add("active");
    }
  });

  if (tabId === "orders") renderOrders();
  if (tabId === "products") renderProducts();
  if (tabId === "categories") renderCategories();
  if (tabId === "theme") renderSlides();
  if (tabId === "articles") renderArticles();
  if (tabId === "faqs") renderFaqs();
  if (tabId === "coupons") renderCoupons();
  if (tabId === "community") loadCommunityAdmin();
  if (tabId === "inquiries") renderInquiries();

  if (window.innerWidth < 900) {
    document.getElementById("sidebar").classList.remove("open");
  }
}

// ================= DATA LOADING =================
async function loadCatalog() {
  const res = await fetch("/api/catalog", { credentials: "same-origin", cache: "no-store" });
  if (!res.ok) throw new Error(`Catalog database request failed (${res.status})`);
  const data = await res.json();
  const serverData = data.catalog || data;
  if (!serverData || !Array.isArray(serverData.products)) throw new Error("Invalid catalog database response");
  catalogData = serverData;
}

async function loadOrders() {
  try {
    const res = await fetch("/api/orders");
    if (res.ok) {
      const data = await res.json();
      allOrders = data.orders || [];
    }
  } catch(e) {}


  updateDashboardKpis();
  renderRecentOrders();
  renderOrders();
}

async function loadInquiries() {
  try {
    const res = await fetch("/api/inquiries");
    if (res.ok) {
      const data = await res.json();
      allInquiries = data.inquiries || [];
    }
  } catch(e){}
  renderInquiries();
}

async function syncAllToLive() {
  if (!catalogData) return;
  // Persist the complete admin catalog directly to MySQL. Browser storage is never the source of truth.
  try {
    const res = await fetch("/api/catalog", {
      method: "PUT",
      headers: { "Content-Type": "application/json", "X-Requested-With": "XMLHttpRequest" },
      body: JSON.stringify(catalogData)
    });
    if (res.ok) {
      const d = await res.json().catch(() => ({}));
      if (d.success) {
        toast("✅ All changes saved & synced directly to live website!");
        return;
      }
    } else if (res.status === 401 || res.status === 403) {
      toast("⚠️ Admin session required. Nothing was saved.");
      return;
    }
    toast("❌ Database save failed.");
  } catch(e) {
    console.error(e);
    toast("❌ Database unavailable. Nothing was saved.");
  }
}

// ================= DASHBOARD =================
function updateDashboardKpis() {
  let revenue = 0, pending = 0, active = 0;
  allOrders.forEach(o => {
    if (o.status !== "Cancelled") revenue += (Number(o.total) || 0);
    if (o.status === "Pending Payment") pending++;
    else active++;
  });

  document.getElementById("kpiRevenue").textContent = "Rs " + Math.round(revenue).toLocaleString("en-PK");
  document.getElementById("kpiOrders").textContent = allOrders.length;
  document.getElementById("navOrderCount").textContent = allOrders.length;
  document.getElementById("kpiOrdersBreakdown").textContent = `${pending} Pending · ${active} Active`;
  const avg = allOrders.length ? Math.round(revenue / allOrders.length) : 0;
  document.getElementById("kpiAvgOrder").textContent = `Avg: Rs ${avg.toLocaleString("en-PK")} / order`;

  const prods = catalogData?.products || [];
  document.getElementById("kpiProducts").textContent = prods.length;
  document.getElementById("kpiInquiries").textContent = allInquiries.length;
}

function renderRecentOrders() {
  const tbody = document.getElementById("recentOrdersBody");
  if (!allOrders.length) {
    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;color:var(--slate-400);padding:30px">No orders placed yet.</td></tr>`;
    return;
  }
  tbody.innerHTML = allOrders.slice(0, 5).map(o => {
    const name = o.address?.name || o.customerName || "Customer";
    return `
      <tr>
        <td><b>#${o.id}</b></td>
        <td>${name}</td>
        <td><span class="badge ${o.payment === 'bank' ? 'badge-bank' : 'badge-cod'}">${o.payment === 'bank' ? 'Bank Transfer' : 'COD'}</span></td>
        <td><b>Rs ${(Number(o.total)||0).toLocaleString("en-PK")}</b></td>
        <td>${renderStatusBadge(o.status)}</td>
        <td><button class="btn-action" onclick="viewOrderDetails('${o.id}')">View</button></td>
      </tr>
    `;
  }).join("");
}

// ================= ORDERS =================
function renderStatusBadge(status) {
  const s = (status || 'Confirmed').toLowerCase();
  if (s.includes('pending')) return `<span class="badge badge-pending">● Pending Payment</span>`;
  if (s.includes('packed')) return `<span class="badge badge-packed">● Packed</span>`;
  if (s.includes('shipped')) return `<span class="badge badge-shipped">● Shipped</span>`;
  if (s.includes('delivered')) return `<span class="badge badge-delivered">● Delivered</span>`;
  if (s.includes('cancelled')) return `<span class="badge badge-cancelled">● Cancelled</span>`;
  return `<span class="badge badge-confirmed">● Confirmed</span>`;
}

function filterOrderStatus(status, btn) {
  currentOrderFilter = status;
  document.querySelectorAll(".status-pill").forEach(p => p.classList.remove("active"));
  if (btn) btn.classList.add("active");
  renderOrders();
}

function renderOrders() {
  const tbody = document.getElementById("ordersTableBody");
  const query = (document.getElementById("orderSearch").value || "").toLowerCase().trim();

  let counts = { all: allOrders.length, confirmed: 0, pending: 0, packed: 0, shipped: 0, delivered: 0, cancelled: 0 };
  allOrders.forEach(o => {
    const st = (o.status || 'Confirmed').toLowerCase();
    if (st.includes('pending')) counts.pending++;
    else if (st.includes('packed')) counts.packed++;
    else if (st.includes('shipped')) counts.shipped++;
    else if (st.includes('delivered')) counts.delivered++;
    else if (st.includes('cancelled')) counts.cancelled++;
    else counts.confirmed++;
  });
  document.getElementById("countAll").textContent = counts.all;
  document.getElementById("countConfirmed").textContent = counts.confirmed;
  document.getElementById("countPending").textContent = counts.pending;
  document.getElementById("countPacked").textContent = counts.packed;
  document.getElementById("countShipped").textContent = counts.shipped;
  document.getElementById("countDelivered").textContent = counts.delivered;
  document.getElementById("countCancelled").textContent = counts.cancelled;

  let list = allOrders.slice();
  if (currentOrderFilter !== 'all') {
    list = list.filter(o => (o.status || '').toLowerCase().includes(currentOrderFilter.toLowerCase()));
  }
  if (query) {
    list = list.filter(o => {
      return (o.id || '').toLowerCase().includes(query) ||
             (o.address?.name || '').toLowerCase().includes(query) ||
             (o.address?.phone || '').toLowerCase().includes(query) ||
             (o.address?.city || '').toLowerCase().includes(query);
    });
  }

  if (!list.length) {
    tbody.innerHTML = `<tr><td colspan="9" style="text-align:center;color:var(--slate-400);padding:40px">No matching orders found.</td></tr>`;
    return;
  }

  tbody.innerHTML = list.map(o => {
    const d = o.createdAt ? new Date(o.createdAt).toLocaleDateString("en-PK", { day: 'numeric', month: 'short' }) : "Recent";
    const name = o.address?.name || o.customerName || "Customer";
    const phone = o.address?.phone || o.customerPhone || "";
    const city = o.address?.city || o.city || "Pakistan";
    const itemsCount = (o.items || []).length;
    const payment = o.payment === 'bank' ? 'Bank Transfer' : 'COD';
    const waText = encodeURIComponent(`Assalam o Alaikum ${name}, your order #${o.id} on ParentingHub.pk is currently ${o.status || 'Confirmed'}.`);

    return `
      <tr>
        <td><b>#${o.id}</b></td>
        <td><span style="font-size:12px;color:var(--slate-500)">${d}</span></td>
        <td><b>${name}</b><br><span style="font-size:12px;color:var(--slate-500)">${phone}</span></td>
        <td>${city}</td>
        <td><span class="badge" style="background:var(--slate-100);color:var(--slate-700)">${itemsCount} item(s)</span></td>
        <td><span class="badge ${payment === 'COD' ? 'badge-cod' : 'badge-bank'}">${payment}</span></td>
        <td><b>Rs ${(Number(o.total)||0).toLocaleString("en-PK")}</b></td>
        <td>
          <select class="form-select" style="padding:4px 8px;font-size:12px;width:auto" onchange="updateOrderStatus('${o.id}', this.value)">
            <option value="Confirmed" ${o.status === 'Confirmed' ? 'selected' : ''}>Confirmed</option>
            <option value="Pending Payment" ${o.status === 'Pending Payment' ? 'selected' : ''}>Pending Payment</option>
            <option value="Packed" ${o.status === 'Packed' ? 'selected' : ''}>Packed</option>
            <option value="Shipped" ${o.status === 'Shipped' ? 'selected' : ''}>Shipped</option>
            <option value="Delivered" ${o.status === 'Delivered' ? 'selected' : ''}>Delivered</option>
            <option value="Cancelled" ${o.status === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
          </select>
        </td>
        <td>
          <div style="display:flex;gap:6px">
            <button class="btn-action" onclick="viewOrderDetails('${o.id}')">View</button>
            <button class="btn-action btn-del" onclick="deleteOrder('${o.id}')">✕</button>
          </div>
        </td>
      </tr>
    `;
  }).join("");
}

async function updateOrderStatus(orderId, newStatus) {
  try {
    const res = await fetch(`/api/orders/${orderId}`, {
      method: "PATCH",
      headers: { "Content-Type": "application/json", "X-Requested-With": "XMLHttpRequest" },
      body: JSON.stringify({ status: newStatus })
    });
    if (!res.ok) throw new Error(`Order update failed (${res.status})`);
  } catch(e){ toast("Order database update failed."); await loadOrders(); return; }

  const idx = allOrders.findIndex(o => o.id === orderId);
  if (idx !== -1) allOrders[idx].status = newStatus;
  toast(`Order #${orderId} marked as ${newStatus}`);
  updateDashboardKpis();
  renderOrders();
}

async function deleteOrder(orderId) {
  if (!confirm(`Are you sure you want to delete order #${orderId}?`)) return;
  try {
    const res = await fetch(`/api/orders/${orderId}`, { method: "DELETE", headers: { "X-Requested-With": "XMLHttpRequest" } });
    if (!res.ok) throw new Error(`Order delete failed (${res.status})`);
  } catch(e){ toast("Order database delete failed."); await loadOrders(); return; }
  allOrders = allOrders.filter(o => o.id !== orderId);
  toast(`Order #${orderId} deleted.`);
  updateDashboardKpis();
  renderOrders();
}

function viewOrderDetails(orderId) {
  const o = allOrders.find(x => x.id === orderId);
  if (!o) return;

  document.getElementById("modalOrderTitle").textContent = `Order Details #${o.id}`;
  const d = o.createdAt ? new Date(o.createdAt).toLocaleString("en-PK") : "Recent";
  const name = o.address?.name || o.customerName || "Customer";
  const phone = o.address?.phone || o.customerPhone || "";
  const address = o.address?.address || o.deliveryAddress || "Address";
  const city = o.address?.city || o.city || "";
  const notes = o.address?.notes || o.notes || "None";

  let itemsHtml = (o.items || []).map(it => {
    const title = it.product?.name || it.name || "Product";
    const sku = it.product?.sku || it.sku || "";
    const price = Number(it.product?.price || it.price || 0);
    const qty = Number(it.qty || 1);
    const img = it.product?.image || it.product?.img || it.img || "art/placeholder.jpg";
    return `
      <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--slate-100)">
        <img src="${img}" style="width:48px;height:48px;border-radius:8px;object-fit:cover;border:1px solid var(--slate-200)">
        <div style="flex:1">
          <b style="font-size:13.5px;color:var(--slate-900)">${title}</b>
          <div style="font-size:12px;color:var(--slate-500)">SKU: ${sku} ${it.option ? '· Option: ' + it.option : ''}</div>
        </div>
        <div style="text-align:right">
          <b>Rs ${price.toLocaleString("en-PK")} × ${qty}</b>
          <div style="font-size:12px;color:var(--primary)"><b>Rs ${(price * qty).toLocaleString("en-PK")}</b></div>
        </div>
      </div>
    `;
  }).join("");

  document.getElementById("modalOrderBody").innerHTML = `
    <div style="background:var(--slate-50);padding:14px;border-radius:10px;margin-bottom:16px;border:1px solid var(--slate-200)">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;font-size:13px">
        <div><b>Customer:</b> ${name}</div>
        <div><b>Phone:</b> <a href="tel:${phone}" style="color:var(--primary)">${phone}</a></div>
        <div><b>City:</b> ${city}</div>
        <div><b>Date:</b> ${d}</div>
        <div style="grid-column:1/-1"><b>Delivery Address:</b> ${address}</div>
        <div style="grid-column:1/-1"><b>Special Instructions:</b> ${notes}</div>
      </div>
    </div>
    <h4 style="font-size:14px;font-weight:700;margin-bottom:10px">Ordered Items</h4>
    <div style="margin-bottom:18px">${itemsHtml}</div>
    <div style="background:var(--slate-50);padding:14px;border-radius:10px;font-size:13.5px;line-height:1.8">
      <div style="display:flex;justify-content:space-between"><span>Subtotal:</span> <b>Rs ${(Number(o.subtotal)||0).toLocaleString("en-PK")}</b></div>
      <div style="display:flex;justify-content:space-between"><span>Shipping Fee:</span> <b>Rs ${(Number(o.shipping)||0).toLocaleString("en-PK")}</b></div>
      ${o.discount ? `<div style="display:flex;justify-content:space-between;color:var(--success)"><span>Discount:</span> <b>- Rs ${(Number(o.discount)||0).toLocaleString("en-PK")}</b></div>` : ''}
      <div style="display:flex;justify-content:space-between;border-top:1px solid var(--slate-200);padding-top:6px;margin-top:6px;font-size:16px">
        <span><b>Grand Total:</b></span> <b style="color:var(--primary)">Rs ${(Number(o.total)||0).toLocaleString("en-PK")}</b>
      </div>
    </div>
  `;
  document.getElementById("orderModal").classList.add("active");
}
function closeOrderModal() { document.getElementById("orderModal").classList.remove("active"); }

function exportOrdersCsv() {
  if (!allOrders.length) { alert("No orders to export."); return; }
  let csv = "Order ID,Date,Customer,Phone,City,Address,Payment,Subtotal,Shipping,Discount,Total,Status\n";
  allOrders.forEach(o => {
    const row = [
      o.id,
      o.createdAt ? new Date(o.createdAt).toISOString().split('T')[0] : '',
      `"${(o.address?.name || '').replace(/"/g, '""')}"`,
      `"${(o.address?.phone || '').replace(/"/g, '""')}"`,
      `"${(o.address?.city || '').replace(/"/g, '""')}"`,
      `"${(o.address?.address || '').replace(/"/g, '""')}"`,
      o.payment || 'cod',
      o.subtotal || 0,
      o.shipping || 0,
      o.discount || 0,
      o.total || 0,
      o.status || 'Confirmed'
    ];
    csv += row.join(",") + "\n";
  });
  const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = `ParentingHub_Orders_${new Date().toISOString().split('T')[0]}.csv`;
  a.click();
}

// ================= PRODUCTS & INVENTORY =================
function initCategorySelects() {
  const cats = catalogData?.categories || [];
  const filterSel = document.getElementById("catFilter");
  const editSel = document.getElementById("editCategory");
  filterSel.innerHTML = '<option value="all">All Categories</option>';
  editSel.innerHTML = '';
  cats.forEach(c => {
    const name = c.name || c;
    const opt1 = document.createElement("option"); opt1.value = name; opt1.textContent = name;
    filterSel.appendChild(opt1);
    const opt2 = document.createElement("option"); opt2.value = name; opt2.textContent = name;
    editSel.appendChild(opt2);
  });
}

function initBrandSelects() {
  const brands = Array.isArray(catalogData?.brands) ? catalogData.brands : [];
  const filterSel = document.getElementById("brandFilter");
  const editSel = document.getElementById("editBrand");
  if (filterSel) {
    const curVal = filterSel.value || "all";
    filterSel.innerHTML = '<option value="all">All Brands</option>';
    brands.forEach(b => {
      const name = typeof b === "string" ? b : (b.name || b.handle);
      const opt = document.createElement("option"); opt.value = name; opt.textContent = name;
      filterSel.appendChild(opt);
    });
    filterSel.value = curVal;
  }
  if (editSel) {
    editSel.innerHTML = '';
    brands.forEach(b => {
      const name = typeof b === "string" ? b : (b.name || b.handle);
      const opt = document.createElement("option"); opt.value = name; opt.textContent = name;
      editSel.appendChild(opt);
    });
  }
}

function renderProducts() {
  const tbody = document.getElementById("productsTableBody");
  const prods = catalogData?.products || [];
  const search = (document.getElementById("productSearch").value || "").toLowerCase().trim();
  const brand = document.getElementById("brandFilter").value;
  const cat = document.getElementById("catFilter").value;

  let list = prods.slice();
  if (brand !== "all") list = list.filter(p => (p.brand || "").toLowerCase() === brand.toLowerCase());
  if (cat !== "all") list = list.filter(p => (p.category || p.cat || "").toLowerCase() === cat.toLowerCase());
  if (search) list = list.filter(p => (p.name || "").toLowerCase().includes(search) || (p.sku || "").toLowerCase().includes(search));

  const totalPages = Math.max(1, Math.ceil(list.length / PRODUCTS_PER_PAGE));
  if (productPage > totalPages) productPage = totalPages;
  if (productPage < 1) productPage = 1;
  const start = (productPage - 1) * PRODUCTS_PER_PAGE;
  const end = Math.min(start + PRODUCTS_PER_PAGE, list.length);
  const pageItems = list.slice(start, end);

  document.getElementById("productCatalogStats").textContent = list.length
    ? `Showing ${start + 1}-${end} of ${list.length} matching products • ${prods.length} total`
    : `No matching products • ${prods.length} total`;

  tbody.innerHTML = pageItems.map(p => {
    const img = p.image || p.img || "art/placeholder.jpg";
    const inStock = p.inStock !== false;
    return `
      <tr>
        <td><img src="${img}" style="width:42px;height:42px;border-radius:8px;object-fit:cover;border:1px solid var(--slate-200)" onerror="this.onerror=null;this.src='art/placeholder.jpg'"></td>
        <td>
          <b style="font-size:13.5px;color:var(--slate-900)">${p.name}</b><br>
          <span style="font-size:12px;color:var(--slate-500)">SKU: ${p.sku || 'N/A'}</span>
        </td>
        <td><span class="badge" style="background:var(--slate-100);color:var(--slate-700)">${p.brand || 'ParentingHub'}</span></td>
        <td>${p.category || p.cat || 'General'}</td>
        <td><b>Rs ${(Number(p.price)||0).toLocaleString("en-PK")}</b></td>
        <td>
          <button class="status-pill" style="padding:4px 10px;font-size:11px;background:${inStock ? 'var(--success-bg)' : 'var(--danger-bg)'};color:${inStock ? 'var(--success)' : 'var(--danger)'}" onclick="toggleProductStock('${p.id}')">
            ${inStock ? '● In Stock' : '✕ Out of Stock'}
          </button>
        </td>
        <td>
          <div style="display:flex;gap:6px">
            <button class="btn-action" onclick="openEditProductModal('${p.id}')">Edit</button>
            <button class="btn-action btn-del" onclick="deleteProduct('${p.id}')">✕</button>
          </div>
        </td>
      </tr>
    `;
  }).join("");

  const pager = document.getElementById("productsPagination");
  if (pager) {
    if (list.length <= PRODUCTS_PER_PAGE) {
      pager.innerHTML = '';
    } else {
      const pageButtons = [];
      const fromPage = Math.max(1, productPage - 2);
      const toPage = Math.min(totalPages, productPage + 2);
      pageButtons.push(`<button class="btn-action" ${productPage===1?'disabled':''} onclick="setProductPage(${productPage-1})">← Previous</button>`);
      if (fromPage > 1) pageButtons.push(`<button class="btn-action" onclick="setProductPage(1)">1</button>${fromPage>2?'<span style="color:var(--slate-400)">…</span>':''}`);
      for (let n=fromPage; n<=toPage; n++) {
        pageButtons.push(`<button class="btn-action" onclick="setProductPage(${n})" style="${n===productPage?'background:var(--primary);color:white;border-color:var(--primary)':''}">${n}</button>`);
      }
      if (toPage < totalPages) pageButtons.push(`${toPage<totalPages-1?'<span style="color:var(--slate-400)">…</span>':''}<button class="btn-action" onclick="setProductPage(${totalPages})">${totalPages}</button>`);
      pageButtons.push(`<button class="btn-action" ${productPage===totalPages?'disabled':''} onclick="setProductPage(${productPage+1})">Next →</button>`);
      pager.innerHTML = pageButtons.join('');
    }
  }
}

function setProductPage(page) {
  productPage = page;
  renderProducts();
  const panel = document.getElementById('tab-products');
  if (panel) panel.scrollIntoView({behavior:'smooth', block:'start'});
}

function toggleProductStock(prodId) {
  const p = (catalogData?.products || []).find(x => x.id === prodId);
  if (p) {
    p.inStock = !p.inStock;
    toast(`${p.name} marked ${p.inStock ? 'In Stock' : 'Out of Stock'}`);
    renderProducts();
    syncAllToLive();
  }
}

let modalGalleryImages = [];

// Compress image via HTML5 Canvas for speed and quality
function compressImageFile(file, maxWidth = 1200, quality = 0.85) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = e => {
      const img = new Image();
      img.onload = () => {
        let w = img.width;
        let h = img.height;
        if (w > maxWidth) {
          h = Math.round((h * maxWidth) / w);
          w = maxWidth;
        }
        const canvas = document.createElement("canvas");
        canvas.width = w;
        canvas.height = h;
        const ctx = canvas.getContext("2d");
        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = "high";
        ctx.drawImage(img, 0, 0, w, h);
        resolve(canvas.toDataURL("image/jpeg", quality));
      };
      img.onerror = reject;
      img.src = e.target.result;
    };
    reader.onerror = reject;
    reader.readAsDataURL(file);
  });
}

// Upload file data to server (/api/upload), falling back to data URL
async function uploadMediaFile(file, dataUrl) {
  try {
    const res = await fetch("/api/upload", {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-Requested-With": "XMLHttpRequest" },
      body: JSON.stringify({ filename: file.name, data: dataUrl })
    });
    if (res.ok) {
      const json = await res.json().catch(() => null);
      if (json && json.success && json.url) {
        return json.url;
      }
    }
  } catch(e) {}
  return dataUrl;
}

function renderModalGallery() {
  const grid = document.getElementById("modalGalleryGrid");
  const countEl = document.getElementById("modalGalleryCount");
  if (!grid) return;
  countEl.textContent = `(${modalGalleryImages.length} photo${modalGalleryImages.length === 1 ? '' : 's'})`;

  if (modalGalleryImages.length === 0) {
    grid.innerHTML = `<div style="grid-column:1/-1;padding:16px;text-align:center;color:var(--slate-400);font-size:12.5px">No images in gallery yet. Click "Upload Photos" or add an image URL above.</div>`;
    document.getElementById("editImage").value = "art/placeholder.jpg";
    return;
  }

  document.getElementById("editImage").value = modalGalleryImages[0];

  grid.innerHTML = modalGalleryImages.map((src, idx) => {
    const isCover = (idx === 0);
    return `
      <div class="gallery-thumb-card ${isCover ? 'is-cover' : ''}">
        ${isCover ? '<span class="cover-badge-tag">Cover</span>' : ''}
        <img src="${src}" alt="Product Photo ${idx + 1}" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
        <div class="gallery-thumb-actions">
          ${!isCover ? `<button type="button" class="btn-thumb-action btn-thumb-cover" onclick="setAsCoverImage(${idx})">⭐ Set Cover</button>` : ''}
          <button type="button" class="btn-thumb-action btn-thumb-del" onclick="removeGalleryImage(${idx})">✕ Delete</button>
        </div>
      </div>
    `;
  }).join("");
}

function setAsCoverImage(idx) {
  if (idx <= 0 || idx >= modalGalleryImages.length) return;
  const picked = modalGalleryImages.splice(idx, 1)[0];
  modalGalleryImages.unshift(picked);
  renderModalGallery();
  toast("Set as Primary Cover photo.");
}

function removeGalleryImage(idx) {
  if (idx < 0 || idx >= modalGalleryImages.length) return;
  modalGalleryImages.splice(idx, 1);
  renderModalGallery();
}

function addGalleryImageUrl() {
  const inp = document.getElementById("modalAddImageUrl");
  const url = (inp.value || "").trim();
  if (!url) return;
  modalGalleryImages.push(url);
  inp.value = "";
  renderModalGallery();
  toast("Added image URL to gallery.");
}

async function handleGalleryFilesSelected(event) {
  const files = Array.from(event.target.files || []);
  if (!files.length) return;
  toast(`Processing ${files.length} photo(s)...`);
  for (const file of files) {
    try {
      const compressedDataUrl = await compressImageFile(file);
      const serverUrl = await uploadMediaFile(file, compressedDataUrl);
      modalGalleryImages.push(serverUrl);
    } catch(err) {
      console.warn("Image process error:", err);
    }
  }
  event.target.value = "";
  renderModalGallery();
  toast(`✅ Added ${files.length} photo(s) to gallery.`);
}

function extractYouTubeId(url) {
  if (!url) return null;
  const u = url.trim();
  const m1 = u.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|shorts\/))([\w-]{11})/i);
  return m1 ? m1[1] : null;
}

function updateVideoPreview() {
  const box = document.getElementById("modalVideoPreviewBox");
  const url = (document.getElementById("editVideo").value || "").trim();
  if (!box) return;

  if (!url) {
    box.innerHTML = `<div style="color:var(--slate-400);font-size:12.5px;padding:24px;text-align:center">No video attached yet. Enter a YouTube URL or upload an MP4 clip to showcase this product.</div>`;
    return;
  }

  const ytId = extractYouTubeId(url);
  if (ytId) {
    box.innerHTML = `<iframe src="https://www.youtube-nocookie.com/embed/${ytId}?rel=0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
  } else {
    box.innerHTML = `<video src="${url}" controls preload="metadata" style="width:100%;height:100%;max-height:240px"></video>`;
  }
}

function removeProductVideo() {
  document.getElementById("editVideo").value = "";
  updateVideoPreview();
  toast("Video removed from product.");
}

async function handleVideoFileSelected(event) {
  const file = (event.target.files || [])[0];
  if (!file) return;
  const box = document.getElementById("modalVideoPreviewBox");
  try {
    toast("Uploading video clip...");
    box.innerHTML = `<div style="color:#fff;font-size:13px;padding:24px;text-align:center">⏳ Uploading video clip (${Math.round(file.size / 1024 / 1024 * 10) / 10} MB)...</div>`;
    const finalUrl = await uploadAdminFileFromPc(file);
    document.getElementById("editVideo").value = finalUrl;
    updateVideoPreview();
    toast("✅ Video uploaded from computer and attached to product.");
  } catch (err) {
    box.innerHTML = `<div style="color:#b91c1c;font-size:13px;padding:24px;text-align:center">${adminEsc(err.message || 'Video upload failed.')}</div>`;
    toast(err.message || "Video upload failed.");
  } finally {
    event.target.value = "";
  }
}

function openEditProductModal(prodId) {
  const p = (catalogData?.products || []).find(x => x.id === prodId);
  if (!p) return;
  document.getElementById("modalProductTitle").textContent = "Edit Product";
  document.getElementById("editProductId").value = p.id;
  document.getElementById("editTitle").value = p.name || "";
  document.getElementById("editBrand").value = p.brand || (catalogData?.brands?.[0] || "");
  document.getElementById("editCategory").value = p.category || p.cat || "Accessories";
  document.getElementById("editSku").value = p.sku || "";
  document.getElementById("editPrice").value = p.price || 0;
  document.getElementById("editWeightKg").value = p.weightKg || "";
  document.getElementById("editOldPrice").value = p.compareAt || p.old || "";
  document.getElementById("editDesc").value = p.description || p.desc || "";
  document.getElementById("editInStock").checked = p.inStock !== false;

  // Media gallery
  if (Array.isArray(p.images) && p.images.length > 0) {
    modalGalleryImages = p.images.slice();
  } else if (p.image || p.img) {
    modalGalleryImages = [p.image || p.img];
  } else {
    modalGalleryImages = ["art/placeholder.jpg"];
  }
  renderModalGallery();

  // Video
  document.getElementById("editVideo").value = p.video || p.videoUrl || "";
  updateVideoPreview();

  // Color variants
  let colors = "";
  if (Array.isArray(p.options)) {
    const colorOpt = p.options.find(o => (o.name || "").toLowerCase() === "color");
    if (colorOpt && Array.isArray(colorOpt.values)) {
      colors = colorOpt.values.join(", ");
    }
  }
  document.getElementById("editColors").value = colors;

  document.getElementById("productModal").classList.add("active");
}

function openAddProductModal() {
  document.getElementById("modalProductTitle").textContent = "Add New Product";
  document.getElementById("editProductId").value = "p_" + Date.now();
  document.getElementById("editTitle").value = "";
  document.getElementById("editSku").value = "";
  document.getElementById("editPrice").value = "";
  document.getElementById("editWeightKg").value = "";
  document.getElementById("editOldPrice").value = "";
  document.getElementById("editDesc").value = "";
  document.getElementById("editInStock").checked = true;

  modalGalleryImages = ["art/placeholder.jpg"];
  renderModalGallery();

  document.getElementById("editVideo").value = "";
  updateVideoPreview();

  document.getElementById("editColors").value = "";

  document.getElementById("productModal").classList.add("active");
}
function closeProductModal() { document.getElementById("productModal").classList.remove("active"); }

function saveProductModal() {
  const id = document.getElementById("editProductId").value;
  const title = document.getElementById("editTitle").value.trim();
  const brand = document.getElementById("editBrand").value;
  const cat = document.getElementById("editCategory").value;
  const sku = document.getElementById("editSku").value.trim();
  const price = Number(document.getElementById("editPrice").value) || 0;
  const weightKg = Number(document.getElementById("editWeightKg").value) || 0;
  const oldPrice = Number(document.getElementById("editOldPrice").value) || 0;
  const desc = document.getElementById("editDesc").value.trim();
  const inStock = document.getElementById("editInStock").checked;
  const video = document.getElementById("editVideo").value.trim();
  const coverImage = modalGalleryImages[0] || document.getElementById("editImage").value || "art/placeholder.jpg";

  if (!title || !price) { alert("Please provide product title and price."); return; }

  const colorsRaw = document.getElementById("editColors").value.trim();
  const colorList = colorsRaw ? colorsRaw.split(",").map(c => c.trim()).filter(Boolean) : [];
  const options = colorList.length > 0 ? [{ name: "Color", values: colorList }] : [];
  const hasOptions = options.length > 0;

  const prods = catalogData.products;
  let existing = prods.find(x => x.id === id);
  if (existing) {
    existing.name = title;
    existing.brand = brand;
    existing.category = cat;
    existing.cat = cat;
    existing.sku = sku;
    existing.price = price;
    existing.weightKg = weightKg > 0 ? weightKg : undefined;
    existing.compareAt = oldPrice;
    existing.old = oldPrice;
    existing.image = coverImage;
    existing.img = coverImage;
    existing.images = modalGalleryImages.length ? modalGalleryImages.slice() : [coverImage];
    existing.video = video;
    existing.videoUrl = video;
    existing.hasOptions = hasOptions;
    existing.options = options;
    existing.description = desc;
    existing.desc = desc;
    existing.inStock = inStock;
  } else {
    prods.unshift({
      id, name: title, brand, category: cat, cat, sku, price, weightKg: weightKg > 0 ? weightKg : undefined,
      compareAt: oldPrice, old: oldPrice, image: coverImage, img: coverImage,
      images: modalGalleryImages.length ? modalGalleryImages.slice() : [coverImage],
      video, videoUrl: video, hasOptions, options,
      description: desc, desc, inStock,
      tags: [brand.toLowerCase().replace(/\s+/g, '-'), cat.toLowerCase().replace(/\s+/g, '-')]
    });
  }

  toast("Product saved successfully.");
  closeProductModal();
  renderProducts();
  syncAllToLive();
}

function deleteProduct(prodId) {
  if (!confirm("Are you sure you want to delete this product?")) return;
  catalogData.products = (catalogData.products || []).filter(p => p.id !== prodId);
  toast("Product deleted.");
  renderProducts();
  syncAllToLive();
}

// ================= CATEGORIES & COLLECTIONS =================
function categorySlug(value) {
  return String(value || "")
    .trim()
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-+|-+$/g, "");
}

function categoryProductCount(name) {
  const needle = String(name || "").trim().toLowerCase();
  return (catalogData?.products || []).filter(p =>
    String(p.category || p.cat || "").trim().toLowerCase() === needle
  ).length;
}

function renderCategories() {
  const tbody = document.getElementById("categoriesTableBody");
  const cats = Array.isArray(catalogData?.categories) ? catalogData.categories : [];

  if (!cats.length) {
    tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;color:var(--slate-400);padding:34px">
      No categories found in the database catalog. Click “＋ Add Category” to create one.
    </td></tr>`;
    return;
  }

  tbody.innerHTML = cats.map(c => {
    const name = String(c.name || "").trim();
    const encodedName = encodeURIComponent(name);
    const count = categoryProductCount(name);
    return `
      <tr>
        <td><img src="${c.image || 'art/placeholder.jpg'}" style="width:40px;height:40px;border-radius:8px;object-fit:cover;border:1px solid var(--slate-200)" onerror="this.onerror=null;this.src='art/placeholder.jpg'"></td>
        <td>
          <b>${adminEsc(name)}</b>
          <div style="font-size:11px;color:var(--slate-500);margin-top:3px">${count} product${count === 1 ? '' : 's'}</div>
        </td>
        <td><code>${adminEsc(c.handle || categorySlug(name))}</code></td>
        <td style="color:var(--slate-500);max-width:300px">${adminEsc(c.description || c.desc || 'No description')}</td>
        <td>
          <div style="display:flex;gap:6px;flex-wrap:wrap">
            <button class="btn-action" onclick="editCategory(decodeURIComponent('${encodedName}'))">Edit</button>
            <button class="btn-action btn-del" onclick="deleteCategory(decodeURIComponent('${encodedName}'))">Delete</button>
          </div>
        </td>
      </tr>
    `;
  }).join("");
}

async function openAddCategoryModal() {
  if (!catalogData) return;

  const rawName = prompt("Enter Category Name (e.g. Baby Gear, Feeding Bottles):");
  const name = String(rawName || "").trim();
  if (!name) return;

  const exists = (catalogData.categories || []).some(c =>
    String(c.name || "").trim().toLowerCase() === name.toLowerCase()
  );
  if (exists) {
    toast(`Category "${name}" already exists.`);
    return;
  }

  const rawHandle = prompt("Enter Category Handle / Slug:", categorySlug(name));
  if (rawHandle === null) return;
  const handle = categorySlug(rawHandle || name);
  if (!handle) {
    toast("Category handle cannot be empty.");
    return;
  }

  const handleExists = (catalogData.categories || []).some(c =>
    String(c.handle || categorySlug(c.name)).trim().toLowerCase() === handle.toLowerCase()
  );
  if (handleExists) {
    toast(`Category handle "${handle}" already exists.`);
    return;
  }

  const desc = prompt("Enter Category Description:", "") ?? "";
  const image = prompt("Category Image URL (optional):", "art/placeholder.jpg");
  if (image === null) return;

  if (!Array.isArray(catalogData.categories)) catalogData.categories = [];
  catalogData.categories.push({
    name,
    handle,
    description: String(desc || "").trim(),
    image: String(image || "").trim() || "art/placeholder.jpg"
  });

  initCategorySelects();
  renderCategories();
  renderProducts();
  await syncAllToLive();
  toast(`Category "${name}" added and saved.`);
}

async function editCategory(name) {
  if (!catalogData) return;
  const categories = Array.isArray(catalogData.categories) ? catalogData.categories : [];
  const category = categories.find(c =>
    String(c.name || "").trim().toLowerCase() === String(name || "").trim().toLowerCase()
  );
  if (!category) {
    toast("Category not found.");
    return;
  }

  const oldName = String(category.name || "").trim();
  const rawName = prompt("Edit Category Name:", oldName);
  if (rawName === null) return;
  const newName = String(rawName || "").trim();
  if (!newName) {
    toast("Category name cannot be empty.");
    return;
  }

  const duplicateName = categories.some(c =>
    c !== category && String(c.name || "").trim().toLowerCase() === newName.toLowerCase()
  );
  if (duplicateName) {
    toast(`Category "${newName}" already exists.`);
    return;
  }

  const rawHandle = prompt("Edit Handle / Slug:", category.handle || categorySlug(newName));
  if (rawHandle === null) return;
  const newHandle = categorySlug(rawHandle || newName);
  if (!newHandle) {
    toast("Category handle cannot be empty.");
    return;
  }

  const duplicateHandle = categories.some(c =>
    c !== category &&
    String(c.handle || categorySlug(c.name)).trim().toLowerCase() === newHandle.toLowerCase()
  );
  if (duplicateHandle) {
    toast(`Category handle "${newHandle}" already exists.`);
    return;
  }

  const newDesc = prompt(
    "Edit Category Description:",
    category.description || category.desc || ""
  );
  if (newDesc === null) return;

  const newImage = prompt(
    "Edit Category Image URL:",
    category.image || "art/placeholder.jpg"
  );
  if (newImage === null) return;

  category.name = newName;
  category.handle = newHandle;
  category.description = String(newDesc || "").trim();
  category.desc = category.description;
  category.image = String(newImage || "").trim() || "art/placeholder.jpg";

  // Keep all existing products in sync when a category is renamed.
  if (oldName.toLowerCase() !== newName.toLowerCase()) {
    (catalogData.products || []).forEach(p => {
      const current = String(p.category || p.cat || "").trim();
      if (current.toLowerCase() === oldName.toLowerCase()) {
        p.category = newName;
        p.cat = newName;
      }
    });
  }

  initCategorySelects();
  renderCategories();
  renderProducts();
  await syncAllToLive();
  toast(`Category "${oldName}" updated to "${newName}".`);
}

async function deleteCategory(name) {
  if (!catalogData) return;
  const usedBy = categoryProductCount(name);

  if (usedBy > 0) {
    alert(`Cannot delete "${name}" because ${usedBy} product${usedBy === 1 ? '' : 's'} currently use this category.\n\nReassign those products to another category first, then delete it.`);
    return;
  }

  if (!confirm(`Delete category "${name}" permanently?`)) return;

  catalogData.categories = (catalogData.categories || []).filter(c =>
    String(c.name || "").trim().toLowerCase() !== String(name || "").trim().toLowerCase()
  );

  initCategorySelects();
  renderCategories();
  renderProducts();
  await syncAllToLive();
  toast(`Category "${name}" deleted.`);
}

// ================= HOMEPAGE & THEME CUSTOMIZER =================
function populateThemeForm() {
  const s = catalogData?.settings || {};
  const h = catalogData?.homeData?.hero || {};

  document.getElementById("themeAnnouncement").value = s.announcement || "Everything parenting. One trusted hub.";
  document.getElementById("themeAnnouncementLink").value = s.announcementLink || "shop.html";
  document.getElementById("themeHeroHeadline").value = h.headline || "Their little world.<br><em>Your whole heart.</em>";
  document.getElementById("themeHeroLede").value = h.lede || "Shop essentials, learn what comes next, and find parents walking the same road — all in one warm Pakistani space.";
  document.getElementById("themeHeroBtnText").value = h.btnText || "Explore the shop";
  document.getElementById("themeHeroBtnLink").value = h.btnLink || "shop.html";

  renderSlides();
}

function saveThemeCustomizer() {
  if (!catalogData.settings) catalogData.settings = {};
  if (!catalogData.homeData) catalogData.homeData = {};
  if (!catalogData.homeData.hero) catalogData.homeData.hero = {};

  catalogData.settings.announcement = document.getElementById("themeAnnouncement").value.trim();
  catalogData.settings.announcementLink = document.getElementById("themeAnnouncementLink").value.trim();

  catalogData.homeData.hero.headline = document.getElementById("themeHeroHeadline").value.trim();
  catalogData.homeData.hero.lede = document.getElementById("themeHeroLede").value.trim();
  catalogData.homeData.hero.btnText = document.getElementById("themeHeroBtnText").value.trim();
  catalogData.homeData.hero.btnLink = document.getElementById("themeHeroBtnLink").value.trim();

  syncAllToLive();
  toast("Homepage content & announcement updated!");
}

function renderSlides() {
  const container = document.getElementById("slidesListContainer");
  const slides = catalogData?.homeData?.slides || [];
  if (!slides.length) {
    container.innerHTML = `<p style="font-size:13px;color:var(--slate-500)">No carousel slides created yet. Click "＋ Add Slide" to create slides for the homepage carousel.</p>`;
    return;
  }
  container.innerHTML = slides.map((s, idx) => `
    <div style="display:flex;align-items:center;gap:14px;padding:12px;border:1px solid var(--slate-200);border-radius:10px;margin-bottom:8px;background:var(--slate-50)">
      <img src="${s.image}" style="width:60px;height:45px;border-radius:6px;object-fit:cover;border:1px solid var(--slate-200)">
      <div style="flex:1">
        <b style="font-size:14px">${s.title}</b>
        <div style="font-size:12px;color:var(--slate-500)">${s.tag} · ${s.desc}</div>
      </div>
      <button class="btn-action" onclick="openEditSlideModal(${idx})">Edit</button>
      <button class="btn-action btn-del" onclick="deleteSlide(${idx})">Delete</button>
    </div>
  `).join("");
}

function openAddSlideModal() {
  document.getElementById("editSlideIdx").value = "-1";
  document.getElementById("editSlideTag").value = "Feeding edit";
  document.getElementById("editSlideTitle").value = "Little things. Lots of love.";
  document.getElementById("editSlideDesc").value = "Bottles, teats and first-sip cups — chosen with hospital-grade advice.";
  document.getElementById("editSlideBtnText").value = "Shop feeding ↗";
  document.getElementById("editSlideLink").value = "shop.html?cat=Feeding%20Bottles";
  document.getElementById("editSlideImage").value = "assets/img/slider1.jpg";
  document.getElementById("slideModal").classList.add("active");
}

function openEditSlideModal(idx) {
  const s = catalogData.homeData.slides[idx];
  if (!s) return;
  document.getElementById("editSlideIdx").value = idx;
  document.getElementById("editSlideTag").value = s.tag || "";
  document.getElementById("editSlideTitle").value = s.title || "";
  document.getElementById("editSlideDesc").value = s.desc || "";
  document.getElementById("editSlideBtnText").value = s.btnText || "";
  document.getElementById("editSlideLink").value = s.link || "";
  document.getElementById("editSlideImage").value = s.image || "";
  document.getElementById("slideModal").classList.add("active");
}
function closeSlideModal() { document.getElementById("slideModal").classList.remove("active"); }

function saveSlideModal() {
  const idx = parseInt(document.getElementById("editSlideIdx").value, 10);
  const slide = {
    tag: document.getElementById("editSlideTag").value.trim(),
    title: document.getElementById("editSlideTitle").value.trim(),
    desc: document.getElementById("editSlideDesc").value.trim(),
    btnText: document.getElementById("editSlideBtnText").value.trim(),
    link: document.getElementById("editSlideLink").value.trim(),
    image: document.getElementById("editSlideImage").value.trim()
  };

  if (!catalogData.homeData.slides) catalogData.homeData.slides = [];
  if (idx >= 0) {
    catalogData.homeData.slides[idx] = slide;
  } else {
    catalogData.homeData.slides.push(slide);
  }
  closeSlideModal();
  renderSlides();
  syncAllToLive();
  toast("Slide saved.");
}

function deleteSlide(idx) {
  if (!confirm("Delete this slide?")) return;
  catalogData.homeData.slides.splice(idx, 1);
  renderSlides();
  syncAllToLive();
  toast("Slide deleted.");
}

// ================= LEARN / GUIDES DATABASE CMS =================
let learnAdminArticles = [];
let learnAdminLoaded = false;

async function learnAdminApi(url, options = {}) {
  const opts = { credentials:'same-origin', ...options };
  opts.headers = {
    ...(options.body instanceof FormData ? {} : {'Content-Type':'application/json'}),
    'X-Requested-With':'XMLHttpRequest',
    ...(options.headers || {})
  };
  const res = await fetch(url, opts);
  let data = {};
  try { data = await res.json(); } catch(e) {}
  if(!res.ok || data.success === false) throw new Error(data.message || `Request failed (${res.status})`);
  return data;
}

function articleAdminEsc(v){
  return String(v ?? '').replace(/[&<>"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[ch]));
}

async function renderArticles(force=false) {
  const tbody = document.getElementById("articlesTableBody");
  if(!tbody) return;
  if(!learnAdminLoaded || force){
    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:24px">Loading Learn guides from database…</td></tr>`;
    try{
      const data = await learnAdminApi('/api/admin/learn/articles');
      learnAdminArticles = Array.isArray(data.articles) ? data.articles : [];
      learnAdminLoaded = true;
    }catch(err){
      tbody.innerHTML = `<tr><td colspan="6" style="color:#b42318;padding:20px">${articleAdminEsc(err.message)}</td></tr>`;
      return;
    }
  }
  tbody.innerHTML = learnAdminArticles.length ? learnAdminArticles.map(a => `
    <tr>
      <td><img src="${articleAdminEsc(a.cover || '/art/placeholder.jpg')}" style="width:40px;height:40px;border-radius:6px;object-fit:cover;border:1px solid var(--slate-200)" onerror="this.onerror=null;this.src='/art/placeholder.jpg'"></td>
      <td><b>${articleAdminEsc(a.title)}</b><br><span style="font-size:12px;color:var(--slate-500)">${articleAdminEsc(a.excerpt || '')}</span></td>
      <td><span class="badge" style="background:var(--slate-100);color:var(--slate-700)">${articleAdminEsc(a.category || 'Guide')}</span></td>
      <td>${articleAdminEsc(a.author || 'ParentingHub Editorial')}</td>
      <td>${articleAdminEsc(a.readTime || '4 min')}</td>
      <td><div style="display:flex;gap:6px"><button class="btn-action" onclick="openEditArticleModal('${articleAdminEsc(a.id)}')">Edit</button><button class="btn-action btn-del" onclick="deleteArticle('${articleAdminEsc(a.id)}')">✕</button></div></td>
    </tr>`).join('') : `<tr><td colspan="6" style="text-align:center;padding:24px">No Learn guides found in database.</td></tr>`;
}

function learnTodayLabel(){
  return new Date().toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'}).replace(/,/g,'');
}

function openAddArticleModal() {
  document.getElementById("modalArticleTitle").textContent = "New Parenting Guide";
  document.getElementById("editArtId").value = "";
  document.getElementById("editArtTitle").value = "";
  document.getElementById("editArtCat").value = "Feeding";
  document.getElementById("editArtAuthor").value = "ParentingHub Editorial";
  document.getElementById("editArtDate").value = learnTodayLabel();
  document.getElementById("editArtRead").value = "5 min";
  document.getElementById("editArtImage").value = "/art/placeholder.jpg";
  document.getElementById("editArtExcerpt").value = "";
  document.getElementById("editArtBody").value = "";
  const f=document.getElementById('editArtImageFile'); if(f) f.value='';
  const st=document.getElementById('editArtUploadStatus'); if(st) st.textContent='Optional: upload cover from computer';
  document.getElementById("articleModal").classList.add("active");
}

function openEditArticleModal(artId) {
  const a = learnAdminArticles.find(x => x.id === artId);
  if (!a) return;
  document.getElementById("modalArticleTitle").textContent = "Edit Learn Guide";
  document.getElementById("editArtId").value = a.id;
  document.getElementById("editArtTitle").value = a.title || "";
  document.getElementById("editArtCat").value = a.category || "Guide";
  document.getElementById("editArtAuthor").value = a.author || "ParentingHub Editorial";
  document.getElementById("editArtDate").value = a.date || "";
  document.getElementById("editArtRead").value = a.readTime || "4 min";
  document.getElementById("editArtImage").value = a.cover || "/art/placeholder.jpg";
  document.getElementById("editArtExcerpt").value = a.excerpt || "";
  document.getElementById("editArtBody").value = Array.isArray(a.body) ? a.body.join("\n\n") : "";
  const f=document.getElementById('editArtImageFile'); if(f) f.value='';
  const st=document.getElementById('editArtUploadStatus'); if(st) st.textContent='Optional: upload a replacement cover';
  document.getElementById("articleModal").classList.add("active");
}
function closeArticleModal() { document.getElementById("articleModal").classList.remove("active"); }

async function uploadLearnCoverFromPc(file){
  if(!file) return;
  const status = document.getElementById('editArtUploadStatus');
  try{
    if(status) status.textContent='Uploading cover…';
    const url = await uploadAdminFileFromPc(file, status);
    document.getElementById('editArtImage').value = url;
    if(status) status.textContent='Cover uploaded ✓';
  }catch(err){
    if(status) status.textContent='Upload failed: '+err.message;
    toast(err.message);
  }
}

async function saveArticleModal() {
  const id = document.getElementById("editArtId").value.trim();
  const title = document.getElementById("editArtTitle").value.trim();
  const category = document.getElementById("editArtCat").value.trim();
  const author = document.getElementById("editArtAuthor").value.trim();
  const date = document.getElementById("editArtDate").value.trim();
  const readTime = document.getElementById("editArtRead").value.trim();
  const cover = document.getElementById("editArtImage").value.trim();
  const excerpt = document.getElementById("editArtExcerpt").value.trim();
  const body = document.getElementById("editArtBody").value.split(/\n\s*\n/).map(x=>x.trim()).filter(Boolean);
  if(!title){ toast('Please provide guide title.'); return; }
  if(!excerpt){ toast('Please provide a short excerpt / summary.'); return; }
  const payload = {title,category,author,date,readTime,cover,excerpt,body};
  if(id){ payload._action='update'; payload.id=id; }
  try{
    await learnAdminApi('/api/admin/learn/articles',{method:'POST',body:JSON.stringify(payload)});
    closeArticleModal();
    learnAdminLoaded=false;
    await renderArticles(true);
    toast(id ? 'Learn guide updated in database.' : 'Learn guide published to database.');
  }catch(err){ toast(err.message); }
}

async function deleteArticle(artId) {
  if (!confirm("Delete this Learn guide from database?")) return;
  try{
    await learnAdminApi('/api/admin/learn/articles',{method:'POST',body:JSON.stringify({_action:'delete',id:artId})});
    learnAdminLoaded=false;
    await renderArticles(true);
    toast("Learn guide deleted.");
  }catch(err){ toast(err.message); }
}

// ================= FAQS =================
function renderFaqs() {
  const container = document.getElementById("faqsListContainer");
  const faqs = catalogData?.faqs || [];
  if (!faqs.length) {
    container.innerHTML = `<p style="font-size:13px;color:var(--slate-500)">No FAQs added yet.</p>`;
    return;
  }
  container.innerHTML = faqs.map(f => `
    <div style="padding:14px;border:1px solid var(--slate-200);border-radius:12px;margin-bottom:10px;background:#ffffff">
      <div style="display:flex;justify-content:space-between;align-items:flex-start">
        <b style="font-size:14.5px;color:var(--slate-900)">Q: ${f.q || f.question}</b>
        <div style="display:flex;gap:6px">
          <button class="btn-action" onclick="openEditFaqModal('${f.id}')">Edit</button>
          <button class="btn-action btn-del" onclick="deleteFaq('${f.id}')">✕</button>
        </div>
      </div>
      <p style="font-size:13px;color:var(--slate-600);margin-top:6px;line-height:1.5">A: ${f.a || f.answer}</p>
    </div>
  `).join("");
}

function openAddFaqModal() {
  document.getElementById("modalFaqTitle").textContent = "Add FAQ";
  document.getElementById("editFaqId").value = "faq_" + Date.now();
  document.getElementById("editFaqQ").value = "";
  document.getElementById("editFaqA").value = "";
  document.getElementById("faqModal").classList.add("active");
}

function openEditFaqModal(id) {
  const f = (catalogData?.faqs || []).find(x => x.id === id);
  if (!f) return;
  document.getElementById("modalFaqTitle").textContent = "Edit FAQ";
  document.getElementById("editFaqId").value = f.id;
  document.getElementById("editFaqQ").value = f.q || f.question || "";
  document.getElementById("editFaqA").value = f.a || f.answer || "";
  document.getElementById("faqModal").classList.add("active");
}
function closeFaqModal() { document.getElementById("faqModal").classList.remove("active"); }

function saveFaqModal() {
  const id = document.getElementById("editFaqId").value;
  const q = document.getElementById("editFaqQ").value.trim();
  const a = document.getElementById("editFaqA").value.trim();
  if (!q || !a) { alert("Please provide question and answer."); return; }

  if (!catalogData.faqs) catalogData.faqs = [];
  const existing = catalogData.faqs.find(x => x.id === id);
  if (existing) {
    existing.q = q; existing.question = q;
    existing.a = a; existing.answer = a;
  } else {
    catalogData.faqs.push({ id, q, question: q, a, answer: a });
  }

  closeFaqModal();
  renderFaqs();
  syncAllToLive();
  toast("FAQ saved.");
}

function deleteFaq(id) {
  if (!confirm("Delete this FAQ?")) return;
  catalogData.faqs = (catalogData.faqs || []).filter(f => f.id !== id);
  renderFaqs();
  syncAllToLive();
  toast("FAQ deleted.");
}

// ================= COUPONS & DISCOUNTS =================
function renderCoupons() {
  const tbody = document.getElementById("couponsTableBody");
  const coupons = catalogData?.coupons || [];
  if (!coupons.length) {
    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;color:var(--slate-400);padding:30px">No coupons active. Click "＋ Create Coupon" to add promotional discounts.</td></tr>`;
    return;
  }
  tbody.innerHTML = coupons.map((c, idx) => `
    <tr>
      <td><b><code style="font-size:14px;color:var(--primary)">${c.code}</code></b></td>
      <td><span class="badge" style="background:var(--slate-100);color:var(--slate-700)">${c.discountPercent ? 'Percentage' : 'Fixed'}</span></td>
      <td><b>${c.discountPercent ? c.discountPercent + '%' : ('Rs ' + (c.discountAmount || 0))}</b></td>
      <td>Rs ${(c.minSpend || c.minOrder || 0).toLocaleString("en-PK")}</td>
      <td style="color:var(--slate-600)">${c.desc || ''}</td>
      <td>
        <button class="btn-action btn-del" onclick="deleteCoupon(${idx})">Delete</button>
      </td>
    </tr>
  `).join("");
}

function openAddCouponModal() {
  document.getElementById("editCouponCode").value = "";
  document.getElementById("editCouponVal").value = "";
  document.getElementById("editCouponMin").value = "1500";
  document.getElementById("editCouponDesc").value = "";
  document.getElementById("couponModal").classList.add("active");
}
function closeCouponModal() { document.getElementById("couponModal").classList.remove("active"); }

function saveCouponModal() {
  const code = document.getElementById("editCouponCode").value.toUpperCase().trim();
  const type = document.getElementById("editCouponType").value;
  const val = Number(document.getElementById("editCouponVal").value) || 0;
  const min = Number(document.getElementById("editCouponMin").value) || 0;
  const desc = document.getElementById("editCouponDesc").value.trim();

  if (!code) { alert("Please provide coupon code."); return; }

  const coupon = { code, minSpend: min, desc: desc || `Discount with code ${code}`, active: true };
  if (type === "percent") coupon.discountPercent = val;
  else if (type === "fixed") coupon.discountAmount = val;
  else coupon.discountAmount = val;

  if (!catalogData.coupons) catalogData.coupons = [];
  catalogData.coupons.push(coupon);

  closeCouponModal();
  renderCoupons();
  syncAllToLive();
  toast(`Coupon ${code} created!`);
}

function deleteCoupon(idx) {
  if (!confirm("Delete this coupon?")) return;
  catalogData.coupons.splice(idx, 1);
  renderCoupons();
  syncAllToLive();
  toast("Coupon deleted.");
}

// ================= INQUIRIES =================
function renderInquiries() {
  const tbody = document.getElementById("inquiriesTableBody");
  if (!allInquiries.length) {
    tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;color:var(--slate-400);padding:30px">No customer inquiries submitted yet.</td></tr>`;
    return;
  }
  tbody.innerHTML = allInquiries.map(inq => {
    const d = inq.createdAt ? new Date(inq.createdAt).toLocaleDateString("en-PK") : "Recent";
    return `
      <tr>
        <td><b>#${inq.id}</b></td>
        <td><span style="font-size:12px;color:var(--slate-500)">${d}</span></td>
        <td><b>${inq.name}</b></td>
        <td>${inq.contact}</td>
        <td><span class="badge" style="background:var(--slate-100);color:var(--slate-700)">${inq.topic || 'General'}</span></td>
        <td style="max-width:240px;line-height:1.4">${inq.message}</td>
        <td>
          <select class="form-select" style="padding:4px 8px;font-size:12px;width:auto" onchange="updateInquiryStatus('${inq.id}', this.value)">
            <option value="new" ${inq.status === 'new' ? 'selected' : ''}>New</option>
            <option value="in-progress" ${inq.status === 'in-progress' ? 'selected' : ''}>In Progress</option>
            <option value="resolved" ${inq.status === 'resolved' ? 'selected' : ''}>Resolved</option>
          </select>
        </td>
        <td>
          <div style="display:flex;gap:6px">
            <button class="btn-action btn-del" onclick="deleteInquiry('${inq.id}')">✕</button>
          </div>
        </td>
      </tr>
    `;
  }).join("");
}

async function updateInquiryStatus(id, status) {
  try {
    await fetch(`/api/inquiries/${id}`, {
      method: "PATCH",
      headers: { "Content-Type": "application/json", "X-Requested-With": "XMLHttpRequest" },
      body: JSON.stringify({ status })
    });
    toast(`Inquiry marked as ${status}`);
  } catch(e){}
}

async function deleteInquiry(id) {
  if (!confirm("Delete this inquiry?")) return;
  try {
    await fetch(`/api/inquiries/${id}`, { method: "DELETE", headers: { "X-Requested-With": "XMLHttpRequest" } });
  } catch(e){}
  allInquiries = allInquiries.filter(i => i.id !== id);
  toast("Inquiry deleted.");
  renderInquiries();
}

// ================= STORE SETTINGS =================
function populateSettingsForm() {
  const s = catalogData?.settings || {};
  document.getElementById("cfgSiteName").value = s.siteName || "ParentingHub";
  document.getElementById("cfgTagline").value = s.tagline || "Shop • Learn • Connect • Grow";
  document.getElementById("cfgEmail").value = s.email || "support@parentinghub.pk";
  document.getElementById("cfgWhatsapp").value = s.whatsapp || "923293007360";
  document.getElementById("cfgShippingFee").value = 200;
  document.getElementById("cfgBankName").value = s.bankName || "Faysal Bank";
  document.getElementById("cfgAccountTitle").value = s.accountTitle || "SHAGUFTA KANWAL SHEERAZI";
  document.getElementById("cfgIban").value = s.iban || "PK23FAYS3207575000006005";
  document.getElementById("cfgBranch").value = s.branch || "IBB SOLDIER BAZAR, KARACHI";
  document.getElementById("cfgShippingPolicy").value = "Fixed PKR 200 standard delivery charge on every order. No free delivery and no weight-based pricing.";
  document.getElementById("cfgReturnPolicy").value = s.returnPolicy || "7-day return policy for sealed and unopened baby products.";
}

function saveStoreSettings() {
  if (!catalogData.settings) catalogData.settings = {};
  catalogData.settings.siteName = document.getElementById("cfgSiteName").value.trim();
  catalogData.settings.tagline = document.getElementById("cfgTagline").value.trim();
  catalogData.settings.email = document.getElementById("cfgEmail").value.trim();
  catalogData.settings.whatsapp = document.getElementById("cfgWhatsapp").value.trim();
  catalogData.settings.shippingFee = 200;
  catalogData.settings.deliveryPolicy = "fixed";
  delete catalogData.settings.deliveryRatePerKg;
  delete catalogData.settings.defaultProductWeightKg;
  delete catalogData.settings.freeShippingAbove;
  catalogData.settings.bankName = document.getElementById("cfgBankName").value.trim();
  catalogData.settings.accountTitle = document.getElementById("cfgAccountTitle").value.trim();
  catalogData.settings.iban = document.getElementById("cfgIban").value.trim();
  catalogData.settings.branch = document.getElementById("cfgBranch").value.trim();
  catalogData.settings.shippingPolicy = "Fixed PKR 200 standard delivery charge on every order. No free delivery and no weight-based pricing.";
  catalogData.settings.returnPolicy = document.getElementById("cfgReturnPolicy").value.trim();

  syncAllToLive();
  toast("Store configuration saved successfully!");
}
</script>
</body>
</html>
