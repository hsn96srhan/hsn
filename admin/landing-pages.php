<?php
require_once __DIR__ . '/includes/header.php';

$db = getDBConnection();
$msg = '';
$msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'])) {
    $action = $_POST['action_type'];

    if ($action === 'save_landing') {
        $id = (int)($_POST['landing_id'] ?? 0);
        $title_ar = trim($_POST['title_ar'] ?? '');
        $title_en = trim($_POST['title_en'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $meta_desc_ar = trim($_POST['meta_description_ar'] ?? '');
        $meta_desc_en = trim($_POST['meta_description_en'] ?? '');
        $blocks_json = trim($_POST['blocks_json'] ?? '[]');
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        $is_published = isset($_POST['is_published']) ? 1 : 0;

        if (empty($slug)) {
            $slug = 'lp-' . time();
        }

        if ($id > 0) {
            $stmt = $db->prepare("UPDATE landing_pages SET title_ar=?, title_en=?, slug=?, meta_description_ar=?, meta_description_en=?, blocks_json=?, sort_order=?, is_published=? WHERE id=?");
            $stmt->execute([$title_ar, $title_en, $slug, $meta_desc_ar, $meta_desc_en, $blocks_json, $sort_order, $is_published, $id]);
            $msg = 'تم تحديث صفحة الهبوط بنجاح! 🚀';
        } else {
            $stmt = $db->prepare("INSERT INTO landing_pages (title_ar, title_en, slug, meta_description_ar, meta_description_en, blocks_json, sort_order, is_published) VALUES (?,?,?,?,?,?,?,?)");
            $stmt->execute([$title_ar, $title_en, $slug, $meta_desc_ar, $meta_desc_en, $blocks_json, $sort_order, $is_published]);
            $msg = 'تم إنشاء صفحة الهبوط المخصصة الجديدة بنجاح! ✨';
        }
    } elseif ($action === 'delete_landing') {
        $id = (int)$_POST['landing_id'];
        $stmt = $db->prepare("DELETE FROM landing_pages WHERE id = ?");
        $stmt->execute([$id]);
        $msg = 'تم حذف صفحة الهبوط بنجاح.';
    }
}

$landings = $db->query("SELECT * FROM landing_pages ORDER BY sort_order ASC, id DESC")->fetchAll();

$editLp = null;
if (isset($_GET['edit_id'])) {
    $editId = (int)$_GET['edit_id'];
    $stmtEdit = $db->prepare("SELECT * FROM landing_pages WHERE id = ?");
    $stmtEdit->execute([$editId]);
    $editLp = $stmtEdit->fetch();
}
?>

<style>
.template-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
    gap: 12px;
    margin-bottom: 1.5rem;
}

.template-card {
    background: rgba(11, 19, 43, 0.8);
    border: 1px solid rgba(212, 175, 55, 0.25);
    border-radius: 12px;
    padding: 12px;
    text-align: center;
    transition: all 0.2s ease;
    cursor: pointer;
}

.template-card:hover {
    border-color: #D4AF37;
    transform: translateY(-2px);
    background: rgba(212, 175, 55, 0.1);
}

.template-badge {
    font-size: 0.72rem;
    background: rgba(212, 175, 55, 0.2);
    color: #D4AF37;
    padding: 2px 8px;
    border-radius: 4px;
    font-weight: 700;
    display: inline-block;
    margin-bottom: 6px;
}

/* Visual Block Editor Cards */
.section-editor-card {
    background: rgba(15, 23, 42, 0.95);
    border: 1px solid rgba(212, 175, 55, 0.3);
    border-radius: 14px;
    padding: 1.5rem;
    margin-bottom: 1.2rem;
    position: relative;
}

.section-editor-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    padding-bottom: 10px;
    margin-bottom: 1rem;
}

.section-title-tag {
    font-size: 1.05rem;
    font-weight: 800;
    color: #D4AF37;
    display: flex;
    align-items: center;
    gap: 8px;
}

.sub-item-box {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 10px;
    position: relative;
}
</style>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.8rem; flex-wrap:wrap; gap:12px;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 900; color: #FFF; margin-bottom: 4px;">باني صفحات الهبوط المخصصة (Visual Landing Builder)</h1>
        <p style="color: var(--admin-text-muted); font-size: 0.92rem;">توليد وتعديل كافة حقول السكاشن بصرياً لمنتجاتك وخدماتك بدون أية أكواد برمجة.</p>
    </div>
    <div style="display:flex; gap:10px;">
        <button type="button" onclick="loadDefault10SectionsPhp()" class="btn-action" style="background: linear-gradient(135deg, #10B981, #059669); color:#FFF;">⚡ تعبئة نموذج 10 سكاشن جاهز</button>
        <a href="landing-pages.php#form_section" class="btn-action">➕ صفحة جديدة فارغة</a>
    </div>
