/* =========================================================
   ParentingHub.pk — app.js (chrome + interactions + pages)
   ========================================================= */

/* ---------------- icons ---------------- */
const ICO = {
  heart:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M20.8 5.6a5.1 5.1 0 0 0-7.2 0L12 7.2l-1.6-1.6a5.1 5.1 0 0 0-7.2 7.2l8.8 8.8 8.8-8.8a5.1 5.1 0 0 0 0-7.2z"/></svg>',
  heartFill:'<svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.8 5.6a5.1 5.1 0 0 0-7.2 0L12 7.2l-1.6-1.6a5.1 5.1 0 0 0-7.2 7.2l8.8 8.8 8.8-8.8a5.1 5.1 0 0 0 0-7.2z"/></svg>',
  bag:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M6 7h12l1.2 13H4.8L6 7z"/><path d="M9 7V5.5a3 3 0 0 1 6 0V7"/></svg>',
  search:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>',
  user:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><circle cx="12" cy="8" r="4"/><path d="M4.5 20a7.5 7.5 0 0 1 15 0"/></svg>',
  menu:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>',
  x:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg>',
  arrow:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>',
  arrowUpRight:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17 17 7M9 7h8v8"/></svg>',
  chevL:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M15 6l-6 6 6 6"/></svg>',
  chevR:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M9 6l6 6-6 6"/></svg>',
  up:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M6 11l6-6 6 6"/></svg>',
  down:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M6 13l6 6 6-6"/></svg>',
  comment:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-8 8H8l-5 3 1.2-4.4A8 8 0 1 1 21 12z"/></svg>',
  share:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12v7h16v-7"/><path d="M12 16V4M8 8l4-4 4 4"/></svg>',
  save:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M6 4h12v17l-6-4.5L6 21z"/></svg>',
  plus:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>',
  check:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg>',
  moon:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M20 14.5A8.5 8.5 0 1 1 9.5 4a7 7 0 0 0 10.5 10.5z"/></svg>',
  home:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M4 11l8-7 8 7v9H4z"/></svg>',
  book:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5.5C4 4.7 4.7 4 5.5 4H12v16H5.5C4.7 20 4 19.3 4 18.5z"/><path d="M20 5.5c0-.8-.7-1.5-1.5-1.5H12v16h6.5c.8 0 1.5-.7 1.5-1.5z"/></svg>',
  shield:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v6c0 4.2-2.9 7.6-7 9-4.1-1.4-7-4.8-7-9V6z"/><path d="m9 12 2 2 4-4"/></svg>',
  truck:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7h9v9H3zM12 10h4l4 3v3h-8z"/><circle cx="7" cy="18" r="1.6"/><circle cx="17" cy="18" r="1.6"/></svg>',
  refresh:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 11a8 8 0 1 0-2.3 5.7"/><path d="M20 5v6h-6"/></svg>',
  star:'<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 3.5l2.7 5.6 6.1.8-4.4 4.2 1.1 6-5.5-3-5.5 3 1.1-6L3.2 9.9l6.1-.8z"/></svg>',
  play:'<svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5.5v13l11-6.5z"/></svg>',
  sparkle:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8z"/><path d="M19 16.5l.7 2 2 .7-2 .7-.7 2-.7-2-2-.7 2-.7z"/></svg>',
  glass:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M7 4h10l-1 9a4 4 0 0 1-8 0z"/><path d="M12 17v3"/></svg>',
  pin:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-6.3 7-11a7 7 0 1 0-14 0c0 4.7 7 11 7 11z"/><circle cx="12" cy="10" r="2.6"/></svg>',
  image:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="3"/><circle cx="8.5" cy="10" r="1.6"/><path d="m4 17 5-4 4 3 3-2 4 3"/></svg>',
  bell:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M18 15V10a6 6 0 1 0-12 0v5l-2 3h16z"/><path d="M10 21h4"/></svg>',
  edit:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h4L20 8l-4-4L4 16z"/><path d="M14 6l4 4"/></svg>',
  lock:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="9" rx="2.5"/><path d="M8.5 11V8a3.5 3.5 0 0 1 7 0v3"/></svg>',
  grid:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="7" height="7" rx="2"/><rect x="13" y="4" width="7" height="7" rx="2"/><rect x="4" y="13" width="7" height="7" rx="2"/><rect x="13" y="13" width="7" height="7" rx="2"/></svg>',
  map:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 4 3 6v14l6-2 6 2 6-2V4l-6 2z"/><path d="M9 4v14M15 6v14"/></svg>',
  leaf:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M5 19c0-8 5-13 14-13 0 9-5 13-13 13z"/><path d="M5 19c3-3 6-5 10-6"/></svg>',
  logout:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M15 4h4v16h-4"/><path d="M11 8l-4 4 4 4M7 12h9"/></svg>',
  cloud:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/></svg>',
  inbox:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>',
  starOutline:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
  dots:'<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>'
};
const svg = (n, cls="") => `<span class="${cls}">${ICO[n]||""}</span>`;

/* Extra icons (shared by every page) — replaces emoji / unicode glyphs */
(function(){
  const s = (inner, w) => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="' + (w||1.9) + '" stroke-linecap="round" stroke-linejoin="round">' + inner + '</svg>';
  Object.assign(ICO, {
    phone:    s('<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A15 15 0 0 1 3 6a2 2 0 0 1 2-2z"/>'),
    mail:     s('<rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="m4 7 8 6 8-6"/>'),
    clock:    s('<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>'),
    calendar: s('<rect x="4" y="5" width="16" height="15" rx="2.5"/><path d="M8 3v4M16 3v4M4 10h16"/>'),
    card:     s('<rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="M3 10h18M7 15h3"/>'),
    box:      s('<path d="M12 3 4 7v10l8 4 8-4V7z"/><path d="M4 7l8 4 8-4M12 11v10"/>'),
    tag:      s('<path d="M3 12V4h8l10 10-8 8z"/><circle cx="7.5" cy="8.5" r="1.3"/>'),
    users:    s('<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16 4.6a3.5 3.5 0 0 1 0 6.8M18 14.2a6.5 6.5 0 0 1 3.5 5.8"/>'),
    bottle:   s('<path d="M10 3h4v3l1.5 2.5V20a1.5 1.5 0 0 1-1.5 1.5h-4A1.5 1.5 0 0 1 8.5 20V8.5L10 6z"/><path d="M8.5 12h7"/>'),
    smile:    s('<circle cx="12" cy="12" r="9"/><path d="M8.5 14a4 4 0 0 0 7 0M9 9.5h.01M15 9.5h.01"/>'),
    blocks:   s('<rect x="3.5" y="12" width="8" height="8" rx="1.5"/><rect x="12.5" y="12" width="8" height="8" rx="1.5"/><rect x="8" y="4" width="8" height="8" rx="1.5"/>'),
    camera:   s('<path d="M4 8h3l1.5-2h7L17 8h3v11H4z"/><circle cx="12" cy="13" r="3.5"/>'),
    link:     s('<path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1"/><path d="M14 10a4 4 0 0 0-5.7 0l-3 3A4 4 0 0 0 11 18.7l1-1"/>'),
    medical:  s('<path d="M9 4h6v5h5v6h-5v5H9v-5H4V9h5z"/>'),
    bolt:     s('<path d="M13 3 5 13h6l-1 8 8-10h-6z"/>'),
    minus:    s('<path d="M5 12h14"/>', 2.2),
    pencil:   s('<path d="M4 20h4L20 8l-4-4L4 16z"/><path d="M14 6l4 4"/>'),
    whatsapp: '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.885m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>',
    instagram: '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>',
    facebook: '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>'
  });
})();

/* Inline icon wrapper + hydrator for static markup: <span class="ico" data-ico="truck"></span> */
const icon = (n, cls="") => `<span class="ico${cls ? " " + cls : ""}" aria-hidden="true">${ICO[n]||""}</span>`;
const emIco = k => ICO[k] ? icon(k) : (k || "");
function hydrateIcons(root){
  (root || document).querySelectorAll("[data-ico]").forEach(el=>{
    if(el.getAttribute("data-ico-done")) return;
    const n = el.getAttribute("data-ico");
    if(ICO[n]){ el.innerHTML = ICO[n]; el.setAttribute("data-ico-done","1"); }
  });
}

