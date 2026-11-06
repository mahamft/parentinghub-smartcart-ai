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
<title>My Space | Parent Dashboard | ParentingHub</title>
<meta name="description" content="Your personal parenting dashboard — track placed orders, saved questions, milestones, and wishlist in one calm place.">
<link rel="canonical" href="https://www.parentinghub.pk/profile">
<meta name="robots" content="noindex, nofollow">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:locale" content="en_PK">
<meta property="og:site_name" content="ParentingHub Pakistan">
<meta property="og:title" content="My Space | Parent Dashboard | ParentingHub">
<meta property="og:description" content="Your personal parenting dashboard — track placed orders, saved questions, milestones, and wishlist in one calm place.">
<meta property="og:url" content="https://www.parentinghub.pk/profile">
<meta property="og:image" content="https://www.parentinghub.pk/assets/visual/banner-bottles.jpg">

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
@media(max-width:760px){.ph-blog-form-grid{grid-template-columns:1fr!important}}
.ph-blog-status{display:inline-flex;padding:4px 9px;border-radius:999px;font-size:11px;font-weight:800;background:#f1f5f9;color:#475569}.ph-blog-status.published{background:#dcfce7;color:#166534}.ph-blog-status.pending{background:#fef3c7;color:#92400e}.ph-blog-status.rejected{background:#fee2e2;color:#991b1b}.ph-blog-status.needs_changes{background:#e0f2fe;color:#075985}
</style>
</head>
<body data-page="profile">
<div id="chrome-top"></div>

<main class="profile-shell" id="profWrap">
  <!-- ===== cover ===== -->
  <div class="cover">
    <svg class="doodle" viewBox="0 0 1200 260" preserveAspectRatio="none" aria-hidden="true">
      <g fill="none" stroke="#8063ac" stroke-width="1.4" opacity=".55">
        <path d="M120 210c60-90 130-96 190-40"/><path d="M980 60c-40 60-110 70-170 26"/>
        <circle cx="330" cy="70" r="4"/><circle cx="860" cy="190" r="4"/><circle cx="620" cy="46" r="3"/>
      </g>
      <g fill="none" stroke="#bd3d6b" stroke-width="1.6" opacity=".5">
        <path d="M420 180c10-16 26-16 34 0-8 14-26 14-34 0z"/>
        <path d="M780 120c8-13 22-13 29 0-7 12-22 12-29 0z"/>
      </g>
    </svg>
  </div>

  <div class="container">
    <!-- ===== profile card ===== -->
    <div class="p-card">
      <div class="p-top">
        <span class="avatar lg g" id="userAvatarDisplay" style="background:var(--terra);color:#fff;font-weight:700">P</span>
        <div class="p-name grow">
          <h1 id="userNameDisplay">Loading profile…</h1>
          <p class="headline" id="userHeadlineDisplay">Your private ParentingHub account</p>
          <p class="meta" id="userMetaDisplay">Loading account details…</p>
        </div>
        <div class="p-actions">
          <a class="btn btn-soft btn-s" href="/edit-profile">Change photo</a>
          <button class="btn btn-ghost btn-s" onclick="openProfileEdit()">Edit profile</button>
          <button class="btn btn-s" onclick="logoutUser()" style="background:#fee2e2;color:#dc2626;border:1px solid #fecaca;font-weight:600;display:inline-flex;align-items:center;gap:6px">
            Sign Out
          </button>
        </div>
      </div>

      <div class="p-stats">
        <div><b id="statOrders">0</b><span>Placed orders</span></div>
        <div><b id="statWish">0</b><span>Wishlist items</span></div>
        <div><b id="statQuestions">0</b><span>Questions asked</span></div>
        <div><b>100%</b><span>Authentic items</span></div>
        <div><b style="color:var(--green)">Active</b><span>Member status</span></div>
      </div>

      <div class="row wrap gap-s" style="margin-top:20px" id="profileChips">
        <a class="chip on" href="#orders"><span class="ico" data-ico="bag" aria-hidden="true"></span> My Orders</a>
        <a class="chip" href="#saved"><span class="ico" data-ico="heart" aria-hidden="true"></span> Saved Wishlist</a>
        <a class="chip" href="#journey"><span class="ico" data-ico="moon" aria-hidden="true"></span> Baby journey</a>
        <a class="chip" href="#skills"><span class="ico" data-ico="sparkle" aria-hidden="true"></span> Milestone tracker</a>
        <a class="chip" href="#activity"><span class="ico" data-ico="pencil" aria-hidden="true"></span> Activity</a>
        <a class="chip" href="#my-blogs"><span aria-hidden="true">📝</span> My Blogs</a>
        <a class="chip" href="#write-blog"><span aria-hidden="true">✍️</span> Write a Blog</a>
        <a class="chip" href="/edit-profile" style="margin-inline-start:auto;background:var(--cream);font-weight:700"><span class="ico" data-ico="pencil" aria-hidden="true"></span> Edit Profile Page ↗</a>
      </div>
    </div>

    <!-- ===== 2-column ===== -->
    <div class="layout-profile">
      <div>
        <!-- my orders -->
        <section class="section-card" id="orders">
          <div class="row" style="justify-content:space-between;align-items:center;margin-bottom:12px">
            <h3 style="margin:0"><span class="ico" data-ico="bag" aria-hidden="true"></span> My Orders</h3>
            <span class="tiny muted">Real-time status & parcel tracking</span>
          </div>
          <div id="myOrders" style="margin-top:14px"></div>
        </section>

        <!-- wishlist / saved -->
        <div id="saved" style="scroll-margin-top:90px"></div>
        <section class="section-card" id="wishlist" style="scroll-margin-top:90px">
          <div class="row" style="justify-content:space-between;align-items:center;margin-bottom:12px">
            <h3 style="margin:0"><span class="ico" data-ico="heart" aria-hidden="true"></span> My Saved Items &amp; Wishlist</h3>
            <span class="tiny muted">Items saved for your baby</span>
          </div>
          <div class="grid-products g3" id="wishGrid" style="gap:16px"></div>
        </section>

        <!-- journey -->
        <section class="section-card" id="journey">
          <div class="row" style="justify-content:space-between;align-items:center;margin-bottom:12px;flex-wrap:wrap;gap:8px">
            <h3 style="margin:0"><span class="ico" data-ico="moon" aria-hidden="true"></span> Baby journey</h3>
            <div style="display:flex;gap:8px">
              <button class="btn btn-soft btn-xs" onclick="if(window.openAiMilestoneAdvisor) window.openAiMilestoneAdvisor();" style="color:var(--primary);font-weight:700">✨ Ask AI Advisor</button>
              <button class="btn btn-ghost btn-xs" onclick="openAddMilestone()"><span class="ico" data-ico="plus" aria-hidden="true"></span> Add milestone</button>
            </div>
          </div>
          <div class="tl" id="journeyTimeline">
            <div class="empty" style="padding:24px 12px"><b>No milestones added yet</b><p class="small muted" style="margin-top:6px">Add real milestones to build your private baby journey.</p></div>
          </div>
        </section>

        <!-- milestone tracker -->
        <section class="section-card" id="skills">
          <h3><span class="ico" data-ico="sparkle" aria-hidden="true"></span> Milestone Tracker</h3>
          <div id="skillsList">
            <div class="empty" style="padding:22px 12px"><b>No progress data recorded</b><p class="small muted" style="margin-top:6px">Only progress you explicitly save to your account will appear here.</p></div>
          </div>
          <p class="tiny muted" style="margin-top:12px">Self-tracked parenting notes. Not formal medical advice.</p>
        </section>

        <!-- activity -->
        <section class="section-card" id="activity">
          <h3><span class="ico" data-ico="pencil" aria-hidden="true"></span> Community Questions & Activity</h3>
          <div id="myPosts">
            <div style="text-align:center;padding:24px 16px;background:var(--cream);border-radius:14px">
              <p class="small muted" style="margin:0 0 12px">Have a question about bottle feeding, weaning, or sleep?</p>
              <a href="/community#ask" class="btn btn-primary btn-xs">Ask the Parent Community <span class="ico" data-ico="arrow" aria-hidden="true"></span></a>
            </div>
          </div>
        </section>

        <!-- user blog submissions -->
        <section class="section-card" id="my-blogs" style="scroll-margin-top:90px">
          <div class="row" style="justify-content:space-between;align-items:center;margin-bottom:12px;gap:12px;flex-wrap:wrap">
            <div>
              <h3 style="margin:0">📝 My Blogs</h3>
              <p class="tiny muted" style="margin:4px 0 0">Track articles you submitted to ParentingHub Editorial.</p>
            </div>
            <a class="btn btn-soft btn-xs" href="#write-blog">Write a Blog</a>
          </div>
          <div id="myBlogSubmissions">
            <div class="empty" style="padding:26px 12px"><b>Loading your submissions…</b></div>
          </div>
        </section>

        <section class="section-card" id="write-blog" style="scroll-margin-top:90px">
          <div style="margin-bottom:16px">
            <h3 style="margin:0">✍️ Write for ParentingHub</h3>
            <p class="small muted" style="margin:6px 0 0">Share a useful parenting experience or guide. Every submission is reviewed before it appears on the public Blog.</p>
          </div>
          <form id="parentBlogForm" onsubmit="submitParentBlog(event)">
            <div style="display:grid;grid-template-columns:1fr 220px;gap:12px" class="ph-blog-form-grid">
              <div>
                <label class="tiny" style="font-weight:700" for="blogTitle">Article title</label>
                <input id="blogTitle" maxlength="180" required placeholder="e.g. What helped us build a calmer bedtime routine" style="width:100%;padding:11px 13px;border:1px solid var(--line);border-radius:10px;margin-top:5px">
              </div>
              <div>
                <label class="tiny" style="font-weight:700" for="blogCategory">Category</label>
                <select id="blogCategory" style="width:100%;padding:11px 13px;border:1px solid var(--line);border-radius:10px;margin-top:5px;background:#fff">
                  <option>Parenting Tips</option>
                  <option>Baby Care</option>
                  <option>Feeding & Nutrition</option>
                  <option>Child Development</option>
                  <option>Family & Lifestyle</option>
                  <option>Product Guides</option>
                </select>
              </div>
            </div>
            <div style="margin-top:12px">
              <label class="tiny" style="font-weight:700" for="blogExcerpt">Short summary</label>
              <textarea id="blogExcerpt" maxlength="420" required placeholder="2–3 sentences explaining what readers will learn." style="width:100%;min-height:82px;padding:11px 13px;border:1px solid var(--line);border-radius:10px;margin-top:5px;resize:vertical"></textarea>
            </div>
            <div style="margin-top:12px">
              <label class="tiny" style="font-weight:700" for="blogContent">Article</label>
              <textarea id="blogContent" required placeholder="Write your article here. Please keep it useful, original and easy to understand." style="width:100%;min-height:280px;padding:13px;border:1px solid var(--line);border-radius:10px;margin-top:5px;resize:vertical;line-height:1.65"></textarea>
              <div class="tiny muted" id="blogWordCount" style="margin-top:5px">0 words · Aim for at least 300 words.</div>
            </div>
            <div style="margin-top:12px;padding:14px;background:var(--cream);border:1px solid var(--line);border-radius:12px">
              <label class="tiny" style="font-weight:700" for="blogImage">Featured image <span class="muted">(optional)</span></label>
              <input id="blogImage" type="file" accept="image/jpeg,image/png,image/webp" style="display:block;margin-top:8px">
              <div id="blogImagePreview" style="display:none;margin-top:10px;max-width:320px"></div>
              <div class="tiny muted" style="margin-top:6px">JPG, PNG or WebP. The image is optimized before submission.</div>
            </div>
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-top:16px">
              <button class="btn btn-primary" id="blogSubmitBtn" type="submit">Submit for Review</button>
              <span id="blogSubmitStatus" class="small muted" role="status" aria-live="polite"></span>
            </div>
          </form>
        </section>

      </div>

      <!-- right rail -->
      <aside class="rail rail-r2">
        <div class="panel">
          <h5>Account Status</h5>
          <div class="row" style="justify-content:space-between;margin-bottom:8px">
            <b class="serif" style="font-size:22px">Verified</b><span class="tag tag-new">100% Ready</span>
          </div>
          <p class="tiny muted" style="margin-top:8px">Your profile is connected. All orders and saved products sync to your account.</p>
        </div>

        <div class="panel">
          <h5>Quick Parent Support</h5>
          <div class="stack" style="gap:10px;font-size:13.5px">
            <div><b>Email Support:</b> <br><a href="mailto:support@parentinghub.pk" target="_blank" style="color:var(--terra);font-weight:600">Email Support</a></div>
            <div><b>Delivery Coverage:</b> <br><span class="muted">All Pakistan (Karachi, Lahore, Islamabad & 200+ cities)</span></div>
            <div><b>Payment:</b> <br><span class="muted">Cash on Delivery & Bank Transfer</span></div>
          </div>
        </div>

        <div class="panel">
          <h5>Recommended Communities</h5>
          <div class="stack" style="gap:8px">
            <div class="people"><span class="em" style="font-size:18px"><span class="ico" data-ico="moon" aria-hidden="true"></span></span><span class="grow"><b>Newborn Parents</b><span>Live discussions</span></span><a class="btn btn-ghost btn-xs" href="/community">Explore</a></div>
            <div class="people"><span class="em" style="font-size:18px"><span class="ico" data-ico="bottle" aria-hidden="true"></span></span><span class="grow"><b>Feeding & Bottles</b><span>Live discussions</span></span><a class="btn btn-ghost btn-xs" href="/community">Explore</a></div>
            <div class="people"><span class="em" style="font-size:18px"><span class="ico" data-ico="leaf" aria-hidden="true"></span></span><span class="grow"><b>Starting Solids</b><span>Live discussions</span></span><a class="btn btn-ghost btn-xs" href="/community">Explore</a></div>
          </div>
        </div>
      </aside>
    </div>
  </div>
</main>

<!-- Profile Edit Modal -->
<div class="modal" id="profileEditModal">
  <div class="scrim" onclick="closeAll()"></div>
  <div class="box" style="max-width:520px">
    <header>
      <b class="serif" style="font-size:18px">Edit Parent Profile</b>
      <button class="icon-btn" aria-label="Close" onclick="closeAll()"><span class="ico" data-ico="x" aria-hidden="true"></span></button>
    </header>
    <div class="pad">
      <div style="display:flex;flex-direction:column;gap:14px">
        <div style="display:flex;align-items:center;flex-wrap:wrap;gap:14px;padding-bottom:16px;border-bottom:1px solid var(--line)">
          <span class="avatar lg g" id="profEditAvatar" style="background:var(--terra);color:#fff;font-weight:700;width:72px;height:72px;border-radius:50%;overflow:hidden;flex-shrink:0">P</span>
          <div style="flex:1;min-width:180px">
            <b class="small" style="display:block;margin-bottom:8px">Profile picture</b>
            <div style="display:flex;flex-wrap:wrap;gap:8px">
              <button type="button" class="btn btn-soft btn-xs" id="changeProfilePicture">Change Photo</button>
              <button type="button" class="btn btn-ghost btn-xs" id="removeProfilePicture" hidden>Remove Photo</button>
            </div>
            <input type="file" id="profilePictureInput" accept="image/jpeg,image/png,image/webp" hidden aria-label="Choose profile picture">
            <span class="tiny muted" style="display:block;margin-top:8px">JPG, PNG or WebP · Maximum 5 MB. Saved securely to your ParentingHub account.</span>
            <span id="profilePictureStatus" class="tiny" role="status" aria-live="polite" style="display:block;margin-top:6px"></span>
          </div>
        </div>
        <div>
          <label class="tiny" style="font-weight:700">Full Name</label>
          <input type="text" id="profNameInput" class="form-input" placeholder="e.g. Ayesha Khan" style="width:100%;padding:10px 14px;border:1px solid var(--line);border-radius:10px;margin-top:4px">
        </div>
        <div>
          <label class="tiny" style="font-weight:700">Headline / Baby Stage</label>
          <input type="text" id="profHeadInput" class="form-input" placeholder="e.g. Mom of 6-month-old · Natural Response feeding" style="width:100%;padding:10px 14px;border:1px solid var(--line);border-radius:10px;margin-top:4px">
        </div>
        <div>
          <label class="tiny" style="font-weight:700">City / Location</label>
          <input type="text" id="profCityInput" class="form-input" placeholder="e.g. Karachi, Pakistan" style="width:100%;padding:10px 14px;border:1px solid var(--line);border-radius:10px;margin-top:4px">
        </div>
        <button class="btn btn-primary" onclick="saveProfileEdit()">Save Profile Changes</button>
        <div style="text-align:center;margin-top:4px">
          <a href="/edit-profile" class="tiny" style="color:var(--primary);font-weight:600;text-decoration:none">Need to update email, phone or baby birthday? Open full edit page ↗</a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add Milestone Modal -->
<div class="modal" id="milestoneModal">
  <div class="scrim" onclick="closeAll()" style="position:fixed;inset:0;opacity:1;pointer-events:auto"></div>
  <div class="box" style="max-width:500px">
    <header>
      <b class="serif" style="font-size:18px">Add Baby Milestone</b>
      <button class="icon-btn" aria-label="Close" onclick="closeAll()"><span class="ico" data-ico="x" aria-hidden="true"></span></button>
    </header>
    <div class="pad">
      <div style="display:flex;flex-direction:column;gap:14px">
        <div>
          <label class="tiny" style="font-weight:700">Milestone Title</label>
          <input type="text" id="msTitleInput" placeholder="e.g. First smile, First full bottle, Sat up" style="width:100%;padding:10px 14px;border:1px solid var(--line);border-radius:10px;margin-top:4px">
        </div>
        <div>
          <label class="tiny" style="font-weight:700">Little Details / Memories</label>
          <textarea id="msDescInput" placeholder="What happened? Share the little memory..." style="width:100%;height:80px;padding:10px 14px;border:1px solid var(--line);border-radius:10px;margin-top:4px;resize:none"></textarea>
        </div>
        <div>
          <label class="tiny" style="font-weight:700">When did it happen?</label>
          <input type="text" id="msDateInput" placeholder="e.g. Today, Yesterday, Last week" style="width:100%;padding:10px 14px;border:1px solid var(--line);border-radius:10px;margin-top:4px" value="Today">
        </div>
        <button class="btn btn-primary" onclick="saveNewMilestone()">Save to Baby Journey</button>
      </div>
    </div>
  </div>
</div>

<div id="chrome-bottom"></div>
<script src="js/data.js?v=5.5-product-visual"></script>
<script src="js/app.js?v=5.5-product-visual"></script>
<script>
let dashboard={user:null,orders:[],posts:[],wishlist:[],stats:{orders:0,wishlist:0,questions:0}};
let pendingProfilePicture="", pictureProcessing=false, pictureRequest=0;

async function profileApi(url,options={}){
  const headers={'Accept':'application/json','X-Requested-With':'XMLHttpRequest',...(options.body?{'Content-Type':'application/json'}:{}),...(options.headers||{})};
  const res=await fetch(url,{credentials:'same-origin',...options,headers});
  const data=await res.json().catch(()=>({success:false,message:'Invalid server response.'}));
  if(!res.ok||data.success===false){const err=new Error(data.message||`Request failed (${res.status})`);err.status=res.status;throw err;}
  return data;
}
function validProfilePicture(value){return typeof value==='string' && (/^data:image\/(jpeg|png|webp);base64,[A-Za-z0-9+/=]+$/.test(value)||/^https?:\/\//i.test(value)||value.startsWith('/assets/')) && value.length<700000;}
function pictureStatus(message,error=false){const el=document.getElementById('profilePictureStatus');if(el){el.textContent=message;el.style.color=error?'#b42318':'var(--ink-3)';}}
function renderProfilePicture(){
  const av=document.getElementById('profEditAvatar'); if(!av)return; av.replaceChildren();
  if(validProfilePicture(pendingProfilePicture)){const img=document.createElement('img');img.src=pendingProfilePicture;img.alt='Your profile picture';img.style.cssText='display:block;width:100%;height:100%;object-fit:cover;border-radius:50%';av.appendChild(img);}
  else av.textContent=(document.getElementById('profNameInput')?.value||'P').trim().charAt(0).toUpperCase()||'P';
  const rm=document.getElementById('removeProfilePicture');if(rm)rm.hidden=!pendingProfilePicture;
}
async function chooseProfilePicture(event){
  const file=event.target.files&&event.target.files[0];event.target.value='';if(!file)return;
  if(!['image/jpeg','image/png','image/webp'].includes(file.type)){pictureStatus('Please choose a JPG, PNG or WebP image.',true);return;}
  if(file.size>5*1024*1024){pictureStatus('Please choose an image smaller than 5 MB.',true);return;}
  const request=++pictureRequest;pictureProcessing=true;pictureStatus('Preparing photo…');const url=URL.createObjectURL(file);
  try{const img=await new Promise((resolve,reject)=>{const i=new Image();i.onload=()=>resolve(i);i.onerror=()=>reject(new Error('Invalid image'));i.src=url;});
    if(img.naturalWidth*img.naturalHeight>40000000)throw new Error('Image dimensions too large');const size=Math.min(img.naturalWidth,img.naturalHeight);const canvas=document.createElement('canvas');canvas.width=512;canvas.height=512;const ctx=canvas.getContext('2d');ctx.fillStyle='#fff';ctx.fillRect(0,0,512,512);ctx.drawImage(img,(img.naturalWidth-size)/2,(img.naturalHeight-size)/2,size,size,0,0,512,512);const photo=canvas.toDataURL('image/jpeg',.82);if(request!==pictureRequest)return;pendingProfilePicture=photo;renderProfilePicture();pictureStatus('Photo ready. Click Save Profile Changes.');
  }catch(e){if(request===pictureRequest)pictureStatus('This image could not be processed. Try a smaller image.',true);}finally{URL.revokeObjectURL(url);if(request===pictureRequest)pictureProcessing=false;}
}
function renderSavedProfilePhotos(user){
  const initial=(user.name||'P').trim().charAt(0).toUpperCase()||'P', photo=validProfilePicture(user.avatar)?user.avatar:'';
  for(const id of ['userAvatarDisplay','navAvatar']){const av=document.getElementById(id);if(!av)continue;av.replaceChildren();av.style.overflow='hidden';if(photo){const img=document.createElement('img');img.src=photo;img.alt='Your profile picture';img.style.cssText='display:block;width:100%;height:100%;object-fit:cover;border-radius:inherit';img.onerror=()=>{av.replaceChildren();av.textContent=initial;};av.appendChild(img);}else av.textContent=initial;}
}
function renderDashboardUser(){
  const user=dashboard.user;if(!user)return;renderSavedProfilePhotos(user);if(typeof setCurrentUser==='function')setCurrentUser(user);
  const nm=document.getElementById('userNameDisplay');if(nm)nm.textContent=user.name||'Parent User';
  const hd=document.getElementById('userHeadlineDisplay');if(hd)hd.textContent=user.headline||'Parent Member · Baby Essentials & Care';
  const city=user.city||'Pakistan', email=user.email?` · ${user.email}`:'';const mt=document.getElementById('userMetaDisplay');if(mt)mt.innerHTML=`${icon('pin')} ${esc(city)}${esc(email)} · <span style="color:var(--green)">${icon('check')} Active Account</span>`;
  const pName=document.getElementById('profNameInput');if(pName)pName.value=user.name||'';const pHead=document.getElementById('profHeadInput');if(pHead)pHead.value=user.headline||'';const pCity=document.getElementById('profCityInput');if(pCity)pCity.value=user.city||'';
}
function renderOrdersList(){
  const box=document.getElementById('myOrders');if(!box)return;const orders=dashboard.orders||[];document.getElementById('statOrders').textContent=orders.length;
  if(!orders.length){box.innerHTML=`<div style="text-align:center;padding:36px 16px;background:var(--cream);border-radius:18px;border:1px dashed var(--line)"><h4 style="margin:0 0 6px">No orders placed yet</h4><p class="muted small">Your account orders will appear here.</p><a href="/shop" class="btn btn-primary btn-s">Explore Shop</a></div>`;return;}
  box.innerHTML=orders.map(ord=>{const items=(ord.items||[]).map(i=>`${esc(i.product?.name||i.name||'Product')}${i.option?` (${esc(i.option)})`:''} × ${Number(i.qty||1)}`).join(', ');const addr=ord.address||{};return `<div class="panel" style="margin-bottom:14px"><div class="row" style="justify-content:space-between;gap:8px;flex-wrap:wrap"><b>Order #${esc(ord.id)}</b><span class="tag">${esc(ord.status||'Confirmed')}</span></div><p class="small" style="margin-top:10px"><b>Items:</b> ${items||'Order items'}</p><p class="tiny muted">${esc(addr.address||'')} ${esc(addr.city||'')}</p><div class="row" style="justify-content:space-between;border-top:1px solid var(--line);padding-top:10px;margin-top:10px"><span class="small">${esc(ord.paymentMethod||'')}</span><b>${money(ord.total||0)}</b></div></div>`;}).join('');
}
function renderWishlist(){
  const grid=document.getElementById('wishGrid');if(!grid)return;const ids=dashboard.wishlist||[];document.getElementById('statWish').textContent=ids.length;
  if(typeof S!=='undefined'){S.wish=[...ids];if(typeof save==='function')save();}
  const items=(typeof PRODUCTS!=='undefined'&&Array.isArray(PRODUCTS))?ids.map(id=>PRODUCTS.find(p=>p.id===id)).filter(Boolean):[];
  grid.innerHTML=items.length&&typeof productCard==='function'?items.map(productCard).join(''):`<div style="grid-column:1/-1;text-align:center;padding:32px 16px;background:var(--cream);border-radius:18px"><b>Your account wishlist is empty</b><p class="small muted" style="margin-top:6px">Save products with the heart icon.</p><a href="/shop" class="btn btn-soft btn-s" style="margin-top:10px">Browse Products</a></div>`;
}
function renderActivity(){
  const box=document.getElementById('myPosts');if(!box)return;const posts=dashboard.posts||[];document.getElementById('statQuestions').textContent=posts.length;
  box.innerHTML=posts.length?posts.map(p=>`<div class="panel" style="margin-bottom:12px"><a href="/post?id=${encodeURIComponent(p.id)}"><b>${esc(p.title)}</b></a><p class="small muted" style="margin-top:6px">${esc(p.body)}</p><div class="tiny muted" style="margin-top:8px">${Number(p.comments||0)} answers · ${Number(p.votes||0)} votes</div></div>`).join(''):`<div class="empty" style="padding:30px 10px"><b>No questions yet</b><p class="small muted">Your database-backed community posts will appear here.</p><a href="/community#ask" class="btn btn-soft btn-s">Ask a question</a></div>`;
}
function renderJourney(){
  const tl=document.getElementById('journeyTimeline');if(!tl)return;const ms=Array.isArray(dashboard.user?.milestones)?dashboard.user.milestones:[];
  tl.innerHTML=ms.length?ms.slice().reverse().map(m=>`<div class="tl-item sage"><b>${esc(m.title)}</b><div class="sub">${esc(m.description||'')}</div><div class="when">${esc(m.when||'Recently')}</div></div>`).join(''):`<div class="empty" style="padding:24px 12px"><b>No milestones added yet</b><p class="small muted" style="margin-top:6px">Add real milestones to build your private baby journey.</p></div>`;
}
function renderSkills(){
  const box=document.getElementById('skillsList');if(!box)return;const skills=Array.isArray(dashboard.user?.skills)?dashboard.user.skills:[];
  box.innerHTML=skills.length?skills.map(x=>{const value=Math.max(0,Math.min(100,Number(x.value||0)));return `<div class="skill"><div class="row" style="justify-content:space-between"><span class="small" style="font-weight:600">${esc(x.label||'Progress')}</span><span class="tiny muted">${value}%</span></div><div class="bar"><i style="width:${value}%"></i></div></div>`;}).join(''):`<div class="empty" style="padding:22px 12px"><b>No progress data recorded</b><p class="small muted" style="margin-top:6px">Only progress you explicitly save will appear here.</p></div>`;
}
async function loadDashboard(){
  try{const d=await profileApi('/api/profile/dashboard');dashboard=d;if(typeof setCurrentUser==='function')setCurrentUser(d.user);renderDashboardUser();renderOrdersList();renderWishlist();renderActivity();renderJourney();renderSkills();}
  catch(err){if(err.status===401){location.replace('/login?next=%2Fprofile');return;}if(typeof toast==='function')toast(err.message||'Dashboard could not load.');}
}
function openProfileEdit(){const u=dashboard.user||{};document.getElementById('profNameInput').value=u.name||'';document.getElementById('profHeadInput').value=u.headline||'';document.getElementById('profCityInput').value=u.city||'';pendingProfilePicture=validProfilePicture(u.avatar)?u.avatar:'';renderProfilePicture();pictureStatus('');const m=document.getElementById('profileEditModal');if(m){m.classList.add('open','on');}}
async function saveProfileEdit(){
  if(pictureProcessing){pictureStatus('Please wait for the photo to finish processing.',true);return;}const name=document.getElementById('profNameInput').value.trim(),headline=document.getElementById('profHeadInput').value.trim(),city=document.getElementById('profCityInput').value.trim();if(!name){pictureStatus('Name is required.',true);return;}
  try{const d=await profileApi('/api/auth/profile',{method:'PUT',body:JSON.stringify({name,city,headline,avatar:pendingProfilePicture||''})});dashboard.user=d.user;renderDashboardUser();document.getElementById('profileEditModal')?.classList.remove('open','on');if(typeof closeAll==='function')closeAll();toast('Profile saved to your account','check');}
  catch(err){pictureStatus(err.message||'Profile could not be saved.',true);}
}
function openAddMilestone(){document.getElementById('milestoneModal')?.classList.add('open','on');}
async function saveNewMilestone(){
  const title=document.getElementById('msTitleInput').value.trim(),description=document.getElementById('msDescInput').value.trim(),when=document.getElementById('msDateInput').value.trim()||'Today';if(!title){toast('Please enter a milestone title');return;}
  const milestones=Array.isArray(dashboard.user?.milestones)?[...dashboard.user.milestones]:[];milestones.push({title,description,when,createdAt:new Date().toISOString()});
  try{const d=await profileApi('/api/auth/profile',{method:'PUT',body:JSON.stringify({milestones})});dashboard.user=d.user;renderJourney();document.getElementById('msTitleInput').value='';document.getElementById('msDescInput').value='';if(typeof closeAll==='function')closeAll();toast('Milestone saved to your account','heart');}
  catch(err){toast(err.message||'Milestone could not be saved.');}
}
function handleProfileHash(){const hash=(location.hash||'').replace('#','').toLowerCase();const el=document.getElementById(hash==='saved'?'wishlist':hash);if(el)setTimeout(()=>el.scrollIntoView({behavior:'smooth',block:'start'}),100);if(hash==='edit')openProfileEdit();}
window.addEventListener('hashchange',handleProfileHash);


// ===== ParentingHub user blog submissions (WordPress moderation bridge) =====
let parentBlogImageData='';
const PH_BLOG_APIS=[
  '/wp-build/wp-json/parentinghub/v1/blog-submissions',
  '/wp-json/parentinghub/v1/blog-submissions'
];
async function parentBlogApi(options={}){
  let lastErr=null;
  for(const endpoint of PH_BLOG_APIS){
    try{
      const res=await fetch(endpoint,{credentials:'include',...options,headers:{'Accept':'application/json','Content-Type':'application/json','X-ParentingHub-Client':'my-space',...(options.headers||{})}});
      const data=await res.json().catch(()=>({success:false,message:'Invalid WordPress response.'}));
      if(res.status===404){lastErr=new Error('Blog endpoint not found.');continue;}
      if(!res.ok||data.success===false)throw new Error(data.message||`Request failed (${res.status})`);
      return data;
    }catch(err){lastErr=err;}
  }
  throw lastErr||new Error('Blog service is unavailable.');
}
function phBlogEsc(v){return String(v??'').replace(/[&<>'"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[c]));}
function phBlogStatusLabel(status){return ({published:'Published',pending:'Pending Review',needs_changes:'Needs Changes',rejected:'Not Approved',draft:'Draft'}[status]||status||'Pending Review');}
async function loadMyBlogs(){
  const box=document.getElementById('myBlogSubmissions');if(!box)return;
  try{const d=await parentBlogApi({method:'GET'});const rows=Array.isArray(d.submissions)?d.submissions:[];
    if(!rows.length){box.innerHTML='<div class="empty" style="padding:28px 12px"><b>No blog submissions yet</b><p class="small muted" style="margin-top:6px">Use “Write a Blog” below to submit your first article for editorial review.</p></div>';return;}
    box.innerHTML=rows.map(x=>`<div class="panel" style="margin-bottom:12px"><div class="row" style="justify-content:space-between;gap:10px;align-items:flex-start;flex-wrap:wrap"><div class="grow"><b>${phBlogEsc(x.title)}</b><div class="tiny muted" style="margin-top:4px">${phBlogEsc(x.category||'Parenting')} · ${phBlogEsc(x.date||'')}</div></div><span class="ph-blog-status ${phBlogEsc(x.status)}">${phBlogEsc(phBlogStatusLabel(x.status))}</span></div>${x.feedback?`<div class="small" style="margin-top:10px;padding:10px 12px;background:var(--cream);border-radius:10px"><b>Editorial note:</b> ${phBlogEsc(x.feedback)}</div>`:''}${x.url?`<a href="${phBlogEsc(x.url)}" class="btn btn-ghost btn-xs" style="margin-top:10px">View published article ↗</a>`:''}</div>`).join('');
  }catch(e){box.innerHTML=`<div class="empty" style="padding:24px 12px"><b>Could not load blog submissions</b><p class="small muted">${phBlogEsc(e.message)}</p></div>`;}
}
async function phProcessBlogImage(file){
  if(!file)return '';if(!/^image\/(jpeg|png|webp)$/i.test(file.type))throw new Error('Please choose a JPG, PNG or WebP image.');if(file.size>8*1024*1024)throw new Error('Image is too large. Please use an image under 8 MB.');
  const url=URL.createObjectURL(file);try{const img=new Image();await new Promise((ok,bad)=>{img.onload=ok;img.onerror=bad;img.src=url;});const maxW=1600,maxH=1000,scale=Math.min(1,maxW/img.naturalWidth,maxH/img.naturalHeight);const c=document.createElement('canvas');c.width=Math.max(1,Math.round(img.naturalWidth*scale));c.height=Math.max(1,Math.round(img.naturalHeight*scale));const ctx=c.getContext('2d');ctx.fillStyle='#fff';ctx.fillRect(0,0,c.width,c.height);ctx.drawImage(img,0,0,c.width,c.height);return c.toDataURL('image/jpeg',.82);}finally{URL.revokeObjectURL(url);}
}
async function submitParentBlog(ev){
  ev.preventDefault();const btn=document.getElementById('blogSubmitBtn'),status=document.getElementById('blogSubmitStatus');const title=document.getElementById('blogTitle').value.trim(),category=document.getElementById('blogCategory').value,excerpt=document.getElementById('blogExcerpt').value.trim(),content=document.getElementById('blogContent').value.trim();const words=content.split(/\s+/).filter(Boolean).length;
  if(title.length<8){status.textContent='Please enter a clearer title.';return;}if(words<120){status.textContent='Please add more detail before submitting (minimum 120 words).';return;}
  btn.disabled=true;status.textContent='Submitting for editorial review…';
  try{const d=await parentBlogApi({method:'POST',body:JSON.stringify({title,category,excerpt,content,featuredImage:parentBlogImageData})});status.textContent=d.message||'Submitted for review.';document.getElementById('parentBlogForm').reset();parentBlogImageData='';document.getElementById('blogImagePreview').style.display='none';document.getElementById('blogWordCount').textContent='0 words · Aim for at least 300 words.';await loadMyBlogs();document.getElementById('my-blogs')?.scrollIntoView({behavior:'smooth',block:'start'});}catch(e){status.textContent=e.message||'Submission failed.';}finally{btn.disabled=false;}
}

document.addEventListener('DOMContentLoaded',()=>{
  document.getElementById('changeProfilePicture')?.addEventListener('click',()=>document.getElementById('profilePictureInput').click());document.getElementById('profilePictureInput')?.addEventListener('change',chooseProfilePicture);document.getElementById('profNameInput')?.addEventListener('input',renderProfilePicture);document.getElementById('removeProfilePicture')?.addEventListener('click',()=>{++pictureRequest;pendingProfilePicture='';renderProfilePicture();pictureStatus('Photo removed. Save changes to update your account.');});loadDashboard().then(()=>setTimeout(handleProfileHash,100));
  loadMyBlogs();
  document.getElementById('blogContent')?.addEventListener('input',e=>{const n=e.target.value.trim()?e.target.value.trim().split(/\s+/).filter(Boolean).length:0;document.getElementById('blogWordCount').textContent=`${n} words · Aim for at least 300 words.`;});
  document.getElementById('blogImage')?.addEventListener('change',async e=>{const status=document.getElementById('blogSubmitStatus'),preview=document.getElementById('blogImagePreview');try{parentBlogImageData=await phProcessBlogImage(e.target.files?.[0]);if(parentBlogImageData){preview.innerHTML=`<img src="${parentBlogImageData}" alt="Featured image preview" style="width:100%;max-height:180px;object-fit:cover;border-radius:10px">`;preview.style.display='block';status.textContent='Featured image ready.';}}catch(err){parentBlogImageData='';preview.style.display='none';status.textContent=err.message;}});
});
</script>
<script src="js/milestone-ai.js?v=5.5-product-visual"></script>
</body>
</html>