</div>

<?php if (!empty($msg)): ?>
    <div class="alert-box alert-<?php echo $msgType; ?>"><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>

<!-- 10 Section Templates Showcase Bar -->
<div class="glass-card">
    <div class="card-header-flex">
        <h2 class="card-title"><i class="fas fa-plus-circle"></i> إضافة سكشن جديد لبنية الصفحة (انقر للإضافة):</h2>
    </div>

    <div class="template-grid">
        <div class="template-card" onclick="addPhpSection('hero')">
            <span class="template-badge">+ إضافة</span>
            <div style="font-size:1.3rem; margin:2px 0;">🚀</div>
            <div style="font-weight:800; color:#FFF; font-size:0.85rem;">1. الهيدر والقيمة الجوهرية</div>
        </div>
        <div class="template-card" onclick="addPhpSection('problems')">
            <span class="template-badge">+ إضافة</span>
            <div style="font-size:1.3rem; margin:2px 0;">⚠️</div>
            <div style="font-weight:800; color:#FFF; font-size:0.85rem;">2. المشاكل والتحديات</div>
        </div>
        <div class="template-card" onclick="addPhpSection('solution')">
            <span class="template-badge">+ إضافة</span>
            <div style="font-size:1.3rem; margin:2px 0;">🎯</div>
            <div style="font-weight:800; color:#FFF; font-size:0.85rem;">3. الحل الاستراتيجي</div>
        </div>
        <div class="template-card" onclick="addPhpSection('features')">
            <span class="template-badge">+ إضافة</span>
            <div style="font-size:1.3rem; margin:2px 0;">⚡</div>
            <div style="font-weight:800; color:#FFF; font-size:0.85rem;">4. المزايا التنافسية الـ 4</div>
        </div>
        <div class="template-card" onclick="addPhpSection('process')">
            <span class="template-badge">+ إضافة</span>
            <div style="font-size:1.3rem; margin:2px 0;">🛤️</div>
            <div style="font-weight:800; color:#FFF; font-size:0.85rem;">5. خريطة الطريق 4 خطوات</div>
        </div>
        <div class="template-card" onclick="addPhpSection('case_studies')">
            <span class="template-badge">+ إضافة</span>
            <div style="font-size:1.3rem; margin:2px 0;">📊</div>
            <div style="font-weight:800; color:#FFF; font-size:0.85rem;">6. الأرقام والإنجازات</div>
        </div>
        <div class="template-card" onclick="addPhpSection('testimonials')">
            <span class="template-badge">+ إضافة</span>
            <div style="font-size:1.3rem; margin:2px 0;">⭐</div>
            <div style="font-weight:800; color:#FFF; font-size:0.85rem;">7. آراء العملاء والشهادات</div>
        </div>
        <div class="template-card" onclick="addPhpSection('guarantee')">
            <span class="template-badge">+ إضافة</span>
            <div style="font-size:1.3rem; margin:2px 0;">🛡️</div>
            <div style="font-weight:800; color:#FFF; font-size:0.85rem;">8. الضمان الذهبي 100%</div>
        </div>
        <div class="template-card" onclick="addPhpSection('pricing')">
            <span class="template-badge">+ إضافة</span>
            <div style="font-size:1.3rem; margin:2px 0;">💳</div>
            <div style="font-weight:800; color:#FFF; font-size:0.85rem;">9. عرض الباقة والتسعير</div>
        </div>
        <div class="template-card" onclick="addPhpSection('faq_cta')">
            <span class="template-badge">+ إضافة</span>
            <div style="font-size:1.3rem; margin:2px 0;">❓</div>
            <div style="font-weight:800; color:#FFF; font-size:0.85rem;">10. الأسئلة والتحويل الفوري</div>
        </div>
    </div>
</div>