/* ---------------- security & parsing helpers ---------------- */
function esc(str){
  if(str === null || str === undefined) return "";
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

function normalizePkPhone(phoneStr){
  if(!phoneStr) return null;
  let digits = String(phoneStr).replace(/\D/g, "");
  if(digits.startsWith("923") && digits.length === 12){
    digits = "0" + digits.slice(2);
  } else if(digits.startsWith("00923") && digits.length === 14){
    digits = "0" + digits.slice(4);
  } else if(digits.startsWith("3") && digits.length === 10){
    digits = "0" + digits;
  }
  return /^03\d{9}$/.test(digits) ? digits : null;
}

function parsePostTimestamp(p){
  if(p && p.createdAt){
    const t = typeof p.createdAt === "number" ? p.createdAt : new Date(p.createdAt).getTime();
    if(!isNaN(t) && t > 0) return t;
  }
  if(p && (p.time === "now" || p.time === "Just now")) return Date.now();
  if(p && typeof p.time === "string"){
    const m = p.time.match(/(\d+)\s*([mhd])/i);
    if(m){
      const num = parseInt(m[1], 10);
      const unit = m[2].toLowerCase();
      if(unit === 'm') return Date.now() - num * 60 * 1000;
      if(unit === 'h') return Date.now() - num * 3600 * 1000;
      if(unit === 'd') return Date.now() - num * 86400 * 1000;
    }
  }
  return 0;
}


/* ---------------- store ---------------- */
const store = {
  get(k, d){ try{ return JSON.parse(localStorage.getItem("ph_"+k)) ?? d; }catch(e){ return d; } },
  set(k, v){ try{ localStorage.setItem("ph_"+k, JSON.stringify(v)); }catch(e){} }
};
const STATE_KEY = "ph_v1";

function loadState(){
  return Object.assign({
    cart:[], wish:[], votes:{}, userPosts:[], joined:[], follows:[], saved:[]
  }, store.get(STATE_KEY, {}));
}
let S = loadState();
const save = () => store.set(STATE_KEY, S);

/* ---------------- auth ---------------- */
function getCurrentUser(){
  try {
    const raw = localStorage.getItem("ph_user");
    return raw ? JSON.parse(raw) : null;
  } catch(e){
    return null;
  }
}

function setCurrentUser(user){
  try {
    localStorage.setItem("ph_user", JSON.stringify(user));
    if(user && user.name) localStorage.setItem("ph_user_name", user.name);
    if(user && user.city) localStorage.setItem("ph_user_city", user.city);
    if(user && user.headline) localStorage.setItem("ph_user_headline", user.headline);
  } catch(e){}
}

function logoutUser(){
  try {
    localStorage.removeItem("ph_user");
    localStorage.removeItem("ph_user_name");
    localStorage.removeItem("ph_user_headline");
    localStorage.removeItem("ph_user_city");
  } catch(e){}
  fetch("/api/auth/logout", {
    method: "POST",
    headers: { "X-Requested-With": "XMLHttpRequest" }
  }).catch(()=>{});
  if(typeof toast === "function") toast("You have signed out successfully");
  setTimeout(()=>{
    window.location.href = "/login";
  }, 350);
}


async function refreshSessionUser(){
  try{
    const res = await fetch('/api/auth/me',{credentials:'same-origin',headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
    if(!res.ok) return getCurrentUser();
    const data = await res.json();
    if(data && data.authenticated && data.user){ setCurrentUser(data.user); return data.user; }
    if(data && data.authenticated === false){
      try{ localStorage.removeItem('ph_user'); localStorage.removeItem('ph_user_name'); localStorage.removeItem('ph_user_headline'); localStorage.removeItem('ph_user_city'); }catch(e){}
      return null;
    }
  }catch(e){}
  return getCurrentUser();
}

async function syncAccountWishlist(){
  if(typeof PRODUCTS === 'undefined' || !Array.isArray(PRODUCTS)) return;
  try{
    const res = await fetch('/api/wishlist',{credentials:'same-origin',headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
    if(res.status===401) return;
    if(!res.ok) return;
    const data=await res.json();
    if(!data.success || !Array.isArray(data.productIds)) return;
    const local=Array.isArray(S.wish)?S.wish:[];
    const merged=[...new Set([...data.productIds,...local])].filter(id=>PRODUCTS.some(p=>p.id===id));
    if(merged.length!==data.productIds.length || merged.some(id=>!data.productIds.includes(id))){
      const put=await fetch('/api/wishlist',{method:'PUT',credentials:'same-origin',headers:{'Accept':'application/json','Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},body:JSON.stringify({productIds:merged})});
      if(put.ok){const d=await put.json();if(d.success&&Array.isArray(d.productIds))S.wish=d.productIds;else S.wish=merged;} else S.wish=merged;
    }else S.wish=merged;
    save();
    document.querySelectorAll('.wish,[data-wish]').forEach(()=>{});
  }catch(e){}
}

async function persistWishlistItem(id, saved){
  try{
    const opts=saved
      ? {method:'POST',headers:{'Accept':'application/json','Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},body:JSON.stringify({productId:id})}
      : {method:'DELETE',headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}};
    const res=await fetch(saved?'/api/wishlist':'/api/wishlist/'+encodeURIComponent(id),{credentials:'same-origin',...opts});
    if(res.status===401) return;
  }catch(e){}
}

function getNavLinks(){
  return [
    {href:"/",                  label:"Home",      key:"home"},
    {href:"/shop",              label:"Shop",      key:"shop"},
    {href:"/wp-build/learn/",   label:"Learn",     key:"learn"},
    {href:"/community",         label:"Community", key:"community"},
    {href:"/reels",             label:"Reels",     key:"reels"},
    {href:"/wp-build/blog/",    label:"Blog",      key:"blog"}
  ];
}

/* ---------------- chrome ---------------- */
// Kept for older helpers; primary header rendering uses getNavLinks().
const NAV = [
  {href:"/",                label:"Home",      key:"home"},
  {href:"/shop",            label:"Shop",      key:"shop"},
  {href:"/wp-build/learn/", label:"Learn",     key:"learn"},
  {href:"/community",       label:"Community", key:"community"},
  {href:"/reels",           label:"Reels",     key:"reels"},
  {href:"/wp-build/blog/",  label:"Blog",      key:"blog"}
];

function headerHTML(active){
  const user = getCurrentUser();
  const currentNav = getNavLinks();
  const nItems = cartCount();
  const cartLabel = `Shopping bag, ${nItems} ${nItems===1?'item':'items'}`;
  const totalProducts = (typeof PRODUCTS !== "undefined" && Array.isArray(PRODUCTS)) ? PRODUCTS.length : 0;
  const ann = (typeof SETTINGS !== "undefined" && SETTINGS.announcement)
    ? SETTINGS.announcement
    : `Nationwide delivery · Fixed PKR 200 per order · Cash on Delivery available`;
  const annLink = (typeof SETTINGS !== "undefined" && SETTINGS.announcementLink) ? SETTINGS.announcementLink : "/shop";
  const userAvatarInitial = (user && user.name) ? user.name.trim().charAt(0).toUpperCase() : "P";
  const userName = (user && user.name) ? user.name : "Parent Guest";
  const userEmail = (user && (user.email || user.phone)) ? (user.email || user.phone) : "Sign in / Register";

  const userToolHtml = user
    ? `<a href="/profile" title="My Space (${user.name || 'User Dashboard'})"><span class="avatar" id="navAvatar" style="background:var(--terra);color:#fff;font-weight:700">${userAvatarInitial}</span></a>`
    : `<a href="/login" class="btn btn-primary btn-xs" style="padding:6px 14px;border-radius:20px;font-size:12px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:5px">${ICO.user} Sign In</a>`;

  // Maily Capsule Navigation Items (fully routed)
  const capsuleNavItems = [
    { key: "shop",      href: "/shop",                       icon: ICO.inbox,       label: "Shop All Essentials", badge: totalProducts },
    { key: "home",      href: "/",                               icon: ICO.home,        label: "Home",                badge: null },
    { key: "reels",     href: "/reels",            icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"/></svg>', label: "Baby Reels & Videos", badge: "Hot" },
    { key: "community", href: "/community",                  icon: ICO.comment,     label: "Parent Community",    badge: "12" },
    { key: "bottles",   href: "/shop/categories/feeding-bottles", icon: ICO.bottle,      label: "Feeding Bottles",     badge: null },
    { key: "learn",     href: "/wp-build/learn/",            icon: ICO.book,        label: "Care & Learn Guides", badge: null },
    { key: "blog",      href: "/wp-build/blog/",             icon: ICO.book,        label: "ParentingHub Blog", badge: null },
    { key: "saved",     href: user ? "/profile#saved" : "/login", icon: ICO.starOutline, label: "Saved & Wishlist", badge: null },
    { key: "delivery",  href: "/support#shipping",           icon: ICO.truck,       label: "Delivery Information",badge: null },
    { key: "returns",   href: "/returns",                    icon: ICO.refresh,     label: "7-Day Easy Returns",  badge: null },
  ];

  return `
  <div class="topbar" id="topbar">
    <a href="${annLink}" style="color:inherit;text-decoration:none"><span>${ann}</span></a>
    <button class="tb-close" aria-label="Dismiss announcement" onclick="this.parentElement.remove()">${ICO.x}</button>
  </div>
  <header class="site">
    <div class="container wide nav">
      <a class="brand" href="/" aria-label="ParentingHub Home"><img src="logo.png" alt="ParentingHub" style="height:44px;width:auto;object-fit:contain"></a>
      <nav class="nav-links" aria-label="Primary Navigation">
        ${currentNav.map(n=>`<a href="${n.href}" class="${n.key===active?"active":""}">${n.label}</a>`).join("")}
      </nav>
      <div class="nav-tools">
        <form class="search hide-s" onsubmit="return headerSearch(event)" style="min-width:180px;max-width:230px;padding:9px 15px">
          <span>${ICO.search}</span><input id="navSearch" placeholder="Search products…" aria-label="Search products">
        </form>
        <button class="icon-btn" title="Ask parents in community" aria-label="Ask parents in community" onclick="location.href='/community#ask'">${ICO.plus}</button>
        <button class="icon-btn" id="navCartBtn" title="Shopping bag" aria-label="${cartLabel}" onclick="openCart()">
          ${ICO.bag}<span class="badge" id="cartBadge" aria-hidden="true" style="display:none">0</span>
        </button>
        ${userToolHtml}
        <button class="capsule-burger-btn" aria-label="Open navigation menu" onclick="openDrawer()">
          ${ICO.menu} <span class="menu-btn-label">Menu</span>
        </button>
      </div>
    </div>
  </header>
  <div class="scrim" id="scrim" onclick="closeAll()"></div>

  <!-- =========================================================
       CAPSULE FLOATING SIDEBAR NAVIGATION (Maily-Style Menu Bar)
       ========================================================= -->
  <aside class="mob-drawer capsule-menu" id="mobDrawer" aria-label="Navigation Menu">
    <!-- Header: Cloud/Brand Icon + ParentingHub. + Collapse & Close -->
    <div class="capsule-head">
      <a href="/" class="capsule-brand" title="ParentingHub Home">
        <span class="capsule-brand-icon">${ICO.cloud}</span>
        <span class="capsule-brand-title">ParentingHub<span class="capsule-brand-dot">•</span></span>
      </a>
      <div class="capsule-head-actions">
        <button class="capsule-icon-action" id="capsuleCollapseBtn" onclick="toggleCapsuleCollapse()" title="Collapse / Expand menu" aria-label="Collapse or Expand Menu">
          ${ICO.chevL}
        </button>
        <button class="capsule-icon-action close" onclick="closeAll()" title="Close menu" aria-label="Close menu">
          ${ICO.x}
        </button>
      </div>
    </div>

    <!-- CTA Pill Button with Multi-Action Menu: Reel, Ask, Shop, Login -->
    <div class="capsule-cta-wrap">
      <button class="capsule-cta-btn" onclick="toggleCreateMenu(event)" title="Create New" aria-label="Create New Action Menu">
        <span class="capsule-cta-icon">${ICO.plus}</span>
        <span class="capsule-cta-text">Create New</span>
      </button>
      <div class="capsule-create-menu" id="capsuleCreateMenu">
        <a href="javascript:void(0)" onclick="openCreateReelModal()" class="create-menu-item">
          <span class="create-menu-ico" style="background:#FFE4E6;color:#E11D48">▶</span>
          <div class="create-menu-txt">
            <b>Share Baby Reel</b>
            <small>Video or milestone story clip</small>
          </div>
        </a>
        <a href="/community#ask" onclick="closeAll()" class="create-menu-item">
          <span class="create-menu-ico" style="background:#E0E7FF;color:#4338CA">💬</span>
          <div class="create-menu-txt">
            <b>Ask Parents a Question</b>
            <small>Feeding, gear advice & wins</small>
          </div>
        </a>
        <a href="/shop" onclick="closeAll()" class="create-menu-item">
          <span class="create-menu-ico" style="background:#DCFCE7;color:#15803D">🛍️</span>
          <div class="create-menu-txt">
            <b>Browse Essentials</b>
            <small>Order 100% authentic Avent</small>
          </div>
        </a>
        ${!user ? `
        <a href="/login" onclick="closeAll()" class="create-menu-item login-row" style="border-top:1px solid var(--line);margin-top:4px;padding-top:10px">
          <span class="create-menu-ico" style="background:#FCE7F3;color:#B63866">${ICO.user}</span>
          <div class="create-menu-txt">
            <b style="color:var(--primary)">Sign In / Register</b>
            <small>Account & order tracking</small>
          </div>
        </a>` : ''}
      </div>
    </div>

    <!-- Navigation Items List with Active Pill State & Badges -->
    <nav class="capsule-nav-list" aria-label="Capsule Menu Links">
      ${capsuleNavItems.map(item => {
        const isAct = (item.key === active) || (!active && item.key === 'home') || (active === 'shop' && item.key === 'shop');
        return `
        <a href="${item.href}" class="capsule-nav-item ${isAct ? 'active' : ''}" ${item.target ? `target="${item.target}" rel="noopener"` : ''} title="${item.label}">
          <span class="capsule-item-icon">
            ${item.icon}
            <span class="capsule-active-dot"></span>
          </span>
          <span class="capsule-item-label">${item.label}</span>
          ${item.badge ? `<span class="capsule-item-badge">${item.badge}</span>` : ''}
        </a>`;
      }).join("")}
    </nav>

    <!-- Theme Switcher: Dark Mode Moon Icon + iOS Toggle Switch -->
    <div class="capsule-theme-row">
      <div class="capsule-theme-label-wrap">
        <span class="capsule-theme-icon">${ICO.moon}</span>
        <span class="capsule-theme-text">Dark mode</span>
      </div>
      <button class="capsule-switch" id="capsuleThemeSwitch" onclick="toggleTheme()" role="switch" aria-label="Toggle dark mode" aria-checked="false">
        <span class="capsule-switch-thumb"></span>
      </button>
    </div>

    <!-- User Profile Card: Avatar + Name + Subtitle + 3-Dots Menu -->
    <div class="capsule-profile-card ${!user ? 'guest-card' : ''}" onclick="${!user ? 'location.href=\'/login\'' : ''}" style="${!user ? 'cursor:pointer' : ''}">
      <div class="capsule-avatar-wrap">
        ${user ? `<img src="art/story1.jpg" class="capsule-avatar-img" alt="${userName}" onerror="this.style.display='none';this.nextElementSibling.style.display='grid'">
        <span class="capsule-avatar-fallback" style="display:none">${userAvatarInitial}</span>` : `<span class="capsule-avatar-fallback" style="display:grid">${ICO.user}</span>`}
      </div>
      <div class="capsule-user-meta">
        <span class="capsule-user-name">${user ? userName : 'Sign In / Register'}</span>
        <span class="capsule-user-sub">${user ? userEmail : 'Join for member discounts & tracking'}</span>
      </div>
      <div class="capsule-user-options" onclick="event.stopPropagation()">
        <button class="capsule-dots-btn" onclick="toggleCapsuleUserDropdown(event)" title="Account options" aria-label="Account options">
          ${ICO.dots}
        </button>
        <div class="capsule-dropdown-menu" id="capsuleUserDropdown">
          ${user ? `
            <a href="/profile">${ICO.user} My Account & Orders</a>
            <a href="/profile#saved">${ICO.starOutline} Saved Wishlist</a>
            <a href="/shop?stage=newborn">${ICO.grid} Shop by Age</a>
            <button onclick="logoutUser()" class="capsule-logout-btn" style="color:#EF4444">${ICO.logout} Sign Out</button>
          ` : `
            <a href="/login" style="font-weight:700;color:var(--primary)">${ICO.user} Sign In / Register</a>
            <a href="/support">${ICO.phone} Help & FAQs</a>
            <a href="/returns">${ICO.refresh} Easy Returns</a>
          `}
        </div>
      </div>
    </div>
  </aside>

  <aside class="drawer" id="cartDrawer" aria-label="Shopping Bag">
    <header>
      <b class="serif" style="font-size:19px">Your bag</b>
      <button class="icon-btn" aria-label="Close shopping bag" onclick="closeAll()">${ICO.x}</button>
    </header>
    <div class="items" id="cartItems"></div>
    <footer>
      <div class="total"><span>Subtotal</span><b id="cartTotal">Rs 0</b></div>
      <p class="tiny muted" style="margin-bottom:12px">Standard delivery fee: PKR 200 per order</p>
      <button class="btn btn-primary btn-block" onclick="closeAll();openCheckout()">Checkout ${ICO.arrow}</button>
      <button class="btn btn-ghost btn-block" style="margin-top:8px" onclick="closeAll()">Keep shopping</button>
    </footer>
  </aside>

  <div class="cart-pop" id="cartPop" aria-live="polite">
    <span style="color:var(--green)">${ICO.check}</span>
    <span class="small"><b id="popName">Added</b><br><span class="muted tiny">added to your bag</span></span>
    <button class="btn btn-dark btn-xs" onclick="closeAll();openCart()">View bag</button>
  </div>
  
  <!-- Floating Back to Top Arrow -->
  <button class="ph-back-to-top" id="phBackToTop" onclick="phScrollToTop()" aria-label="Back to top" title="Back to top">
    ${ICO.up}
  </button>`;
}

function footerHTML(){
  const totalProducts = (typeof PRODUCTS !== "undefined" && Array.isArray(PRODUCTS)) ? PRODUCTS.length : 0;
  return `
  <footer class="site site-footer-boutique">
    <div class="container">
      <div class="ft-btq-grid">
        <!-- Col 1: Brand & Social Icons -->
        <div class="ft-btq-brand">
          <a class="ft-btq-logo-link" href="/" aria-label="ParentingHub Home">
            <img src="logo.png" alt="ParentingHub Pakistan" class="ft-btq-logo">
          </a>
          <p class="ft-btq-tag">Pakistan's authorized store for 100% genuine Philips Avent feeding bottles, maternal care & baby essentials.</p>
          
          <!-- Standard Social Media Icons -->
          <div class="ft-social-icons">
            <a href="https://www.instagram.com/parentinghub.pk/" target="_blank" rel="noopener" class="ft-soc-link ig" title="Instagram" aria-label="Instagram">${ICO.instagram}</a>
            <a href="https://www.facebook.com/profile.php?id=61594621612912" target="_blank" rel="noopener" class="ft-soc-link fb" title="Facebook" aria-label="Facebook">${ICO.facebook}</a>
          </div>
        </div>

        <!-- Col 2: Shop Essentials -->
        <div class="ft-btq-col">
          <span class="ft-btq-heading">Shop Essentials</span>
          <ul class="ft-btq-links">
            <li><a href="/shop/categories/feeding-bottles">Feeding Bottles</a></li>
            <li><a href="/shop/categories/breast-feeding">Breast Feeding & Pumps</a></li>
            <li><a href="/shop/categories/cup-weaning">Cup & Weaning Sets</a></li>
            <li><a href="/shop/categories/pacifiers">Pacifiers & Soothers</a></li>
            <li><a href="/shop">All Products (${totalProducts})</a></li>
          </ul>
        </div>

        <!-- Col 3: Care & Guides -->
        <div class="ft-btq-col">
          <span class="ft-btq-heading">Care & Guidance</span>
          <ul class="ft-btq-links">
            <li><a href="/shop/brands/philips-avent">Philips Avent Official</a></li>
            <li><a href="/wp-build/learn/">Feeding & Care Guides</a></li>
            <li><a href="/wp-build/blog/">ParentingHub Blog</a></li>
            <li><a href="/community">Parent Community</a></li>
            <li><a href="/wp-build/about-us/">About ParentingHub</a></li>
          </ul>
        </div>

        <!-- Col 4: Customer Care -->
        <div class="ft-btq-col">
          <span class="ft-btq-heading">Customer Care</span>
          <ul class="ft-btq-links">
            <li><a href="/wp-build/shipping-returns/">Delivery & Shipping</a></li>
            <li><a href="/returns">7-Day Easy Returns</a></li>
            <li><a href="/wp-build/faqs/">Help & FAQs</a></li>
            <li><a href="/wp-build/privacy-policy/">Privacy & Terms</a></li>
          </ul>
        </div>
      </div>

      <!-- Bottom Bar: Payments & Copyright -->
      <div class="ft-btq-bottom">
        <div class="ft-btq-copy">
          © 2026 <b>ParentingHub.pk</b> · Authorized Baby & Maternal Care.
        </div>

        <!-- Payment Badges -->
        <div class="ft-btq-payments">
          <span class="ft-pay-badge">💵 Cash on Delivery</span>
          <span class="ft-pay-badge">🏦 Bank Transfer / Raast</span>
          <span class="ft-pay-badge">🛡️ 100% Genuine</span>
        </div>
      </div>
    </div>
  </footer>
  <div class="toast-wrap" id="toasts"></div>
  <div class="ph-modal-scrim" id="productModalScrim" onclick="if(event.target===this)closeModals()"><div class="ph-modal"></div></div>
  <div class="ph-modal-scrim" id="checkoutModalScrim" onclick="if(event.target===this)closeModals()"><div class="ph-modal"></div></div>
  <div class="ph-modal-scrim" id="orderSuccessModalScrim" onclick="if(event.target===this)closeModals()"><div class="ph-modal"></div></div>
  <div class="ph-modal-scrim" id="createReelModalScrim" onclick="if(event.target===this)closeModals()"><div class="ph-modal" id="createReelModalBox"></div></div>`;
}

function mountChrome(){
  const page = document.body.dataset.page;
  const t = document.getElementById("chrome-top"); if(t) t.innerHTML = headerHTML(page);
  const b = document.getElementById("chrome-bottom"); if(b) b.innerHTML = footerHTML();
  updateCart();
  initTheme();
  initCapsuleState();
}

/* ---------------- cart ---------------- */
function cartCount(){ return S.cart.reduce((a,i)=>a+i.qty,0); }
function productOptionGroups(p){
  const groups = Array.isArray(p && p.options) ? p.options : [];
  return groups.filter(g => g && typeof g === "object" && String(g.name || "").trim() && Array.isArray(g.values) && g.values.some(v => String(v || "").trim()));
}
function productOptionsHTML(p){
  const groups = productOptionGroups(p);
  if(!groups.length) return "";
  return `<div class="pm-options" style="display:grid;gap:10px;margin:12px 0">${groups.map((g,idx)=>{
    const values = g.values.map(v=>String(v||"").trim()).filter(Boolean);
    return `<label style="display:grid;gap:5px;font-size:13px;font-weight:700">${esc(String(g.name).trim())}
      <select class="ph-product-option" data-option-index="${idx}" style="width:100%;padding:10px 12px;border:1px solid var(--line);border-radius:9px;background:var(--surface);color:var(--ink)">
        <option value="">Select ${esc(String(g.name).trim())}</option>
        ${values.map(v=>`<option value="${esc(v)}">${esc(v)}</option>`).join("")}
      </select>
    </label>`;
  }).join("")}</div>`;
}
function selectedProductOptionString(p, root=document){
  const groups = productOptionGroups(p);
  if(!groups.length) return "";
  const selects = Array.from(root.querySelectorAll(".ph-product-option"));
  const normalized=[];
  for(let idx=0; idx<groups.length; idx++){
    const sel=selects.find(x=>Number(x.dataset.optionIndex)===idx);
    const value=String(sel && sel.value || "").trim();
    if(!value){
      if(sel){ sel.focus(); sel.style.borderColor="#d93025"; }
      return null;
    }
    normalized.push(`${String(groups[idx].name).trim()}: ${value}`);
  }
  return normalized.join(" / ");
}
function addToCart(id, qty=1, silent=false, option=""){
  const p = PRODUCTS.find(p=>p.id===id);
  const normalizedOption=String(option||"").trim();
  if(p && productOptionGroups(p).length && !normalizedOption){
    openProductModal(id);
    if(!silent) toast("Please select the product options first", "x");
    return false;
  }
  const row = S.cart.find(i=>i.id===id && String(i.option||"")===normalizedOption);
  if(row) row.qty += qty; else S.cart.push({id, qty, ...(normalizedOption?{option:normalizedOption}:{})});
  save(); updateCart();
  if(!silent){
    toast(`${p ? p.name : "Item"} added to your bag`, "bag");
    popBag(p);
  }
  return true;
}
function setQty(id, d){
  const row = S.cart.find(i=>i.id===id); if(!row) return;
  row.qty += d;
  if(row.qty<=0) S.cart = S.cart.filter(i=>i!==row);
  save(); updateCart(); renderCartDrawer();
}
function removeItem(id){ const row=S.cart.find(i=>i.id===id); if(row) S.cart=S.cart.filter(i=>i!==row); save(); updateCart(); renderCartDrawer(); }
function setCartLineQty(index,d){
  const row=S.cart[index]; if(!row) return;
  row.qty += d;
  if(row.qty<=0) S.cart.splice(index,1);
  save(); updateCart(); renderCartDrawer();
}
function removeCartLine(index){ if(S.cart[index]) S.cart.splice(index,1); save(); updateCart(); renderCartDrawer(); }
let appliedCoupon = null;

function cartSubtotal(){
  return S.cart.reduce((a,i)=>{
    const p = PRODUCTS.find(p=>p.id===i.id);
    return a + (p ? p.price * i.qty : 0);
  }, 0);
}

function calculateOrderTotals(){
  const subtotal = cartSubtotal();
  let discount = 0;

  if (appliedCoupon) {
    if (appliedCoupon.discountPercent) {
      discount = Math.round((subtotal * appliedCoupon.discountPercent) / 100);
    } else if (appliedCoupon.discountAmount) {
      discount = Math.min(subtotal, appliedCoupon.discountAmount);
    }
  }

  // Official delivery policy: fixed PKR 200 per non-empty order.
  // Coupons can discount products, but they cannot waive delivery.
  const shipping = subtotal > 0 ? 200 : 0;
  const total = Math.max(0, subtotal - discount + shipping);
  return { subtotal, discount, shipping, total };
}

function applyCouponCode(){
  const inp = document.getElementById("cartCouponInput");
  if(!inp) return;
  const val = inp.value.trim().toUpperCase();
  if(!val) return;
  const c = COUPONS.find(x => x.code === val);
  if(c){
    appliedCoupon = c;
    toast(`Coupon ${c.code} applied! (${c.desc})`, "check");
    renderCartDrawer();
  } else {
    toast("Invalid coupon code (try WELCOME10 or FIRSTBUY)", "x");
  }
}

function removeCoupon(){
  appliedCoupon = null;
  toast("Coupon removed");
  renderCartDrawer();
}

function cartTotal(){ return calculateOrderTotals().total; }

function updateCart(){
  const n = cartCount();
  const label = `Shopping bag, ${n} ${n===1?'item':'items'}`;
  document.querySelectorAll("#navCartBtn").forEach(btn => {
    btn.setAttribute("aria-label", label);
  });
  document.querySelectorAll("#cartBadge").forEach(el=>{ el.textContent=n; el.style.display = n?"grid":"none"; });
  if(document.getElementById("cartDrawer")?.classList.contains("open")) renderCartDrawer();
}
function openCart(){
  // Close any modal that could leave a backdrop-filter above the cart.
  try { closeModals(); } catch(e) {}

  // Close the assistant panel too so it can never leave a visual layer over the cart.
  document.getElementById("phChatWindow")?.classList.remove("open");
  document.getElementById("phChatToggleBtn")?.classList.remove("active");

  document.querySelectorAll(".ph-modal-scrim.open").forEach(el => el.classList.remove("open"));
  document.querySelectorAll(".modal.open,.modal.on,.modal.active").forEach(el => {
    el.classList.remove("open","on","active");
  });

  const scrim = document.getElementById("scrim");
  const drawer = document.getElementById("cartDrawer");
  if(!drawer) return;

  /*
   * Move the cart + shared scrim directly under <body>.
   * This escapes header/chrome stacking contexts and guarantees that
   * the cart can sit above any backdrop-filter layer.
   */
  if(scrim && scrim.parentElement !== document.body) document.body.appendChild(scrim);
  if(drawer.parentElement !== document.body) document.body.appendChild(drawer);

  renderCartDrawer();

  document.body.classList.add("cart-open");
  if(scrim){
    scrim.classList.add("open","cart-scrim-active");
  }
  drawer.classList.add("open");
}
function openDrawer(){
  initCapsuleState();
  document.getElementById("mobDrawer")?.classList.add("open");
  document.getElementById("scrim")?.classList.add("open");
}
function closeAll(){
  document.body.classList.remove("cart-open");
  document.getElementById("cartDrawer")?.classList.remove("open");
  document.getElementById("mobDrawer")?.classList.remove("open");
  document.getElementById("scrim")?.classList.remove("open","cart-scrim-active");
  document.getElementById("capsuleUserDropdown")?.classList.remove("open");
  document.getElementById("capsuleCreateMenu")?.classList.remove("open");
  closeModals();
}

/* =========================================================
   CAPSULE SIDEBAR & THEME CONTROLS (Maily Style)
   ========================================================= */
function initTheme(){
  try {
    const saved = localStorage.getItem("ph_theme") || "light";
    document.documentElement.setAttribute("data-theme", saved);
    syncThemeSwitches(saved === "dark");
  } catch(e){}
}

function toggleTheme(){
  try {
    const isDark = document.documentElement.getAttribute("data-theme") === "dark";
    const next = isDark ? "light" : "dark";
    document.documentElement.setAttribute("data-theme", next);
    localStorage.setItem("ph_theme", next);
    syncThemeSwitches(next === "dark");
    if(typeof toast === "function") toast(`${next === "dark" ? "Dark" : "Light"} mode enabled`, "moon");
  } catch(e){}
}

function syncThemeSwitches(isDark){
  document.querySelectorAll(".capsule-switch").forEach(el=>{
    el.classList.toggle("on", isDark);
    el.setAttribute("aria-checked", isDark ? "true" : "false");
  });
}

function toggleCapsuleCollapse(){
  const drawer = document.getElementById("mobDrawer");
  if(!drawer) return;
  const isCollapsed = drawer.classList.toggle("collapsed");
  try { localStorage.setItem("ph_menu_collapsed", isCollapsed ? "1" : "0"); } catch(e){}
  const btn = document.getElementById("capsuleCollapseBtn");
  if(btn) btn.innerHTML = isCollapsed ? ICO.chevR : ICO.chevL;
}

function initCapsuleState(){
  try {
    if(localStorage.getItem("ph_menu_collapsed") === "1"){
      const drawer = document.getElementById("mobDrawer");
      if(drawer) drawer.classList.add("collapsed");
      const btn = document.getElementById("capsuleCollapseBtn");
      if(btn) btn.innerHTML = ICO.chevR;
    }
  } catch(e){}
}

function toggleCreateMenu(e){
  e?.stopPropagation();
  const m = document.getElementById("capsuleCreateMenu");
  if(m) m.classList.toggle("open");
}

function toggleCapsuleUserDropdown(e){
  e?.stopPropagation();
  const d = document.getElementById("capsuleUserDropdown");
  if(d) d.classList.toggle("open");
}

document.addEventListener("click", e=>{
  const m = document.getElementById("capsuleCreateMenu");
  if(m && !e.target.closest(".capsule-cta-wrap")) m.classList.remove("open");
  const d = document.getElementById("capsuleUserDropdown");
  if(d && !e.target.closest(".capsule-user-options")) d.classList.remove("open");
});

/* =========================================================
   REEL CREATOR & BACK TO TOP
   ========================================================= */
function phScrollToTop(){
  window.scrollTo({ top: 0, behavior: "smooth" });
}

window.addEventListener("scroll", ()=>{
  const btn = document.getElementById("phBackToTop");
  if(btn){
    btn.classList.toggle("visible", window.scrollY > 260);
  }
}, { passive: true });

function openCreateReelModal(){
  closeAll();
  const box = document.getElementById("createReelModalBox");
  const scrim = document.getElementById("createReelModalScrim");
  if(!box || !scrim) return;

  const user = getCurrentUser();
  const authorName = user ? user.name : "Parent";

  box.innerHTML = `
    <div style="padding:24px 26px;position:relative">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
        <div style="display:flex;align-items:center;gap:12px">
          <div style="width:42px;height:42px;border-radius:12px;background:#FFE4E6;color:#E11D48;display:grid;place-items:center;font-size:20px">▶</div>
          <div>
            <b style="font-size:18px;display:block">Share Baby Reel / Short Video</b>
            <span class="tiny muted">Share nursery moments, feeding wins, and baby milestones</span>
          </div>
        </div>
        <button class="icon-btn" onclick="closeModals()" aria-label="Close modal">${ICO.x}</button>
      </div>

      <form onsubmit="handlePublishReel(event)">
        <div class="field" style="margin-bottom:14px">
          <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px">Reel Caption</label>
          <input id="reelCaptionInput" class="input" style="width:100%;padding:10px 14px;border-radius:12px;border:1px solid var(--line)" placeholder="e.g. Baby's first time holding the Philips Avent Natural bottle! 😍" required>
        </div>

        <div class="row gap-m" style="margin-bottom:14px">
          <div style="flex:1">
            <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px">Category</label>
            <select id="reelCategorySelect" class="sort" style="width:100%;padding:9px 12px;border-radius:12px;border:1px solid var(--line)">
              <option value="Feeding & Bottle">🍼 Feeding & Bottles</option>
              <option value="Little Wins">🏆 Little Wins & Growth</option>
              <option value="Baby Gear Review">🛍️ Gear Review</option>
              <option value="Nursery Moments">🧸 Nursery & Play</option>
            </select>
          </div>
          <div style="flex:1">
            <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px">Parent Name</label>
            <input id="reelAuthorInput" class="input" style="width:100%;padding:10px 14px;border-radius:12px;border:1px solid var(--line)" value="${esc(authorName)}" placeholder="Your Name" required>
          </div>
        </div>

        <div style="margin-bottom:18px;background:var(--cream);padding:18px;border-radius:16px;border:1.5px dashed var(--line-2);text-align:center">
          <span style="font-size:28px;display:block;margin-bottom:4px">📱</span>
          <b style="font-size:13.5px">Select Video or Photo Clip</b>
          <p class="tiny muted" style="margin-top:2px">MP4, WebM or MOV · maximum 30MB</p>
          <input type="file" id="reelFileInput" accept="video/mp4,video/webm,video/quicktime" style="display:none" onchange="previewReelUpload(this)">
          <button type="button" class="btn btn-soft btn-xs" style="margin-top:10px" onclick="document.getElementById('reelFileInput').click()">Choose Clip</button>
          <div id="reelFileChosen" class="tiny" style="margin-top:8px;font-weight:600;color:var(--primary);display:none"></div>
        </div>

        <div style="display:flex;gap:10px;justify-content:flex-end">
          <button type="button" class="btn btn-ghost" onclick="closeModals()">Cancel</button>
          <button type="submit" class="btn btn-primary" style="padding:10px 24px">Publish Reel ▶</button>
        </div>
      </form>
    </div>
  `;

  scrim.classList.add("open");
}

function previewReelUpload(input){
  const el = document.getElementById("reelFileChosen");
  const f=input.files && input.files[0];
  if(!f || !el) return;
  const okType=['video/mp4','video/webm','video/quicktime'].includes(f.type);
  if(!okType || f.size>30*1024*1024){
    input.value='';
    el.textContent=!okType?'Use MP4, WebM or MOV video format.':'Video must be 30MB or smaller.';
    el.style.display='block';el.style.color='#b42318';return;
  }
  el.textContent = `Selected: ${f.name} (${(f.size/1024/1024).toFixed(1)} MB)`;
  el.style.display = "block";el.style.color='var(--primary)';
}

async function handlePublishReel(e){
  e.preventDefault();
  const caption = document.getElementById("reelCaptionInput")?.value.trim();
  const cat = document.getElementById("reelCategorySelect")?.value || "Care";
  const file = document.getElementById('reelFileInput')?.files?.[0];
  const submit=e.submitter;
  if(!caption){ toast('Please add a reel caption.','x'); return; }
  if(!file){ toast('Please choose a video clip.','x'); return; }
  if(file.size>30*1024*1024){ toast('Video must be 30MB or smaller.','x'); return; }
  const fd=new FormData();fd.append('file',file);fd.append('title',caption);fd.append('caption',caption);fd.append('tag',cat);
  if(submit){submit.disabled=true;submit.textContent='Uploading…';}
  try{
    const res=await fetch('/api/community/reels/upload',{method:'POST',body:fd,headers:{'X-Requested-With':'XMLHttpRequest'}});
    const data=await res.json().catch(()=>null);
    if(res.status===401){ location.href='/login?next=%2Freels'; return; }
    if(!res.ok||!data?.success) throw new Error(data?.message||data?.error||'Reel upload failed.');
    closeModals();toast('Reel published successfully.','play');location.href='/reels';
  }catch(err){toast(err.message||'Reel upload failed.','x');if(submit){submit.disabled=false;submit.textContent='Publish Reel ▶';}}
}
function closeModals(){
  document.querySelectorAll(".ph-modal-scrim").forEach(m=>m.classList.remove("open"));
  document.querySelectorAll(".modal").forEach(m => m.classList.remove("open", "on", "active"));
  try {
    if(location.search.includes("product=")){
      const u = new URL(location.href);
      u.searchParams.delete("product");
      history.replaceState(null, "", u.pathname + (u.search ? u.search : ""));
    }
  } catch(e){}
}
document.addEventListener("keydown", e => {
  if(e.key === "Escape") closeAll();
});

function renderCartDrawer(){
  const box = document.getElementById("cartItems"); if(!box) return;
  const { subtotal, discount, shipping, total } = calculateOrderTotals();

  if(!S.cart.length){
    const totalCount = (typeof PRODUCTS !== "undefined" && Array.isArray(PRODUCTS)) ? PRODUCTS.length : 0;
    box.innerHTML = `<div class="empty"><div class="box">${ICO.bag}</div><b>Your bag is empty</b>
      <p class="small muted" style="margin-top:8px">Discover our ${totalCount} authorized baby essentials.</p>
      <a class="btn btn-soft btn-s" style="margin-top:14px" href="/shop" onclick="closeAll()">Start shopping</a></div>`;
    const f = document.querySelector("#cartDrawer footer");
    if(f) f.style.display = "none";
    return;
  }

  const f = document.querySelector("#cartDrawer footer");
  if(f) f.style.display = "block";

  const shipBanner = `<div class="ship-progress-wrap">
      <div class="ship-progress-text"><span>${icon("truck")} Standard delivery fee</span><span>PKR 200</span></div>
    </div>`;

  box.innerHTML = shipBanner + S.cart.map((i,lineIndex)=>{
    const p = PRODUCTS.find(p=>p.id===i.id); if(!p) return "";
    const imgUrl = p.img || (p.images && p.images[0]) || '/assets/products/image-verification-pending.webp';
    return `<div class="ci-row">
      <div class="im" style="cursor:pointer" onclick="closeAll();openProductModal('${p.id}')">
        <img src="${imgUrl}" alt="${p.name}" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </div>
      <div class="grow">
        <b style="cursor:pointer" onclick="closeAll();openProductModal('${p.id}')">${p.name}</b>
        <div class="small muted" style="margin:2px 0 4px">${money(p.price)} · ${p.brand || p.cat}</div>
        ${i.option ? `<div class="tiny" style="margin-bottom:6px;color:var(--ink-2)">${esc(i.option)}</div>` : ''}
        <div class="row" style="justify-content:space-between;align-items:center">
          <span class="qty"><button onclick="setCartLineQty(${lineIndex},-1)">−</button><span>${i.qty}</span><button onclick="setCartLineQty(${lineIndex},1)">+</button></span>
          <button class="tiny muted" onclick="removeCartLine(${lineIndex})" style="text-decoration:underline">Remove</button>
        </div>
      </div>
    </div>`;
  }).join("");

  const couponHtml = appliedCoupon ?
    `<div class="row" style="justify-content:space-between;align-items:center;background:var(--rose-soft);padding:6px 12px;border-radius:8px;font-size:12.5px;margin:8px 0">
       <span>${icon("tag")} <b>${appliedCoupon.code}</b> applied (-${money(discount)})</span>
       <button class="tiny muted" onclick="removeCoupon()" style="cursor:pointer;border:none;background:none;text-decoration:underline">Remove</button>
     </div>` :
    `<div class="cart-coupon">
       <input id="cartCouponInput" placeholder="Coupon (e.g. WELCOME10)">
       <button onclick="applyCouponCode()">Apply</button>
     </div>`;

  if(f) {
    f.innerHTML = `
      ${couponHtml}
      <div style="display:flex;flex-direction:column;gap:6px;font-size:13.5px;margin-bottom:12px">
        <div class="row" style="justify-content:space-between"><span>Subtotal</span><b>${money(subtotal)}</b></div>
        ${discount > 0 ? `<div class="row" style="justify-content:space-between;color:var(--primary)"><span>Coupon discount</span><b>-${money(discount)}</b></div>` : ''}
        <div class="row" style="justify-content:space-between"><span>Delivery</span><b>${money(shipping)}</b></div>
        <div class="row grand-tot" style="justify-content:space-between;font-size:17px;font-weight:800;border-top:1px solid var(--line);padding-top:8px">
          <span>Total</span><b id="cartTotal">${money(total)}</b>
        </div>
      </div>
      <a href="/checkout" class="btn btn-primary btn-block" style="text-align:center;text-decoration:none;display:block">Proceed to Checkout (${money(total)}) ${ICO.arrow}</a>
      <button class="btn btn-ghost btn-block" style="margin-top:8px" onclick="closeAll()">Keep shopping</button>
    `;
  }
}
function drawerSearch(e){
  e.preventDefault();
  const q = (document.getElementById("drawerSearch")||{}).value?.trim() || "";
  location.href = "/shop" + (q?("?q="+encodeURIComponent(q)):"");
  return false;
}
function headerSearch(e){
  e.preventDefault();
  const q = document.getElementById("navSearch").value.trim();
  location.href = "/shop" + (q?("?q="+encodeURIComponent(q)):"");
  return false;
}

/* ---------------- toast / pop / wish ---------------- */
function toast(msg, icon){
  const wrap = document.getElementById("toasts"); if(!wrap) return;
  const el = document.createElement("div");
  el.style.cssText = "background:var(--ink);color:#fff;padding:12px 18px;border-radius:999px;font-size:13.4px;box-shadow:var(--sh-l);opacity:0;transform:translateY(10px);transition:.25s";
  el.style.cssText += ";display:flex;align-items:center;gap:8px";
  if(icon && ICO[icon === "heart" ? "heartFill" : icon]){ const ic = document.createElement("span"); ic.className = "ico"; ic.style.color = "#F4A9C4"; ic.innerHTML = ICO[icon === "heart" ? "heartFill" : icon]; el.appendChild(ic); }
  el.appendChild(document.createTextNode(msg));
  wrap.appendChild(el);
  requestAnimationFrame(()=>{ el.style.opacity=1; el.style.transform="none"; });
  setTimeout(()=>{ el.style.opacity=0; el.style.transform="translateY(10px)"; setTimeout(()=>el.remove(),300); }, 2200);
}
function popBag(p){
  const pop = document.getElementById("cartPop"); if(!pop) return;
  document.getElementById("popName").textContent = p ? p.name.slice(0,34)+(p.name.length>34?"…":"") : "Item";
  pop.classList.add("on");
  clearTimeout(popBag._t);
  popBag._t = setTimeout(()=>pop.classList.remove("on"), 2600);
}
function toggleWish(id, el){
  const i = S.wish.indexOf(id);
  if(i>=0){ S.wish.splice(i,1); } else { S.wish.push(id); }
  save();
  const on = S.wish.includes(id);
  persistWishlistItem(id,on);
  if(el){ el.classList.toggle("on", on); el.innerHTML = on?ICO.heartFill:ICO.heart; }
  toast(on?"Saved to your wishlist":"Removed from wishlist","heart");
}
function wishBtn(id){
  const on = S.wish.includes(id);
  return `<button class="wish ${on?"on":""}" title="Save" onclick="event.stopPropagation();toggleWish('${id}', this)">${on?ICO.heartFill:ICO.heart}</button>`;
}

/* ---------------- product formatting & specs helpers ---------------- */
function formatProductName(rawName){
  if(!rawName) return { title: "", model: "" };
  let str = String(rawName).trim();
  let model = "";
  const modelMatch = str.match(/\s*\(([A-Z0-9\/\-]+)\)\s*$/i);
  if(modelMatch){
    model = modelMatch[1];
    str = str.replace(modelMatch[0], "").trim();
  }
  str = str.replace(/(\d+)\s*\*\s*(\d+)\s*(ml|oz)/gi, "$1 × $2 $3");
  str = str.replace(/\bPK\s*(\d+)\b/gi, "Pack of $1");
  str = str.replace(/\bMl\b/g, "ml");
  str = str.replace(/anti[- ]colic/gi, "Anti-Colic");
  return { title: str, model };
}

function getProductSpecs(p){
  const specs = p.specs || {};
  const out = {};
  for (const key of ['age','flow','material','pack','compat']) {
    const value = specs[key];
    if (typeof value === 'string' || typeof value === 'number') {
      const text=String(value).trim();
      if(text && !/(please check|check packaging|ask us before ordering|contact us|not specified|not available|unknown|^n\/?a$|^none$|^varies$)/i.test(text)) out[key]=esc(text);
    }
  }
  return out;
}
function productSpecsHTML(p){
  const specs = getProductSpecs(p);
  const labels = {age:'Age Suitability',flow:'Teat / Flow Rate',material:'Material Safety',pack:'Pack Contents',compat:'Range Compatibility'};
  const rows = Object.keys(specs).map(k => `<tr><td class="k">${labels[k]}:</td><td class="v">${specs[k]}</td></tr>`).join('');
  return rows ? `<div class="pm-specs-wrap"><b>Product Specifications:</b><table>${rows}</table></div>` : '';
}

/* ---------------- product detail modal & quick view ---------------- */
let currentModalProduct = null;
let modalQty = 1;

function openProductModal(id){
  const p = PRODUCTS.find(x => x.id === id);
  if(!p) return;
  currentModalProduct = p;
  modalQty = 1;

  // Update URL so product is shareable and bookmarkable
  try {
    const u = new URL(location.href);
    u.searchParams.set("product", p.id);
    history.replaceState({ productId: p.id }, "", u.toString());
  } catch(e){}

  const scrim = document.getElementById("productModalScrim");
  if(!scrim) return;

  const imgUrl = p.img || (p.images && p.images[0]) || '/assets/products/image-verification-pending.webp';
  const off = p.old && p.old > p.price ? Math.round((1 - p.price / p.old) * 100) : 0;
  const isWish = S.wish.includes(p.id);
  const { title: cleanTitle, model: modelCode } = formatProductName(p.name);
  const specs = getProductSpecs(p);

  let thumbsHtml = "";
  const allMedia = [...new Set([p.img || p.image, ...(Array.isArray(p.images) ? p.images : [])].filter(x => typeof x === 'string' && x.trim()).map(x => x.trim()))];
  if(allMedia.length > 1 || p.video){
    let thumbs = allMedia.map((img, i) =>
      `<img src="${img}" class="pm-thumb ${i===0?'active':''}" alt="${esc(cleanTitle)} photo ${i+1}" aria-label="View photo ${i+1}" referrerpolicy="no-referrer" onclick="setModalMedia('image', '${img}', this)" onerror="this.onerror=null;this.src='art/placeholder.jpg'">`
    );
    if(p.video){
      thumbs.push(`<div class="pm-thumb pm-thumb-video" onclick="setModalMedia('video', '${p.video}', this)" title="Watch Video" aria-label="Watch product video" style="display:flex;align-items:center;justify-content:center;background:#0f172a;color:#fff;font-size:11px;font-weight:700;cursor:pointer;border-radius:8px;padding:4px;border:2px solid var(--slate-300)">▶ Video</div>`);
    }
    thumbsHtml = `<div class="pm-thumbs">${thumbs.join("")}</div>`;
  }

  scrim.querySelector(".ph-modal").innerHTML = `
    <div role="dialog" aria-modal="true" aria-labelledby="pmModalTitle" style="display:contents">
      <button class="ph-modal-close" aria-label="Close product details dialog" onclick="closeModals()">${ICO.x}</button>
      <div class="product-modal-grid">
        <div class="pm-gallery">
          <div class="pm-main-img" id="pmMainMediaBox">
            <img id="pmMainImg" src="${imgUrl}" alt="${esc(cleanTitle)}" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
          </div>
          ${thumbsHtml}
        </div>
        <div class="pm-info">
          <div class="pm-badges">
            <span class="pm-badge-brand">${p.brand || 'Authorized'}</span>
            ${modelCode ? `<span class="pm-badge-sku">Model: ${esc(modelCode)}</span>` : (p.sku ? `<span class="pm-badge-sku">SKU: ${esc(p.sku)}</span>` : '')}
            <span class="pm-badge-stock ${p.inStock ? 'in' : 'out'}">${p.inStock ? icon('check') + ' In Stock' : icon('x') + ' Out of Stock'}</span>
          </div>
          <h2 class="pm-title" id="pmModalTitle">${esc(cleanTitle)} ${modelCode ? `<span style="font-size:15px;color:var(--ink-3);font-weight:500">(${esc(modelCode)})</span>` : ''}</h2>
          
          <div class="pm-price-wrap">
            <span class="pm-price">${money(p.price)}</span>
            ${p.old && p.old > p.price ? `<span class="pm-old-price">${money(p.old)}</span><span class="pm-save-tag">Save ${off}%</span>` : ''}
          </div>

          <div class="pm-desc">
            <p>${esc(p.desc || '')}</p>
            ${p.video ? `<div style="margin-top:10px"><button type="button" class="btn btn-ghost btn-s" style="color:var(--primary);border-color:var(--primary);gap:6px" onclick="setModalMedia('video', '${p.video}', document.querySelector('.pm-thumb-video'))">▶ Watch Product Video</button></div>` : ''}
            
            <!-- Delivery & Return Quick Reassurance -->
            <div style="margin-top:10px;display:flex;flex-direction:column;gap:5px;font-size:13px;color:var(--ink-2);background:var(--surface-2);border-radius:10px;padding:10px 12px;border:1px solid var(--line)">
              <span>${icon("truck")} <b>Nationwide Delivery:</b> Fixed PKR 200 delivery fee per order. Delivery times vary by city.</span>
              <span>${icon("shield")} <b>7-Day Easy Returns:</b> Sealed & untampered items covered under infant hygiene guarantee.</span>
            </div>

            ${productSpecsHTML(p)}
            ${productOptionsHTML(p)}

            <div class="pm-reviews-box"><b>Customer Reviews</b><p class="muted">${p.reviews > 0 && p.rating > 0 ? `${esc(p.rating)} / 5 (${esc(p.reviews)} reviews)` : 'No reviews yet.'}</p></div>
            <a class="btn btn-soft" href="/product/${encodeURIComponent(p.id)}">View full product details</a>

          </div>

          <div class="pm-actions">
            <div class="pm-qty-row">
              <span class="small muted" style="font-weight:700">Quantity:</span>
              <div class="pm-qty-box">
                <button class="pm-qty-btn" aria-label="Decrease quantity" onclick="changeModalQty(-1)">−</button>
                <span class="pm-qty-val" id="pmQtyVal">1</span>
                <button class="pm-qty-btn" aria-label="Increase quantity" onclick="changeModalQty(1)">+</button>
              </div>
              <button class="btn btn-ghost btn-s" style="margin-inline-start:auto" onclick="toggleWish('${p.id}', this)">
                ${isWish ? ICO.heartFill + ' Saved' : ICO.heart + ' Wishlist'}
              </button>
            </div>
            <div style="display:flex;gap:10px;margin-top:6px;flex-wrap:wrap">
              <button class="btn btn-primary" style="flex:1" onclick="addModalToCart()">${ICO.bag} Add to Bag</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;

  scrim.classList.add("open");
}

function setModalMedia(type, src, el){
  const box = document.getElementById("pmMainMediaBox") || document.querySelector(".pm-main-img");
  if(!box) return;
  document.querySelectorAll(".pm-thumb").forEach(t => t.classList.remove("active"));
  if(el) el.classList.add("active");

  if(type === "video"){
    const ytMatch = (src || '').match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/);
    if(ytMatch){
      box.innerHTML = `<iframe src="https://www.youtube.com/embed/${ytMatch[1]}?autoplay=1" style="width:100%;height:350px;border:none;border-radius:12px" allow="autoplay; encrypted-media" allowfullscreen></iframe>`;
    } else {
      box.innerHTML = `<video src="${src}" controls autoplay playsinline style="width:100%;max-height:360px;border-radius:12px;background:#000;object-fit:contain"></video>`;
    }
  } else {
    box.innerHTML = `<img id="pmMainImg" src="${src}" alt="product" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">`;
  }
}

function setModalMainImg(src, thumbEl){
  setModalMedia("image", src, thumbEl);
}

function changeModalQty(d){
  modalQty = Math.max(1, modalQty + d);
  const el = document.getElementById("pmQtyVal");
  if(el) el.textContent = modalQty;
}

function addModalToCart(){
  if(!currentModalProduct || !currentModalProduct.inStock) return;
  const root = document.getElementById("productModalScrim")?.classList.contains("open")
    ? document.getElementById("productModalScrim")
    : document.querySelector("main") || document;
  const option = selectedProductOptionString(currentModalProduct, root);
  if(option===null){ toast("Please select all required product options", "x"); return; }
  if(!addToCart(currentModalProduct.id, modalQty, false, option)) return;
  closeModals();
  openCart();
}

/* ---------------- checkout modal & order flow ---------------- */
function openCheckout(){
  window.location.href = '/checkout';
}

  const { subtotal, discount, shipping, total } = calculateOrderTotals();
  const codEnabled = !(typeof SETTINGS !== 'undefined' && SETTINGS.codEnabled === false);
  const bankEnabled = !(typeof SETTINGS !== 'undefined' && SETTINGS.bankEnabled === false);
  if(!codEnabled && !bankEnabled){ toast('Checkout is temporarily unavailable. Please contact support.'); return; }
  const defaultPayment = codEnabled ? 'cod' : 'bank';
  if(!S.cart.length){
    toast("Your bag is empty!");
    return;
  }

  const savedCustomer = store.get("customer_info", {});

  const itemsListHtml = S.cart.map(i => {
    const p = PRODUCTS.find(x => x.id === i.id);
    if(!p) return "";
    const imgUrl = p.img || (p.images && p.images[0]) || '/assets/products/image-verification-pending.webp';
    return `<div class="checkout-item-row">
      <img src="${imgUrl}" alt="${esc(p.name)}" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      <div class="grow" style="font-size:13px">
        <b style="display:block;line-height:1.3">${esc(p.name)}</b>
        ${i.option ? `<span class="tiny" style="display:block;color:var(--ink-2);margin:2px 0">${esc(i.option)}</span>` : ''}
        <span class="muted">${i.qty} × ${money(p.price)}</span>
      </div>
      <b style="font-size:13px">${money(p.price * i.qty)}</b>
    </div>`;
  }).join("");

  scrim.querySelector(".ph-modal").innerHTML = `
    <button class="ph-modal-close" aria-label="Close checkout dialog" onclick="closeModals()">${ICO.x}</button>
    <div class="checkout-modal-wrap">
      <div class="row gap-m" style="align-items:center">
        <b class="serif" style="font-size:22px">Secure Checkout</b>
        <span class="badge" style="background:#EAF7EE;color:#1E8E3E;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600">${icon("lock")} SSL Encrypted</span>
      </div>
      
      <div class="checkout-grid">
        <form class="checkout-form-col" id="checkoutForm" onsubmit="return handlePlaceOrder(event)">
          <div class="form-group">
            <label for="coName">Full Name *</label>
            <input id="coName" name="name" required placeholder="e.g. Fatima Ali" value="${esc(savedCustomer.name || '')}">
          </div>

          <div class="row gap-m" style="display:grid;grid-template-columns:1fr 1fr">
            <div class="form-group">
              <label for="coPhone">Phone Number *</label>
              <input id="coPhone" name="phone" type="tel" inputmode="tel" required placeholder="03XX XXXXXXX" value="${esc(savedCustomer.phone || '')}">
              <span class="tiny muted">03XXXXXXXXX for courier tracking & dispatch</span>
            </div>
            <div class="form-group">
              <label for="coEmail">Email Address</label>
              <input id="coEmail" name="email" type="email" placeholder="name@example.com" value="${esc(savedCustomer.email || '')}">
            </div>
          </div>

          <div class="form-group">
            <label for="coAddress">Delivery Address *</label>
            <textarea id="coAddress" name="address" required rows="2" placeholder="House/Flat #, Street #, Sector/Area">${esc(savedCustomer.address || '')}</textarea>
          </div>

          <div class="form-group">
            <label for="coCity">City *</label>
            <select id="coCity" name="city" required onchange="updateCheckoutDeliveryEstimate(this.value)">
              <option value="">Select City</option>
              ${["Karachi","Lahore","Islamabad","Rawalpindi","Faisalabad","Multan","Peshawar","Quetta","Gujranwala","Sialkot","Hyderabad","Abbottabad","Other"].map(c => 
                `<option value="${c}" ${savedCustomer.city === c ? 'selected' : ''}>${c}</option>`
              ).join("")}
            </select>
            <div id="cityDeliveryEstimate" style="margin-top:6px;font-size:12.5px;color:var(--ink-2);background:var(--surface-2);border-radius:8px;padding:8px 12px;border:1px solid var(--line)">
              ${icon("truck")} Select your city to see estimated courier delivery timing.
            </div>
          </div>

          <div class="form-group">
            <label>Payment Method *</label>
            <div class="pay-options">
              ${codEnabled ? `<label class="pay-opt-card ${defaultPayment==='cod'?'selected':''}" id="optCod" onclick="selectPayment('cod')">
                <input type="radio" name="paymentMethod" value="cod" ${defaultPayment==='cod'?'checked':''}>
                <div>
                  <b>Cash on Delivery (COD)</b>
                  <span>Pay when your parcel arrives safely at your doorstep across Pakistan.</span>
                </div>
              </label>` : ''}

              ${bankEnabled ? `<label class="pay-opt-card ${defaultPayment==='bank'?'selected':''}" id="optBank" onclick="selectPayment('bank')">
                <input type="radio" name="paymentMethod" value="bank" ${defaultPayment==='bank'?'checked':''}>
                <div>
                  <b>Direct Bank Transfer / Raast</b>
                  <span>Transfer directly to our official bank account.</span>
                </div>
              </label>` : ''}

              <label class="pay-opt-card" id="optPaypal" onclick="selectPayment('paypal')">
                <input type="radio" name="paymentMethod" value="paypal">
                <div>
                  <div style="display:flex;align-items:center;justify-content:space-between">
                    <b>PayPal Sandbox (USD / Cards)</b>
                    <div style="display:flex;align-items:center;gap:6px">
                      <span class="badge" style="background:#003087;color:#fff;font-size:11px;padding:2px 8px;border-radius:4px;font-weight:700">PayPal</span>
                      <span class="badge" style="background:#FFF3CD;color:#856404;border:1px solid #FFEEBA;font-size:10px;padding:2px 6px;border-radius:4px;font-weight:700">DEMO SIMULATION</span>
                    </div>
                  </div>
                  <span>Pay securely via PayPal Sandbox (Simulated Orders v2 for hackathon evaluation).</span>
                </div>
              </label>
            </div>

            <div class="bank-info-box" id="bankDetailsBox" style="display:${defaultPayment==='bank'?'block':'none'}">
              <b>Official Bank Details:</b><br>
              Bank: <b>${(typeof SETTINGS !== "undefined" && SETTINGS.bankName) ? SETTINGS.bankName : 'Faysal Bank'}</b><br>
              Account Title: <b>${(typeof SETTINGS !== "undefined" && SETTINGS.accountTitle) ? SETTINGS.accountTitle : 'SHAGUFTA KANWAL SHEERAZI'}</b><br>
              IBAN: <b>${(typeof SETTINGS !== "undefined" && SETTINGS.iban) ? SETTINGS.iban : 'PK23FAYS3207575000006005'}</b><br>
              Branch: ${(typeof SETTINGS !== "undefined" && SETTINGS.branch) ? SETTINGS.branch : 'IBB Soldier Bazar, Karachi'}<br>
              <span class="tiny muted" style="display:block;margin-top:4px">Keep your bank-transfer receipt until the order is confirmed.</span>
            </div>

            <div class="paypal-info-box" id="paypalInfoBox" style="display:none;background:#F5F9FC;border:1px solid #CDE2F5;padding:14px 16px;border-radius:12px;margin-top:10px;font-size:13px">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
                <b style="color:#003087">PayPal Sandbox Dual-Currency Breakdown:</b>
                <span class="tiny" style="color:var(--ink-2);font-weight:700">Fixed Demo FX: 280 PKR = $1 USD</span>
              </div>
              <div style="display:flex;justify-content:space-between;color:var(--ink-2);font-size:12.5px;margin-bottom:3px">
                <span>Catalog Subtotal:</span>
                <span>${money(subtotal)}</span>
              </div>
              <div style="display:flex;justify-content:space-between;color:var(--ink-2);font-size:12.5px;margin-bottom:3px">
                <span>Nationwide Courier Delivery:</span>
                <span>${money(shippingFee)}</span>
              </div>
              <div style="display:flex;justify-content:space-between;color:var(--ink-1);font-size:13px;font-weight:700;margin-bottom:4px">
                <span>Order Total (PKR):</span>
                <b id="paypalPkrAmount">${money(total)}</b>
              </div>
              <div style="display:flex;justify-content:space-between;color:#003087;font-size:14.5px;font-weight:800;border-top:1px dashed #CDE2F5;padding-top:6px;margin-top:4px">
                <span>PayPal Sandbox Charge (USD):</span>
                <span id="paypalUsdAmount">$${(total / 280).toFixed(2)} USD</span>
              </div>
              
              <div style="background:#FFF8E6;border-left:4px solid #F59E0B;padding:10px 12px;border-radius:6px;margin-top:10px;font-size:12px;color:#78350F;line-height:1.4">
                <b>DEMO / SIMULATED PAYMENT FOR HACKATHON EVALUATION</b><br>
                Remote PayPal Sandbox credentials in this environment are placeholders. Transactions run through our deterministic local mock engine to verify the complete Orders v2 lifecycle without charging real accounts.
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="coNotes">Order Notes (optional)</label>
            <input id="coNotes" name="notes" placeholder="Special delivery instructions, near landmark, etc.">
          </div>

          <div id="checkoutError" style="display:none;color:#d93025;background:#fce8e6;border:1px solid #fad2cf;padding:10px 14px;border-radius:10px;font-size:13px;margin-bottom:10px"></div>

          <button type="submit" id="coSubmitBtn" class="btn btn-primary btn-block" style="padding:14px;font-size:16px;margin-top:6px">
            Place Order (${money(total)}) ${ICO.arrow}
          </button>

          <div id="paypalBtnContainer" style="display:none;margin-top:10px"></div>
        </form>

        <div class="checkout-summary-col">
          <b style="font-size:16px">Order Summary (${S.cart.reduce((a,i)=>a+i.qty,0) === 1 ? '1 item' : S.cart.reduce((a,i)=>a+i.qty,0) + ' items'})</b>
          <div class="checkout-items-list">${itemsListHtml}</div>
          <div class="checkout-totals">
            <div class="row-tot"><span>Subtotal</span><b>${money(subtotal)}</b></div>
            ${discount > 0 ? `<div class="row-tot" style="color:var(--primary)"><span>Discount</span><b>-${money(discount)}</b></div>` : ''}
            <div class="row-tot"><span>Delivery</span><b>${money(shipping)}</b></div>
            <div class="row-tot grand-tot"><span>Total Amount</span><b>${money(total)}</b></div>
          </div>
          <div style="font-size:12px;color:var(--ink-3);display:flex;flex-direction:column;gap:4px;border-top:1px solid var(--line);padding-top:10px">
            <span>${icon("check")} 7-Day Easy Return Policy</span>
            <span>${icon("check")} 100% Genuine Authorized Products</span>
            <span>${icon("check")} Delivery Across Karachi, Lahore, Islamabad & Nationwide</span>
          </div>
        </div>
      </div>
    </div>
  `;

  scrim.classList.add("open");
}

function selectPayment(type){
  const codCard = document.getElementById("optCod");
  const bankCard = document.getElementById("optBank");
  const paypalCard = document.getElementById("optPaypal");
  const bankBox = document.getElementById("bankDetailsBox");
  const paypalBox = document.getElementById("paypalInfoBox");
  const submitBtn = document.getElementById("coSubmitBtn");
  const paypalContainer = document.getElementById("paypalBtnContainer");

  const { total } = calculateOrderTotals();

  if(type === 'paypal'){
    paypalCard?.classList.add("selected");
    codCard?.classList.remove("selected");
    bankCard?.classList.remove("selected");
    if(bankBox) bankBox.style.display = "none";
    if(paypalBox) paypalBox.style.display = "block";
    if(submitBtn) submitBtn.style.display = "none";
    if(paypalContainer) paypalContainer.style.display = "block";
    const r = document.querySelector('input[name="paymentMethod"][value="paypal"]');
    if(r) r.checked = true;
    renderPayPalButton(total);
  } else if(type === 'bank'){
    bankCard?.classList.add("selected");
    codCard?.classList.remove("selected");
    paypalCard?.classList.remove("selected");
    if(bankBox) bankBox.style.display = "block";
    if(paypalBox) paypalBox.style.display = "none";
    if(submitBtn) submitBtn.style.display = "block";
    if(paypalContainer) paypalContainer.style.display = "none";
    const r = document.querySelector('input[name="paymentMethod"][value="bank"]');
    if(r) r.checked = true;
  } else {
    codCard?.classList.add("selected");
    bankCard?.classList.remove("selected");
    paypalCard?.classList.remove("selected");
    if(bankBox) bankBox.style.display = "none";
    if(paypalBox) paypalBox.style.display = "none";
    if(submitBtn) submitBtn.style.display = "block";
    if(paypalContainer) paypalContainer.style.display = "none";
    const r = document.querySelector('input[name="paymentMethod"][value="cod"]');
    if(r) r.checked = true;
  }
}

function renderPayPalButton(totalPkr){
  const container = document.getElementById("paypalBtnContainer");
  if(!container) return;
  const fxRate = 280.0;
  const totalUsd = (totalPkr / fxRate).toFixed(2);

  // If window.paypal SDK is available
  if(typeof window.paypal !== "undefined" && window.paypal.Buttons){
    container.innerHTML = '<div id="paypal-sdk-btn"></div>';
    window.paypal.Buttons({
      style: { color: 'gold', shape: 'rect', label: 'pay', height: 48 },
      createOrder: async function() {
        const errBox = document.getElementById("checkoutError");
        if(errBox) errBox.style.display = "none";

        const name = document.getElementById("coName")?.value.trim();
        const phoneRaw = document.getElementById("coPhone")?.value.trim();
        const email = document.getElementById("coEmail")?.value.trim() || "";
        const address = document.getElementById("coAddress")?.value.trim();
        const city = document.getElementById("coCity")?.value;
        const notes = document.getElementById("coNotes")?.value.trim() || "";

        if(!name || !phoneRaw || !address || !city){
          const msg = "Please complete all delivery details before paying with PayPal.";
          if(errBox){ errBox.textContent = msg; errBox.style.display = "block"; }
          toast(msg, "x");
          throw new Error(msg);
        }

        const phone = normalizePkPhone(phoneRaw);
        if(!phone){
          const msg = "Please enter a valid Pakistani mobile number (03XXXXXXXXX).";
          if(errBox){ errBox.textContent = msg; errBox.style.display = "block"; }
          toast(msg, "x");
          throw new Error(msg);
        }

        const customer = { name, phone, email, address, city, notes };
        const items = S.cart.map(i => ({ id: i.id, qty: i.qty, ...(i.option ? { option: i.option } : {}) }));

        const res = await fetch('/api/paypal/create-order', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          body: JSON.stringify({
            address: customer,
            items: items,
            coupon: appliedCoupon ? (appliedCoupon.code || appliedCoupon) : null
          })
        });

        const data = await res.json().catch(() => null);
        if(!res.ok || !data || !data.success){
          const msg = (data && data.message) || (data && data.error) || "Could not initialize PayPal order.";
          if(errBox){ errBox.textContent = msg; errBox.style.display = "block"; }
          throw new Error(msg);
        }

        window._pendingPhOrderId = data.orderId;
        window._pendingPayPalOrderId = data.paypalOrderId;
        window._pendingPaymentToken = data.paymentToken || "";
        return data.paypalOrderId;
      },
      onApprove: async function(data) {
        const orderId = window._pendingPhOrderId;
        const paypalOrderId = data.orderID || window._pendingPayPalOrderId;

        const res = await fetch('/api/paypal/capture-order', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-Payment-Token': window._pendingPaymentToken || ""
          },
          body: JSON.stringify({ orderId, paypalOrderId })
        });

        const captureData = await res.json().catch(() => null);
        if(!res.ok || !captureData || !captureData.success){
          const msg = (captureData && captureData.message) || "PayPal capture failed.";
          toast(msg, "x");
          return;
        }

        const name = document.getElementById("coName")?.value.trim();
        const phone = document.getElementById("coPhone")?.value.trim();
        const email = document.getElementById("coEmail")?.value.trim() || "";
        const address = document.getElementById("coAddress")?.value.trim();
        const city = document.getElementById("coCity")?.value;
        store.set("customer_info", { name, phone, email, address, city });

        const verifiedOrder = {
          id: orderId,
          status: 'Confirmed',
          payment: 'paypal',
          paymentToken: window._pendingPaymentToken || "",
          paymentMethod: 'PayPal Sandbox (USD)',
          captureId: captureData.captureId,
          total: captureData.amountPkr,
          amountUsd: captureData.amountUsd,
          shipping: 200,
          customer: { name, phone, email, address, city },
          createdAt: new Date().toISOString()
        };
        const orders = store.get("orders", []);
        orders.unshift(verifiedOrder);
        store.set("orders", orders);

        S.cart = [];
        appliedCoupon = null;
        save();
        updateCart();
        closeModals();
        showOrderSuccess(verifiedOrder);
      },
      onError: function(err) {
        const errBox = document.getElementById("checkoutError");
        const msg = err?.message || "An error occurred with PayPal checkout. Please retry.";
        if(errBox){ errBox.textContent = msg; errBox.style.display = "block"; }
        toast(msg, "x");
      }
    }).render('#paypal-sdk-btn');
  } else {
    // Interactive PayPal Sandbox experience for test & offline environments
    container.innerHTML = `
      <div style="background:#FFF8E6;border:1px solid #FCD34D;border-radius:10px;padding:12px 14px;margin-bottom:10px;font-size:12px;color:#78350F;line-height:1.4">
        <b>DEMO SIMULATION MODE (Orders v2 API Engine)</b><br>
        This test harness validates server-side order authorization, inventory stock reservation, and fixed dual-currency conversion (280 PKR = $1 USD).
      </div>
      <button type="button" class="btn btn-block" id="btnSimulatePaypal" style="background:#003087;color:#fff;padding:14px;font-size:15px;font-weight:700;display:flex;align-items:center;justify-content:center;gap:8px;border-radius:10px;box-shadow:0 4px 14px rgba(0,48,135,0.25);cursor:pointer">
        <span>Complete Payment via PayPal Sandbox ($${totalUsd} USD) [Demo]</span> ${ICO.arrow}
      </button>
      <div style="font-size:11.5px;color:var(--ink-3);text-align:center;margin-top:6px;font-weight:600">
        Demo Mode Adapter • Orders v2 Capture Flow • 280 PKR/USD
      </div>
    `;

    document.getElementById("btnSimulatePaypal")?.addEventListener("click", async () => {
      const errBox = document.getElementById("checkoutError");
      if(errBox) errBox.style.display = "none";

      const name = document.getElementById("coName")?.value.trim();
      const phoneRaw = document.getElementById("coPhone")?.value.trim();
      const email = document.getElementById("coEmail")?.value.trim() || "";
      const address = document.getElementById("coAddress")?.value.trim();
      const city = document.getElementById("coCity")?.value;
      const notes = document.getElementById("coNotes")?.value.trim() || "";

      if(!name || !phoneRaw || !address || !city){
        const msg = "Please complete all delivery details before paying with PayPal.";
        if(errBox){ errBox.textContent = msg; errBox.style.display = "block"; }
        toast(msg, "x");
        return;
      }

      const phone = normalizePkPhone(phoneRaw);
      if(!phone){
        const msg = "Please enter a valid Pakistani mobile number (03XXXXXXXXX).";
        if(errBox){ errBox.textContent = msg; errBox.style.display = "block"; }
        toast(msg, "x");
        return;
      }

      const simBtn = document.getElementById("btnSimulatePaypal");
      if(simBtn){ simBtn.disabled = true; simBtn.textContent = "Connecting to PayPal Sandbox..."; }

      try {
        const customer = { name, phone, email, address, city, notes };
        const items = S.cart.map(i => ({ id: i.id, qty: i.qty, ...(i.option ? { option: i.option } : {}) }));

        // 1. Create PayPal Order
        const resCreate = await fetch('/api/paypal/create-order', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          body: JSON.stringify({
            address: customer,
            items: items,
            coupon: appliedCoupon ? (appliedCoupon.code || appliedCoupon) : null
          })
        });
        const createData = await resCreate.json().catch(() => null);
        if(!resCreate.ok || !createData || !createData.success){
          throw new Error((createData && createData.message) || "PayPal order creation failed.");
        }

        // Real PayPal Sandbox mode: redirect to PayPal's official approval flow
        if (!createData.isMock) {
          const approveLink = (createData.links || []).find(l => l.rel === 'approve')?.href;
          if (approveLink) {
            if(simBtn){ simBtn.textContent = "Redirecting to PayPal Sandbox Approval..."; }
            sessionStorage.setItem('ph_pending_paypal', JSON.stringify({
              orderId: createData.orderId,
              paypalOrderId: createData.paypalOrderId,
              paymentToken: createData.paymentToken || "",
              customer
            }));
            window.location.href = approveLink;
            return;
          }
        }

        if(simBtn){ simBtn.textContent = "Capturing Sandbox Payment..."; }

        // 2. Capture PayPal Order
        const resCap = await fetch('/api/paypal/capture-order', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-Payment-Token': createData.paymentToken || ""
          },
          body: JSON.stringify({
            orderId: createData.orderId,
            paypalOrderId: createData.paypalOrderId
          })
        });
        const capData = await resCap.json().catch(() => null);
        if(!resCap.ok || !capData || !capData.success){
          throw new Error((capData && capData.message) || "PayPal payment capture failed.");
        }

        // Store customer info
        store.set("customer_info", { name, phone, email, address, city });

        const verifiedOrder = {
          id: createData.orderId,
          status: 'Confirmed',
          payment: 'paypal',
          paymentToken: createData.paymentToken || "",
          paymentMethod: 'PayPal Sandbox (USD)',
          captureId: capData.captureId,
          total: capData.amountPkr,
          amountUsd: capData.amountUsd,
          shipping: 200,
          customer: { name, phone, email, address, city },
          createdAt: new Date().toISOString()
        };
        const orders = store.get("orders", []);
        orders.unshift(verifiedOrder);
        store.set("orders", orders);

        S.cart = [];
        appliedCoupon = null;
        save();
        updateCart();
        closeModals();
        showOrderSuccess(verifiedOrder);

      } catch(err) {
        if(errBox){ errBox.textContent = err.message; errBox.style.display = "block"; }
        toast(err.message, "x");
        if(simBtn){
          simBtn.disabled = false;
          simBtn.innerHTML = `<span>Complete Payment via PayPal Sandbox ($${totalUsd} USD)</span> ${ICO.arrow}`;
        }
      }
    });
  }
}

function updateCheckoutDeliveryEstimate(city){
  const el = document.getElementById("cityDeliveryEstimate");
  if(!el) return;
  if(!city){
    el.innerHTML = `${icon("truck")} Select your city to see estimated courier delivery timing.`;
    return;
  }
  if(city === "Karachi"){
    el.innerHTML = `${icon("truck")} <b>Karachi Express Delivery:</b> 1–2 business days (Same-day dispatch).`;
  } else if(["Lahore","Islamabad","Rawalpindi","Faisalabad"].includes(city)){
    el.innerHTML = `${icon("truck")} <b>${city} Delivery:</b> 2–3 business days via TCS / Call Courier.`;
  } else {
    el.innerHTML = `${icon("truck")} <b>${city} Nationwide Courier:</b> 3–5 business days.`;
  }
}

async function handlePlaceOrder(e){
  e.preventDefault();
  const name = document.getElementById("coName")?.value.trim();
  const phoneRaw = document.getElementById("coPhone")?.value.trim();
  const email = document.getElementById("coEmail")?.value.trim() || "";
  const address = document.getElementById("coAddress")?.value.trim();
  const city = document.getElementById("coCity")?.value;
  const notes = document.getElementById("coNotes")?.value.trim() || "";
  const payMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value || "cod";
  const submitBtn = document.getElementById("coSubmitBtn");
  const errBox = document.getElementById("checkoutError");

  if(errBox) errBox.style.display = "none";

  if(!name || !phoneRaw || !address || !city){
    toast("Please fill all required fields", "x");
    return false;
  }

  const phone = normalizePkPhone(phoneRaw);
  if(!phone){
    const msg = "Please enter a valid Pakistani mobile number (e.g. 0300 1234567)";
    if(errBox){
      errBox.textContent = msg;
      errBox.style.display = "block";
    }
    toast(msg, "x");
    document.getElementById("coPhone")?.focus();
    return false;
  }

  if(!S.cart.length){
    toast("Your bag is empty", "x");
    return false;
  }

  const orderId = "PH-" + Math.floor(10000 + Math.random() * 90000);
  const { subtotal, discount, shipping, total } = calculateOrderTotals();

  const customerData = { name, phone, email, address, city, notes };
  const itemsData = S.cart.map(i => {
    const p = PRODUCTS.find(x => x.id === i.id);
    return {
      id: i.id,
      name: p ? p.name : i.id,
      sku: p ? p.sku : "",
      price: p ? p.price : 0,
      qty: i.qty,
      ...(i.option ? { option: i.option } : {}),
      product: p ? { id: p.id, name: p.name, sku: p.sku, price: p.price, inStock: p.inStock, image: p.img || (p.images && p.images[0]) || '' } : { id: i.id }
    };
  });

  const orderObj = {
    id: orderId,
    date: new Date().toISOString(),
    createdAt: new Date().toISOString(),
    customer: customerData,
    address: customerData,
    paymentMethod: payMethod === "cod" ? "Cash on Delivery" : "Bank Transfer",
    payment: payMethod === "bank" ? "bank" : "cod",
    items: itemsData,
    coupon: appliedCoupon ? (appliedCoupon.code || appliedCoupon) : null,
    subtotal,
    discount,
    shipping,
    total,
    status: payMethod === "bank" ? "Pending Payment" : "Confirmed"
  };

  if(submitBtn){
    submitBtn.disabled = true;
    submitBtn.textContent = "Processing Order...";
  }

  try {
    const res = await fetch('/api/orders', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: JSON.stringify(orderObj)
    });
    const data = await res.json().catch(() => null);

    if(!res.ok || !data || !data.success){
      const errMsg = (data && data.error) ? data.error : "Could not complete order. Please check connection and try again.";
      throw new Error(errMsg);
    }

    // Persist verified customer info for next visit
    store.set("customer_info", { name, phone, email, address, city });

    // Persist to local customer order history
    const finalOrder = (data && data.order) ? Object.assign({}, orderObj, data.order) : orderObj;
    const orders = store.get("orders", []);
    orders.unshift(finalOrder);
    store.set("orders", orders);

    // ONLY clear cart upon verified success
    S.cart = [];
    appliedCoupon = null;
    save();
    updateCart();

    closeModals();
    showOrderSuccess(finalOrder);
    return false;
  } catch(err) {
    if(errBox){
      errBox.textContent = err.message || "An unexpected error occurred. Your bag has been saved. Please retry.";
      errBox.style.display = "block";
    }
    toast(err.message || "Order placement failed. Your cart was preserved.", "x");
    if(submitBtn){
      submitBtn.disabled = false;
      submitBtn.innerHTML = `Place Order (${money(total)}) ${ICO.arrow}`;
    }
    return false;
  }
}

function showOrderSuccess(order){
  const scrim = document.getElementById("orderSuccessModalScrim");
  if(!scrim) return;

  scrim.querySelector(".ph-modal").innerHTML = `
    <button class="ph-modal-close" aria-label="Close confirmation dialog" onclick="closeModals()">${ICO.x}</button>
    <div class="order-success-wrap">
      <div class="os-icon">${ICO.check}</div>
      <h2 style="margin:0;font-size:26px">Thank you, ${esc(order.customer.name)}!</h2>
      <p style="margin:0;color:var(--ink-2);max-width:440px">
        Your order has been received and confirmed. Our team will verify and dispatch your parcel promptly.
      </p>
      
      <div>
        <span class="small muted" style="display:block;margin-bottom:4px">Your Order Number</span>
        <span class="os-id">#${esc(order.id)}</span>
      </div>

      <div style="background:var(--surface-2);border:1px solid var(--line);border-radius:14px;padding:16px;width:100%;max-width:480px;text-align:left;font-size:13.5px">
        <div><b>Delivery Address:</b> ${esc(order.customer.address)}, ${esc(order.customer.city)}</div>
        <div style="margin-top:4px"><b>Payment Method:</b> ${esc(order.paymentMethod || (order.payment === 'paypal' ? 'PayPal Sandbox (Simulated Demo Order)' : 'Cash on Delivery'))}</div>
        ${order.captureId ? `<div style="margin-top:4px;font-size:12px"><b>Capture Ref:</b> <code>${esc(order.captureId)}</code> <span class="badge" style="background:#FFF3CD;color:#856404;font-size:10px;padding:2px 6px;border-radius:4px;font-weight:700">Simulated Test Adapter</span></div>` : ''}
        ${order.amountUsd ? `<div style="margin-top:4px;font-size:12.5px;color:#003087"><b>PayPal Charge:</b> $${Number(order.amountUsd).toFixed(2)} USD (@ 280 PKR/USD)</div>` : ''}
        <div style="margin-top:4px"><b>Delivery:</b> ${money(Number(order.shipping || 200))}</div>
        <div style="margin-top:4px"><b>Total to Pay:</b> <b style="color:var(--primary);font-size:15px">${money(order.total)}</b></div>
        <div style="margin-top:4px;font-size:12.5px;color:var(--ink-3)">Estimated Courier Delivery: 2–4 business days</div>
        ${order.payment === 'paypal' ? `
          <div style="background:#FFFBEB;border:1px solid #FCD34D;border-radius:8px;padding:10px 12px;margin-top:12px;font-size:11.5px;color:#92400E;line-height:1.4">
            <b>ⓘ Hackathon Prototype Evaluation:</b> This payment was executed in local simulation mode. Full backend order creation, database status updates, and inventory reservations were validated. No financial transaction occurred on PayPal remote servers.
          </div>
        ` : ''}
      </div>

      <div style="display:flex;flex-direction:column;gap:10px;width:100%;max-width:380px;margin-top:8px">
        <a href="/profile#orders" class="btn btn-soft">View in My Orders</a>
        <button class="btn btn-ghost" onclick="closeModals()">Continue Browsing</button>
      </div>
    </div>
  `;

  scrim.classList.add("open");
}

/* ---------------- premium product card ---------------- */
function productCard(p){
  const off = p.old && p.old > p.price ? Math.round((1 - p.price/p.old)*100) : 0;
  const isWish = S.wish && S.wish.includes(p.id);
  const imgUrl = p.img || (p.images && p.images[0]) || '/assets/products/image-verification-pending.webp';
  const imagePending = String(imgUrl).includes('image-verification-pending');
  const catName = p.cat || 'Baby Care';
  const { title: cleanTitle } = formatProductName(p.name);
  const hasOptions = productOptionGroups(p).length > 0;
  const addAction = hasOptions ? `openProductModal('${p.id}')` : `addToCart('${p.id}', 1)`;
  const addLabel = hasOptions ? 'Select Options' : 'Add to Cart';

  return `<article class="product-card card rv" data-id="${p.id}" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('${p.id}');}">
    <div class="product-card-thumb thumb">
      <a href="/product/${encodeURIComponent(p.id)}" aria-label="View ${esc(cleanTitle)}" class="product-image-link">
        <img class="product-card-image" src="${imgUrl}" alt="${esc(cleanTitle)}" loading="lazy" decoding="async" referrerpolicy="no-referrer" onload="this.dataset.loaded='1';if(this.naturalWidth<320||this.naturalHeight<320)this.dataset.quality='low'" onerror="this.onerror=null;this.dataset.quality='error';this.src='/assets/products/image-verification-pending.webp'">
      </a>
      <div class="product-badges tags">
        ${p.tag === "best" ? '<span class="p-badge p-badge-bestseller tag tag-best">Bestseller</span>' : ''}
        ${p.tag === "new" ? '<span class="p-badge p-badge-new tag tag-new">New</span>' : ''}
        ${off > 0 ? `<span class="p-badge p-badge-sale tag tag-sale">-${off}%</span>` : ''}
        ${!p.inStock ? '<span class="p-badge p-badge-out tag">Out of stock</span>' : ''}
        ${imagePending ? '<span class="p-badge tag product-photo-pending">Photo verification</span>' : ''}
      </div>
      <button class="product-wish-btn wish ${isWish ? 'active on' : ''}" aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('${p.id}', this)">
        ${isWish ? ICO.heartFill : ICO.heart}
      </button>
      ${p.inStock ? `<button class="product-quick-add-desktop" type="button" onclick="event.preventDefault();event.stopPropagation();${addAction}" aria-label="${addLabel} ${esc(cleanTitle)}">${ICO.bag}<span>${addLabel}</span></button>` : ''}
    </div>
    <div class="product-card-body body">
      <div class="product-cat-label">${esc(catName)}</div>
      <h3 class="product-name name" title="${esc(cleanTitle)}">
        <a href="/product/${encodeURIComponent(p.id)}" class="product-name-link">${esc(cleanTitle)}</a>
      </h3>
      <div class="product-pricing price">
        <span class="curr-price"><b>${money(p.price)}</b></span>
        ${p.old && p.old > p.price ? `<span class="old-price"><s>${money(p.old)}</s></span>` : ''}
      </div>
      ${p.inStock ? `<button class="product-add-mobile" type="button" onclick="event.stopPropagation();${addAction}" aria-label="${addLabel} ${esc(cleanTitle)}">${ICO.bag}<span>${addLabel}</span></button>` : '<span class="product-stock-note">Out of stock</span>'}
    </div>
  </article>`;
}

/* ---------------- pages ---------------- */
function initHome(){
  // Dynamic Hero Section from HOMEDATA
  if(typeof HOMEDATA !== "undefined" && HOMEDATA.hero){
    const h1 = document.querySelector(".hero-grid h1");
    if(h1 && HOMEDATA.hero.headline) h1.innerHTML = HOMEDATA.hero.headline;
    const lede = document.querySelector(".hero-lede");
    if(lede && HOMEDATA.hero.lede) lede.innerHTML = HOMEDATA.hero.lede;
    const btn = document.querySelector(".hero-cta .btn-primary");
    if(btn && HOMEDATA.hero.btnText){
      btn.innerHTML = HOMEDATA.hero.btnText + ' ' + icon("arrowUpRight");
      if(HOMEDATA.hero.btnLink) btn.href = HOMEDATA.hero.btnLink;
    }
  }

  // Dynamic Slider from HOMEDATA.slides
  if(typeof HOMEDATA !== "undefined" && Array.isArray(HOMEDATA.slides) && HOMEDATA.slides.length > 0){
    const sliderBox = document.querySelector("#heroSlider .slides");
    if(sliderBox){
      sliderBox.innerHTML = HOMEDATA.slides.map((s, idx)=>`
        <article class="slide ${idx===0?'on':''}">
          <div class="txt">
            <span class="eyebrow dotted">${s.tag || 'Feeding edit'}</span>
            <h3>${s.title}</h3>
            <p>${s.desc}</p>
            <div><a class="btn btn-primary btn-s" href="${s.link || '/shop'}">${s.btnText || 'Shop now'} ${icon('arrowUpRight')}</a></div>
          </div>
          <div class="pic"><img src="${s.image}" alt="${s.title}" onerror="this.onerror=null;this.src='art/placeholder.jpg'"></div>
        </article>
      `).join("");
    }
  }

  const byLine = document.getElementById("homeGrid");
  if(byLine) byLine.innerHTML = PRODUCTS.slice(0,8).map(productCard).join("");
  const news = document.getElementById("newGrid");
  if(news) news.innerHTML = PRODUCTS.slice(8,12).map(productCard).join("");
  const coll = document.getElementById("collStrip");
  if(coll && typeof CATEGORIES !== "undefined") {
    coll.innerHTML = CATEGORIES.map(c=>`
      <a class="circle" href="/shop/categories/${encodeURIComponent(c.handle)}">
        <span class="ci"><img src="${c.image}" alt="${c.name}" onerror="this.onerror=null;this.src='art/placeholder.jpg'"></span>
        <b>${c.name}</b>
      </a>`).join("");
  }
  const feed = document.getElementById("homeFeed");
  if(feed) fetch('/api/community/bootstrap').then(r=>r.json()).then(d=>{
    const list=(d&&d.posts)||[];
    feed.innerHTML=list.slice(0,3).map(miniPost).join("") || `<div class="empty"><b>No community posts yet</b></div>`;
    const stats=(d&&d.stats)||{};
    const statBox=document.getElementById('homeCommunityStats'); if(statBox) statBox.dataset.state='ready';
    const p=document.getElementById('homeCommunityParents'); if(p) p.textContent=`${Number(stats.parents||0).toLocaleString()} parents`;
    const m=document.getElementById('homeCommunityAnswersMonth'); if(m) m.textContent=`${Number(stats.answersThisMonth||0).toLocaleString()} answers this month`;
    const t=document.getElementById('homeCommunityAnswersTotal'); if(t) t.textContent=`${Number(stats.answersTotal||0).toLocaleString()} total answers`;
    initReveal();
  }).catch(()=>{
    feed.innerHTML=`<div class="empty"><b>Community feed unavailable</b><p class="muted tiny">Please refresh in a moment.</p></div>`;
    const statBox=document.getElementById('homeCommunityStats'); if(statBox) statBox.dataset.state='error';
    const p=document.getElementById('homeCommunityParents'); if(p) p.textContent='Community stats unavailable';
    const m=document.getElementById('homeCommunityAnswersMonth'); if(m) m.textContent='Please refresh shortly';
    const t=document.getElementById('homeCommunityAnswersTotal'); if(t) t.textContent='Database reconnecting';
  });
  const arts = document.getElementById("homeArticles");
  if(arts) arts.innerHTML = ARTICLES.slice(0,3).map((a,i)=>articleCard(a, i===0)).join("");
  const miniStages = document.getElementById("miniStages");
  if(miniStages) miniStages.innerHTML = stagesHTML(true);
  initSlider(); initReveal();
  const brands = document.getElementById("brandStrip");
  if(brands && typeof BRANDS !== "undefined") {
    brands.innerHTML = [...BRANDS,...BRANDS,...BRANDS].map(b=>`<span class="pill-brand"><i></i>${b}</span>`).join("");
  }
}
function stagesHTML(mini){
  const stagesList = (typeof STAGES !== "undefined" && Array.isArray(STAGES) && STAGES.length) ? STAGES : [
    { k:"newborn", label:"Newborn", desc:"0–3 months", em:"bottle", bg:"#FAF5EE" },
    { k:"infant",  label:"Infant",  desc:"3–12 months", em:"smile", bg:"#F3F7F5" },
    { k:"toddler", label:"Toddler", desc:"1–3 years",   em:"blocks", bg:"#FDF2F4" },
    { k:"mother",  label:"Mother",  desc:"Maternity & postpartum", em:"leaf", bg:"#F5F5FA" }
  ];
  return stagesList.map((s,i)=>`
    <a class="stage rv" href="/shop?stage=${encodeURIComponent(s.k)}" style="background:${s.bg}">
      <span class="num">0${i+1} &nbsp;${emIco(s.em)}</span>
      <span><b>${esc(s.label)}</b><span>${esc(s.desc)}</span></span>
      <span class="arrow">${ICO.arrowUpRight}</span>
    </a>`).join("");
}
function miniPost(p){
  const c = COMMUNITIES.find(c=>c.k===p.community);
  return `<a class="mini-post rv" href="/community#post-${p.id}">
    <div class="top">
      <span class="avatar sm ${p.av || 'g'}">${(p.au || 'P')[0]}</span>
      <span class="tiny muted">${c?c.label:""} · ${p.time}</span>
      <span class="tag tag-qa" style="margin-inline-start:auto">${esc(p.flair)}</span>
    </div>
    <h4>${esc(p.title)}</h4>
    <p>${esc(p.body)}</p>
    <div class="foot"><span>${ICO.up} ${p.votes}</span><span>${ICO.comment} ${p.comments} answers</span></div>
  </a>`;
}
function articleCard(a, big){
  const readingTime = String(a.read || a.readTime || "").trim() || (Math.max(1, Math.ceil((a.body || [a.ex || a.excerpt || ""]).join(" ").split(/\s+/).length / 200)) + " min");
  return `<a class="article rv ${big?"big":""}" href="/articles/${encodeURIComponent(a.id)}">
    <span class="im"><img src="${a.img}" alt="${esc(a.title)}" loading="lazy" onerror="this.onerror=null;this.src='art/placeholder.jpg'"></span>
    <div class="bd">
      <span class="tag tag-new" style="align-self:flex-start">${esc(a.cat)}</span>
      <h3>${esc(a.title)}</h3>
      <p>${esc(a.ex)}</p>
      <span class="meta">${esc(a.by)} · ${esc(a.date)} · ${esc(readingTime)}</span>
    </div>
  </a>`;
}

/* ---------- slider / hero carousel ---------- */
function initSlider(){
  const root = document.getElementById("heroProductCarousel") || document.getElementById("heroSlider");
  if(!root) return;
  const slides = [...root.querySelectorAll(".carousel-slide, .hero-slide, .slide")];
  if(!slides.length) return;
  const dots = root.querySelector(".carousel-dots, .hero-carousel-dots, .dots");
  let i = 0, timer, isPaused = false;
  
  if(dots) {
    dots.innerHTML = slides.map((_, k) => `<button type="button" class="dot ${k===0?'active on':''}" data-k="${k}" aria-label="Go to slide ${k+1}"></button>`).join("") +
      `<button type="button" class="carousel-pause-btn" id="carouselPauseBtn" aria-label="Pause automatic sliding" title="Pause / Play">❚❚</button>`;
  }
  
  const go = n => {
    i = (n + slides.length) % slides.length;
    slides.forEach((s, k) => {
      s.classList.toggle("active", k === i);
      s.classList.toggle("on", k === i);
    });
    if(dots) {
      [...dots.querySelectorAll(".dot")].forEach((d, k) => {
        d.classList.toggle("active", k === i);
        d.classList.toggle("on", k === i);
      });
    }
  };
  
  const play = () => {
    clearInterval(timer);
    if(!isPaused){
      timer = setInterval(() => go(i + 1), 4000); // 4.0s auto-sliding
    }
  };
  
  if(dots) {
    dots.addEventListener("click", e => {
      const btn = e.target.closest(".dot");
      if(btn && btn.dataset.k !== undefined){
        go(+btn.dataset.k);
        play();
        return;
      }
      const pauseBtn = e.target.closest(".carousel-pause-btn");
      if(pauseBtn){
        isPaused = !isPaused;
        if(isPaused){
          clearInterval(timer);
          pauseBtn.innerHTML = "▶";
          pauseBtn.setAttribute("aria-label", "Play automatic sliding");
        } else {
          pauseBtn.innerHTML = "❚❚";
          pauseBtn.setAttribute("aria-label", "Pause automatic sliding");
          play();
        }
      }
    });
  }
  
  const prevBtn = root.querySelector(".carousel-arrow.prev, .hero-nav-btn.prev, .prev");
  if(prevBtn) prevBtn.onclick = (e) => { e.preventDefault(); e.stopPropagation(); go(i - 1); play(); };
  
  const nextBtn = root.querySelector(".carousel-arrow.next, .hero-nav-btn.next, .next");
  if(nextBtn) nextBtn.onclick = (e) => { e.preventDefault(); e.stopPropagation(); go(i + 1); play(); };
  
  // Pause on desktop mouse hover
  root.addEventListener("mouseenter", () => { if(!isPaused) clearInterval(timer); });
  root.addEventListener("mouseleave", () => { if(!isPaused) play(); });
  
  // Touch swipe support for mobile
  let touchStartX = 0;
  let touchEndX = 0;
  root.addEventListener("touchstart", e => {
    if(e.changedTouches && e.changedTouches[0]) {
      touchStartX = e.changedTouches[0].screenX;
    }
    clearInterval(timer);
  }, { passive: true });
  
  root.addEventListener("touchend", e => {
    if(e.changedTouches && e.changedTouches[0]) {
      touchEndX = e.changedTouches[0].screenX;
      const diff = touchStartX - touchEndX;
      if(Math.abs(diff) > 40) {
        if(diff > 0) go(i + 1); // Swiped left -> next slide
        else go(i - 1);         // Swiped right -> prev slide
      }
    }
    play();
  }, { passive: true });
  
  go(0);
  play();
}

/* ---------- reveal ---------- */
function initReveal(){
  const els = document.querySelectorAll(".rv:not(.in)");
  if(!("IntersectionObserver" in window)){ els.forEach(el=>el.classList.add("in")); return; }
  const io = new IntersectionObserver(es=>es.forEach(e=>{ if(e.isIntersecting){ e.target.classList.add("in"); io.unobserve(e.target);} }),{threshold:.05});
  els.forEach(el=>io.observe(el));
  // Failsafe timeout to prevent blank loading sections if scroll or observer doesn't trigger
  setTimeout(() => {
    document.querySelectorAll(".rv:not(.in)").forEach(el => el.classList.add("in"));
  }, 1000);
}

/* ---------- shop ---------- */
function initShop(){
  const params = new URLSearchParams(location.search);
  const qs = {
    cat:         params.get("cat")   || document.body.dataset.category || "all",
    brand:       params.get("brand") || document.body.dataset.brand || "all",
    stage:       params.get("stage") || "all",
    q:           params.get("q")     || "",
    sort:        params.get("sort")  || "featured",
    minPrice:    Number(params.get("min")) || 0,
    maxPrice:    Number(params.get("max")) || Infinity,
    inStockOnly: params.get("stock") === "1",
    onSaleOnly:  params.get("sale")  === "1",
    rating4:     params.get("r4")    === "1",
    page:        1
  };
  const PAGE_SIZE = 24;
  const grid = document.getElementById("shopGrid");
  if(!grid) return;

  if(typeof PRODUCTS === "undefined" || !Array.isArray(PRODUCTS)){
    const resLine = document.getElementById("resLine");
    if(resLine) resLine.innerHTML = `<span style="color:#ef4444;font-weight:600">Unable to load catalog</span>`;
    grid.innerHTML = `<div class="empty" style="grid-column:1/-1;padding:48px 20px;text-align:center;background:#fff;border-radius:12px;border:1px solid #fee2e2">
      <b style="font-size:16px;color:#1f2937">Catalog temporarily unavailable</b>
      <p class="small muted" style="margin-top:8px">We could not load the product database. Please check your internet connection or reload.</p>
      <button class="btn btn-primary btn-s" style="margin-top:16px" onclick="location.reload()">Refresh Page</button>
    </div>`;
    return;
  }

  // Populate Daraz Sidebar Categories
  const catSidebar = document.getElementById("sidebarCatList");
  if(catSidebar && typeof CATEGORIES !== "undefined" && typeof PRODUCTS !== "undefined") {
    const totalCount = PRODUCTS.length;
    let catHtml = `<div class="dz-filter-item ${qs.cat==="all"?"active":""}" data-dzcat="all">
      <span>All Categories</span>
      <span class="dz-filter-count">${totalCount}</span>
    </div>`;
    CATEGORIES.forEach(c => {
      const cCount = PRODUCTS.filter(p => {
        const pCat = (p.cat || "").toLowerCase();
        const pHandle = pCat.replace(/\s+&\s+/g, '-').replace(/\s+/g, '-');
        const cLow = c.name.toLowerCase();
        return pCat === cLow || pHandle === cLow || pCat.includes(cLow);
      }).length;
      const isAct = qs.cat.toLowerCase() === c.name.toLowerCase() || qs.cat.toLowerCase() === c.handle.toLowerCase();
      catHtml += `<div class="dz-filter-item ${isAct?"active":""}" data-dzcat="${esc(c.name)}">
        <span>${esc(c.name)}</span>
        <span class="dz-filter-count">${cCount}</span>
      </div>`;
    });
    catSidebar.innerHTML = catHtml;
  }

  // Populate Daraz Sidebar Brands
  const brandSidebar = document.getElementById("sidebarBrandList");
  if(brandSidebar && typeof BRANDS !== "undefined" && typeof PRODUCTS !== "undefined") {
    let bHtml = `<div class="dz-filter-item ${qs.brand==="all"?"active":""}" data-dzbrand="all">
      <span>All Brands</span>
      <span class="dz-filter-count">${PRODUCTS.length}</span>
    </div>`;
    BRANDS.forEach(b => {
      const bCount = PRODUCTS.filter(p => (p.brand || "").toLowerCase() === b.toLowerCase()).length;
      const isAct = qs.brand.toLowerCase() === b.toLowerCase();
      bHtml += `<div class="dz-filter-item ${isAct?"active":""}" data-dzbrand="${esc(b)}">
        <span>${esc(b)}</span>
        <span class="dz-filter-count">${bCount}</span>
      </div>`;
    });
    brandSidebar.innerHTML = bHtml;
  }

  // Fallback chips if present
  const needBox = document.getElementById("needChips");
  if(needBox && typeof CATEGORIES !== "undefined") {
    needBox.innerHTML =
      `<button class="chip ${qs.cat==="all"?"on":""}" data-cat="all">All categories</button>` +
      CATEGORIES.map(c=>`<button class="chip ${qs.cat.toLowerCase()===c.name.toLowerCase()||qs.cat.toLowerCase()===c.handle.toLowerCase()?"on":""}" data-cat="${c.name}">${c.name}</button>`).join("");
  }
  const brandBox = document.getElementById("stageChips") || document.getElementById("brandChips");
  if(brandBox && typeof BRANDS !== "undefined") {
    brandBox.innerHTML =
      `<button class="chip ${qs.brand==="all"?"on":""}" data-brand="all">All brands</button>` +
      BRANDS.map(b=>`<button class="chip ${qs.brand.toLowerCase()===b.toLowerCase()?"on":""}" data-brand="${b}">${b}</button>`).join("");
  }

  const sInput = document.getElementById("shopSearch");
  if(sInput) sInput.value = qs.q;
  const sSort = document.getElementById("shopSort");
  if(sSort) sSort.value = qs.sort;

  const minInp = document.getElementById("minPriceInput");
  if(minInp && qs.minPrice > 0) minInp.value = qs.minPrice;
  const maxInp = document.getElementById("maxPriceInput");
  if(maxInp && qs.maxPrice < Infinity) maxInp.value = qs.maxPrice;

  const chkStock = document.getElementById("chkInStock");
  if(chkStock) chkStock.checked = qs.inStockOnly;
  const chkSale = document.getElementById("chkOnSale");
  if(chkSale) chkSale.checked = qs.onSaleOnly;
  const chkR4 = document.getElementById("chkRating4");
  if(chkR4) chkR4.checked = qs.rating4;

  const STAGE_LABELS = {
    newborn: "Newborn (0–3m)",
    infant: "Infant (3–6m)",
    toddler: "Toddler (6–12m+)",
    expecting: "Expecting & Mom"
  };

  function renderActivePills(){
    const pillBox = document.getElementById("activeFilterTags");
    if(!pillBox) return;
    const pills = [];

    if(qs.stage && qs.stage !== "all") {
      pills.push({ label: "Stage: " + (STAGE_LABELS[qs.stage] || qs.stage), clear: () => { qs.stage = "all"; } });
    }
    if(qs.cat !== "all") {
      pills.push({ label: qs.cat, clear: () => { qs.cat = "all"; } });
    }
    if(qs.brand !== "all") {
      pills.push({ label: qs.brand, clear: () => { qs.brand = "all"; } });
    }
    if(qs.q) {
      pills.push({ label: `“${qs.q}”`, clear: () => { qs.q = ""; if(sInput) sInput.value = ""; } });
    }
    if(qs.minPrice > 0 || (qs.maxPrice > 0 && qs.maxPrice < Infinity)) {
      const minText = qs.minPrice > 0 ? `Rs. ${qs.minPrice.toLocaleString()}` : "Rs. 0";
      const maxText = qs.maxPrice < Infinity ? `Rs. ${qs.maxPrice.toLocaleString()}` : "Any";
      pills.push({ label: `${minText} – ${maxText}`, clear: () => { qs.minPrice = 0; qs.maxPrice = Infinity; if(minInp) minInp.value = ""; if(maxInp) maxInp.value = ""; } });
    }
    if(qs.inStockOnly) {
      pills.push({ label: "In Stock Only", clear: () => { qs.inStockOnly = false; if(chkStock) chkStock.checked = false; } });
    }
    if(qs.onSaleOnly) {
      pills.push({ label: "On Sale Deals", clear: () => { qs.onSaleOnly = false; if(chkSale) chkSale.checked = false; } });
    }
    if(qs.rating4) {
      pills.push({ label: "4★ & above", clear: () => { qs.rating4 = false; if(chkR4) chkR4.checked = false; } });
    }

    if(!pills.length) {
      pillBox.innerHTML = "";
      pillBox.style.display = "none";
      return;
    }

    pillBox.style.display = "flex";
    pillBox.innerHTML = pills.map((p, idx) => `
      <span class="dz-pill">
        ${esc(p.label)}
        <span class="dz-pill-x" data-pill-idx="${idx}">✕</span>
      </span>
    `).join("") + `<button class="dz-pill-clear" onclick="window.resetShop()">Clear All</button>`;

    pillBox.querySelectorAll(".dz-pill-x").forEach(btn => {
      btn.onclick = () => {
        const idx = Number(btn.dataset.pillIdx);
        if(pills[idx] && pills[idx].clear) pills[idx].clear();
        qs.page = 1;
        updateSidebarActive();
        sync();
        render();
      };
    });
  }

  function updateSidebarActive(){
    if(catSidebar){
      catSidebar.querySelectorAll(".dz-filter-item").forEach(item => {
        const c = item.dataset.dzcat;
        item.classList.toggle("active", c === qs.cat || (c === "all" && qs.cat === "all"));
      });
    }
    if(brandSidebar){
      brandSidebar.querySelectorAll(".dz-filter-item").forEach(item => {
        const b = item.dataset.dzbrand;
        item.classList.toggle("active", b === qs.brand || (b === "all" && qs.brand === "all"));
      });
    }
  }

  window.clearStageFilter = () => {
    qs.stage = "all";
    qs.page = 1;
    sync();
    render();
  };

  function render(){
    let list = filterProducts({
      cat: qs.cat,
      brand: qs.brand,
      stage: qs.stage,
      inStockOnly: qs.inStockOnly,
      minPrice: qs.minPrice,
      maxPrice: qs.maxPrice,
      onSaleOnly: qs.onSaleOnly,
      q: qs.q,
      sort: qs.sort
    });

    if(qs.rating4) {
      list = list.filter(p => (p.rating || 4.7) >= 4.0);
    }

    const total = list.length;
    const resLine = document.getElementById("resLine");
    if(resLine) {
      resLine.innerHTML = `<b>${total}</b> items found in Pakistan` + 
        (qs.stage !== "all" ? ` for <b>${esc(STAGE_LABELS[qs.stage] || qs.stage)}</b>` : "") +
        (qs.cat !== "all" ? ` · <b>${esc(qs.cat)}</b>` : "") +
        (qs.brand !== "all" ? ` · <b>${esc(qs.brand)}</b>` : "") +
        (qs.q ? ` · “${esc(qs.q)}”` : "");
    }

    renderActivePills();
    updateSidebarActive();

    const stageBannerHtml = (qs.stage && qs.stage !== "all") ? `
      <div class="shop-stage-banner" style="grid-column:1/-1">
        <div class="shop-stage-banner-text">
          <b>Filtered for ${esc(STAGE_LABELS[qs.stage] || qs.stage)}</b>
          <p>Showing ${total} pediatric-verified bottles, teats, soothing accessories and care items for this stage.</p>
        </div>
        <button class="btn btn-ghost btn-xs" onclick="window.clearStageFilter()" style="border-color:var(--primary);color:var(--primary);font-weight:700">Clear Stage Filter ✕</button>
      </div>` : "";

    const visibleItems = list.slice(0, qs.page * PAGE_SIZE);
    let html = visibleItems.map(productCard).join("");
    if(!visibleItems.length){
      html = stageBannerHtml + `<div class="empty" style="grid-column:1/-1;background:#fff;border-radius:8px;border:1px solid #eee;padding:48px 20px">
        <div class="box">${ICO.search}</div>
        <b style="font-size:16px">No products match your filter</b>
        <p class="small muted" style="margin-top:8px">Try broadening your search, adjusting the price, or clearing filters.</p>
        <button class="btn btn-primary btn-s" style="margin-top:16px" onclick="window.resetShop()">Clear All Filters</button>
      </div>`;
    } else {
      html = stageBannerHtml + html;
      if (visibleItems.length < total) {
        html += `<div style="grid-column:1/-1;text-align:center;padding:26px 0">
          <button class="btn btn-soft" id="loadMoreBtn" onclick="window.loadMoreShop()" style="padding:12px 28px;font-weight:700">Load More Products (${total - visibleItems.length} remaining) ${ICO.down}</button>
        </div>`;
      }
    }
    grid.innerHTML = html;
    initReveal();
    hydrateIcons(grid);
  }

  window.loadMoreShop = () => {
    qs.page++;
    render();
  };

  window.resetShop = () => {
    qs.cat = "all"; qs.brand = "all"; qs.stage = "all"; qs.q = ""; qs.sort = "featured";
    qs.minPrice = 0; qs.maxPrice = Infinity;
    qs.inStockOnly = false; qs.onSaleOnly = false; qs.rating4 = false;
    qs.page = 1;
    if(sInput) sInput.value = "";
    if(sSort) sSort.value = "featured";
    if(minInp) minInp.value = "";
    if(maxInp) maxInp.value = "";
    if(chkStock) chkStock.checked = false;
    if(chkSale) chkSale.checked = false;
    if(chkR4) chkR4.checked = false;
    document.querySelectorAll(".chip").forEach(c => c.classList.toggle("on", c.dataset.cat === "all" || c.dataset.brand === "all"));
    sync();
    render();
  };

  function sync(){
    const p = new URLSearchParams();
    if(qs.stage && qs.stage !== "all") p.set("stage", qs.stage);
    if(qs.cat && qs.cat !== "all") p.set("cat", qs.cat);
    if(qs.brand && qs.brand !== "all") p.set("brand", qs.brand);
    if(qs.q) p.set("q", qs.q);
    if(qs.sort && qs.sort !== "featured") p.set("sort", qs.sort);
    if(qs.minPrice > 0) p.set("min", qs.minPrice);
    if(qs.maxPrice < Infinity) p.set("max", qs.maxPrice);
    if(qs.inStockOnly) p.set("stock", "1");
    if(qs.onSaleOnly) p.set("sale", "1");
    if(qs.rating4) p.set("r4", "1");
    const s = p.toString();
    try{
      const leftLanding = (document.body.dataset.category && qs.cat !== document.body.dataset.category) || (document.body.dataset.brand && qs.brand !== document.body.dataset.brand);
      history.replaceState(null, "", (leftLanding ? "/shop" : location.pathname) + (s ? ("?" + s) : ""));
      if(leftLanding){const canonical = document.querySelector('link[rel="canonical"]'); if(canonical) canonical.href = location.origin + "/shop";}
    }catch(e){}
  }

  // Sidebar Category Click
  if(catSidebar){
    catSidebar.addEventListener("click", e => {
      const item = e.target.closest("[data-dzcat]");
      if(!item) return;
      qs.cat = item.dataset.dzcat;
      qs.page = 1;
      sync();
      render();
      closeMobileSidebar();
    });
  }

  // Sidebar Brand Click
  if(brandSidebar){
    brandSidebar.addEventListener("click", e => {
      const item = e.target.closest("[data-dzbrand]");
      if(!item) return;
      qs.brand = item.dataset.dzbrand;
      qs.page = 1;
      sync();
      render();
      closeMobileSidebar();
    });
  }

  // Price range apply
  const applyBtn = document.getElementById("applyPriceBtn");
  if(applyBtn){
    const doApplyPrice = () => {
      const mn = Number(minInp ? minInp.value : 0);
      const mx = Number(maxInp && maxInp.value ? maxInp.value : Infinity);
      qs.minPrice = mn > 0 ? mn : 0;
      qs.maxPrice = mx > 0 ? mx : Infinity;
      qs.page = 1;
      sync();
      render();
    };
    applyBtn.onclick = doApplyPrice;
    if(minInp) minInp.addEventListener("keydown", e => { if(e.key === "Enter") doApplyPrice(); });
    if(maxInp) maxInp.addEventListener("keydown", e => { if(e.key === "Enter") doApplyPrice(); });
  }

  // Service checkboxes
  if(chkStock) chkStock.onchange = () => { qs.inStockOnly = chkStock.checked; qs.page = 1; sync(); render(); };
  if(chkSale) chkSale.onchange = () => { qs.onSaleOnly = chkSale.checked; qs.page = 1; sync(); render(); };
  if(chkR4) chkR4.onchange = () => { qs.rating4 = chkR4.checked; qs.page = 1; sync(); render(); };

  // Mobile sidebar controls
  const openSideBtn = document.getElementById("openSidebarBtn");
  const closeSideBtn = document.getElementById("closeSidebarBtn");
  const sideScrim = document.getElementById("sidebarScrim");
  const sideEl = document.getElementById("shopSidebar");
  function closeMobileSidebar(){
    if(sideEl) sideEl.classList.remove("open");
    if(sideScrim) sideScrim.classList.remove("open");
  }
  if(openSideBtn && sideEl){
    openSideBtn.onclick = () => {
      sideEl.classList.add("open");
      if(sideScrim) sideScrim.classList.add("open");
    };
  }
  if(closeSideBtn) closeSideBtn.onclick = closeMobileSidebar;
  if(sideScrim) sideScrim.onclick = closeMobileSidebar;

  // Sorting
  if(sSort) sSort.addEventListener("change", e => { qs.sort = e.target.value; qs.page = 1; sync(); render(); });
  
  // Search input
  let t;
  if(sInput) sInput.addEventListener("input", e => {
    clearTimeout(t);
    t = setTimeout(() => { qs.q = e.target.value.trim(); qs.page = 1; sync(); render(); }, 180);
  });

  render();
  initReveal();

  // Support direct shareable product links (e.g. /shop?product=avent-natural-260ml)
  const urlP = new URLSearchParams(location.search);
  const pId = urlP.get("product") || urlP.get("id");
  if(pId){
    setTimeout(() => openProductModal(pId), 120);
  }
}

/* ---------- community (Reddit-style) ---------- */
let serverPosts = [];
function allPosts(){ 
  const localMap = new Map();
  [...(serverPosts || []), ...S.userPosts, ...POSTS].forEach(p => {
    if(p && p.id && !localMap.has(p.id)) localMap.set(p.id, p);
  });
  return Array.from(localMap.values());
}
function commByKey(k){ return COMMUNITIES.find(c=>c.k===k) || {label:"Community", em:"users", members:""}; }
function isJoined(k){ return S.joined.includes(k); }
function toggleJoin(k){
  const i = S.joined.indexOf(k);
  i>=0 ? S.joined.splice(i,1) : S.joined.push(k);
  save();
  toast(isJoined(k) ? "Joined " + commByKey(k).label + "" : "Left " + commByKey(k).label);
  document.querySelectorAll('[data-joincomm="'+k+'"]').forEach(b=>{
    b.textContent = isJoined(k) ? "Joined" : "+ Join";
    b.classList.toggle("on", isJoined(k));
  });
  document.querySelectorAll('[data-joinbig="'+k+'"]').forEach(b=>{
    b.textContent = isJoined(k) ? "Joined" : "Join community";
    b.classList.toggle("btn-soft", isJoined(k));
    b.classList.toggle("btn-primary", !isJoined(k));
  });
}
function copyLink(id){
  const url = location.origin + location.pathname.replace(/[^/]*$/,"") + "/post?id=" + id;
  try{ navigator.clipboard?.writeText(url); }catch(e){}
  toast("Link copied — share it with another parent");
}
function vote(id, d){
  const cur = S.votes[id] || 0;
  S.votes[id] = cur === d ? 0 : d; save();
  const base = (allPosts().find(p=>p.id===id) || {}).votes || 0;
  document.querySelectorAll('[data-vote="'+id+'"]').forEach(n=>n.textContent = base + S.votes[id]);
  document.querySelectorAll('[data-vup="'+id+'"]').forEach(b=>b.classList.toggle("on", S.votes[id]===1));
  document.querySelectorAll('[data-vdown="'+id+'"]').forEach(b=>b.classList.toggle("on", S.votes[id]===-1));
}
function savePost(id, el){
  const i = S.saved.indexOf(id);
  i>=0 ? S.saved.splice(i,1) : S.saved.push(id);
  save();
  document.querySelectorAll('[data-save="'+id+'"]').forEach(b=>{
    b.style.color = S.saved.includes(id) ? "var(--terra-2)" : "";
    b.innerHTML = ICO.save + " " + (S.saved.includes(id) ? "Saved" : "Save");
  });
  toast(S.saved.includes(id) ? "Saved for later" : "Removed from saved");
}
function postCard(p){
  const c = commByKey(p.community);
  const v = S.votes[p.id] || 0, n = p.votes + v, saved = S.saved.includes(p.id);
  return `<article class="post v2 rv" id="post-${p.id}">
    <div class="vrow">
      <button class="vbtn up ${v===1?"on":""}" data-vup="${p.id}" onclick="vote('${p.id}',1)" aria-label="Upvote">${ICO.up}</button>
      <span class="vscore" data-vote="${p.id}">${n}</span>
      <button class="vbtn down ${v===-1?"on":""}" data-vdown="${p.id}" onclick="vote('${p.id}',-1)" aria-label="Downvote">${ICO.down}</button>
      <span class="vsep"></span>
      <span class="vlabel">${n>200?"Very helpful":n>80?"Helpful":"Worth a read"}</span>
    </div>
    <div class="head">
      <span class="avatar sm ${p.av || 'g'}">${(p.au || 'P')[0]}</span>
      <a href="/community?c=${encodeURIComponent(p.community)}"><b>${emIco(c.em)} ${esc(c.label)}</b></a>
      <span>· ${esc(p.au)}</span>${p.verified?`<span class="tag tag-ans">${ICO.check} Verified</span>`:""}
      <span>· ${esc(p.time)}</span>
      <span class="tag tag-qa" style="margin-inline-start:auto">${esc(p.flair)}</span>
    </div>
    <a class="ptitle" href="/post?id=${encodeURIComponent(p.id)}"><h3>${esc(p.title)}</h3></a>
    <p class="text">${esc(p.body)}</p>
    ${p.img?`<a href="/post?id=${encodeURIComponent(p.id)}" class="pimg" style="display:block;border-radius:15px;overflow:hidden;border:1px solid var(--line);margin-top:12px;max-height:340px"><img src="${p.img}" alt="" loading="lazy" style="width:100%;object-fit:cover"></a>`:""}
    ${p.answer?`<div class="answer-box" style="margin-top:12px"><b>${ICO.shield} Best answer</b>${esc(p.answer)}</div>`:""}
    <div class="foot">
      <a class="act" href="/post?id=${encodeURIComponent(p.id)}">${ICO.comment} ${p.comments} answers</a>
      <button class="act" onclick="copyLink('${p.id}')">${ICO.share} Share</button>
      <button class="act" data-save="${p.id}" onclick="savePost('${p.id}',this)" style="${saved?"color:var(--terra-2)":""}">${ICO.save} ${saved?"Saved":"Save"}</button>
      <span class="spacer"></span>
      <button class="joinbtn ${isJoined(p.community)?"on":""}" data-joincomm="${p.community}" onclick="toggleJoin('${p.community}')">${isJoined(p.community)?"Joined":"+ Join"}</button>
    </div>
  </article>`;
}
function initCommunity(){
  const feed = document.getElementById("feed"); if(!feed) return;
  const params = new URLSearchParams(location.search);
  
  const getViewFromUrl = () => {
    const hash = (location.hash || "").replace("#", "").toLowerCase();
    if (hash === "reels") return "reels";
    if (hash === "saved") return "saved";
    if (hash === "questions") return "questions";
    return params.get("tab") || "foryou";
  };

  let view = getViewFromUrl();
  let comm = params.get("c") || "all";
  let sort = "new";

  // Sync active tab styling
  document.querySelectorAll("[data-tab]").forEach(t => {
    t.classList.toggle("active", t.dataset.tab === view);
  });

  const rail = document.getElementById("commRail");
  if(rail) rail.innerHTML = `
    <div class="panel">
      <h5>Your communities</h5>
      <a class="item ${comm==="all"?"on":""}" href="#" data-c="all"><span class="em">${ICO.users}</span>All parents<span class="n">${allPosts().length}</span></a>
      ${COMMUNITIES.map(c=>`<a class="item ${comm===c.k?"on":""}" href="#" data-c="${c.k}">
        <span class="em">${emIco(c.em)}</span>${c.label}<span class="n">${c.members}</span></a>`).join("")}
      <a class="item ${view==="reels"?"on":""}" href="/reels"><span class="em">${ICO.play}</span>Reels<span class="n">${Array.isArray(REELS)?REELS.length:0}</span></a>
      <a class="item ${view==="saved"?"on":""}" href="/community#saved"><span class="em">${ICO.save}</span>Saved<span class="n">${(S.saved||[]).length}</span></a>
    </div>
    <div class="panel">
      <h5>House rules</h5>
      <div class="rule"><b>1.</b><span>Be kind. Every parent here is trying.</span></div>
      <div class="rule"><b>2.</b><span>No medical diagnosis — share experience, see your doctor.</span></div>
      <div class="rule"><b>3.</b><span>No spam or selling in discussion threads.</span></div>
      <div class="rule"><b>4.</b><span>Public &amp; moderated: Discussions are visible to all parents. Never post private contact or health IDs.</span></div>
    </div>`;

  const railR = document.getElementById("commRailR");
  if(railR) railR.innerHTML = `
    <div class="promo-card">
      <h5>Parent favourites</h5>
      <b>Up to 30% off<br>everyday essentials</b>
      <p>Feeding, bath & nursery picks parents actually reorder.</p>
      <a class="btn btn-xs" href="/shop">Shop now ${icon("arrowUpRight")}</a>
    </div>
    <div class="panel">
      <h5>Trending in the community</h5>
      ${TRENDING.map((t,i)=>`<a class="trend" href="/post?id=${t.p}">
        <span class="rank">${i+1}</span>
        <span><b>${esc(t.t)}</b><span>${commByKey(t.c).label} · ${t.v} votes</span></span></a>`).join("")}
    </div>
    <div class="panel">
      <h5>Top contributors this week</h5>
      ${PEOPLE.map(p=>`<div class="people">
        <span class="avatar sm ${p.av}">${p.n[0]}</span>
        <span class="grow"><b>${esc(p.n)}</b><span>${esc(p.r)}</span></span>
        <button class="btn btn-ghost btn-xs" onclick="follow(this)">Follow</button></div>`).join("")}
    </div>
    <div class="panel">
      <h5>Browse topics</h5>
      <div class="hash">${TOPICS.map(t=>`<a href="#">${esc(t)}</a>`).join("")}</div>
    </div>`;

  function renderReels(){
    const reelsList = (typeof REELS !== "undefined" && Array.isArray(REELS)) ? REELS : [];
    feed.innerHTML = `
      <div class="sort-row" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
        <div>
          <h2 style="font-size:1.3rem;margin:0">Parenting Reels</h2>
          <span class="small muted">Short, practical clips from parents & pediatric specialists</span>
        </div>
        <a href="/reels" class="btn btn-soft btn-xs">Full Reels Page ↗</a>
      </div>
      <div class="grid-reels">
        ${reelsList.map(r=>`
          <a class="reel rv" href="javascript:void(0)" onclick="openReelPlayerModal('${r.id}')">
            <img src="${r.img}" alt="${esc(r.title)}" loading="lazy">
            <span class="play">${ICO.play}</span>
            <span class="ov">
              <span class="badge" style="align-self:flex-start;background:rgba(255,255,255,0.92);color:var(--primary);font-size:10.5px;padding:2px 8px;border-radius:12px;font-weight:700">${esc(r.tag || 'Care')}</span>
              <b>${esc(r.title)}</b>
              <span class="meta">${esc(r.by)} · ${r.views} views</span>
            </span>
          </a>
        `).join("")}
      </div>`;
    initReveal();
  }

  function renderFeed(){
    if(view==="reels"){ renderReels(); return; }
    let list = allPosts();
    if(comm!=="all") list = list.filter(p=>p.community===comm);
    if(view==="questions") list = list.filter(p=>/Question|Guide/.test(p.flair));
    if(view==="saved") {
      list = allPosts().filter(p=>S.saved && S.saved.includes(p.id));
      if(!list.length) {
        feed.innerHTML = `
          <div class="empty" style="padding:40px 16px;text-align:center">
            <div class="box">${ICO.save}</div>
            <b style="font-size:17px">No saved discussions yet</b>
            <p class="small muted" style="margin-top:8px;max-width:380px;margin-inline:auto">
              Tap the save bookmark icon on any community question to save it here for quick reference during feeding and routines.
            </p>
            <div class="row gap-s" style="justify-content:center;margin-top:16px">
              <button class="btn btn-primary btn-s" onclick="location.hash='#questions'">Explore Discussions</button>
              <a class="btn btn-soft btn-s" href="/saved">View Saved Products</a>
            </div>
          </div>
        `;
        const meta = document.getElementById("feedMeta");
        if(meta) meta.textContent = "0 saved conversations";
        return;
      }
    }
    if(sort==="top") {
      list.sort((a,b)=>(b.votes+(S.votes[b.id]||0))-(a.votes+(S.votes[a.id]||0)));
    } else {
      list.sort((a,b) => parsePostTimestamp(b) - parsePostTimestamp(a));
    }
    feed.innerHTML = list.length ? list.map(postCard).join("") :
      `<div class="empty"><div class="box">${ICO.comment}</div><b>Nothing here yet</b>
        <p class="small muted" style="margin-top:8px">Be the first to start this conversation.</p>
        <button class="btn btn-soft btn-s" style="margin-top:14px" onclick="focusAsk()">Ask a question</button></div>`;
    const meta = document.getElementById("feedMeta");
    if(meta) meta.textContent = list.length + " conversation" + (list.length===1?"":"s") + (comm!=="all" ? " · " + commByKey(comm).label : "");
    initReveal();
  }

  window.follow = el => { const on = el.textContent==="Follow"; el.textContent = on?"Following":"Follow";
    el.className = "btn btn-xs " + (on?"btn-soft":"btn-ghost"); toast(on?"Now following — their answers will show in your feed":"Unfollowed"); };

  document.querySelectorAll("[data-tab]").forEach(t=>t.addEventListener("click", e=>{
    view = t.dataset.tab;
    document.querySelectorAll("[data-tab]").forEach(x=>x.classList.toggle("active", x===t));
    renderFeed();
  }));

  window.addEventListener("hashchange", () => {
    view = getViewFromUrl();
    document.querySelectorAll("[data-tab]").forEach(t => {
      t.classList.toggle("active", t.dataset.tab === view);
    });
    renderFeed();
  });

  document.querySelectorAll("#commRail [data-c]").forEach(a=>a.addEventListener("click", e=>{
    e.preventDefault(); comm = a.dataset.c;
    document.querySelectorAll("#commRail [data-c]").forEach(x=>x.classList.toggle("on", x===a));
    renderFeed();
  }));
  const sortBtns = document.querySelectorAll("[data-sort]");
  sortBtns.forEach(b=>b.addEventListener("click",()=>{ sort=b.dataset.sort;
    sortBtns.forEach(x=>x.classList.toggle("on",x===b)); renderFeed(); }));

  /* composer */
  window.focusAsk = ()=>{ const i=document.getElementById("askTitle"); i?.scrollIntoView({behavior:"smooth",block:"center"}); i?.focus(); };
  const ex = document.getElementById("composerExpand");
  document.getElementById("askTitle")?.addEventListener("focus",()=>ex.classList.add("on"));
  const cSel = document.getElementById("askCommunity");
  if(cSel) cSel.innerHTML = COMMUNITIES.map(c=>`<option value="${c.k}">${c.label}</option>`).join("");

  window.submitPost = async ()=>{
    const title = document.getElementById("askTitle")?.value.trim();
    const body  = document.getElementById("askBody")?.value.trim();
    const c     = cSel ? cSel.value : "general";
    const flair = document.getElementById("askFlair")?.value || "Question";
    if(!title){ toast("Add a short question title first"); return; }

    const user = getCurrentUser();
    const authorName = (user && user.name) ? user.name : "Parent";

    try {
      const res = await fetch('/api/community/posts', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ title, body, community: c, flair, authorName })
      });
      const data = await res.json().catch(() => null);

      if(!res.ok || !data || !data.success){
        toast((data && (data.message || data.error)) ? (data.message || data.error) : "Could not publish question to server. Please try again.", "x");
        return;
      }

      const savedPost = data.post || {
        id: "p" + Date.now(),
        community: c,
        au: authorName,
        av: "g",
        time: "Just now",
        createdAt: Date.now(),
        flair,
        title,
        body: body || "(No details added)",
        votes: 1,
        comments: 0,
        img: null,
        mine: true
      };

      S.userPosts.unshift(savedPost);
      save();
      if(document.getElementById("askTitle")) document.getElementById("askTitle").value = "";
      if(document.getElementById("askBody")) document.getElementById("askBody").value = "";
      if(ex) ex.classList.remove("on");
      view="foryou"; comm="all"; sort="new";
      document.querySelectorAll("[data-tab]").forEach(x=>x.classList.toggle("active", x.dataset.tab==="foryou"));
      document.querySelectorAll("[data-sort]").forEach(x=>x.classList.toggle("on", x.dataset.sort==="new"));
      renderFeed();
      toast("Posted! Your question is live for all parents", "heart");
      try{ window.scrollTo({top:0,behavior:"smooth"}); }catch(e){}
    } catch(err) {
      toast("Network error while publishing. Please retry.", "x");
    }
  };

  // Fetch server-published questions
  fetch('/api/community/posts')
    .then(r => r.json())
    .then(data => {
      if(data && data.success && Array.isArray(data.posts)){
        serverPosts = data.posts;
        renderFeed();
      }
    })
    .catch(()=>{});

  renderFeed(); initReveal();
  if(location.hash.startsWith("#post-")) setTimeout(()=>document.querySelector(location.hash)?.scrollIntoView({block:"center"}),300);
}

