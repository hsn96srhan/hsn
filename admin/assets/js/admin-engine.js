/**
 * HSN SRHAN Admin Dashboard Interactive Control Engine
 * Renders & manages full CRUD & ordering for all 18 Certs, 13 Case Studies, 5 Career items, 3 Products, D360 Bank Receipts & Sub-users
 */

document.addEventListener('DOMContentLoaded', function() {
    initAdminPageRenderers();
});

function initAdminPageRenderers() {
    const pageName = window.location.pathname.split('/').pop().replace('.html', '').replace('.php', '');

    switch (pageName) {
        case 'index':
        case '':
            renderDashboardOverview();
            break;
        case 'home':
            renderHomeControl();
            break;
        case 'qualifications':
            renderQualificationsControl();
            break;
        case 'career':
            renderCareerControl();
            break;
        case 'portfolio':
            renderPortfolioControl();
            break;
        case 'services':
            renderServicesControl();
            break;
        case 'courses':
            renderCoursesControl();
            break;
        case 'digital-products':
            renderDigitalProductsControl();
            break;
        case 'blog':
            renderBlogControl();
            break;
        case 'contact-settings':
            renderContactSettingsControl();
            break;
        case 'landing-pages':
            renderLandingPagesControl();
            break;
        case 'users':
            renderUsersControl();
            break;
    }
}

/* 1. Dashboard Overview */
function renderDashboardOverview() {
    const orders = getMasterData('bank_orders') || [];
    const projects = getMasterData('projects') || [];
    const quals = getMasterData('qualifications') || [];
    const shorts = getMasterData('youtube_shorts') || [];

    const pendingCount = orders.filter(o => o.status === 'pending').length;

    // Update KPI numbers if elements exist
    const kpiOrderEl = document.querySelector('#stat_pending_orders');
    if (kpiOrderEl) kpiOrderEl.textContent = pendingCount;

    const kpiPrjEl = document.querySelector('#stat_projects');
    if (kpiPrjEl) kpiPrjEl.textContent = projects.length;

    const kpiQualEl = document.querySelector('#stat_quals');
    if (kpiQualEl) kpiQualEl.textContent = quals.length;

    const kpiShortEl = document.querySelector('#stat_shorts');
    if (kpiShortEl) kpiShortEl.textContent = shorts.length;

    // Render Recent Orders Table
    const tableBody = document.querySelector('#overview_orders_table tbody');
    if (tableBody) {
        tableBody.innerHTML = orders.slice(0, 5).map(o => `
            <tr>
                <td><strong>${o.order_number || '#' + o.id}</strong></td>
                <td>${escapeHtml(o.customer_name)}</td>
                <td>
                    <div>${escapeHtml(o.customer_phone)}</div>
                    <div style="font-size:0.78rem; color:var(--admin-text-muted);">${escapeHtml(o.customer_email)}</div>
                </td>
                <td><span style="color:var(--admin-primary); font-weight:700;">${escapeHtml(o.product_title)}</span></td>
                <td><strong>${o.amount_sar} ﷼</strong></td>
                <td>${renderOrderStatusBadge(o.status)}</td>
                <td>${o.receipt_url ? `<a href="../${o.receipt_url}" target="_blank" style="color:#38BDF8; font-weight:700;">عرض الإيصال 📎</a>` : '<span style="color:var(--admin-text-muted);">لا يوجد</span>'}</td>
                <td style="font-size:0.8rem; color:var(--admin-text-muted);">${o.created_at || 'الآن'}</td>
            </tr>
        `).join('') || '<tr><td colspan="8" style="text-align:center; padding:2rem; color:var(--admin-text-muted);">لا توجد طلبات بنكية مسجلة بعد.</td></tr>';
    }
}