<!-- Form Section -->
<div class="glass-card" id="form_section">
    <div class="card-header-flex">
        <h2 class="card-title">
            <i class="fas fa-edit"></i> 
            <?php echo $editLp ? 'تعديل صفحة الهبوط #' . $editLp['id'] : 'بناء صفحة هبوط مخصصة جديدة'; ?>
        </h2>
        <?php if ($editLp): ?>
            <a href="landing-pages.php" class="btn-secondary-action">إلغاء التعديل ✖️</a>
        <?php endif; ?>
    </div>

    <form method="POST" action="landing-pages.php" id="php_lp_main_form" onsubmit="preparePhpSubmission(event)">
        <input type="hidden" name="action_type" value="save_landing">
        <input type="hidden" name="landing_id" value="<?php echo $editLp['id'] ?? 0; ?>">
        <input type="hidden" name="blocks_json" id="php_hidden_blocks_json" value="<?php echo htmlspecialchars($editLp['blocks_json'] ?? '[]'); ?>">

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">عنوان صفحة الهبوط (عربي) *</label>
                <input type="text" name="title_ar" id="php_title_ar" class="form-input" required value="<?php echo htmlspecialchars($editLp['title_ar'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">عنوان صفحة الهبوط (إنجليزي)</label>
                <input type="text" name="title_en" id="php_title_en" class="form-input" value="<?php echo htmlspecialchars($editLp['title_en'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">المعرف الفريد والرابط (Slug) *</label>
                <input type="text" name="slug" id="php_slug" class="form-input" required placeholder="offer-product-saudi" value="<?php echo htmlspecialchars($editLp['slug'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">وصف الـ SEO والـ Meta Description</label>
                <input type="text" name="meta_description_ar" id="php_meta_description_ar" class="form-input" placeholder="استشارتك التسويقية لزيادة المبيعات..." value="<?php echo htmlspecialchars($editLp['meta_description_ar'] ?? ''); ?>">
            </div>
        </div>

        <!-- Visual Section Builder Container -->
        <div style="margin: 2rem 0;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1rem;">
                <h3 style="font-size:1.2rem; font-weight:800; color:#FFF;"><i class="fas fa-layer-group"></i> محرر حقول السكاشن البصري (Visual Builder)</h3>
                <span style="font-size:0.85rem; color:#D4AF37; font-weight:700;" id="php_sections_count_badge">0 سكاشن مضمنة</span>
            </div>

            <div id="php_visual_sections_container">
                <!-- Populated dynamically with labeled fields for each section -->
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">أولوية الترتيب (Sort Order)</label>
                <input type="number" name="sort_order" class="form-input" value="<?php echo $editLp['sort_order'] ?? count($landings) + 1; ?>">
            </div>
        </div>

        <div style="margin-bottom: 1.2rem; display:flex; align-items:center; gap:8px;">
            <input type="checkbox" name="is_published" id="is_published_lp" value="1" <?php echo (!isset($editLp) || !empty($editLp['is_published'])) ? 'checked' : ''; ?>>
            <label for="is_published_lp" style="font-size:0.9rem; font-weight:700; color:#FFF; cursor:pointer;">تفعيل ونشر صفحة الهبوط لتكون لايف عبر رابط الـ Slug</label>
        </div>

        <button type="submit" class="btn-action" style="font-size:1.05rem; padding:12px 30px;">💾 <?php echo $editLp ? 'حفظ التعديلات' : 'إنشاء ونشر صفحة الهبوط'; ?></button>
    </form>
</div>

<!-- List of Landing Pages -->
<div class="glass-card">
    <div class="card-header-flex">
        <h2 class="card-title"><i class="fas fa-layer-group"></i> صفحات الهبوط الحالية (<?php echo count($landings); ?>)</h2>
    </div>

    <?php if (empty($landings)): ?>
        <p style="color: var(--admin-text-muted); font-size: 0.9rem; text-align: center; padding: 2rem 0;">لا توجد صفحات هبوط مخصصة مضافة حتى الآن.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>عنوان الصفحة</th>
                        <th>الرابط (Slug)</th>
                        <th>السكاشن المضمنة</th>
                        <th>معاينة الصفحة</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($landings as $lp): ?>
                        <?php 
                        $parsedBlks = json_decode($lp['blocks_json'] ?? '[]', true) ?: [];
                        $blockCount = count($parsedBlks);
                        ?>
                        <tr>
                            <td><strong>#<?php echo $lp['id']; ?></strong></td>
                            <td><strong><?php echo htmlspecialchars($lp['title_ar']); ?></strong></td>
                            <td><code><?php echo htmlspecialchars($lp['slug']); ?></code></td>
                            <td><span style="background:rgba(212,175,55,0.15); color:#D4AF37; padding:2px 8px; border-radius:6px; font-weight:800; font-size:0.8rem;"><?php echo $blockCount; ?> سكاشن 💎</span></td>
                            <td>
                                <div style="display:flex; gap:6px;">
                                    <a href="../lp.php?slug=<?php echo htmlspecialchars($lp['slug']); ?>" target="_blank" style="color:#38BDF8; font-weight:700; font-size:0.82rem;">PHP 🔗</a>
                                    <a href="../lp.html?slug=<?php echo htmlspecialchars($lp['slug']); ?>" target="_blank" style="color:#4ADE80; font-weight:700; font-size:0.82rem;">HTML <ctrl42></a>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($lp['is_published'])): ?>
                                    <span style="color: #4ADE80; font-weight: 700; font-size: 0.82rem;">نشطة ومتاحة 🟢</span>
                                <?php else: ?>
                                    <span style="color: #F87171; font-weight: 700; font-size: 0.82rem;">معطلة 🔴</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size: 0.8rem; color: var(--admin-text-muted);"><?php echo $lp['created_at']; ?></td>
                            <td>
                                <div style="display:flex; gap: 6px;">
                                    <a href="landing-pages.php?edit_id=<?php echo $lp['id']; ?>#form_section" class="btn-secondary-action" style="padding: 4px 10px; font-size: 0.8rem;">تعديل ✏️</a>
                                    <form method="POST" action="landing-pages.php" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف صفحة الهبوط هذه؟');">
                                        <input type="hidden" name="action_type" value="delete_landing">
                                        <input type="hidden" name="landing_id" value="<?php echo $lp['id']; ?>">
                                        <button type="submit" class="btn-danger-action">حذف 🗑️</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script src="../assets/js/site-data.js"></script>
