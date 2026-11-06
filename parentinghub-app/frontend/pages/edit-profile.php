<?php
if (!defined('PH_ENTRY')) { http_response_code(404); exit; }
require_once dirname(__DIR__, 2) . '/backend/api/bootstrap.php';
$phProfilePdo = ph_pdo();
$phProfileUser = ph_session_user($phProfilePdo);
if (!$phProfileUser) { header('Location: /login?next=' . rawurlencode($_SERVER['REQUEST_URI'] ?? '/profile'), true, 302); exit; }
// ph_require_profile_page_user
?>
<!DOCTYPE html>
<html lang="en">
<head>
<base href="/">
<meta name="theme-color" content="#bd3d6b">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edit Profile & Baby Details | ParentingHub Pakistan</title>
<meta name="description" content="Update your ParentingHub parent profile, delivery address, contact info and baby milestone details.">
<link rel="canonical" href="https://www.parentinghub.pk/edit-profile">
<meta name="robots" content="noindex, nofollow">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css?v=5.5-product-visual">

<!-- Favicons -->
<link rel="icon" type="image/png" href="favicon.png">
<link rel="icon" type="image/x-icon" href="favicon.ico">
</head>
<body data-page="edit-profile">
<div id="chrome-top"></div>

<main style="padding:40px 0 70px;background:var(--cream);min-height:80vh">
  <div class="container" style="max-width:760px">
    
    <div style="margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
      <div>
        <a href="/profile" class="tiny" style="color:var(--primary);font-weight:700;display:inline-flex;align-items:center;gap:6px;text-decoration:none;margin-bottom:8px">
          ← Back to My Space & Orders
        </a>
        <h1 style="margin:0;font-size:28px">Edit Parent Profile</h1>
        <p class="muted small" style="margin:4px 0 0">Manage your contact details, delivery address, and baby's stage for personalized advice.</p>
      </div>
      <button class="btn btn-primary" onclick="saveAllSettings()" style="padding:10px 24px">
        Save Changes
      </button>
    </div>

    <!-- Main Card -->
    <div class="section-card" style="padding:32px;background:var(--surface);border-radius:24px;box-shadow:var(--sh-m)">
      
      <!-- User Avatar / Header -->
      <div style="display:flex;align-items:center;flex-wrap:wrap;gap:18px;padding-bottom:24px;border-bottom:1px solid var(--line);margin-bottom:28px">
        <span class="avatar lg g" id="editAvatar" style="background:var(--terra);color:#fff;font-weight:700;font-size:24px;overflow:hidden;flex-shrink:0;width:80px;height:80px;border-radius:50%">P</span>
        <div>
          <b style="font-size:18px;display:block" id="editHeaderName">Parent Member</b>
          <span class="tiny muted" id="editHeaderSub">ParentingHub Registered Family</span>
          <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:12px">
            <button type="button" class="btn btn-soft btn-xs" id="changeProfilePicture">Change Photo</button>
            <button type="button" class="btn btn-ghost btn-xs" id="removeProfilePicture" hidden>Remove Photo</button>
          </div>
          <input type="file" id="profilePictureInput" accept="image/jpeg,image/png,image/webp" hidden aria-label="Choose profile picture">
          <span class="tiny muted" style="display:block;margin-top:8px">JPG, PNG or WebP · Maximum 5 MB. Saved securely to your ParentingHub account.</span>
          <span id="profilePictureStatus" class="tiny" role="status" aria-live="polite" style="display:block;margin-top:6px"></span>
        </div>
      </div>

      <form id="editProfileForm" onsubmit="event.preventDefault();saveAllSettings();">
        <!-- Section 1: Parent Details -->
        <h3 style="font-size:17px;margin:0 0 16px;color:var(--ink);display:flex;align-items:center;gap:8px">
          <span class="ico" data-ico="user" aria-hidden="true"></span> Parent Information
        </h3>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px">
          <div>
            <label class="tiny" style="font-weight:700;display:block;margin-bottom:6px">Full Name *</label>
            <input type="text" id="setName" required placeholder="e.g. Ayesha Khan" style="width:100%;padding:11px 14px;border:1px solid var(--line);border-radius:12px">
          </div>
          <div>
            <label class="tiny" style="font-weight:700;display:block;margin-bottom:6px">Profile Headline / Tagline</label>
            <input type="text" id="setHeadline" placeholder="e.g. Mom of 6-month-old · Avent feeder fan" style="width:100%;padding:11px 14px;border:1px solid var(--line);border-radius:12px">
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px">
          <div>
            <label class="tiny" style="font-weight:700;display:block;margin-bottom:6px">Email Address</label>
            <input type="email" id="setEmail" placeholder="name@example.com" readonly aria-readonly="true" style="width:100%;padding:11px 14px;border:1px solid var(--line);border-radius:12px;background:var(--cream)">
            <span class="tiny muted" style="display:block;margin-top:4px">Email changes require account verification and are not edited from this page.</span>
          </div>
          <div>
            <label class="tiny" style="font-weight:700;display:block;margin-bottom:6px">Phone Number</label>
            <input type="tel" id="setPhone" placeholder="03XXXXXXXXX" style="width:100%;padding:11px 14px;border:1px solid var(--line);border-radius:12px">
          </div>
        </div>

        <div style="margin-bottom:28px">
          <label class="tiny" style="font-weight:700;display:block;margin-bottom:6px">City & Delivery Address</label>
          <input type="text" id="setCity" placeholder="e.g. DHA Phase 6, Karachi" style="width:100%;padding:11px 14px;border:1px solid var(--line);border-radius:12px">
          <span class="tiny muted" style="margin-top:4px;display:block">Used to auto-fill Cash on Delivery orders at checkout.</span>
        </div>

        <!-- Section 2: Baby Details -->
        <div style="border-top:1px solid var(--line);padding-top:24px;margin-top:24px">
          <h3 style="font-size:17px;margin:0 0 16px;color:var(--ink);display:flex;align-items:center;gap:8px">
            <span class="ico" data-ico="sparkle" aria-hidden="true"></span> Baby & Stage Details
          </h3>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px">
            <div>
              <label class="tiny" style="font-weight:700;display:block;margin-bottom:6px">Baby's Name / Nickname</label>
              <input type="text" id="setBabyName" placeholder="e.g. Zayd" style="width:100%;padding:11px 14px;border:1px solid var(--line);border-radius:12px">
            </div>
            <div>
              <label class="tiny" style="font-weight:700;display:block;margin-bottom:6px">Date of Birth (or Due Date)</label>
              <input type="date" id="setBabyDob" style="width:100%;padding:11px 14px;border:1px solid var(--line);border-radius:12px">
            </div>
          </div>

          <div style="margin-bottom:28px">
            <label class="tiny" style="font-weight:700;display:block;margin-bottom:6px">Current Feeding & Care Stage</label>
            <select id="setStage" style="width:100%;padding:11px 14px;border:1px solid var(--line);border-radius:12px;background:var(--surface)">
              <option value="Expecting">Expecting / Newborn prep</option>
              <option value="Newborn (0-3 months)">Newborn (0–3 months) · Natural Response teats</option>
              <option value="Infant (3-6 months)">Infant (3–6 months) · Anti-colic feeding</option>
              <option value="Weaning (6-12 months)">Weaning (6–12 months) · Solids & transition cups</option>
              <option value="Toddler (1-3 years)">Toddler (1–3 years) · Straw cups & meals</option>
            </select>
          </div>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--line);padding-top:24px">
          <a href="/profile" class="btn btn-ghost" style="text-decoration:none">Cancel</a>
          <button type="submit" class="btn btn-primary" style="padding:12px 28px">
            Save All Changes
          </button>
        </div>
      </form>
    </div>

  </div>