/* 2. Home & Shorts Control */
function renderHomeControl() {
    const settings = getMasterData('site_settings') || {};
    const shorts = getMasterData('youtube_shorts') || [];

    // Populate inputs if they exist
    setInputValue('hero_badge_ar', settings.hero_badge_ar);
    setInputValue('hero_badge_en', settings.hero_badge_en);
    setInputValue('hero_name_ar', settings.hero_name_ar);
    setInputValue('hero_name_en', settings.hero_name_en);
    setInputValue('hero_subtitle_ar', settings.hero_subtitle_ar);
    setInputValue('hero_subtitle_en', settings.hero_subtitle_en);
    setInputValue('hero_video_url', settings.hero_video_url);

    setInputValue('kpi_exp', settings.kpi_exp);
    setInputValue('kpi_brands', settings.kpi_brands);
    setInputValue('kpi_certs', settings.kpi_certs);
    setInputValue('kpi_roas', settings.kpi_roas);

    setInputValue('current_role_badge_ar', settings.current_role_badge_ar);
    setInputValue('current_role_badge_en', settings.current_role_badge_en);
    setInputValue('current_role_title_ar', settings.current_role_title_ar);
    setInputValue('current_role_title_en', settings.current_role_title_en);
    setInputValue('current_role_desc_ar', settings.current_role_desc_ar);
    setInputValue('current_role_desc_en', settings.current_role_desc_en);

    setInputValue('philosophy_quote_ar', settings.philosophy_quote_ar);
    setInputValue('philosophy_quote_en', settings.philosophy_quote_en);
    setInputValue('philosophy_desc_ar', settings.philosophy_desc_ar);
    setInputValue('philosophy_desc_en', settings.philosophy_desc_en);

    // Shorts Table Render
    const shortsBody = document.querySelector('#shorts_table tbody');
    if (shortsBody) {
        shortsBody.innerHTML = shorts.map(s => `
            <tr>
                <td><strong>#${s.id}</strong></td>
                <td><strong>${escapeHtml(s.title)}</strong></td>
                <td><code>${escapeHtml(s.youtube_video_id)}</code></td>
                <td>${escapeHtml(s.views_count || '10K+')}</td>
                <td>${escapeHtml(s.likes_count || '1K+')}</td>
                <td>${s.priority || 0}</td>
                <td>
                    <button class="btn-danger-action" onclick="deleteShortItem(${s.id})">حذف 🗑️</button>
                </td>
            </tr>
        `).join('');
    }
}

/* 3. Qualifications Control (All 18 Certs) */
function renderQualificationsControl() {
    const quals = getMasterData('qualifications') || [];
    const tableBody = document.querySelector('#quals_table tbody');
    if (tableBody) {
        tableBody.innerHTML = quals.map(q => `
            <tr>
                <td>
                    <strong style="color:var(--admin-primary);">#${q.id}</strong>
                    <div style="font-size:0.75rem; color:var(--admin-text-muted);">ترتيب: ${q.sort_order || q.id}</div>
                </td>
                <td>
                    <img src="${fixAdminImgPath(q.image_url)}" alt="" style="width:60px; height:42px; object-fit:contain; background:#FFF; border-radius:6px; padding:2px;">
                </td>
                <td>
                    <strong>${escapeHtml(q.title_ar)}</strong>
                    <div style="font-size:0.8rem; color:var(--admin-text-muted);">${escapeHtml(q.title_en || '')}</div>
                </td>
                <td>
                    <span style="background:rgba(255,255,255,0.08); padding:4px 10px; border-radius:12px; font-size:0.82rem; font-weight:700;">
                        ${escapeHtml(q.issuer_ar)}
                    </span>
                </td>
                <td>${q.is_published ? '<span style="color:#4ADE80; font-weight:700; font-size:0.82rem;">منشور 🟢</span>' : '<span style="color:#F87171; font-weight:700; font-size:0.82rem;">مخفي 🔴</span>'}</td>
                <td>
                    <div style="display:flex; gap:6px;">
                        <button class="btn-secondary-action" style="padding:4px 10px; font-size:0.8rem;" onclick="editQualItem(${q.id})">تعديل ✏️</button>
                        <button class="btn-danger-action" onclick="deleteQualItem(${q.id})">حذف 🗑️</button>
                    </div>
                </td>
            </tr>
        `).join('');
    }
}