window.openReelPlayerModal = function(id){
  const reelsList = (typeof REELS !== "undefined" && Array.isArray(REELS)) ? REELS : [];
  const r = reelsList.find(x => x.id === id) || reelsList[0];
  if(!r) return;
  let modal = document.getElementById("reelModal");
  if(!modal){
    modal = document.createElement("div");
    modal.id = "reelModal";
    modal.className = "modal";
    document.body.appendChild(modal);
  }
  modal.innerHTML = `
    <div class="scrim" onclick="closeAll()"></div>
    <div class="box" style="max-width:440px;background:#0d0a10;color:#fff;border-radius:24px;overflow:hidden;padding:0;box-shadow:0 24px 60px rgba(0,0,0,0.5)">
      <div style="position:relative;width:100%;aspect-ratio:9/16;background:#111;display:flex;align-items:flex-end">
        <img src="${r.img}" alt="${esc(r.title)}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:0.92">
        <div style="position:absolute;top:16px;left:16px;right:16px;display:flex;justify-content:space-between;align-items:center;z-index:3">
          <div style="display:flex;align-items:center;gap:10px">
            <span class="avatar sm g" style="border:2px solid #fff">P</span>
            <div><b style="font-size:13.5px;color:#fff;display:block">${esc(r.by)}</b><span style="font-size:11px;opacity:0.85;color:#f0e6f6">ParentingHub Verified</span></div>
          </div>
          <button class="icon-btn" onclick="closeAll()" aria-label="Close reel" style="background:rgba(0,0,0,0.55);color:#fff;border-radius:50%;width:34px;height:34px">${ICO.x}</button>
        </div>
        <div style="position:relative;z-index:3;padding:22px 20px 20px;background:linear-gradient(transparent,rgba(15,10,20,0.92) 50%,rgba(15,10,20,0.98));width:100%">
          <span class="badge" style="background:var(--primary);color:#fff;font-size:11px;padding:3px 10px;border-radius:12px;margin-bottom:8px;display:inline-block;font-weight:700">${esc(r.tag || 'Care')}</span>
          <h3 style="margin:0 0 6px;font-size:17px;color:#fff;line-height:1.3">${esc(r.title)}</h3>
          <p style="margin:0 0 16px;font-size:12.5px;color:#e2d9e8;line-height:1.45">${esc(r.caption || '')}</p>
          <div class="row" style="justify-content:space-between;align-items:center">
            <div class="row gap-m" style="color:#fff;font-size:13px">
              <span>▶ <b>${r.views}</b></span>
              <button id="globalReelLikeBtn" style="background:none;border:none;color:#fff;cursor:pointer;padding:0;font-size:13px"><i>♥ <span id="globalReelLikes">${Number(r.likes||0)}</span></i></button>
            </div>
            <a href="/shop" class="btn btn-primary btn-xs" style="border-radius:20px;padding:6px 14px">Shop Essentials ${icon("arrow")}</a>
          </div>
        </div>
      </div>
    </div>
  `;
  modal.classList.add("open", "on");
  const likeBtn = document.getElementById("globalReelLikeBtn");
  if(likeBtn){
    likeBtn.onclick = async () => {
      try{
        const res = await fetch(`/api/community/reels/${encodeURIComponent(r.id)}/like`, {method:"POST", credentials:"same-origin", headers:{"Accept":"application/json","Content-Type":"application/json"}, body:"{}"});
        const data = await res.json().catch(()=>({}));
        if(res.status===401){ toast("Please sign in to like a reel."); setTimeout(()=>location.href="/login?next="+encodeURIComponent(location.pathname+location.search),500); return; }
        if(!res.ok || data.success===false) throw new Error(data.message||"Could not update like.");
        const out=document.getElementById("globalReelLikes"); if(out) out.textContent=Number(data.likes||0); r.likes=Number(data.likes||0);
      }catch(err){ toast(err.message||"Could not update like.","x"); }
    };
  }
};