<script>
let phpBlocks = [];

document.addEventListener('DOMContentLoaded', () => {
    const rawVal = document.getElementById('php_hidden_blocks_json').value;
    try {
        phpBlocks = rawVal ? JSON.parse(rawVal) : [];
    } catch(e) {
        phpBlocks = [];
    }

    if (phpBlocks.length === 0 && typeof DEFAULT_SITE_DATA !== 'undefined') {
        const defBlks = DEFAULT_SITE_DATA.landing_pages[0].blocks_json;
        phpBlocks = typeof defBlks === 'string' ? JSON.parse(defBlks) : JSON.parse(JSON.stringify(defBlks));
    }

    renderPhpVisualBuilder();
});

function loadDefault10SectionsPhp() {
    const defBlks = DEFAULT_SITE_DATA.landing_pages[0].blocks_json;
    phpBlocks = typeof defBlks === 'string' ? JSON.parse(defBlks) : JSON.parse(JSON.stringify(defBlks));
    renderPhpVisualBuilder();
}

function addPhpSection(type) {
    const allTemplates = JSON.parse(DEFAULT_SITE_DATA.landing_pages[0].blocks_json);
    const tmpl = allTemplates.find(b => b.type === type) || { type: type, title_ar: "قسم جديد " + type };
    phpBlocks.push(JSON.parse(JSON.stringify(tmpl)));
    renderPhpVisualBuilder();
}

function movePhpSection(index, direction) {
    if (direction === 'up' && index > 0) {
        const temp = phpBlocks[index];
        phpBlocks[index] = phpBlocks[index - 1];
        phpBlocks[index - 1] = temp;
    } else if (direction === 'down' && index < phpBlocks.length - 1) {
        const temp = phpBlocks[index];
        phpBlocks[index] = phpBlocks[index + 1];
        phpBlocks[index + 1] = temp;
    }
    renderPhpVisualBuilder();
}

function deletePhpSection(index) {
    if (confirm('هل أنت متأكد من حذف هذا السكشن؟')) {
        phpBlocks.splice(index, 1);
        renderPhpVisualBuilder();
    }
}

function addPhpSubItem(blockIndex, keyName) {
    if (!phpBlocks[blockIndex][keyName]) phpBlocks[blockIndex][keyName] = [];
    if (keyName === 'items') {
        phpBlocks[blockIndex][keyName].push({ icon: "⚡", title_ar: "عنوان جديد", desc_ar: "وصف جديد..." });
    } else if (keyName === 'steps') {
        phpBlocks[blockIndex][keyName].push({ num: "0" + (phpBlocks[blockIndex][keyName].length + 1), title_ar: "عنوان الخطوة", desc_ar: "تفاصيل الخطوة..." });
    } else if (keyName === 'stats') {
        phpBlocks[blockIndex][keyName].push({ number: "+100%", label_ar: "مؤشر جديد" });
    } else if (keyName === 'faqs') {
        phpBlocks[blockIndex][keyName].push({ q_ar: "سؤال جديد؟", a_ar: "إجابة جديدة..." });
    }
    renderPhpVisualBuilder();
}

function deletePhpSubItem(blockIndex, keyName, itemIndex) {
    phpBlocks[blockIndex][keyName].splice(itemIndex, 1);
    renderPhpVisualBuilder();
}