/* 4. Career Timeline Control (All 5 Companies) */
function renderCareerControl() {
    const careers = getMasterData('career_timeline') || [];
    const tableBody = document.querySelector('#career_table tbody');
    if (tableBody) {
        tableBody.innerHTML = careers.map(c => `
            <tr>
                <td>
                    <strong style="color:var(--admin-primary);">#${c.id}</strong>
                    <div style="font-size:0.75rem; color:var(--admin-text-muted);">ترتيب: ${c.sort_order || c.id}</div>
                </td>
                <td>
                    <img src="${fixAdminImgPath(c.logo_url)}" alt="" style="width:42px; height:42px; object-fit:contain; background:#FFF; border-radius:8px; padding:2px;">
                </td>
                <td>
                    <strong>${escapeHtml(c.company_ar)}</strong>
                    <div style="font-size:0.8rem; color:var(--admin-text-muted);">${escapeHtml(c.company_en || '')}</div>
                </td>
                <td><span style="color:var(--admin-primary); font-weight:700;">${escapeHtml(c.title_ar)}</span></td>
                <td>${escapeHtml(c.period_ar)}</td>
                <td>${c.is_published ? '<span style="color:#4ADE80; font-weight:700; font-size:0.82rem;">ظاهر 🟢</span>' : '<span style="color:#F87171; font-weight:700; font-size:0.82rem;">مخفي 🔴</span>'}</td>
                <td>
                    <div style="display:flex; gap:6px;">
                        <button class="btn-secondary-action" style="padding:4px 10px; font-size:0.8rem;" onclick="editCareerItem(${c.id})">تعديل ✏️</button>
                        <button class="btn-danger-action" onclick="deleteCareerItem(${c.id})">حذف 🗑️</button>
                    </div>
                </td>
            </tr>
        `).join('');
    }
}

/* 5. Portfolio Control (All 13 Case Studies) */
function renderPortfolioControl() {
    const projects = getMasterData('projects') || [];
    const tableBody = document.querySelector('#projects_table tbody');
    if (tableBody) {
        tableBody.innerHTML = projects.map(p => `
            <tr>
                <td>
                    <strong style="color:var(--admin-primary);">#${p.id}</strong>
                    <div style="font-size:0.75rem; color:var(--admin-text-muted);">أولوية: ${p.priority || 0}</div>
                </td>
                <td>
                    <strong>${escapeHtml(p.client_name)}</strong>
                    <div style="font-size:0.75rem; color:var(--admin-text-muted);">Slug: ${escapeHtml(p.slug)}</div>
                </td>
                <td><strong>${escapeHtml(p.title_ar)}</strong></td>
                <td>
                    <span style="background:rgba(56, 189, 248, 0.15); color:#38BDF8; padding:4px 10px; border-radius:12px; font-weight:700; font-size:0.8rem;">
                        ${escapeHtml(p.sector)}
                    </span>
                </td>
                <td><code style="font-size:0.78rem; color:#4ADE80;">${escapeHtml(typeof p.results_json === 'string' ? p.results_json : JSON.stringify(p.results_json))}</code></td>
                <td>${p.is_published ? '<span style="color:#4ADE80; font-weight:700; font-size:0.82rem;">منشور 🟢</span>' : '<span style="color:#F87171; font-weight:700; font-size:0.82rem;">مخفي 🔴</span>'}</td>
                <td>
                    <div style="display:flex; gap:6px;">
                        <button class="btn-secondary-action" style="padding:4px 10px; font-size:0.8rem;" onclick="editProjectItem(${p.id})">تعديل ✏️</button>
                        <button class="btn-danger-action" onclick="deleteProjectItem(${p.id})">حذف 🗑️</button>
                    </div>
                </td>
            </tr>
        `).join('');
    }
}