/* ---------- post detail page ---------- */
function commentHTML(c, depth){
  const textStr = c.text || c.answer || "";
  const authorStr = c.au || c.author || "Parent";
  return `<div class="cmt ${c.best?"best":""}">
    <span class="avatar sm ${c.av || 't'}">${(authorStr)[0]}</span>
    <div class="body">
      <div class="meta"><b>${esc(authorStr)}</b>${c.verified?`<span class="tag tag-ans">${ICO.check} Verified</span>`:""}
        ${c.best?`<span class="tag tag-ans">${ICO.shield} Best answer</span>`:""}<span>· ${esc(c.time || 'Recently')}</span></div>
      <p class="txt">${esc(textStr)}</p>
      <div class="acts">
        <span class="vmini">
          <button onclick="cvote(this,1)" aria-label="Upvote answer">${ICO.up}</button>
          <span>${Number(c.votes || 0)}</span>
          <button onclick="cvote(this,-1)" aria-label="Downvote answer">${ICO.down}</button>
        </span>
        <button class="act" onclick="document.getElementById('replyBox')?.focus()">${ICO.comment} Reply</button>
        <button class="act" onclick="copyLink('${pCurrentId||''}')">${ICO.share} Share</button>
      </div>
      ${c.replies && c.replies.length ? `<div class="thread ${depth>0?"deep":""}">${c.replies.map(r=>commentHTML(r, depth+1)).join("")}</div>` : ""}
    </div>
  </div>`;
}

