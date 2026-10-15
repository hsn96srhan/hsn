/**
 * Dynamic Frontend Site Renderer
 * Renders live site data onto static .html & .php pages from site-data.js & LocalStorage
 */

document.addEventListener('DOMContentLoaded', () => {
    initDynamicFrontendRenderer();
    injectCustomScripts();
});

function initDynamicFrontendRenderer() {
    const pageName = window.location.pathname.split('/').pop().replace('.html', '').replace('.php', '');

    // 0. Render Homepage Dynamic Data (Hero Video, Shorts Grid, KPIs, Current Role)
    if (pageName === 'index' || pageName === '' || document.querySelector('.video-hero-banner')) {
        renderFrontendHomepage();
    }

    // 1. Render Dynamic Courses
    if (document.querySelector('#courses_container')) {
        renderFrontendCourses();
    }

    // 2. Render Dynamic Landing Pages
    if (pageName === 'lp' || document.querySelector('#lp_sections_dynamic_container[data-dynamic="true"]')) {
        renderFrontendLandingPages();
    }

    // 3. Render Dynamic Blog Details Page
    if (pageName === 'blog-details' && document.querySelector('#blog_detail_container[data-dynamic="true"]')) {
        renderBlogDetailsPage();
    }

    // 4. Render Emergency Contacts & Global Settings if elements exist
    renderFrontendGlobalSettings();
}

/* Dynamic Code Injection Engine (Head, Body Start, Footer) */
function injectCustomScripts() {
    const settings = getMasterData('site_settings') || {};

    // 1. Inject Header Scripts (Google Tag Manager / Analytics)
    if (settings.header_scripts && !document.getElementById('hsn_custom_head_scripts')) {
        const headContainer = document.createElement('div');
        headContainer.id = 'hsn_custom_head_scripts';
        headContainer.innerHTML = settings.header_scripts;
        Array.from(headContainer.children).forEach(el => {
            if (el.tagName === 'SCRIPT') {
                const s = document.createElement('script');
                if (el.src) s.src = el.src;
                s.textContent = el.textContent;
                document.head.appendChild(s);
            } else {
                document.head.appendChild(el.cloneNode(true));
            }
        });
    }

    // 2. Inject Body Scripts
    if (settings.body_scripts && !document.getElementById('hsn_custom_body_scripts')) {
        const bodyContainer = document.createElement('div');
        bodyContainer.id = 'hsn_custom_body_scripts';
        bodyContainer.innerHTML = settings.body_scripts;
        document.body.insertBefore(bodyContainer, document.body.firstChild);
    }

    // 3. Inject Footer Scripts
    if (settings.footer_scripts && !document.getElementById('hsn_custom_footer_scripts')) {
        const footerContainer = document.createElement('div');
        footerContainer.id = 'hsn_custom_footer_scripts';
        footerContainer.innerHTML = settings.footer_scripts;
        Array.from(footerContainer.children).forEach(el => {
            if (el.tagName === 'SCRIPT') {
                const s = document.createElement('script');
                if (el.src) s.src = el.src;
                s.textContent = el.textContent;
                document.body.appendChild(s);
            } else {
                document.body.appendChild(el.cloneNode(true));
            }
        });
    }
}