function preparePhpSubmission(e) {
    document.getElementById('php_hidden_blocks_json').value = JSON.stringify(phpBlocks);
}

function renderPhpVisualBuilder() {
    const container = document.getElementById('php_visual_sections_container');
    const badge = document.getElementById('php_sections_count_badge');
    if (badge) badge.innerText = phpBlocks.length + ' سكاشن مضمنة 💎';

    if (!container) return;

    if (phpBlocks.length === 0) {
        container.innerHTML = '<div style="text-align:center; padding:3rem; color:var(--admin-text-muted); background:rgba(255,255,255,0.02); border-radius:12px;">لم يتم إضافة أي سكاشن بعد. انقر فوق أحد القوالب بالأعلى لبدء البناء!</div>';
        return;
    }

    container.innerHTML = phpBlocks.map((blk, i) => {
        const type = blk.type || 'hero';
        return `
            <div class="section-editor-card">
                <div class="section-editor-header">
                    <div class="section-title-tag">
                        <span>#${i + 1}</span>
                        <span>${getPhpBlockTypeNameAr(type)}</span>
                    </div>
                    <div style="display:flex; gap:8px;">
                        ${i > 0 ? `<button type="button" class="btn-secondary-action" onclick="movePhpSection(${i}, 'up')">⬆️ للأعلى</button>` : ''}
                        ${i < phpBlocks.length - 1 ? `<button type="button" class="btn-secondary-action" onclick="movePhpSection(${i}, 'down')">⬇️ الأسفل</button>` : ''}
                        <button type="button" class="btn-danger-action" onclick="deletePhpSection(${i})">حذف السكشن 🗑️</button>
                    </div>
                </div>

                ${renderPhpFieldsForBlock(blk, i)}
            </div>
        `;
    }).join('');
}

function getPhpBlockTypeNameAr(type) {
    const names = {
        hero: '🚀 قسم الهيدر والقيمة الجوهرية (Hero)',
        problems: '⚠️ قسم المشاكل والتحديات (Pain Points)',
        solution: '🎯 قسم الحل الاستراتيجي (Solution)',
        features: '⚡ قسم المزايا التنافسية (Features)',
        process: '🛤️ قسم خريطة الطريق 4 خطوات (Process)',
        case_studies: '📊 قسم الأرقام والإنجازات (Proven Stats)',
        testimonials: '⭐ قسم آراء العملاء والشهادات (Testimonials)',
        guarantee: '🛡️ قسم الضمان الذهبي 100% (Guarantee)',
        pricing: '💳 قسم عرض الباقة والتسعير (Pricing Offer)',
        faq_cta: '❓ قسم الأسئلة والتحويل الفوري (FAQ & CTA)'
    };
    return names[type] || type;
}