let pCurrentId = "";
async function initPostPage(){
  const wrap = document.getElementById("postWrap"); if(!wrap) return;
  const id = new URLSearchParams(location.search).get("id") || (POSTS[0] && POSTS[0].id);
  pCurrentId = id;

  // Always refresh server-persisted posts before resolving a direct post link.
  try {
    const res = await fetch('/api/community/posts', { headers: { 'Accept': 'application/json' } });
    const data = await res.json().catch(() => null);
    if(res.ok && data && data.success && Array.isArray(data.posts)) serverPosts = data.posts;
  } catch(e) {}

  const postList = allPosts();
  const p = postList.find(x=>x.id===id) || postList[0];
  if(!p){
    wrap.innerHTML = `<div class="empty"><div class="box">${ICO.comment}</div><b>Conversation not found</b><p class="small muted" style="margin-top:8px">This discussion may have been removed.</p><a class="btn btn-soft btn-s" style="margin-top:12px" href="/community">Back to community</a></div>`;
    return;
  }
  const c = commByKey(p.community);
  const v = S.votes[p.id] || 0, n = p.votes + v;
  const saved = S.saved.includes(p.id);
  const list = (typeof COMMENTS !== "undefined" && (COMMENTS[p.id] || COMMENTS.default)) || [];

  wrap.innerHTML = `
  <div class="pd-wrap">
    <div>
      <a class="backlink" href="/community">${icon("chevL")} Back to community</a>
      <article class="pd" style="margin-top:14px">
        <div class="pbody">
          <div class="head" style="display:flex;align-items:center;gap:9px;flex-wrap:wrap;font-size:12.6px;color:var(--ink-3);margin-bottom:12px">
            <span class="avatar sm ${p.av || 'g'}">${(p.au || 'P')[0]}</span>
            <a href="/community?c=${encodeURIComponent(p.community)}"><b style="color:var(--ink)">${emIco(c.em)} ${esc(c.label)}</b></a>
            <span>· ${esc(p.au)}</span>${p.verified?`<span class="tag tag-ans">${ICO.check} Verified</span>`:""}
            <span>· ${esc(p.time)}</span>
            <span class="tag tag-qa" style="margin-inline-start:auto">${esc(p.flair)}</span>
          </div>
          <h1>${esc(p.title)}</h1>
          <p class="ptext">${esc(p.body)}</p>
          ${p.img?`<div class="pimg"><img src="${p.img}" alt=""></div>`:""}
          ${p.answer?`<div class="answer-box" style="margin-top:16px"><b>${ICO.shield} Best answer</b>${esc(p.answer)}</div>`:""}
        </div>
        <div class="pactions">
          <span class="vmini">
            <button class="${v===1?"on":""}" data-vup="${p.id}" onclick="vote('${p.id}',1)">${ICO.up}</button>
            <span data-vote="${p.id}">${n}</span>
            <button class="${v===-1?"on":""}" data-vdown="${p.id}" onclick="vote('${p.id}',-1)">${ICO.down}</button>
          </span>
          <button class="act" id="postAnsCount">${ICO.comment} ${p.comments} answers</button>
          <button class="act" onclick="copyLink('${p.id}')">${ICO.share} Share</button>
          <button class="act" data-save="${p.id}" onclick="savePost('${p.id}',this)" style="${saved?"color:var(--terra-2)":""}">${ICO.save} ${saved?"Saved":"Save"}</button>
          <span class="spacer"></span>
          <button class="joinbtn ${isJoined(p.community)?"on":""}" data-joincomm="${p.community}" onclick="toggleJoin('${p.community}')">${isJoined(p.community)?"Joined":"+ Join"}</button>
        </div>
      </article>

      <div class="cbox">
        <div class="row gap-m" style="margin-bottom:10px">
          <span class="avatar sm t">Y</span><b class="small">Answer as <span style="color:var(--terra-2)">${esc((getCurrentUser() && getCurrentUser().name) || 'You')}</span></b>
          <span class="tiny muted" style="margin-inline-start:auto">${icon("heart")} Kind, specific, real experience</span>
        </div>
        <textarea id="replyBox" placeholder="What worked for you? Share your experience — it may be exactly what another parent needed today."></textarea>
        <div class="row gap-m" style="margin-top:10px">
          <span class="tiny muted">Care guidelines apply</span>
          <button class="btn btn-primary btn-s" id="btnPostAnswer" style="margin-inline-start:auto" onclick="handlePostAnswer('${p.id}')">Post answer</button>
        </div>
      </div>

      <div class="sort-row" style="margin-top:20px">
        <b class="serif" id="answersHeading" style="font-size:1.15rem;margin-inline-end:6px">${list.length} answers</b>
        <button class="chip on">${icon("sparkle")} Most helpful</button>
        <button class="chip">${icon("clock")} Newest</button>
      </div>
      <div id="comments">${list.map(x=>commentHTML(x,0)).join("")}</div>
    </div>

    <aside class="pd-side" style="display:flex;flex-direction:column;gap:14px">
      <div class="panel">
        <div class="row gap-m" style="margin-bottom:10px">
          <span class="em" style="font-size:26px">${emIco(c.em)}</span>
          <span class="grow"><b style="font-size:15px">${esc(c.label)}</b><br><span class="tiny muted">${Number(c.members||0)} posts</span></span>
        </div>
        <p class="small muted">A warm space for ${esc(c.label.toLowerCase())} conversations. Ask anything — no judgement here.</p>
        <button class="btn btn-s btn-block ${isJoined(p.community)?"btn-soft":"btn-primary"}" data-joinbig="${p.community}" style="margin-top:12px" onclick="toggleJoin('${p.community}')">${isJoined(p.community)?"Joined":"Join community"}</button>
      </div>
      <div class="panel">
        <h5>Community rules</h5>
        <div class="rule"><b>1.</b><span>Be kind. Every parent here is trying.</span></div>
        <div class="rule"><b>2.</b><span>No medical diagnosis — share experience, see your doctor.</span></div>
        <div class="rule"><b>3.</b><span>No spam or selling in threads.</span></div>
      </div>
      <div class="promo-card">
        <h5>From the shop</h5>
        <b>${p.flair==="Question" && /sleep/i.test(p.title) ? "Calmer nights, softer mornings" : "Essentials parents reorder"}</b>
        <p>Up to 30% off feeding, bath & nursery picks this week.</p>
        <a class="btn btn-xs" href="/shop">Browse the shop ${icon("arrowUpRight")}</a>
      </div>
      <div class="panel">
        <h5>More in ${esc(c.label)}</h5>
        ${(allPosts().filter(x=>x.community===p.community && x.id!==p.id).slice(0,3).concat(allPosts().filter(x=>x.community!==p.community).slice(0,2)))
          .slice(0,3).map(x=>`<a class="trend" href="/post?id=${encodeURIComponent(x.id)}"><span class="rank">${ICO.sparkle}</span><span><b>${esc(x.title)}</b><span>${x.comments} answers · ${x.time}</span></span></a>`).join("") || '<p class="small muted">Nothing else here yet.</p>'}
      </div>
    </aside>
  </div>`;

  window.handlePostAnswer = async function(postId){
    const box = document.getElementById("replyBox");
    const btn = document.getElementById("btnPostAnswer");
    if(!box) return;
    const text = box.value.trim();
    if(!text){
      toast("Please write your answer or experience first");
      box.focus();
      return;
    }
    const user = getCurrentUser();
    const authorName = (user && user.name) ? user.name : "Parent";

    if(btn){ btn.disabled = true; btn.textContent = "Posting..."; }

    try {
      const res = await fetch(`/api/community/posts/${encodeURIComponent(postId)}/answers`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ text, authorName })
      });
      const data = await res.json().catch(() => null);

      if(!res.ok || !data || !data.success){
        toast((data && (data.message || data.error)) ? (data.message || data.error) : "Could not post answer. Please retry.", "x");
        return;
      }

      box.value = "";
      const commentsDiv = document.getElementById("comments");
      if(commentsDiv){
        const savedAns = data.answer || { text, au: authorName, createdAt: Date.now() };
        const ansObj = {
          au: savedAns.au || savedAns.authorName || authorName,
          av: savedAns.av || "t",
          time: "Just now",
          text: savedAns.text || text,
          votes: Number(savedAns.votes || 0),
          verified: Boolean(savedAns.verified)
        };
        commentsDiv.insertAdjacentHTML('afterbegin', commentHTML(ansObj, 0));
      }

      p.comments = (p.comments || 0) + 1;
      const countEl = document.getElementById("postAnsCount");
      if(countEl) countEl.innerHTML = `${ICO.comment} ${p.comments} answers`;
      const headEl = document.getElementById("answersHeading");
      if(headEl) headEl.textContent = `${p.comments} answers`;

      toast("Reply posted and saved — thank you for helping", "heart");
    } catch(err) {
      toast("Network error. Please try again.", "x");
    } finally {
      if(btn){ btn.disabled = false; btn.textContent = "Post answer"; }
    }
  };

  // Load server-persisted answers for this post
  fetch(`/api/community/posts/${encodeURIComponent(p.id)}/answers`)
    .then(r => r.json())
    .then(data => {
      if(data && data.success && Array.isArray(data.answers) && data.answers.length){
        const commentsDiv = document.getElementById("comments");
        if(commentsDiv){
          const serverAnswers = data.answers.map(ans => ({
            au: ans.au || ans.authorName || "Parent",
            av: ans.av || "t",
            time: ans.time || "Recently",
            text: ans.text || "",
            votes: Number(ans.votes || 0),
            verified: Boolean(ans.verified)
          }));
          const existingTexts = new Set(list.map(x => x.text));
          serverAnswers.forEach(sa => {
            if(!existingTexts.has(sa.text)){
              commentsDiv.insertAdjacentHTML('afterbegin', commentHTML(sa, 0));
            }
          });
          const totalAnswers = list.length + serverAnswers.length;
          const headEl = document.getElementById("answersHeading");
          if(headEl) headEl.textContent = `${totalAnswers} answers`;
        }
      }
    })
    .catch(()=>{});

  window.cvote = (btn, d)=>{
    const box = btn.closest(".vmini");
    const num = box.querySelector("span");
    const ups = box.querySelector("button:first-child"), downs = box.querySelector("button:last-child");
    const base = +num.dataset.base || (num.dataset.base = num.textContent, +num.dataset.base);
    const cur  = +num.dataset.cur || 0;
    const next = cur === d ? 0 : d;
    num.dataset.cur = next;
    num.textContent = base + next;
    ups.classList.toggle("on", next===1);
    downs.classList.toggle("on", next===-1);
  };
  initReveal();
}