/* 6. Digital Products & D360 Bank Control */
function renderDigitalProductsControl() {
    const bank = getMasterData('bank_accounts') || {};
    const prods = getMasterData('digital_products') || [];
    const orders = getMasterData('bank_orders') || [];

    setInputValue('bank_name_ar', bank.bank_name_ar);
    setInputValue('bank_name_en', bank.bank_name_en);
    setInputValue('account_name_ar', bank.account_name_ar);
    setInputValue('account_name_en', bank.account_name_en);
    setInputValue('iban', bank.iban);
    setInputValue('swift', bank.swift);
    setInputValue('transfer_note_ar', bank.transfer_note_ar);
    setInputValue('transfer_note_en', bank.transfer_note_en);

    // Products Table
    const prodBody = document.querySelector('#products_table tbody');
    if (prodBody) {
        prodBody.innerHTML = prods.map(p => `
            <tr>
                <td>
                    <strong style="color:var(--admin-primary);">#${p.id}</strong>
                    <div style="font-size:0.75rem; color:var(--admin-text-muted);">ترتيب: ${p.sort_order || p.id}</div>
                </td>
                <td>
                    <strong>${escapeHtml(p.title_ar)}</strong>
                    <div style="font-size:0.8rem; color:var(--admin-text-muted);">${escapeHtml(p.title_en || '')}</div>
                </td>
                <td>
                    <strong style="color:#4ADE80;">${p.price_sar} ﷼</strong>
                    <span style="font-size:0.78rem; text-decoration:line-through; color:var(--admin-text-muted);">${p.original_price_sar || ''} ﷼</span>
                </td>
                <td>
                    <span style="background:rgba(212, 175, 55, 0.15); color:var(--admin-primary); padding:4px 10px; border-radius:12px; font-weight:700; font-size:0.8rem;">
                        ${escapeHtml(p.badge_ar || '')}
                    </span>
                </td>
                <td>${p.is_published ? '<span style="color:#4ADE80; font-weight:700; font-size:0.82rem;">منشور 🟢</span>' : '<span style="color:#F87171; font-weight:700; font-size:0.82rem;">مخفي 🔴</span>'}</td>
                <td>
                    <div style="display:flex; gap:6px;">
                        <button class="btn-secondary-action" style="padding:4px 10px; font-size:0.8rem;" onclick="editProductItem(${p.id})">تعديل ✏️</button>
                        <button class="btn-danger-action" onclick="deleteProductItem(${p.id})">حذف 🗑️</button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    // Bank Orders Hub Table
    const ordersBody = document.querySelector('#orders_table tbody');
    if (ordersBody) {
        ordersBody.innerHTML = orders.map(o => `
            <tr>
                <td><strong>${escapeHtml(o.order_number || '#' + o.id)}</strong></td>
                <td><strong>${escapeHtml(o.customer_name)}</strong></td>
                <td>
                    <div>📱 ${escapeHtml(o.customer_phone)}</div>
                    <div style="font-size:0.78rem; color:var(--admin-text-muted);">✉️ ${escapeHtml(o.customer_email)}</div>
                </td>
                <td><span style="color:var(--admin-primary); font-weight:700;">${escapeHtml(o.product_title)}</span></td>
                <td><strong>${o.amount_sar} ﷼</strong></td>
                <td>${o.receipt_url ? `<a href="../${o.receipt_url}" target="_blank" class="btn-secondary-action" style="padding:4px 8px; font-size:0.78rem;">عرض الإيصال 📎</a>` : '<span style="color:var(--admin-text-muted);">بدون إيصال</span>'}</td>
                <td>
                    <select class="form-select" style="padding:4px 8px; font-size:0.8rem;" onchange="updateOrderStatus(${o.id}, this.value)">
                        <option value="pending" ${o.status === 'pending' ? 'selected' : ''}>⏳ بانتظار التأكيد</option>
                        <option value="approved" ${o.status === 'approved' ? 'selected' : ''}>✅ مؤكد ومكتمل</option>
                        <option value="rejected" ${o.status === 'rejected' ? 'selected' : ''}>❌ مرفوض</option>
                    </select>
                </td>
            </tr>
        `).join('') || '<tr><td colspan="7" style="text-align:center; padding:2rem; color:var(--admin-text-muted);">لا توجد طلبات تحويل مسجلة بعد.</td></tr>';
    }
}

function editProductItem(id) {
    const prods = getMasterData('digital_products') || [];
    const item = prods.find(p => p.id === id);
    if (!item) return;

    setInputValue('prod_id', item.id);
    setInputValue('prod_title_ar', item.title_ar);
    setInputValue('prod_title_en', item.title_en);
    setInputValue('prod_price_sar', item.price_sar);
    setInputValue('prod_original_price_sar', item.original_price_sar);
    setInputValue('prod_badge_ar', item.badge_ar);
    setInputValue('prod_badge_en', item.badge_en);
    setInputValue('prod_description_ar', item.description_ar);
    setInputValue('prod_description_en', item.description_en);
    setInputValue('prod_sort_order', item.sort_order || item.id);
    setInputValue('prod_is_published', item.is_published !== false ? 'true' : 'false');

    const titleEl = document.getElementById('product_form_title');
    const btnEl = document.getElementById('prod_submit_btn');
    if (titleEl) titleEl.innerHTML = '<i class="fas fa-edit"></i> تعديل المنتج الرقمي: ' + escapeHtml(item.title_ar || '');
    if (btnEl) btnEl.innerHTML = '💾 حفظ التعديلات على المنتج';

    const card = document.getElementById('product_form_card');
    if (card) card.scrollIntoView({ behavior: 'smooth' });
}

function resetProductForm() {
    setInputValue('prod_id', '0');
    const form = document.getElementById('product_form');
    if (form) form.reset();
    const titleEl = document.getElementById('product_form_title');
    const btnEl = document.getElementById('prod_submit_btn');
    if (titleEl) titleEl.innerHTML = '<i class="fas fa-shopping-bag"></i> إضافة منتج رقمي جديد';
    if (btnEl) btnEl.innerHTML = '➕ حفظ وإضافة المنتج الرقمي';
}

function handleProductSubmit(e) {
    e.preventDefault();
    let prods = getMasterData('digital_products') || [];
    const id = parseInt(document.getElementById('prod_id')?.value) || 0;

    const prodData = {
        id: id > 0 ? id : Date.now(),
        title_ar: document.getElementById('prod_title_ar')?.value || '',
        title_en: document.getElementById('prod_title_en')?.value || '',
        price_sar: parseFloat(document.getElementById('prod_price_sar')?.value) || 0,
        original_price_sar: parseFloat(document.getElementById('prod_original_price_sar')?.value) || 0,
        badge_ar: document.getElementById('prod_badge_ar')?.value || '',
        badge_en: document.getElementById('prod_badge_en')?.value || '',
        description_ar: document.getElementById('prod_description_ar')?.value || '',
        description_en: document.getElementById('prod_description_en')?.value || '',
        sort_order: parseInt(document.getElementById('prod_sort_order')?.value) || 1,
        is_published: document.getElementById('prod_is_published')?.value === 'true'
    };

    if (id > 0) {
        const idx = prods.findIndex(p => p.id === id);
        if (idx !== -1) {
            prods[idx] = { ...prods[idx], ...prodData };
        } else {
            prods.push(prodData);
        }
    } else {
        prods.push(prodData);
    }

    setMasterData('digital_products', prods);
    renderDigitalProductsControl();
    resetProductForm();
    alert('تم حفظ بيانات المنتج الرقمي بنجاح! 💾');
}

/* 7. Services Control */
function renderServicesControl() {
    const servs = getMasterData('services') || [];
    const tableBody = document.querySelector('#services_table tbody');
    if (tableBody) {
        tableBody.innerHTML = servs.map(s => `
            <tr>
                <td>
                    <strong style="color:var(--admin-primary);">#${s.id}</strong>
                    <div style="font-size:0.75rem; color:var(--admin-text-muted);">ترتيب: ${s.sort_order || s.id}</div>
                </td>
                <td>
                    <strong>${escapeHtml(s.title_ar)}</strong>
                    <div style="font-size:0.8rem; color:var(--admin-text-muted);">${escapeHtml(s.title_en || '')}</div>
                </td>
                <td>
                    <span style="background:rgba(212, 175, 55, 0.15); color:var(--admin-primary); padding:4px 10px; border-radius:12px; font-weight:700; font-size:0.8rem;">
                        ${escapeHtml(s.badge_ar || '')}
                    </span>
                </td>
                <td><strong>${escapeHtml(s.price_text_ar || '')}</strong></td>
                <td>${s.is_published ? '<span style="color:#4ADE80; font-weight:700; font-size:0.82rem;">منشور 🟢</span>' : '<span style="color:#F87171; font-weight:700; font-size:0.82rem;">مخفي 🔴</span>'}</td>
                <td>
                    <div style="display:flex; gap:6px;">
                        <button class="btn-secondary-action" style="padding:4px 10px; font-size:0.8rem;" onclick="editServiceItem(${s.id})">تعديل ✏️</button>
                        <button class="btn-danger-action" onclick="deleteServiceItem(${s.id})">حذف 🗑️</button>
                    </div>
                </td>
            </tr>
        `).join('');
    }
}

/* 8. Courses Control */
function renderCoursesControl() {
    const crs = getMasterData('courses') || [];
    const tableBody = document.querySelector('#courses_table tbody');
    if (tableBody) {
        tableBody.innerHTML = crs.map(c => `
            <tr>
                <td>
                    <strong style="color:var(--admin-primary);">#${c.id}</strong>
                    <div style="font-size:0.75rem; color:var(--admin-text-muted);">ترتيب: ${c.sort_order || c.id}</div>
                </td>
                <td>
                    <strong>${escapeHtml(c.title_ar)}</strong>
                    <div style="font-size:0.8rem; color:var(--admin-text-muted);">${escapeHtml(c.title_en || '')}</div>
                </td>
                <td>${escapeHtml(c.duration_ar || '')}</td>
                <td>
                    ${c.original_price_ar ? `<span style="text-decoration: line-through; color: #94A3B8; font-size: 0.8rem; margin-left: 5px;">${escapeHtml(c.original_price_ar)}</span>` : ''}
                    <strong style="color:#4ADE80;">${escapeHtml(c.price_ar || '')}</strong>
                </td>
                <td>${c.is_published ? '<span style="color:#4ADE80; font-weight:700; font-size:0.82rem;">منشور 🟢</span>' : '<span style="color:#F87171; font-weight:700; font-size:0.82rem;">مخفي 🔴</span>'}</td>
                <td>
                    <div style="display:flex; gap:6px;">
                        <button class="btn-secondary-action" style="padding:4px 10px; font-size:0.8rem;" onclick="editCourseItem(${c.id})">تعديل ✏️</button>
                        <button class="btn-danger-action" onclick="deleteCourseItem(${c.id})">حذف 🗑️</button>
                    </div>
                </td>
            </tr>
        `).join('');
    }
}

/* 9. Contact Settings Control */
function renderContactSettingsControl() {
    const settings = getMasterData('site_settings') || {};
    const bookings = getMasterData('bookings') || [];

    setInputValue('emergency_phone', settings.emergency_phone);
    setInputValue('emergency_whatsapp', settings.emergency_whatsapp);
    setInputValue('max_daily_bookings', settings.max_daily_bookings);
    setInputValue('emergency_title_ar', settings.emergency_title_ar);
    setInputValue('emergency_title_en', settings.emergency_title_en);

    const bookingsBody = document.querySelector('#bookings_table tbody');
    if (bookingsBody) {
        bookingsBody.innerHTML = bookings.map(b => `
            <tr>
                <td><strong>#${b.id}</strong></td>
                <td><strong>${escapeHtml(b.full_name)}</strong></td>
                <td>${escapeHtml(b.company_name || 'فردي / غير محدد')}</td>
                <td>
                    <div>📱 ${escapeHtml(b.phone)}</div>
                    <div style="font-size:0.78rem; color:var(--admin-text-muted);">✉️ ${escapeHtml(b.email)}</div>
                </td>
                <td><strong>${escapeHtml(b.booking_date)}</strong> <span style="font-size:0.75rem; color:var(--admin-primary);">${escapeHtml(b.time_slot || '')}</span></td>
                <td>${escapeHtml(b.notes || 'لا يوجد')}</td>
                <td>
                    <select class="form-select" style="padding:4px 8px; font-size:0.8rem;" onchange="updateBookingStatus(${b.id}, this.value)">
                        <option value="pending" ${b.status === 'pending' ? 'selected' : ''}>⏳ قيد الانتظار</option>
                        <option value="confirmed" ${b.status === 'confirmed' ? 'selected' : ''}>✅ تم التأكيد والمقابلة</option>
                        <option value="cancelled" ${b.status === 'cancelled' ? 'selected' : ''}>❌ ملغي</option>
                    </select>
                </td>
            </tr>
        `).join('') || '<tr><td colspan="7" style="text-align:center; padding:2rem; color:var(--admin-text-muted);">لا توجد طلبات استشارات محجوزة بعد.</td></tr>';
    }
}

/* 10. Sub-Users & Assistants Control */
function renderUsersControl() {
    const users = getMasterData('users') || [];
    const usersBody = document.querySelector('#users_table tbody');
    if (usersBody) {
        usersBody.innerHTML = users.map(u => `
            <tr>
                <td><strong>#${u.id}</strong></td>
                <td><strong>${escapeHtml(u.username)}</strong></td>
                <td>${escapeHtml(u.email)}</td>
                <td><span class="badge-role" style="font-size:0.78rem;">${escapeHtml(u.role)}</span></td>
                <td>${(u.status || 'active') === 'active' ? '<span style="color:#4ADE80; font-weight:700; font-size:0.82rem;">مفعل 🟢</span>' : '<span style="color:#F87171; font-weight:700; font-size:0.82rem;">معطل 🔴</span>'}</td>
                <td>
                    <div style="display:flex; gap:6px;">
                        <button class="btn-secondary-action" style="padding:4px 10px; font-size:0.8rem;" onclick="toggleUserStatus(${u.id})">
                            ${(u.status || 'active') === 'active' ? 'تعطيل ⏸️' : 'تفعيل ▶️'}
                        </button>
                        <button class="btn-danger-action" onclick="deleteUserItem(${u.id})">حذف 🗑️</button>
                    </div>
                </td>
            </tr>
        `).join('');
    }
}

/* Helper Utilities */
function setInputValue(id, val) {
    const el = document.getElementById(id) || document.querySelector(`[name="${id}"]`);
    if (el && val !== undefined) {
        el.value = val;
    }
}

function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function renderOrderStatusBadge(status) {
    if (status === 'approved') return '<span style="background:rgba(34, 197, 94, 0.2); color:#4ADE80; padding:4px 10px; border-radius:12px; font-weight:700; font-size:0.8rem;">مؤكد ومكتمل ✅</span>';
    if (status === 'rejected') return '<span style="background:rgba(239, 68, 68, 0.2); color:#F87171; padding:4px 10px; border-radius:12px; font-weight:700; font-size:0.8rem;">مرفوض ❌</span>';
    return '<span style="background:rgba(245, 158, 11, 0.2); color:#FBBF24; padding:4px 10px; border-radius:12px; font-weight:700; font-size:0.8rem;">بانتظار التأكيد ⏳</span>';
}

function fixAdminImgPath(url) {
    if (!url) return "../assets/images/fluxads.png";
    if (url.startsWith("http") || url.startsWith("//") || url.startsWith("data:")) return url;
    if (url.startsWith("../")) return url;
    return "../" + url;
}