</main>

<div id="chrome-bottom"></div>
<script src="js/data.js?v=5.5-product-visual"></script>
<script src="js/app.js?v=5.5-product-visual"></script>
<script>
let pendingProfilePicture = "";
let pictureProcessing = false;
let pictureRequest = 0;

function validProfilePicture(value){
  return typeof value === "string" && (/^data:image\/(jpeg|png|webp);base64,[A-Za-z0-9+/=]+$/.test(value) || /^https?:\/\//i.test(value)) && value.length < 700000;
}
function pictureStatus(message, error=false){
  const el=document.getElementById("profilePictureStatus");
  el.textContent=message;el.style.color=error ? "#b42318" : "var(--ink-3)";
}
function renderProfilePicture(){
  const av=document.getElementById("editAvatar");
  av.replaceChildren();
  if(validProfilePicture(pendingProfilePicture)){
    const img=document.createElement("img");img.src=pendingProfilePicture;img.alt="Your profile picture";
    img.style.cssText="display:block;width:100%;height:100%;object-fit:cover;border-radius:50%";
    av.appendChild(img);
  } else av.textContent=document.getElementById("setName").value.trim().charAt(0).toUpperCase() || "P";
  document.getElementById("removeProfilePicture").hidden=!pendingProfilePicture;
}
async function chooseProfilePicture(event){
  const file=event.target.files && event.target.files[0];
  event.target.value="";
  if(!file)return;
  if(!["image/jpeg","image/png","image/webp"].includes(file.type)){pictureStatus("Please choose a JPG, PNG or WebP image.",true);return;}
  if(file.size > 5*1024*1024){pictureStatus("Please choose an image smaller than 5 MB.",true);return;}
  const request=++pictureRequest;pictureProcessing=true;pictureStatus("Preparing photo…");
  const url=URL.createObjectURL(file);
  try {
    const img=await new Promise((resolve,reject)=>{const image=new Image();image.onload=()=>resolve(image);image.onerror=()=>reject(new Error("Invalid image"));image.src=url;});
    if(img.naturalWidth*img.naturalHeight>40000000)throw new Error("Image dimensions too large");
    const size=Math.min(img.naturalWidth,img.naturalHeight);
    if(!size)throw new Error("Invalid image dimensions");
    const canvas=document.createElement("canvas");canvas.width=384;canvas.height=384;
    const context=canvas.getContext("2d");context.fillStyle="#ffffff";context.fillRect(0,0,384,384);
    context.drawImage(img,(img.naturalWidth-size)/2,(img.naturalHeight-size)/2,size,size,0,0,384,384);
    const photo=canvas.toDataURL("image/jpeg",0.80);
    if(!validProfilePicture(photo))throw new Error("Image could not be resized");
    if(request!==pictureRequest)return;
    pendingProfilePicture=photo;renderProfilePicture();pictureStatus("Photo ready. Click Save Changes to save it to your account.");
  } catch(error){if(request===pictureRequest)pictureStatus("This image could not be loaded. Please try another or a smaller photo.",true);}
  finally{URL.revokeObjectURL(url);if(request===pictureRequest)pictureProcessing=false;}
}

async function profileRequest(url, options={}){
  const res=await fetch(url,{credentials:'same-origin',headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest',...(options.body?{'Content-Type':'application/json'}:{})},...options});
  let data={}; try{data=await res.json();}catch(e){}
  if(res.status===401){window.location.href='/login?next=/edit-profile';throw new Error('Authentication required');}
  if(!res.ok || data.success===false)throw new Error(data.message || 'Unable to update profile');
  return data;
}

function populateUser(user={}){
  const nameVal=user.name || "Parent Member";
  document.getElementById("setName").value=nameVal;
  document.getElementById("setHeadline").value=user.headline || "";
  document.getElementById("setEmail").value=user.email || "";
  document.getElementById("setPhone").value=user.phone || "";
  document.getElementById("setCity").value=user.city || "";
  document.getElementById("setBabyName").value=user.babyName || "";
  document.getElementById("setBabyDob").value=user.babyDob || "";
  if(user.stage && [...document.getElementById("setStage").options].some(o=>o.value===user.stage))document.getElementById("setStage").value=user.stage;
  pendingProfilePicture=validProfilePicture(user.avatar) ? user.avatar : "";
  document.getElementById("editHeaderName").textContent=nameVal;
  document.getElementById("editHeaderSub").textContent=user.email || "ParentingHub Registered Family";
  renderProfilePicture();
}

async function loadUserSettings(){
  try{
    const data=await profileRequest('/api/profile/dashboard');
    populateUser(data.user || {});
    if(typeof setCurrentUser==='function')setCurrentUser(data.user || null);
  }catch(error){
    if(!/Authentication required/.test(error.message))pictureStatus(error.message,true);
  }
}

async function saveAllSettings(){
  if(pictureProcessing){pictureStatus("Please wait for the photo to finish processing.",true);return;}
  if(!document.getElementById("editProfileForm").reportValidity())return;
  const payload={
    name:document.getElementById("setName").value.trim(),
    headline:document.getElementById("setHeadline").value.trim(),
    phone:document.getElementById("setPhone").value.trim(),
    city:document.getElementById("setCity").value.trim(),
    babyName:document.getElementById("setBabyName").value.trim(),
    babyDob:document.getElementById("setBabyDob").value.trim(),
    stage:document.getElementById("setStage").value,
    avatar:pendingProfilePicture || ""
  };
  if(!payload.name){toast("Please enter your name","x");return;}
  const buttons=[...document.querySelectorAll('button[onclick="saveAllSettings()"],button[type="submit"]')];
  buttons.forEach(b=>b.disabled=true);
  pictureStatus("Saving to your account…");
  try{
    const data=await profileRequest('/api/auth/profile',{method:'PUT',body:JSON.stringify(payload)});
    if(typeof setCurrentUser==='function')setCurrentUser(data.user || payload);
    pictureStatus("Profile saved to your account.");
    toast("Profile and baby details saved successfully!","check");
    setTimeout(()=>{window.location.href='/profile';},400);
  }catch(error){pictureStatus(error.message,true);}
  finally{buttons.forEach(b=>b.disabled=false);}
}

document.addEventListener("DOMContentLoaded",()=>{
  loadUserSettings();
  document.getElementById("changeProfilePicture").addEventListener("click",()=>document.getElementById("profilePictureInput").click());
  document.getElementById("profilePictureInput").addEventListener("change",chooseProfilePicture);
  document.getElementById("setName").addEventListener("input",renderProfilePicture);
  document.getElementById("removeProfilePicture").addEventListener("click",()=>{++pictureRequest;pictureProcessing=false;pendingProfilePicture="";renderProfilePicture();pictureStatus("Photo removed. Click Save Changes to save this change.");});
});
</script>
</body>
</html>