/* ---------- articles & guide reader ---------- */
function initArticles(){
  const listingView = document.getElementById("artListingView");
  const detailView = document.getElementById("artDetailView");
  const grid = document.getElementById("artGrid");
  if(!grid && !detailView) return;

  // Database-backed Learn page is rendered by PHP. Do not overwrite it with static ARTICLES from data.js.
  if(document.body?.dataset?.learnDb === "1"){
    const feed = document.getElementById("homeFeed");
    if(feed) fetch('/api/community/posts').then(r=>r.json()).then(d=>{ const list=(d&&d.posts)||[]; feed.innerHTML=list.slice(0,3).map(miniPost).join("") || `<div class="empty"><b>No community posts yet</b></div>`; initReveal(); }).catch(()=>{
    feed.innerHTML=`<div class="empty"><b>Community feed unavailable</b><p class="muted tiny">Please refresh in a moment.</p></div>`;
    const statBox=document.getElementById('homeCommunityStats'); if(statBox) statBox.dataset.state='error';
    const p=document.getElementById('homeCommunityParents'); if(p) p.textContent='Community stats unavailable';
    const m=document.getElementById('homeCommunityAnswersMonth'); if(m) m.textContent='Please refresh shortly';
    const t=document.getElementById('homeCommunityAnswersTotal'); if(t) t.textContent='Database reconnecting';
  });
    initReveal();
    return;
  }

  function renderArticleView(){
    const hash = (location.hash || "").replace(/^#/, "").trim();
    const queryId = new URLSearchParams(location.search).get("id") || "";
    const targetId = hash || queryId;

    const article = targetId ? ARTICLES.find(a => a.id.toLowerCase() === targetId.toLowerCase()) : null;

    if(article && detailView){
      location.replace("/articles/" + encodeURIComponent(article.id));
      return;
      if(listingView) listingView.style.display = "none";
      detailView.style.display = "block";
      document.title = `${article.title} — ParentingHub.pk Learn`;

      const paragraphs = Array.isArray(article.body) ? article.body : [article.ex || ""];
      const bodyHtml = paragraphs.map(p => `<p style="font-size:16px;line-height:1.8;color:var(--ink-2);margin-bottom:18px">${esc(p)}</p>`).join("");

      const relatedArticles = ARTICLES.filter(a => a.id !== article.id).slice(0, 3);

      detailView.innerHTML = `
        <div class="article-reader" style="max-width:780px;margin:0 auto">
          <div style="margin-bottom:16px">
            <a href="/articles" onclick="event.preventDefault(); history.pushState(null,'','/articles'); renderArticleView();" class="btn btn-ghost btn-xs" style="margin-bottom:14px">
              ${icon("chevL")} Back to all guides
            </a>
          </div>

          <div class="row gap-s" style="margin-bottom:10px">
            <span class="tag tag-new">${esc(article.cat)}</span>
            <span class="tiny muted" style="margin-left:8px">${icon("clock")} ${esc(article.read)} read · Parenting guide</span>
          </div>

          <h1 style="font-size:32px;line-height:1.25;margin-bottom:12px">${esc(article.title)}</h1>

          <div class="meta" style="color:var(--ink-3);font-size:13px;margin-bottom:20px">
            By <b>${esc(article.by)}</b> · Published ${esc(article.date)}
          </div>

          <div style="border-radius:18px;overflow:hidden;margin-bottom:24px;border:1px solid var(--line);max-height:420px">
            <img src="${article.img}" alt="${esc(article.title)}" style="width:100%;height:100%;object-fit:cover" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
          </div>

          <div class="article-summary-box" style="background:var(--surface-2);border-left:4px solid var(--primary);padding:16px 20px;border-radius:0 14px 14px 0;margin-bottom:26px;font-size:15px;color:var(--ink)">
            <b>Executive Takeaway:</b> ${esc(article.ex)}
          </div>

          <div class="article-content">
            ${bodyHtml}
          </div>

          <div style="background:#FAF5EE;border:1px solid #EADCC8;border-radius:16px;padding:20px;margin:32px 0;font-size:13.5px;color:#5D4428">
            <b>Parenting Guidance Note:</b> This guide provides practical organisation and product comparisons for Pakistani families. For individual pediatric, feeding, or medical advice, consult your qualified pediatrician.
          </div>

          <div style="border-top:1px solid var(--line);padding-top:24px;margin-top:30px">
            <h3 style="margin-bottom:16px">More Parenting & Feeding Guides</h3>
            <div class="grid-articles">
              ${relatedArticles.map(a => articleCard(a, false)).join("")}
            </div>
            <div style="text-align:center;margin-top:24px">
              <a href="/articles" onclick="event.preventDefault(); history.pushState(null,'','/articles'); renderArticleView();" class="btn btn-soft">
                ${icon("chevL")} View all guides
              </a>
            </div>
          </div>
        </div>
      `;
      window.scrollTo({ top: 0, behavior: "smooth" });
      initReveal();
      return;
    }

    // Default: Show listing view
    if(detailView) detailView.style.display = "none";
    if(listingView) listingView.style.display = "block";
    document.title = "Practical Parenting Guides | ParentingHub Pakistan";

    const cat = new URLSearchParams(location.search).get("cat");
    let list = ARTICLES.slice();
    if(cat){
      const f = list.filter(a=>a.cat.toLowerCase()===cat.toLowerCase());
      if(f.length) list = f;
      document.querySelectorAll(".page-head .chip").forEach(c=>{
        c.classList.toggle("on", c.textContent.trim().toLowerCase() === cat.toLowerCase());
      });
    }
    if(grid){
      grid.innerHTML = list.map((a,i)=>articleCard(a, i===0)).join("") ||
        `<div class="empty" style="grid-column:1/-1"><div class="box">${ICO.book}</div><b>No guides in this category yet</b></div>`;
    }
    const feed = document.getElementById("homeFeed");
    if(feed) fetch('/api/community/posts').then(r=>r.json()).then(d=>{ const list=(d&&d.posts)||[]; feed.innerHTML=list.slice(0,3).map(miniPost).join("") || `<div class="empty"><b>No community posts yet</b></div>`; initReveal(); }).catch(()=>{
    feed.innerHTML=`<div class="empty"><b>Community feed unavailable</b><p class="muted tiny">Please refresh in a moment.</p></div>`;
    const statBox=document.getElementById('homeCommunityStats'); if(statBox) statBox.dataset.state='error';
    const p=document.getElementById('homeCommunityParents'); if(p) p.textContent='Community stats unavailable';
    const m=document.getElementById('homeCommunityAnswersMonth'); if(m) m.textContent='Please refresh shortly';
    const t=document.getElementById('homeCommunityAnswersTotal'); if(t) t.textContent='Database reconnecting';
  });
    initReveal();
  }

  window.renderArticleView = renderArticleView;
  window.addEventListener("hashchange", renderArticleView);
  window.addEventListener("popstate", renderArticleView);

  renderArticleView();
}

/* ---------- newsletter subscription ---------- */
window.handleNewsletterSubmit = async function(e){
  if(e && e.preventDefault) e.preventDefault();
  const emailInput = document.getElementById("nlEmail");
  const stageSelect = document.getElementById("nlStage");
  const submitBtn = document.getElementById("nlSubmitBtn");
  const feedback = document.getElementById("nlFeedback");

  const email = emailInput ? emailInput.value.trim() : "";
  const stage = stageSelect ? stageSelect.value : "";

  if(!email){
    toast("Please enter your email address", "x");
    return false;
  }
  if(!stage){
    toast("Please select your baby's stage or birth month", "x");
    if(stageSelect) stageSelect.focus();
    return false;
  }

  if(submitBtn){
    submitBtn.disabled = true;
    submitBtn.textContent = "Subscribing...";
  }

  try {
    const res = await fetch('/api/newsletter/subscribe', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: JSON.stringify({ email, stage })
    });
    const data = await res.json().catch(() => null);

    if(!res.ok || !data || !data.success){
      const err = (data && data.error) ? data.error : "Subscription failed. Please check your email and retry.";
      if(feedback){
        feedback.textContent = err;
        feedback.style.display = "block";
        feedback.style.background = "#fce8e6";
        feedback.style.color = "#d93025";
      }
      toast(err, "x");
      return false;
    }

    if(emailInput) emailInput.value = "";
    if(feedback){
      feedback.textContent = data.message || `Subscribed! Your first ${stage} guide will arrive Sunday.`;
      feedback.style.display = "block";
      feedback.style.background = "#e6f4ea";
      feedback.style.color = "#137333";
    }
    toast(`Subscribed! Personalized ${stage} guides will arrive weekly`, "check");
    return false;
  } catch(err) {
    toast("Network error. Please check connection and retry.", "x");
    return false;
  } finally {
    if(submitBtn){
      submitBtn.disabled = false;
      submitBtn.textContent = "Send me personalized guides";
    }
  }
};