function renderPhpFieldsForBlock(blk, i) {
    const type = blk.type;

    if (type === 'hero') {
        return `
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">نص البادج الترويجي العلوي Badge Text</label>
                    <input type="text" class="form-input" value="${escapeHtml(blk.badge_ar || '')}" onchange="phpBlocks[${i}].badge_ar = this.value">
                </div>
                <div class="form-group">
                    <label class="form-label">العنوان الرئيسي للسكشن Main Title *</label>
                    <input type="text" class="form-input" value="${escapeHtml(blk.title_ar || '')}" onchange="phpBlocks[${i}].title_ar = this.value">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">العنوان الفرعي الوصفي Subtitle</label>
                <textarea class="form-textarea" rows="2" onchange="phpBlocks[${i}].subtitle_ar = this.value">${escapeHtml(blk.subtitle_ar || '')}</textarea>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">نص زر التحويل الرئيسي Primary CTA Text</label>
                    <input type="text" class="form-input" value="${escapeHtml(blk.primary_cta_text_ar || '')}" onchange="phpBlocks[${i}].primary_cta_text_ar = this.value">
                </div>
                <div class="form-group">
                    <label class="form-label">رابط الزر الرئيسي Primary CTA Link</label>
                    <input type="text" class="form-input" value="${escapeHtml(blk.primary_cta_link || '')}" onchange="phpBlocks[${i}].primary_cta_link = this.value">
                </div>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">نص زر الواتساب / الجانبي Secondary CTA</label>
                    <input type="text" class="form-input" value="${escapeHtml(blk.secondary_cta_text_ar || '')}" onchange="phpBlocks[${i}].secondary_cta_text_ar = this.value">
                </div>
                <div class="form-group">
                    <label class="form-label">رابط الواتساب / الجانبي Secondary Link</label>
                    <input type="text" class="form-input" value="${escapeHtml(blk.secondary_cta_link || '')}" onchange="phpBlocks[${i}].secondary_cta_link = this.value">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">عبارة الموثوقية السريعة Trust Note</label>
                <input type="text" class="form-input" value="${escapeHtml(blk.trust_note_ar || '')}" onchange="phpBlocks[${i}].trust_note_ar = this.value">
            </div>
        `;
    }

    if (type === 'problems' || type === 'solution' || type === 'features') {
        const items = blk.items || [];
        const itemsHtml = items.map((item, idx) => `
            <div class="sub-item-box">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                    <strong style="color:#FFF;">عنصر #${idx + 1}</strong>
                    <button type="button" class="btn-danger-action" onclick="deletePhpSubItem(${i}, 'items', ${idx})">حذف 🗑️</button>
                </div>
                <div class="form-grid">
                    <div class="form-group" style="max-width:100px;">
                        <label class="form-label">الأيقونة Emoji</label>
                        <input type="text" class="form-input" value="${escapeHtml(item.icon || '⚡')}" onchange="phpBlocks[${i}].items[${idx}].icon = this.value">
                    </div>
                    <div class="form-group">
                        <label class="form-label">العنوان</label>
                        <input type="text" class="form-input" value="${escapeHtml(item.title_ar || '')}" onchange="phpBlocks[${i}].items[${idx}].title_ar = this.value">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">الوصف التفصيلي</label>
                    <input type="text" class="form-input" value="${escapeHtml(item.desc_ar || '')}" onchange="phpBlocks[${i}].items[${idx}].desc_ar = this.value">
                </div>
            </div>
        `).join('');

        return `
            <div class="form-group">
                <label class="form-label">عنوان السكشن</label>
                <input type="text" class="form-input" value="${escapeHtml(blk.title_ar || '')}" onchange="phpBlocks[${i}].title_ar = this.value">
            </div>
            <div class="form-group">
                <label class="form-label">الوصف الفرعي</label>
                <input type="text" class="form-input" value="${escapeHtml(blk.subtitle_ar || '')}" onchange="phpBlocks[${i}].subtitle_ar = this.value">
            </div>
            <div style="margin-top:1rem;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <strong style="color:#D4AF37;">عناصر السكشن (${items.length}):</strong>
                    <button type="button" class="btn-secondary-action" onclick="addPhpSubItem(${i}, 'items')">➕ إضافة عنصر جديد</button>
                </div>
                ${itemsHtml}
            </div>
        `;
    }

    if (type === 'process') {
        const steps = blk.steps || [];
        const stepsHtml = steps.map((st, idx) => `
            <div class="sub-item-box">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                    <strong style="color:#FFF;">الخطوة #${idx + 1}</strong>
                    <button type="button" class="btn-danger-action" onclick="deletePhpSubItem(${i}, 'steps', ${idx})">حذف 🗑️</button>
                </div>
                <div class="form-grid">
                    <div class="form-group" style="max-width:100px;">
                        <label class="form-label">الرقم</label>
                        <input type="text" class="form-input" value="${escapeHtml(st.num || '01')}" onchange="phpBlocks[${i}].steps[${idx}].num = this.value">
                    </div>
                    <div class="form-group">
                        <label class="form-label">عنوان الخطوة</label>
                        <input type="text" class="form-input" value="${escapeHtml(st.title_ar || '')}" onchange="phpBlocks[${i}].steps[${idx}].title_ar = this.value">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">تفاصيل الخطوة</label>
                    <input type="text" class="form-input" value="${escapeHtml(st.desc_ar || '')}" onchange="phpBlocks[${i}].steps[${idx}].desc_ar = this.value">
                </div>
            </div>
        `).join('');

        return `
            <div class="form-group">
                <label class="form-label">عنوان السكشن</label>
                <input type="text" class="form-input" value="${escapeHtml(blk.title_ar || '')}" onchange="phpBlocks[${i}].title_ar = this.value">
            </div>
            <div class="form-group">
                <label class="form-label">الوصف الفرعي</label>
                <input type="text" class="form-input" value="${escapeHtml(blk.subtitle_ar || '')}" onchange="phpBlocks[${i}].subtitle_ar = this.value">
            </div>
            <div style="margin-top:1rem;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <strong style="color:#D4AF37;">خطوات العمل (${steps.length}):</strong>
                    <button type="button" class="btn-secondary-action" onclick="addPhpSubItem(${i}, 'steps')">➕ إضافة خطوة جديدة</button>
                </div>
                ${stepsHtml}
            </div>
        `;
    }

    if (type === 'case_studies') {
        const stats = blk.stats || [];
        const statsHtml = stats.map((st, idx) => `
            <div class="sub-item-box">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                    <strong style="color:#FFF;">مؤشر أداء #${idx + 1}</strong>
                    <button type="button" class="btn-danger-action" onclick="deletePhpSubItem(${i}, 'stats', ${idx})">حذف 🗑️</button>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">الرقم/النسبة (مثال: +350%)</label>
                        <input type="text" class="form-input" value="${escapeHtml(st.number || '')}" onchange="phpBlocks[${i}].stats[${idx}].number = this.value">
                    </div>
                    <div class="form-group">
                        <label class="form-label">اسم المؤشر/الإنجاز</label>
                        <input type="text" class="form-input" value="${escapeHtml(st.label_ar || '')}" onchange="phpBlocks[${i}].stats[${idx}].label_ar = this.value">
                    </div>
                </div>
            </div>
        `).join('');

        return `
            <div class="form-group">
                <label class="form-label">عنوان السكشن</label>
                <input type="text" class="form-input" value="${escapeHtml(blk.title_ar || '')}" onchange="phpBlocks[${i}].title_ar = this.value">
            </div>
            <div class="form-group">
                <label class="form-label">الوصف الفرعي</label>
                <input type="text" class="form-input" value="${escapeHtml(blk.subtitle_ar || '')}" onchange="phpBlocks[${i}].subtitle_ar = this.value">
            </div>
            <div style="margin-top:1rem;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <strong style="color:#D4AF37;">مؤشرات الإنجاز (${stats.length}):</strong>
                    <button type="button" class="btn-secondary-action" onclick="addPhpSubItem(${i}, 'stats')">➕ إضافة مؤشر جديد</button>
                </div>
                ${statsHtml}
            </div>
        `;
    }

    if (type === 'testimonials') {
        const items = blk.items || [];
        const itemsHtml = items.map((tm, idx) => `
            <div class="sub-item-box">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                    <strong style="color:#FFF;">رأي عميل #${idx + 1}</strong>
                    <button type="button" class="btn-danger-action" onclick="deletePhpSubItem(${i}, 'items', ${idx})">حذف 🗑️</button>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">اسم العميل</label>
                        <input type="text" class="form-input" value="${escapeHtml(tm.name_ar || '')}" onchange="phpBlocks[${i}].items[${idx}].name_ar = this.value">
                    </div>
                    <div class="form-group">
                        <label class="form-label">المسمى الوظيفي/الشركة</label>
                        <input type="text" class="form-input" value="${escapeHtml(tm.role_ar || '')}" onchange="phpBlocks[${i}].items[${idx}].role_ar = this.value">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">نص الشهادة / التقييم</label>
                    <textarea class="form-textarea" rows="2" onchange="phpBlocks[${i}].items[${idx}].quote_ar = this.value">${escapeHtml(tm.quote_ar || '')}</textarea>
                </div>
            </div>
        `).join('');

        return `
            <div class="form-group">
                <label class="form-label">عنوان السكشن</label>
                <input type="text" class="form-input" value="${escapeHtml(blk.title_ar || '')}" onchange="phpBlocks[${i}].title_ar = this.value">
            </div>
            <div class="form-group">
                <label class="form-label">الوصف الفرعي</label>
                <input type="text" class="form-input" value="${escapeHtml(blk.subtitle_ar || '')}" onchange="phpBlocks[${i}].subtitle_ar = this.value">
            </div>
            <div style="margin-top:1rem;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <strong style="color:#D4AF37;">آراء العملاء (${items.length}):</strong>
                    <button type="button" class="btn-secondary-action" onclick="addPhpSubItem(${i}, 'items')">➕ إضافة رأي عميل</button>
                </div>
                ${itemsHtml}
            </div>
        `;
    }

    if (type === 'guarantee') {
        return `
            <div class="form-group">
                <label class="form-label">عنوان الضمان</label>
                <input type="text" class="form-input" value="${escapeHtml(blk.title_ar || '')}" onchange="phpBlocks[${i}].title_ar = this.value">
            </div>
            <div class="form-group">
                <label class="form-label">تفاصيل نص الضمان وإعادة المخاطر</label>
                <textarea class="form-textarea" rows="3" onchange="phpBlocks[${i}].subtitle_ar = this.value">${escapeHtml(blk.subtitle_ar || '')}</textarea>
            </div>
        `;
    }

    if (type === 'pricing') {
        const ftList = Array.isArray(blk.features) ? blk.features.join('\n') : (blk.features || '');
        return `
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">عنوان السكشن</label>
                    <input type="text" class="form-input" value="${escapeHtml(blk.title_ar || '')}" onchange="phpBlocks[${i}].title_ar = this.value">
                </div>
                <div class="form-group">
                    <label class="form-label">اسم الباقة Package Name</label>
                    <input type="text" class="form-input" value="${escapeHtml(blk.package_name_ar || '')}" onchange="phpBlocks[${i}].package_name_ar = this.value">
                </div>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">السعر الحالي Price</label>
                    <input type="text" class="form-input" value="${escapeHtml(blk.price_ar || '')}" onchange="phpBlocks[${i}].price_ar = this.value">
                </div>
                <div class="form-group">
                    <label class="form-label">السعر السابق Original Price</label>
                    <input type="text" class="form-input" value="${escapeHtml(blk.original_price_ar || '')}" onchange="phpBlocks[${i}].original_price_ar = this.value">
                </div>
                <div class="form-group">
                    <label class="form-label">بادج الخصم Discount Badge</label>
                    <input type="text" class="form-input" value="${escapeHtml(blk.discount_badge_ar || '')}" onchange="phpBlocks[${i}].discount_badge_ar = this.value">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">مزايا ومحتويات الباقة (كل ميزة في سطر)</label>
                <textarea class="form-textarea" rows="5" onchange="phpBlocks[${i}].features = this.value.split('\\n').filter(x=>x.trim())">${escapeHtml(ftList)}</textarea>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">نص زر الاشتراك CTA Text</label>
                    <input type="text" class="form-input" value="${escapeHtml(blk.cta_text_ar || '')}" onchange="phpBlocks[${i}].cta_text_ar = this.value">
                </div>
                <div class="form-group">
                    <label class="form-label">رابط زر الاشتراك CTA Link</label>
                    <input type="text" class="form-input" value="${escapeHtml(blk.cta_link || '')}" onchange="phpBlocks[${i}].cta_link = this.value">
                </div>
            </div>
        `;
    }

    if (type === 'faq_cta') {
        const faqs = blk.faqs || [];
        const faqsHtml = faqs.map((f, idx) => `
            <div class="sub-item-box">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                    <strong style="color:#FFF;">سؤال #${idx + 1}</strong>
                    <button type="button" class="btn-danger-action" onclick="deletePhpSubItem(${i}, 'faqs', ${idx})">حذف 🗑️</button>
                </div>
                <div class="form-group">
                    <label class="form-label">السؤال</label>
                    <input type="text" class="form-input" value="${escapeHtml(f.q_ar || '')}" onchange="phpBlocks[${i}].faqs[${idx}].q_ar = this.value">
                </div>
                <div class="form-group">
                    <label class="form-label">الإجابة</label>
                    <input type="text" class="form-input" value="${escapeHtml(f.a_ar || '')}" onchange="phpBlocks[${i}].faqs[${idx}].a_ar = this.value">
                </div>
            </div>
        `).join('');

        return `
            <div class="form-group">
                <label class="form-label">عنوان قسم الأسئلة</label>
                <input type="text" class="form-input" value="${escapeHtml(blk.title_ar || '')}" onchange="phpBlocks[${i}].title_ar = this.value">
            </div>
            <div style="margin-top:1rem;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <strong style="color:#D4AF37;">الأسئلة والأجوبة (${faqs.length}):</strong>
                    <button type="button" class="btn-secondary-action" onclick="addPhpSubItem(${i}, 'faqs')">➕ إضافة سؤال جديد</button>
                </div>
                ${faqsHtml}
            </div>
            <hr style="border-color:rgba(255,255,255,0.1); margin:1.5rem 0;">
            <div class="form-group">
                <label class="form-label">عنوان البانر الختامي الفوري Final CTA Title</label>
                <input type="text" class="form-input" value="${escapeHtml(blk.final_cta_title_ar || '')}" onchange="phpBlocks[${i}].final_cta_title_ar = this.value">
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">نص زر التواصل الختامي</label>
                    <input type="text" class="form-input" value="${escapeHtml(blk.final_cta_btn_ar || '')}" onchange="phpBlocks[${i}].final_cta_btn_ar = this.value">
                </div>
                <div class="form-group">
                    <label class="form-label">رابط الواتساب / التواصل</label>
                    <input type="text" class="form-input" value="${escapeHtml(blk.whatsapp_url || '')}" onchange="phpBlocks[${i}].whatsapp_url = this.value">
                </div>
            </div>
        `;
    }

    return '';
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