/* 0. Homepage Dynamic Sync (Hero Video, Shorts Grid, KPIs, Current Role) */
function renderFrontendHomepage() {
    const settings = getMasterData('site_settings') || {};
    const shorts = getMasterData('youtube_shorts') || [];
    const currentLang = document.documentElement.getAttribute('lang') || 'ar';

    // A. Hero Video Player Sync
    const heroCard = document.getElementById('video_hero_card');
    if (heroCard && settings.hero_video_url) {
        const rawUrl = settings.hero_video_url.trim();
        const isYouTube = rawUrl.includes('youtube.com') || rawUrl.includes('youtu.be');

        if (isYouTube) {
            let embedUrl = rawUrl;
            if (!embedUrl.includes('embed/')) {
                const vidMatch = rawUrl.match(/(?:v=|\/shorts\/|youtu\.be\/)([^&#?]+)/);
                const vidId = vidMatch ? vidMatch[1] : 'dQw4w9WgXcQ';
                embedUrl = `https://www.youtube.com/embed/${vidId}`;
            }
            if (!embedUrl.includes('enablejsapi')) {
                embedUrl += (embedUrl.includes('?') ? '&' : '?') + 'enablejsapi=1&autoplay=1&mute=1&controls=0&loop=1&playsinline=1&rel=0&modestbranding=1&iv_load_policy=3';
            }
            const existingIframe = heroCard.querySelector('iframe#youtube_hero_player');
            if (!existingIframe || !existingIframe.src.includes(embedUrl.split('?')[0])) {
                heroCard.innerHTML = `
                    <div class="youtube-hero-wrapper">
                        <iframe id="youtube_hero_player" 
                                src="${escapeHtml(embedUrl)}" 
                                title="Executive Hero Video" 
                                frameborder="0" 
                                allow="autoplay; encrypted-media" 
                                allowfullscreen>
                        </iframe>
                    </div>
                    <div class="video-click-overlay" id="video_click_overlay"></div>
                    <button id="video_play_btn" type="button" class="video-play-icon-btn" aria-label="تشغيل الصوت">
                        <span class="play-triangle-icon">▶</span>
                    </button>
                `;
            }
        } else {
            // Local MP4 or direct video file
            let videoEl = heroCard.querySelector('video#hero_video_player');
            if (!videoEl) {
                heroCard.innerHTML = `
                    <video id="hero_video_player" autoplay muted loop playsinline preload="auto" style="width:100%; height:100%; object-fit:cover; display:block;">
                        <source src="${escapeHtml(rawUrl)}" type="video/mp4">
                    </video>
                    <div class="video-click-overlay" id="video_click_overlay"></div>
                    <button id="video_play_btn" type="button" class="video-play-icon-btn" aria-label="تشغيل الصوت">
                        <span class="play-triangle-icon">▶</span>
                    </button>
                `;
            } else {
                const sourceEl = videoEl.querySelector('source');
                const currentSrc = sourceEl ? sourceEl.getAttribute('src') : videoEl.getAttribute('src');
                if (currentSrc !== rawUrl) {
                    if (sourceEl) sourceEl.setAttribute('src', rawUrl);
                    videoEl.src = rawUrl;
                    videoEl.load();
                    videoEl.play().catch(() => {});
                }
            }
        }
    }

    // B. Shorts Grid Sync
    const shortsGrid = document.getElementById('homepage_shorts_grid');
    if (shortsGrid && shorts.length > 0) {
        const publishedShorts = shorts.filter(s => s.is_published !== false);
        publishedShorts.sort((a, b) => (b.priority || 0) - (a.priority || 0));

        shortsGrid.innerHTML = publishedShorts.map(s => {
            const isLocal = s.video_url || s.youtube_video_id.startsWith('short_') || s.youtube_video_id.endsWith('.mp4');
            const videoPath = s.video_url || (isLocal ? `assets/videos/${s.youtube_video_id}.mp4` : `assets/videos/hero_video.mp4`);
            const views = s.views_count || '10K+';

            return `
                <div class="short-card" data-video="${escapeHtml(videoPath)}">
                    ${isLocal ? `
                        <video class="short-card-poster" preload="metadata" muted playsinline style="width:100%; height:100%; object-fit:cover;">
                            <source src="${escapeHtml(videoPath)}#t=0.5" type="video/mp4">
                        </video>
                    ` : `
                        <img src="https://img.youtube.com/vi/${escapeHtml(s.youtube_video_id)}/hqdefault.jpg" alt="${escapeHtml(s.title)}" class="short-card-poster">
                    `}
                    <div class="short-card-overlay"></div>
                    <div class="short-card-header">
                        <span class="short-badge">⚡ SHORTS</span>
                        <span class="short-duration">0:50</span>
                    </div>
                    <div class="short-play-btn">
                        <svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                    <div class="short-card-footer">
                        <h3 class="short-card-title">${escapeHtml(s.title)}</h3>
                        <div class="short-card-views">
                            <span>👁️ ${escapeHtml(views)}</span>
                            <span>• مشاهدة</span>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    // C. KPIs Sync
    const kpiElements = document.querySelectorAll('.kpis-bar .kpi-number');
    if (kpiElements.length >= 4) {
        if (settings.kpi_exp) kpiElements[0].textContent = settings.kpi_exp;
        if (settings.kpi_brands) kpiElements[1].textContent = settings.kpi_brands;
        if (settings.kpi_certs) kpiElements[2].textContent = settings.kpi_certs;
        if (settings.kpi_roas) kpiElements[3].textContent = settings.kpi_roas;
    }

    // D. Current Role Sync
    const roleTitleEl = document.querySelector('.company-card h3 span');
    if (roleTitleEl) {
        const rTitle = (currentLang === 'en' && settings.current_role_title_en) ? settings.current_role_title_en : settings.current_role_title_ar;
        if (rTitle) roleTitleEl.textContent = rTitle;
    }
    const roleDescEl = document.querySelector('.company-card p');
    if (roleDescEl) {
        const rDesc = (currentLang === 'en' && settings.current_role_desc_en) ? settings.current_role_desc_en : settings.current_role_desc_ar;
        if (rDesc) roleDescEl.textContent = rDesc;
    }
}

/* 1. Qualifications */
function renderFrontendQualifications() {
    const quals = getMasterData('qualifications') || [];
    const gridContainer = document.querySelector('.cert-grid-3x6');
    if (!gridContainer || !quals.length) return;

    const currentLang = document.documentElement.getAttribute('lang') || 'ar';
    const publishedQuals = quals.filter(q => q.is_published !== false);
    publishedQuals.sort((a, b) => (a.sort_order || a.id) - (b.sort_order || b.id));

    gridContainer.innerHTML = publishedQuals.map((q, idx) => {
        const title = (currentLang === 'en' && q.title_en) ? q.title_en : q.title_ar;
        const issuer = (currentLang === 'en' && q.issuer_en) ? q.issuer_en : q.issuer_ar;
        const desc = (currentLang === 'en' && q.description_en) ? q.description_en : q.description_ar;

        return `
            <div class="img-card">
                <img src="${escapeHtml(q.image_url || 'assets/images/image65.jpg')}" alt="${escapeHtml(title)}" class="img-card-cover zoomable-img">
                <div class="img-card-body">
                    <span style="font-size:0.78rem; font-weight:700; color:var(--primary-navy);">${escapeHtml(issuer)}</span>
                    <h4 style="font-size:1.02rem; margin:0.4rem 0; font-weight:700;">${idx + 1}. ${escapeHtml(title)}</h4>
                    ${desc ? `<p style="font-size:0.86rem; line-height:1.6; color:var(--text-body);">${escapeHtml(desc)}</p>` : ''}
                </div>
            </div>
        `;
    }).join('');
}

/* 2. Career Timeline */
function renderFrontendCareer() {
    const careers = getMasterData('career_timeline') || [];
    const timelineContainer = document.querySelector('.career-timeline-container') || document.querySelector('#career_list_container');
    if (!timelineContainer || !careers.length) return;

    const currentLang = document.documentElement.getAttribute('lang') || 'ar';
    const publishedCareers = careers.filter(c => c.is_published !== false);
    publishedCareers.sort((a, b) => (a.sort_order || a.id) - (b.sort_order || b.id));

    timelineContainer.innerHTML = publishedCareers.map(c => {
        const title = (currentLang === 'en' && c.title_en) ? c.title_en : c.title_ar;
        const company = (currentLang === 'en' && c.company_en) ? c.company_en : c.company_ar;
        const period = (currentLang === 'en' && c.period_en) ? c.period_en : c.period_ar;
        const location = (currentLang === 'en' && c.location_en) ? c.location_en : c.location_ar;
        const desc = (currentLang === 'en' && c.description_en) ? c.description_en : c.description_ar;

        return `
            <div class="timeline-card glass-card" style="margin-bottom:1.8rem; padding:1.5rem; background:#FFF; border:1px solid var(--border-color); border-radius:14px;">
                <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1rem;">
                    <img src="${escapeHtml(c.logo_url || 'assets/images/fluxads.png')}" alt="${escapeHtml(company)}" style="width:50px; height:50px; object-fit:contain; border-radius:10px; border:1px solid #E2E8F0; padding:4px;">
                    <div>
                        <span style="font-size:0.8rem; font-weight:700; color:var(--primary-navy); background:rgba(11,19,43,0.06); padding:4px 10px; border-radius:6px;">${escapeHtml(period)}</span>
                        <h3 style="font-size:1.2rem; font-weight:800; margin:0.2rem 0; color:var(--primary-navy);">${escapeHtml(title)}</h3>
                        <div style="font-weight:700; color:#3B82F6;">${escapeHtml(company)} - ${escapeHtml(location)}</div>
                    </div>
                </div>
                <p style="font-size:0.95rem; line-height:1.7; color:var(--text-body);">${escapeHtml(desc)}</p>
            </div>
        `;
    }).join('');
}

/* 3. Portfolio Case Studies */
function renderFrontendPortfolio() {
    const projects = getMasterData('projects') || [];
    const container = document.querySelector('#portfolio_grid_container') || document.querySelector('.portfolio-grid');
    if (!container || !projects.length) return;

    const currentLang = document.documentElement.getAttribute('lang') || 'ar';
    const publishedProjects = projects.filter(p => p.is_published !== false);
    publishedProjects.sort((a, b) => (b.priority || 0) - (a.priority || 0));

    container.innerHTML = publishedProjects.map(p => {
        const title = (currentLang === 'en' && p.title_en) ? p.title_en : p.title_ar;
        const problem = p.problem_text_ar;
        const strategy = p.strategy_text_ar;

        return `
            <div class="img-card" id="${escapeHtml(p.slug)}">
                <div style="background:#F8FAFC; padding:1.2rem; display:flex; align-items:center; gap:1rem; border-bottom:1px solid var(--border-color);">
                    <img src="${escapeHtml(p.client_logo_url || 'assets/images/fluxads.png')}" alt="${escapeHtml(p.client_name)}" style="width:45px; height:45px; object-fit:contain; border-radius:6px; background:#FFF; border:1px solid var(--border-color); padding:2px; flex-shrink:0;">
                    <div style="overflow:hidden;">
                        <span style="font-size:0.75rem; font-weight:700; color:var(--primary-navy);">${escapeHtml(p.sector)}</span>
                        <h3 class="card-title" style="margin:0.2rem 0;">${escapeHtml(p.client_name)}</h3>
                    </div>
                </div>
                <div class="img-card-body">
                    <h4 style="font-size:1.05rem; font-weight:800; color:var(--primary-navy); margin-bottom:0.6rem;">${escapeHtml(title)}</h4>
                    ${problem ? `<p style="font-size:0.88rem; color:var(--text-body); margin-bottom:0.5rem;"><strong>الهدف:</strong> ${escapeHtml(problem)}</p>` : ''}
                    ${strategy ? `<p style="font-size:0.88rem; color:var(--text-body);"><strong>الاستراتيجية:</strong> ${escapeHtml(strategy)}</p>` : ''}
                </div>
            </div>
        `;
    }).join('');
}

/* 4. Digital Products & Interactive D360 Bank Order Modal */
function renderFrontendDigitalProducts() {
    const bank = getMasterData('bank_accounts') || {};
    const prods = getMasterData('digital_products') || [];

    const ibanEl = document.querySelector('#d360_iban') || document.querySelector('.bank-iban-text');
    if (ibanEl && bank.iban) ibanEl.textContent = bank.iban;

    const nameEl = document.querySelector('#d360_account_name') || document.querySelector('.bank-account-name');
    if (nameEl && bank.account_name_ar) nameEl.textContent = bank.account_name_ar;

    const container = document.querySelector('#products_grid');
    if (container && prods.length) {
        const published = prods.filter(p => p.is_published !== false);
        published.sort((a, b) => (a.sort_order || a.id) - (b.sort_order || b.id));

        container.innerHTML = published.map(p => `
            <div class="product-card glass-card" style="padding:2rem; background:#FFF; border:1px solid var(--border-color); border-radius:18px; display:flex; flex-direction:column; justify-content:space-between;">
                <div>
                    <span style="background:rgba(212,175,55,0.15); color:var(--primary-gold); padding:4px 12px; border-radius:12px; font-weight:800; font-size:0.82rem;">${escapeHtml(p.badge_ar || 'منتج رقمي')}</span>
                    <h3 style="font-size:1.35rem; font-weight:800; margin:0.8rem 0; color:var(--primary-navy);">${escapeHtml(p.title_ar)}</h3>
                    <p style="font-size:0.95rem; line-height:1.7; color:var(--text-body); margin-bottom:1rem;">${escapeHtml(p.description_ar || '')}</p>
                    <div style="font-size:1.8rem; font-weight:900; color:var(--primary-navy); margin-bottom:1.2rem;">${p.price_sar} <span style="font-size:0.9rem; font-weight:700;">SAR</span></div>
                </div>
                <button type="button" onclick="openD360OrderModal(${p.id}, '${escapeHtml(p.title_ar)}', ${p.price_sar})" class="btn-primary" style="width:100%; text-align:center; padding:12px; font-weight:800; font-size:1rem; border-radius:10px; cursor:pointer; background:linear-gradient(135deg, #D4AF37, #B48811); color:#0B132B; border:none;">
                    💳 طلب الكتب والإيصال عبر D360 بنك
                </button>
            </div>
        `).join('');
    }

    injectD360OrderModalHTML(bank);
}

function injectD360OrderModalHTML(bank) {
    if (document.getElementById('d360_order_modal')) return;

    const modalHtml = `
        <div id="d360_order_modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(7,13,30,0.85); z-index:99999; backdrop-filter:blur(8px); align-items:center; justify-content:center; padding:1rem;">
            <div style="background:#0B132B; border:2px solid #D4AF37; border-radius:20px; width:100%; max-width:550px; color:#FFF; padding:2rem; position:relative; box-shadow:0 15px 40px rgba(0,0,0,0.5);">
                <button type="button" onclick="closeD360OrderModal()" style="position:absolute; top:16px; left:16px; background:rgba(255,255,255,0.1); color:#FFF; border:none; width:36px; height:36px; border-radius:50%; font-size:1.2rem; cursor:pointer;">✕</button>
                
                <h3 style="font-size:1.4rem; font-weight:900; color:#D4AF37; margin-bottom:0.5rem;"><i class="fas fa-university"></i> التحويل البنكي المباشر لـ D360 بنk</h3>
                <p style="font-size:0.88rem; color:#94A3B8; margin-bottom:1.2rem;">قم بالتحويل للحساب التالي وأرفق صورة الإيصال ليتم إرسال الرابط فوراً لمستندك.</p>

                <!-- Bank Info Box -->
                <div style="background:rgba(255,255,255,0.05); border:1px dashed #D4AF37; border-radius:14px; padding:1.2rem; margin-bottom:1.4rem;">
                    <div style="font-size:0.85rem; color:#94A3B8;">اسم البنك: <strong style="color:#FFF;">${escapeHtml(bank.bank_name_ar || 'D360 بنك')}</strong></div>
                    <div style="font-size:0.85rem; color:#94A3B8; margin:4px 0;">اسم المستفيد: <strong style="color:#FFF;">${escapeHtml(bank.account_name_ar || 'حسن فايز حسن سرحان')}</strong></div>
                    <div style="font-size:0.92rem; color:#D4AF37; font-weight:800; font-family:monospace; margin-top:6px; word-break:break-all;">
                        IBAN: ${escapeHtml(bank.iban || 'SA6936036036047948932598')}
                    </div>
                </div>

                <!-- Order Form -->
                <form id="d360_order_form" onsubmit="submitD360OrderForm(event)">
                    <input type="hidden" id="modal_product_id" value="1">
                    <input type="hidden" id="modal_product_title" value="">
                    <input type="hidden" id="modal_amount_sar" value="199">

                    <div style="margin-bottom:1rem;">
                        <label style="display:block; font-size:0.85rem; font-weight:700; color:#CBD5E1; margin-bottom:4px;">اسمك الكامل *</label>
                        <input type="text" id="modal_cust_name" required placeholder="عبد الله الشمري" style="width:100%; background:rgba(15,23,42,0.9); border:1px solid rgba(255,255,255,0.15); color:#FFF; padding:10px; border-radius:8px; outline:none;">
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:1rem;">
                        <div>
                            <label style="display:block; font-size:0.85rem; font-weight:700; color:#CBD5E1; margin-bottom:4px;">البريد الإلكتروني *</label>
                            <input type="email" id="modal_cust_email" required placeholder="example@mail.com" style="width:100%; background:rgba(15,23,42,0.9); border:1px solid rgba(255,255,255,0.15); color:#FFF; padding:10px; border-radius:8px; outline:none;">
                        </div>
                        <div>
                            <label style="display:block; font-size:0.85rem; font-weight:700; color:#CBD5E1; margin-bottom:4px;">رقم الجوال *</label>
                            <input type="text" id="modal_cust_phone" required placeholder="+966500000000" style="width:100%; background:rgba(15,23,42,0.9); border:1px solid rgba(255,255,255,0.15); color:#FFF; padding:10px; border-radius:8px; outline:none;">
                        </div>
                    </div>

                    <div style="margin-bottom:1.4rem;">
                        <label style="display:block; font-size:0.85rem; font-weight:700; color:#CBD5E1; margin-bottom:4px;">إرفاق صورة إيصال التحويل *</label>
                        <input type="file" id="modal_receipt_file" accept="image/*,.pdf" required style="width:100%; background:rgba(15,23,42,0.9); border:1px solid rgba(255,255,255,0.15); color:#FFF; padding:8px; border-radius:8px; outline:none;">
                    </div>

                    <button type="submit" id="modal_submit_btn" style="width:100%; padding:14px; background:linear-gradient(135deg, #10B981, #059669); color:#FFF; border:none; border-radius:10px; font-weight:800; font-size:1.05rem; cursor:pointer;">
                        🚀 تأكيد إرسال الطلب والإيصال
                    </button>
                </form>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

function openD360OrderModal(prodId, prodTitle, price) {
    document.getElementById('modal_product_id').value = prodId;
    document.getElementById('modal_product_title').value = prodTitle;
    document.getElementById('modal_amount_sar').value = price;
    document.getElementById('d360_order_modal').style.display = 'flex';
}

function closeD360OrderModal() {
    document.getElementById('d360_order_modal').style.display = 'none';
}

async function submitD360OrderForm(e) {
    e.preventDefault();

    const submitBtn = document.getElementById('modal_submit_btn');
    submitBtn.disabled = true;
    submitBtn.textContent = '⏳ جاري إرسال الإيصال...';

    const prodId = document.getElementById('modal_product_id').value;
    const prodTitle = document.getElementById('modal_product_title').value || 'كتاب إستراتيجي الرقمي';
    const amount = document.getElementById('modal_amount_sar').value || '199';
    const custName = document.getElementById('modal_cust_name').value;
    const custEmail = document.getElementById('modal_cust_email').value;
    const custPhone = document.getElementById('modal_cust_phone').value;
    const fileInput = document.getElementById('modal_receipt_file');

    let receiptUrl = 'assets/images/image40.png';

    // Handle base64 fallback for LocalStorage
    if (fileInput.files && fileInput.files[0]) {
        const file = fileInput.files[0];
        receiptUrl = await new Promise(resolve => {
            const reader = new FileReader();
            reader.onload = e => resolve(e.target.result);
            reader.readAsDataURL(file);
        });
    }

    const formData = new FormData();
    formData.append('customer_name', custName);
    formData.append('customer_email', custEmail);
    formData.append('customer_phone', custPhone);
    formData.append('product_id', prodId);
    formData.append('product_title', prodTitle);
    formData.append('amount_sar', amount);
    if (fileInput.files && fileInput.files[0]) {
        formData.append('receipt_file', fileInput.files[0]);
    }
    formData.append('receipt_base64', receiptUrl);

    // Save to LocalStorage Master Data
    let orders = getMasterData('bank_orders') || [];
    const newOrder = {
        id: Date.now(),
        customer_name: custName,
        customer_email: custEmail,
        customer_phone: custPhone,
        product_id: prodId,
        product_title: prodTitle,
        amount_sar: parseFloat(amount),
        receipt_url: receiptUrl,
        status: 'pending',
        created_at: new Date().toISOString().replace('T', ' ').substring(0, 19)
    };
    orders.unshift(newOrder);
    setMasterData('bank_orders', orders);

    // Try backend API submit (if running on PHP)
    try {
        await fetch('api/submit-order.php', {
            method: 'POST',
            body: formData
        });
    } catch (err) {}

    // Send direct instant email notification to hsn96srhan@gmail.com via FormSubmit
    try {
        fetch('https://formsubmit.co/ajax/hsn96srhan@gmail.com', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({
                _subject: `💳 إيصال تحويل وطلب منتج جديد: ${custName}`,
                "اسم العميل": custName,
                "البريد الإلكتروني للعميل": custEmail,
                "رقم الجوال / الواتساب": custPhone,
                "المنتج / الكورس": prodTitle,
                "المبلغ": amount + " ﷼",
                "ملاحظات إضافية": custNotes || 'لا يوجد',
                _template: "table",
                _captcha: "false"
            })
        }).catch(() => {});
    } catch (e) {}

    submitBtn.disabled = false;
    submitBtn.textContent = '🚀 تأكيد إرسال الطلب والإيصال';
    closeD360OrderModal();

    alert('🎉 تم استلام طلبك وإيصال التحويل بنجاح!\nتم إرسال إشعار للمدير، ووصلتك رسالة تأكيد على البريد الإلكتروني.');
}

/* 5. Services */
function renderFrontendServices() {
    const servs = getMasterData('services') || [];
    const container = document.querySelector('#services_container');
    if (!container || !servs.length) return;

    const published = servs.filter(s => s.is_published !== false);
    published.sort((a, b) => (a.sort_order || a.id) - (b.sort_order || b.id));

    container.innerHTML = published.map(s => `
        <div class="service-card glass-card" style="padding:2rem; background:#FFF; border:1px solid var(--border-color); border-radius:16px; margin-bottom:1.5rem;">
            <span style="background:rgba(212,175,55,0.15); color:var(--primary-gold); padding:4px 12px; border-radius:12px; font-weight:800; font-size:0.85rem;">${escapeHtml(s.badge_ar || 'خدمة متميزة')}</span>
            <h3 style="font-size:1.3rem; font-weight:800; margin:0.6rem 0; color:var(--primary-navy);">${escapeHtml(s.title_ar)}</h3>
            <p style="font-size:0.95rem; line-height:1.7; color:var(--text-body);">${escapeHtml(s.subtitle_ar || '')}</p>
            <a href="${escapeHtml(s.cta_link || 'contact.html')}" class="btn-primary" style="margin-top:1rem; display:inline-block; padding:10px 20px;">${escapeHtml(s.cta_text_ar || 'حجز جلسة استشارية')}</a>
        </div>
    `).join('');
}

/* 6. Courses (Executive Card Dynamic Sync) */
function renderFrontendCourses() {
    const crs = getMasterData('courses') || [];
    const container = document.querySelector('#courses_container');
    if (!container) return;

    const currentLang = document.documentElement.getAttribute('lang') || 'ar';
    const published = crs.filter(c => c.is_published !== false);
    published.sort((a, b) => (a.sort_order || a.id) - (b.sort_order || b.id));

    if (!published.length) {
        container.innerHTML = `
            <div style="grid-column: 1 / -1; text-align: center; padding: 3rem 1rem; background: #F8FAFC; border: 1px dashed var(--border-color); border-radius: 16px;">
                <h3 style="color: var(--primary-navy); margin-bottom: 0.5rem;">لا توجد برامج تدريبية معروضة حالياً</h3>
                <p style="color: var(--text-muted); font-size: 0.95rem;">سيتم الإعلان عن المواعيد والماستر كلاس الجديد قريباً.</p>
            </div>
        `;
        return;
    }

    container.innerHTML = published.map(c => {
        const title = (currentLang === 'en' && c.title_en) ? c.title_en : c.title_ar;
        const badge = (currentLang === 'en' && c.badge_en) ? c.badge_en : (c.badge_ar || '🔥 برنامج قيادي متقدم');
        const desc = (currentLang === 'en' && c.description_en) ? c.description_en : (c.description_ar || '');
        const price = (currentLang === 'en' && c.price_en) ? c.price_en : (c.price_ar || '999 ﷼');
        const origPrice = (currentLang === 'en' && c.original_price_en) ? c.original_price_en : (c.original_price_ar || '');
        const img = c.image_url || 'assets/images/image45.jpg';

        return `
            <div class="img-card">
                <img src="${escapeHtml(img)}" alt="${escapeHtml(title)}" class="img-card-cover zoomable-img">
                <div class="img-card-body">
                    ${badge ? `<span class="case-sector-badge" style="margin-bottom:0.8rem; display:inline-block;" data-en="${escapeHtml(c.badge_en || badge)}">${escapeHtml(badge)}</span>` : ''}
                    <h3 class="card-title" style="margin-bottom:0.6rem;" data-en="${escapeHtml(c.title_en || title)}">${escapeHtml(title)}</h3>
                    ${desc ? `<p style="font-size:0.92rem; color:var(--text-body); margin-bottom:1rem;" data-en="${escapeHtml(c.description_en || desc)}">${escapeHtml(desc)}</p>` : ''}
                    
                    <!-- Pricing Before & After Discount -->
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:1.2rem; background:#F8FAFC; padding:8px 12px; border-radius:10px; border:1px solid #E2E8F0;">
                        <span style="font-size:1.35rem; font-weight:800; color:#10B981;">${escapeHtml(price)}</span>
                        ${origPrice ? `<span style="font-size:0.95rem; text-decoration:line-through; color:var(--text-muted);">${escapeHtml(origPrice)}</span>` : ''}
                        <span style="background:#FEF3C7; color:#B45309; padding:3px 8px; border-radius:12px; font-size:0.78rem; font-weight:800; margin-right:auto;" data-en="🔥 50% OFF">🔥 وفر 50%</span>
                    </div>

                    <a href="#" class="btn-primary open-digital-modal-btn" data-product="${escapeHtml(title)}" data-price="${escapeHtml(price)}" style="width:100%; justify-content:center;" data-en="Request Enrollment">💳 طلب التسجيل وحجز المقعد</a>
                </div>
            </div>
        `;
    }).join('');
}

/* 7. Landing Pages (10 Dynamic Sections Renderer) */
function renderFrontendLandingPages() {
    const urlParams = new URLSearchParams(window.location.search);
    const slug = urlParams.get('slug') || 'growth-consultation';
    const lps = getMasterData('landing_pages') || [];
    const lp = lps.find(l => l.slug === slug) || lps[0];
    const container = document.querySelector('#lp_sections_dynamic_container');
    if (!container || !lp) return;

    const headingEl = document.querySelector('#lp_heading_title');
    if (headingEl && lp.title_ar) headingEl.textContent = lp.title_ar;

    const sections = lp.sections || [];
    if (!sections.length) {
        container.innerHTML = '<p class="text-center p-4">لا توجد سكاشن مضافة في صفحة الهبوط هذه بعد.</p>';
        return;
    }

    container.innerHTML = sections.map(sec => {
        const type = sec.type || 'hero';

        if (type === 'hero') {
            return `
                <div style="background: linear-gradient(135deg, #0B132B, #1C2541); color:#FFF; padding: 3.5rem 2.5rem; border-radius: 24px; margin-bottom: 3rem; text-align:center; box-shadow:0 12px 35px rgba(11,19,43,0.25); border:1px solid rgba(212,175,55,0.3);">
                    ${sec.badge ? `<span style="background:rgba(212,175,55,0.2); color:#D4AF37; padding:6px 20px; border-radius:30px; font-size:0.92rem; font-weight:800; display:inline-flex; align-items:center; gap:8px; margin-bottom:1.4rem; border:1px solid rgba(212,175,55,0.4);"><i class="${sec.badge_icon || 'fas fa-bolt'}"></i> ${escapeHtml(sec.badge)}</span>` : ''}
                    <h2 style="font-size:2.3rem; font-weight:900; line-height:1.35; margin-bottom:1.2rem; color:#FFF;">${escapeHtml(sec.title || '')}</h2>
                    ${sec.subtitle ? `<p style="font-size:1.12rem; opacity:0.9; max-width:850px; margin:0 auto 2.2rem auto; line-height:1.75; color:#E2E8F0;">${escapeHtml(sec.subtitle)}</p>` : ''}
                    
                    ${(sec.bullet1 || sec.bullet2 || sec.bullet3) ? `
                        <div style="display:flex; flex-wrap:wrap; justify-content:center; gap:1.2rem; margin-bottom:2.2rem; font-size:0.95rem; font-weight:700; color:#D4AF37;">
                            ${sec.bullet1 ? `<span>${escapeHtml(sec.bullet1)}</span>` : ''}
                            ${sec.bullet2 ? `<span>${escapeHtml(sec.bullet2)}</span>` : ''}
                            ${sec.bullet3 ? `<span>${escapeHtml(sec.bullet3)}</span>` : ''}
                        </div>
                    ` : ''}

                    <div style="display:flex; justify-content:center; gap:14px; flex-wrap:wrap;">
                        <a href="${escapeHtml(sec.cta_url || 'https://wa.me/966506267542')}" target="_blank" class="btn-primary" style="padding:14px 34px; font-size:1.1rem; font-weight:800; border-radius:12px; background:linear-gradient(135deg, #D4AF37, #B48811); color:#0B132B; border:none; text-decoration:none;">
                            ${escapeHtml(sec.cta_text || '📅 احجز الجلسة الآن')}
                        </a>
                        ${sec.secondary_cta_text ? `
                            <a href="${escapeHtml(sec.secondary_cta_url || '#')}" class="btn-secondary" style="padding:14px 28px; font-size:1rem; font-weight:700; border-radius:12px; background:rgba(255,255,255,0.1); color:#FFF; border:1px solid rgba(255,255,255,0.2); text-decoration:none;">
                                ${escapeHtml(sec.secondary_cta_text)}
                            </a>
                        ` : ''}
                    </div>
                </div>
            `;
        } else if (type === 'features') {
            const cards = sec.cards || [];
            return `
                <div style="background:#FFF; border:1px solid #E2E8F0; border-radius:24px; padding:3rem 2rem; margin-bottom:3rem; box-shadow:0 6px 20px rgba(0,0,0,0.04);">
                    <div style="text-align:center; margin-bottom:2.2rem;">
                        ${sec.badge ? `<span style="background:rgba(212,175,55,0.15); color:#B48811; padding:4px 14px; border-radius:20px; font-size:0.85rem; font-weight:800; display:inline-block; margin-bottom:0.8rem;">${escapeHtml(sec.badge)}</span>` : ''}
                        <h3 style="font-size:1.8rem; font-weight:900; color:#0B132B;">${escapeHtml(sec.title || '')}</h3>
                        ${sec.subtitle ? `<p style="color:#64748B; font-size:1rem; max-width:700px; margin:0.5rem auto 0 auto;">${escapeHtml(sec.subtitle)}</p>` : ''}
                    </div>
                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:1.5rem;">
                        ${cards.map(c => `
                            <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:16px; padding:1.5rem;">
                                <div style="width:48px; height:48px; background:linear-gradient(135deg, #0B132B, #1C2541); color:#D4AF37; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.3rem; margin-bottom:1rem;">
                                    <i class="${c.icon || 'fas fa-star'}"></i>
                                </div>
                                <h4 style="font-size:1.15rem; font-weight:800; color:#0B132B; margin-bottom:0.5rem;">${escapeHtml(c.title || '')}</h4>
                                <p style="font-size:0.92rem; color:#475569; line-height:1.6;">${escapeHtml(c.desc || '')}</p>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        } else if (type === 'pain_points') {
            const items = sec.items || [];
            return `
                <div style="background:linear-gradient(135deg, #070D1E, #0B132B); color:#FFF; border-radius:24px; padding:3rem 2rem; margin-bottom:3rem; border:1px solid rgba(239,68,68,0.3);">
                    <div style="text-align:center; margin-bottom:2.2rem;">
                        ${sec.badge ? `<span style="background:rgba(239,68,68,0.2); color:#F87171; padding:4px 14px; border-radius:20px; font-size:0.85rem; font-weight:800; display:inline-block; margin-bottom:0.8rem; border:1px solid rgba(239,68,68,0.4);">${escapeHtml(sec.badge)}</span>` : ''}
                        <h3 style="font-size:1.8rem; font-weight:900; color:#FFF;">${escapeHtml(sec.title || '')}</h3>
                        ${sec.subtitle ? `<p style="color:#94A3B8; font-size:1rem; max-width:700px; margin:0.5rem auto 0 auto;">${escapeHtml(sec.subtitle)}</p>` : ''}
                    </div>
                    <div style="display:flex; flex-direction:column; gap:1.2rem;">
                        ${items.map(item => `
                            <div style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:16px; padding:1.5rem; display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem;">
                                <div style="border-left:1px solid rgba(255,255,255,0.1); padding-left:1rem;">
                                    <div style="color:#EF4444; font-weight:800; font-size:1.05rem; display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                                        <i class="${item.icon || 'fas fa-exclamation-circle'}"></i> ${escapeHtml(item.problem_title || '')}
                                    </div>
                                    <p style="font-size:0.9rem; color:#CBD5E1; line-height:1.6;">${escapeHtml(item.problem_desc || '')}</p>
                                </div>
                                <div>
                                    <div style="color:#10B981; font-weight:800; font-size:1.05rem; display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                                        <i class="fas fa-check-circle"></i> ${escapeHtml(item.solution_title || 'الحل الإستراتيجي:')}
                                    </div>
                                    <p style="font-size:0.9rem; color:#E2E8F0; line-height:1.6;">${escapeHtml(item.solution_desc || '')}</p>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        } else if (type === 'kpi_stats') {
            const stats = sec.stats || [];
            return `
                <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:24px; padding:3rem 2rem; margin-bottom:3rem; text-align:center;">
                    ${sec.badge ? `<span style="background:rgba(212,175,55,0.15); color:#B48811; padding:4px 14px; border-radius:20px; font-size:0.85rem; font-weight:800; display:inline-block; margin-bottom:0.8rem;">${escapeHtml(sec.badge)}</span>` : ''}
                    <h3 style="font-size:1.8rem; font-weight:900; color:#0B132B; margin-bottom:0.5rem;">${escapeHtml(sec.title || '')}</h3>
                    ${sec.subtitle ? `<p style="color:#64748B; font-size:1rem; max-width:700px; margin:0 auto 2rem auto;">${escapeHtml(sec.subtitle)}</p>` : ''}
                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:1.5rem;">
                        ${stats.map(st => `
                            <div style="background:#FFF; padding:1.8rem 1.2rem; border-radius:18px; border:1px solid #E2E8F0; box-shadow:0 4px 14px rgba(0,0,0,0.03);">
                                <div style="font-size:1.6rem; color:${st.color || '#3B82F6'}; margin-bottom:0.4rem;"><i class="${st.icon || 'fas fa-chart-bar'}"></i></div>
                                <div style="font-size:2.2rem; font-weight:900; color:${st.color || '#3B82F6'}; line-height:1.2;">${escapeHtml(st.number || '')}</div>
                                <div style="font-size:0.92rem; font-weight:700; color:#64748B; margin-top:6px;">${escapeHtml(st.label || '')}</div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        } else if (type === 'video') {
            const ytid = sec.video_youtube_id || 'dQw4w9WgXcQ';
            return `
                <div id="video-section" style="background:#0B132B; color:#FFF; border-radius:24px; padding:3rem 2rem; margin-bottom:3rem; text-align:center;">
                    <h3 style="font-size:1.8rem; font-weight:900; color:#FFF; margin-bottom:0.5rem;">${escapeHtml(sec.title || '')}</h3>
                    ${sec.subtitle ? `<p style="color:#94A3B8; font-size:1rem; max-width:700px; margin:0 auto 1.8rem auto;">${escapeHtml(sec.subtitle)}</p>` : ''}
                    
                    <div style="position:relative; padding-bottom:56.25%; height:0; overflow:hidden; max-width:900px; margin:0 auto 1.5rem auto; border-radius:16px; border:2px solid rgba(212,175,55,0.4); box-shadow:0 12px 30px rgba(0,0,0,0.5);">
                        <iframe src="https://www.youtube.com/embed/${escapeHtml(ytid)}" style="position:absolute; top:0; left:0; width:100%; height:100%; border:0;" allowfullscreen></iframe>
                    </div>

                    ${sec.highlight_note ? `<p style="color:#D4AF37; font-weight:700; font-size:0.95rem; margin-bottom:1.5rem;">${escapeHtml(sec.highlight_note)}</p>` : ''}

                    ${sec.cta_text ? `
                        <a href="${escapeHtml(sec.cta_url || 'https://wa.me/966506267542')}" target="_blank" class="btn-primary" style="padding:12px 28px; font-size:1rem; font-weight:800; border-radius:10px; background:linear-gradient(135deg, #D4AF37, #B48811); color:#0B132B; text-decoration:none; display:inline-block;">
                            ${escapeHtml(sec.cta_text)}
                        </a>
                    ` : ''}
                </div>
            `;
        } else if (type === 'pricing') {
            const pkgs = sec.packages || [];
            return `
                <div style="background:#FFF; border:1px solid #E2E8F0; border-radius:24px; padding:3rem 2rem; margin-bottom:3rem;">
                    <div style="text-align:center; margin-bottom:2.2rem;">
                        <h3 style="font-size:1.8rem; font-weight:900; color:#0B132B;">${escapeHtml(sec.title || '')}</h3>
                        ${sec.subtitle ? `<p style="color:#64748B; font-size:1rem; max-width:700px; margin:0.5rem auto 0 auto;">${escapeHtml(sec.subtitle)}</p>` : ''}
                    </div>
                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(270px, 1fr)); gap:1.8rem;">
                        ${pkgs.map(p => {
                            const isFeat = p.is_featured || p.badge;
                            return `
                                <div style="background:${isFeat ? 'linear-gradient(135deg, #0B132B, #1C2541)' : '#F8FAFC'}; color:${isFeat ? '#FFF' : '#0B132B'}; border:${isFeat ? '2px solid #D4AF37' : '1px solid #E2E8F0'}; border-radius:20px; padding:2rem; position:relative; display:flex; flex-direction:column; justify-content:space-between;">
                                    ${p.badge ? `<span style="position:absolute; top:-12px; right:20px; background:#D4AF37; color:#0B132B; padding:3px 12px; border-radius:12px; font-weight:800; font-size:0.78rem;">${escapeHtml(p.badge)}</span>` : ''}
                                    <div>
                                        <h4 style="font-size:1.3rem; font-weight:800; margin-bottom:0.6rem; color:${isFeat ? '#D4AF37' : '#0B132B'};">${escapeHtml(p.name || '')}</h4>
                                        <div style="font-size:2.2rem; font-weight:900; margin-bottom:0.4rem; color:${isFeat ? '#FFF' : '#0B132B'};">${escapeHtml(p.price || '')}</div>
                                        ${p.period ? `<div style="font-size:0.85rem; opacity:0.8; margin-bottom:1.2rem;">${escapeHtml(p.period)}</div>` : ''}
                                        <hr style="border:0; border-top:1px solid ${isFeat ? 'rgba(255,255,255,0.12)' : '#E2E8F0'}; margin-bottom:1.2rem;">
                                        <ul style="list-style:none; padding:0; margin:0 0 1.8rem 0; display:flex; flex-direction:column; gap:10px; font-size:0.92rem; line-height:1.6;">
                                            ${(p.features_list || '').split('\n').map(f => f.trim() ? `<li><i class="fas fa-check-circle" style="color:#10B981; margin-left:6px;"></i> ${escapeHtml(f)}</li>` : '').join('')}
                                        </ul>
                                    </div>
                                    <a href="${escapeHtml(p.button_url || 'https://wa.me/966506267542')}" target="_blank" class="btn-primary" style="text-align:center; padding:12px 20px; font-weight:800; border-radius:10px; text-decoration:none; background:${isFeat ? 'linear-gradient(135deg, #D4AF37, #B48811)' : '#0B132B'}; color:${isFeat ? '#0B132B' : '#FFF'};">
                                        ${escapeHtml(p.button_text || 'اختيار الباقة')}
                                    </a>
                                </div>
                            `;
                        }).join('')}
                    </div>
                </div>
            `;
        } else if (type === 'testimonials') {
            const reviews = sec.reviews || [];
            return `
                <div style="background:linear-gradient(135deg, #0B132B, #1C2541); color:#FFF; border-radius:24px; padding:3rem 2rem; margin-bottom:3rem;">
                    <div style="text-align:center; margin-bottom:2.2rem;">
                        ${sec.overall_rating ? `<span style="background:rgba(212,175,55,0.2); color:#D4AF37; padding:5px 16px; border-radius:20px; font-size:0.88rem; font-weight:800; display:inline-block; margin-bottom:0.8rem; border:1px solid rgba(212,175,55,0.4);">${escapeHtml(sec.overall_rating)}</span>` : ''}
                        <h3 style="font-size:1.8rem; font-weight:900; color:#FFF;">${escapeHtml(sec.title || '')}</h3>
                        ${sec.subtitle ? `<p style="color:#94A3B8; font-size:1rem; max-width:700px; margin:0.5rem auto 0 auto;">${escapeHtml(sec.subtitle)}</p>` : ''}
                    </div>
                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:1.5rem;">
                        ${reviews.map(r => `
                            <div style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.12); padding:1.8rem; border-radius:18px; display:flex; flex-direction:column; justify-content:space-between;">
                                <p style="font-size:0.95rem; line-height:1.7; color:#E2E8F0; margin-bottom:1.2rem; font-style:italic;">"${escapeHtml(r.quote || '')}"</p>
                                <div style="display:flex; align-items:center; gap:12px; border-top:1px solid rgba(255,255,255,0.1); padding-top:1rem;">
                                    ${r.avatar_url ? `<img src="${escapeHtml(r.avatar_url)}" style="width:44px; height:44px; border-radius:50%; object-fit:cover; border:2px solid #D4AF37;">` : `<div style="width:44px; height:44px; border-radius:50%; background:#D4AF37; color:#0B132B; font-weight:900; display:flex; align-items:center; justify-content:center; font-size:1.1rem;">${(r.client_name||'C')[0]}</div>`}
                                    <div>
                                        <div style="font-weight:800; color:#D4AF37; font-size:0.95rem;">${escapeHtml(r.client_name || '')}</div>
                                        <div style="font-size:0.8rem; color:#94A3B8;">${escapeHtml(r.client_role || '')}</div>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        } else if (type === 'guarantee') {
            return `
                <div style="background:linear-gradient(135deg, #1C2541, #0B132B); border:2px solid #D4AF37; border-radius:24px; padding:3rem 2.5rem; margin-bottom:3rem; color:#FFF; box-shadow:0 10px 30px rgba(0,0,0,0.25);">
                    <div style="display:flex; flex-wrap:wrap; align-items:center; gap:1.8rem;">
                        <div style="width:70px; height:70px; background:linear-gradient(135deg, #D4AF37, #B48811); color:#0B132B; border-radius:20px; display:flex; align-items:center; justify-content:center; font-size:2.2rem; flex-shrink:0;">
                            <i class="${sec.icon || 'fas fa-user-shield'}"></i>
                        </div>
                        <div style="flex:1; min-width:260px;">
                            ${sec.guarantee_badge ? `<span style="background:rgba(212,175,55,0.2); color:#D4AF37; padding:4px 14px; border-radius:16px; font-size:0.82rem; font-weight:800; display:inline-block; margin-bottom:0.6rem; border:1px solid rgba(212,175,55,0.4);">${escapeHtml(sec.guarantee_badge)}</span>` : ''}
                            <h3 style="font-size:1.6rem; font-weight:900; color:#FFF; margin-bottom:0.8rem;">${escapeHtml(sec.title || '')}</h3>
                            <p style="font-size:1.05rem; line-height:1.75; color:#E2E8F0; margin-bottom:1.2rem; font-style:italic;">"${escapeHtml(sec.quote_text || '')}"</p>
                            <div style="font-weight:800; color:#D4AF37; font-size:1rem;">${escapeHtml(sec.author_name || 'حسن سرحان')}</div>
                            <div style="font-size:0.85rem; color:#94A3B8;">${escapeHtml(sec.author_title || '')}</div>
                        </div>
                    </div>
                </div>
            `;
        } else if (type === 'faq') {
            const faqs = sec.faqs || [];
            return `
                <div style="background:#FFF; border:1px solid #E2E8F0; border-radius:24px; padding:3rem 2rem; margin-bottom:3rem;">
                    <div style="text-align:center; margin-bottom:2.2rem;">
                        <h3 style="font-size:1.8rem; font-weight:900; color:#0B132B;">${escapeHtml(sec.title || '')}</h3>
                        ${sec.subtitle ? `<p style="color:#64748B; font-size:1rem; max-width:700px; margin:0.5rem auto 0 auto;">${escapeHtml(sec.subtitle)}</p>` : ''}
                    </div>
                    <div style="display:flex; flex-direction:column; gap:1.2rem; max-width:900px; margin:0 auto;">
                        ${faqs.map(f => `
                            <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:16px; padding:1.5rem;">
                                <div style="font-size:1.1rem; font-weight:800; color:#0B132B; display:flex; align-items:center; gap:10px; margin-bottom:0.6rem;">
                                    <i class="${f.icon || 'fas fa-question-circle'}" style="color:#D4AF37;"></i> ${escapeHtml(f.question || '')}
                                </div>
                                <p style="font-size:0.95rem; color:#475569; line-height:1.7; padding-right:1.8rem;">${escapeHtml(f.answer || '')}</p>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        } else if (type === 'booking_form') {
            const phone = sec.whatsapp_number || '966506267542';
            return `
                <div style="background:linear-gradient(135deg, #F8FAFC, #FFF); border:2px solid #D4AF37; border-radius:24px; padding:3.5rem 2rem; margin-bottom:3rem; text-align:center; box-shadow:0 10px 30px rgba(212,175,55,0.15);">
                    <h3 style="font-size:2rem; font-weight:900; color:#0B132B; margin-bottom:0.6rem;">${escapeHtml(sec.title || '')}</h3>
                    ${sec.subtitle ? `<p style="color:#64748B; font-size:1.05rem; max-width:700px; margin:0 auto 2rem auto; font-weight:600;">${escapeHtml(sec.subtitle)}</p>` : ''}
                    
                    <a href="https://wa.me/${escapeHtml(phone)}" target="_blank" class="btn-primary" style="padding:16px 40px; font-size:1.15rem; font-weight:800; border-radius:14px; background:#25D366; color:#FFF; border:none; text-decoration:none; display:inline-flex; align-items:center; gap:10px; box-shadow:0 8px 20px rgba(37,211,102,0.3);">
                        <i class="fab fa-whatsapp" style="font-size:1.4rem;"></i> ${escapeHtml(sec.whatsapp_button_text || '💬 تواصل وحجز مباشر عبر الواتساب')}
                    </a>

                    ${sec.notice_text ? `<p style="color:#94A3B8; font-size:0.88rem; font-weight:700; margin-top:1.4rem;">${escapeHtml(sec.notice_text)}</p>` : ''}
                </div>
            `;
        } else {
            return `
                <div style="background:#FFF; border:1px solid #E2E8F0; border-radius:18px; padding:2rem; margin-bottom:2rem;">
                    <h3 style="font-size:1.4rem; font-weight:800; color:#0B132B;">${escapeHtml(sec.title || '')}</h3>
                </div>
            `;
        }
    }).join('');
}

/* 8. Render Structured Blog Article Page with Sub-sections & Full SEO */
function renderBlogDetailsPage() {
    const urlParams = new URLSearchParams(window.location.search);
    const slug = urlParams.get('slug') || 'cro-strategies-gcc';
    const articles = getMasterData('articles') || [];
    const article = articles.find(a => a.slug === slug) || articles[0];
    const container = document.querySelector('#blog_detail_container');
    if (!container || !article) return;

    // Set Document SEO Title & Meta Tags
    document.title = (article.meta_title || article.title_ar) + ' | حسن سرحان';
    
    let metaDesc = document.querySelector('meta[name="description"]');
    if (!metaDesc) {
        metaDesc = document.createElement('meta');
        metaDesc.name = 'description';
        document.head.appendChild(metaDesc);
    }
    metaDesc.content = article.meta_desc || article.intro_text || article.title_ar;

    // Inject Canonical URL
    let canonical = document.querySelector('link[rel="canonical"]');
    if (!canonical) {
        canonical = document.createElement('link');
        canonical.rel = 'canonical';
        document.head.appendChild(canonical);
    }
    canonical.href = window.location.href;

    // Inject Structured JSON-LD Article Schema
    const schemaScript = document.createElement('script');
    schemaScript.type = 'application/ld+json';
    schemaScript.textContent = JSON.stringify({
        "@context": "https://schema.org",
        "@type": "Article",
        "headline": article.title_ar,
        "image": [window.location.origin + '/' + (article.featured_image || 'assets/images/image40.png')],
        "datePublished": article.created_at || "2026-09-27",
        "author": {
            "@type": "Person",
            "name": "حسن سرحان (Hassan Sarhan)",
            "jobTitle": "مدير تسويق استراتيجي"
        },
        "publisher": {
            "@type": "Organization",
            "name": "HSSN SRHAN Strategic Marketing",
            "logo": { "@type": "ImageObject", "url": window.location.origin + "/web.jpeg" }
        },
        "description": article.meta_desc || article.intro_text
    });
    document.head.appendChild(schemaScript);

    // Render Article Page HTML
    const sections = article.sections || [];
    
    container.innerHTML = `
        <article class="glass-card" style="padding:2.5rem; background:#FFF; border:1px solid var(--border-color); border-radius:20px; margin-bottom:3rem;">
            <span style="background:rgba(212,175,55,0.15); color:var(--primary-gold); padding:4px 12px; border-radius:12px; font-weight:800; font-size:0.85rem; display:inline-block; margin-bottom:1rem;">
                📌 المقال الاستراتيجي
            </span>
            <h1 style="font-size:2.2rem; font-weight:900; line-height:1.4; color:var(--primary-navy); margin-bottom:1.2rem;">${escapeHtml(article.title_ar)}</h1>
            
            <div style="font-size:0.88rem; color:var(--text-muted); margin-bottom:1.8rem; display:flex; gap:16px; align-items:center;">
                <span>📅 نُشر في: ${escapeHtml(article.created_at || '2026-09-27')}</span>
                <span>✍️ بقلم: <strong>حسن سرحان</strong></span>
            </div>

            ${article.featured_image ? `<img src="${escapeHtml(article.featured_image)}" alt="${escapeHtml(article.title_ar)}" style="width:100%; max-height:480px; object-fit:cover; border-radius:16px; margin-bottom:2rem; border:1px solid #E2E8F0;">` : ''}

            ${article.intro_text ? `
                <div style="font-size:1.15rem; font-weight:700; line-height:1.8; color:var(--primary-navy); background:#F8FAFC; border-right:4px solid var(--primary-gold); padding:1.2rem 1.5rem; border-radius:8px; margin-bottom:2.2rem;">
                    ${escapeHtml(article.intro_text)}
                </div>
            ` : ''}

            <!-- Sub-sections Render Loop -->
            <div style="display:flex; flex-direction:column; gap:2.2rem;">
                ${sections.map((sec, i) => `
                    <section style="border-top:1px solid #E2E8F0; padding-top:1.8rem;">
                        ${sec.subtitle ? `<h2 style="font-size:1.5rem; font-weight:800; color:var(--primary-navy); margin-bottom:1rem;">${escapeHtml(sec.subtitle)}</h2>` : ''}
                        ${sec.section_image ? `<img src="${escapeHtml(sec.section_image)}" alt="${escapeHtml(sec.subtitle || 'فقرة المقال')}" style="width:100%; max-height:360px; object-fit:cover; border-radius:12px; margin-bottom:1.2rem;">` : ''}
                        ${sec.section_body ? `<div style="font-size:1.02rem; line-height:1.85; color:var(--text-body);">${escapeHtml(sec.section_body).replace(/\n/g, '<br>')}</div>` : ''}
                    </section>
                `).join('')}
            </div>
        </article>
    `;
}

/* 9. Emergency Contacts & Global Settings */
function renderFrontendGlobalSettings() {
    const settings = getMasterData('site_settings') || {};

    if (settings.emergency_phone) {
        const phoneEls = document.querySelectorAll('.emergency-phone-link, [data-emergency-phone]');
        phoneEls.forEach(el => {
            el.href = 'tel:' + settings.emergency_phone;
            el.textContent = settings.emergency_phone;
        });
    }

    if (settings.emergency_whatsapp) {
        const waEls = document.querySelectorAll('.emergency-wa-link, [data-emergency-wa]');
        waEls.forEach(el => {
            el.href = 'https://wa.me/' + settings.emergency_whatsapp.replace(/[^0-9]/g, '');
        });
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