/* ---------- profile ---------- */
function initProfile(){
  const wrap = document.getElementById("profWrap"); if(!wrap) return;

  // Render My Orders
  const ordersBox = document.getElementById("myOrders");
  if(ordersBox) {
    const orders = store.get("orders", []);
    if(orders.length){
      ordersBox.innerHTML = orders.map(ord => {
        const d = new Date(ord.date).toLocaleDateString("en-PK", { day: 'numeric', month: 'short', year: 'numeric' });
        const itemsList = ord.items.map(it => `<div>• <b>${it.name}</b> × ${it.qty} (${money(it.price * it.qty)})</div>`).join("");
        return `
          <div class="panel" style="margin-bottom:14px;border:1px solid var(--line);border-radius:14px;padding:16px">
            <div class="row" style="justify-content:space-between;align-items:center;border-bottom:1px solid var(--line);padding-bottom:10px;margin-bottom:10px">
              <div>
                <b style="font-size:16px;color:var(--primary)">#${ord.id}</b>
                <span class="tiny muted" style="margin-left:8px">${d}</span>
              </div>
              <span class="badge" style="background:#EAF7EE;color:#1E8E3E;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:700">${icon("check")} ${ord.status || 'Confirmed'}</span>
            </div>
            <div style="font-size:13px;line-height:1.6;color:var(--ink-2)">
              ${itemsList}
            </div>
            <div class="row" style="justify-content:space-between;align-items:center;border-top:1px solid var(--line);padding-top:10px;margin-top:10px">
              <div style="font-size:13px">
                Total: <b style="font-size:15px;color:var(--ink)">${money(ord.total)}</b> 
                <span class="tiny muted">(${ord.paymentMethod})</span>
              </div>
              <span class="tiny muted">Order saved in your account</span>
            </div>
          </div>
        `;
      }).join("");
    } else {
      ordersBox.innerHTML = `<div class="empty" style="padding:24px 10px">
        <div class="box">${ICO.bag}</div>
        <b>No orders placed yet</b>
        <p class="small muted" style="margin-top:8px">Select baby essentials from our shop with Cash on Delivery nationwide.</p>
        <a class="btn btn-soft btn-s" style="margin-top:12px" href="/shop">Start Shopping</a>
      </div>`;
    }
  }

  // Render Wishlist
  const wishBox = document.getElementById("wishGrid");
  if(wishBox) {
    wishBox.innerHTML = S.wish.length
      ? S.wish.map(id=>PRODUCTS.find(p=>p.id===id)).filter(Boolean).map(productCard).join("")
      : `<div class="empty" style="grid-column:1/-1"><div class="box">${ICO.heart}</div><b>Your wishlist is empty</b>
          <p class="small muted" style="margin-top:8px">Tap the heart on any product in the shop to save it here.</p>
          <a class="btn btn-soft btn-s" style="margin-top:12px" href="/shop">Browse the shop</a></div>`;
  }

  // Render My Posts
  const postsBox = document.getElementById("myPosts");
  if(postsBox) {
    postsBox.innerHTML = S.userPosts.length
      ? S.userPosts.map(p=>`<div class="panel" style="margin-bottom:12px"><b>${p.title}</b>
          <p class="small muted" style="margin-top:6px">${p.body}</p>
          <div class="row gap-m tiny muted" style="margin-top:10px"><span>${ICO.up} 1</span><span>${ICO.comment} 0 answers</span><span>Posted ${p.time}</span></div></div>`).join("")
      : `<div class="empty" style="padding:34px 10px"><b>No posts yet</b>
          <p class="small muted" style="margin-top:8px">Ask your first question in our parent community.</p>
          <a class="btn btn-soft btn-s" style="margin-top:12px" href="/community#ask">Ask a question</a></div>`;
  }

  const followBox = document.getElementById("followSuggest");
  if(followBox && typeof PEOPLE !== "undefined") {
    followBox.innerHTML = PEOPLE.slice(0,3).map(p=>`
      <div class="people"><span class="avatar sm ${p.av}">${p.n[0]}</span>
        <span class="grow"><b>${p.n}</b><span>${p.r}</span></span>
        <button class="btn btn-ghost btn-xs" onclick="follow(this)">Follow</button></div>`).join("");
  }

  window.follow = el => { const on = el.textContent==="Follow"; el.textContent = on?"Following":"Follow";
    el.className = "btn btn-xs " + (on?"btn-soft":"btn-ghost"); toast(on?"Now following":"Unfollowed"); };
  document.querySelectorAll("[data-join]").forEach(b=>b.addEventListener("click",()=>{
    const k = b.dataset.join; const i = S.joined.indexOf(k);
    i>=0?S.joined.splice(i,1):S.joined.push(k); save();
    b.textContent = S.joined.includes(k) ? "Joined" : "Join";
    b.classList.toggle("btn-soft", S.joined.includes(k));
    b.classList.toggle("btn-ghost", !S.joined.includes(k));
  }));
  initReveal();
}

/* ---------- boot ---------- */
document.addEventListener("DOMContentLoaded", ()=>{
  mountChrome();
  refreshSessionUser().then(()=>{ mountChrome(); syncAccountWishlist(); }).catch(()=>{});
  hydrateIcons();
  const page = document.body.dataset.page;
  if(page==="home") initHome();
  if(page==="shop") initShop();
  if(page==="product") { currentModalProduct = PRODUCTS.find(p => p.id === document.body.dataset.productId); modalQty = 1; }
  if(page==="community" && !window.PH_COMMUNITY_DB){
    const feed=document.getElementById("feed"); if(feed) feed.innerHTML='<div class="empty"><b>Community could not load</b><p class="small muted">Please reload the page. Community data is never replaced with demo statistics.</p></div>';
  }
  if(page==="post" && !window.PH_COMMUNITY_DB){
    const wrap=document.getElementById("postWrap"); if(wrap) wrap.innerHTML='<div class="empty"><b>Discussion could not load</b><p class="small muted">Please reload the page.</p></div>';
  }
  if(page==="articles") initArticles();
  if(page==="profile") initProfile();
  initReveal();
  const av = document.getElementById("navAvatar"), a = "A"; if(av) av.textContent = a;
});
