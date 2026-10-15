<?php
require_once __DIR__ . '/includes/header.php';

$db = getDBConnection();
$msg = '';
$msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'])) {
    $action = $_POST['action_type'];

    if ($action === 'save_service') {
        $id = (int)($_POST['service_id'] ?? 0);
        $title_ar = trim($_POST['title_ar'] ?? '');
        $title_en = trim($_POST['title_en'] ?? '');
        $badge_ar = trim($_POST['badge_ar'] ?? '');
        $badge_en = trim($_POST['badge_en'] ?? '');
        $subtitle_ar = trim($_POST['subtitle_ar'] ?? '');
        $subtitle_en = trim($_POST['subtitle_en'] ?? '');
        $features_json = trim($_POST['features_json'] ?? '[]');
        $price_text_ar = trim($_POST['price_text_ar'] ?? '');
        $price_text_en = trim($_POST['price_text_en'] ?? '');
        $cta_text_ar = trim($_POST['cta_text_ar'] ?? '');
        $cta_text_en = trim($_POST['cta_text_en'] ?? '');
        $cta_link = trim($_POST['cta_link'] ?? 'contact.php');
        $icon_class = trim($_POST['icon_class'] ?? 'fas fa-star');
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        $is_published = isset($_POST['is_published']) ? 1 : 0;

        if ($id > 0) {
            $stmt = $db->prepare("UPDATE services SET title_ar=?, title_en=?, badge_ar=?, badge_en=?, subtitle_ar=?, subtitle_en=?, features_json=?, price_text_ar=?, price_text_en=?, cta_text_ar=?, cta_text_en=?, cta_link=?, icon_class=?, sort_order=?, is_published=? WHERE id=?");
            $stmt->execute([$title_ar, $title_en, $badge_ar, $badge_en, $subtitle_ar, $subtitle_en, $features_json, $price_text_ar, $price_text_en, $cta_text_ar, $cta_text_en, $cta_link, $icon_class, $sort_order, $is_published, $id]);
            $msg = 'تم تحديث الخدمة بنجاح! 🛠️';
        } else {
            $stmt = $db->prepare("INSERT INTO services (title_ar, title_en, badge_ar, badge_en, subtitle_ar, subtitle_en, features_json, price_text_ar, price_text_en, cta_text_ar, cta_text_en, cta_link, icon_class, sort_order, is_published) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$title_ar, $title_en, $badge_ar, $badge_en, $subtitle_ar, $subtitle_en, $features_json, $price_text_ar, $price_text_en, $cta_text_ar, $cta_text_en, $cta_link, $icon_class, $sort_order, $is_published]);
            $msg = 'تمت إضافة الخدمة الجديدة بنجاح! 🚀';
        }
    } elseif ($action === 'delete_service') {
        $id = (int)$_POST['service_id'];
        $stmt = $db->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$id]);
        $msg = 'تم حذف الخدمة بنجاح.';
    }
}

$services = $db->query("SELECT * FROM services ORDER BY sort_order ASC, id ASC")->fetchAll();

$editServ = null;
if (isset($_GET['edit_id'])) {
    $editId = (int)$_GET['edit_id'];
    $stmtEdit = $db->prepare("SELECT * FROM services WHERE id = ?");
    $stmtEdit->execute([$editId]);
    $editServ = $stmtEdit->fetch();
}
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.8rem;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 900; color: #FFF; margin-bottom: 4px;">إدارة الخدمات والحلول الاستراتيجية</h1>
        <p style="color: var(--admin-text-muted); font-size: 0.92rem;">تعديل باقات الخدمات، الميزات، التسعير ورابط الحجز المباشر.</p>
    </div>
    <a href="services.php#form_section" class="btn-action">➕ إضافة خدمة جديدة</a>
</div>

<?php if (!empty($msg)): ?>
    <div class="alert-box alert-<?php echo $msgType; ?>"><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>

<!-- Form Section -->
<div class="glass-card" id="form_section">
    <div class="card-header-flex">
        <h2 class="card-title">
            <i class="fas fa-concierge-bell"></i> 
            <?php echo $editServ ? 'تعديل الخدمة #' . $editServ['id'] : 'إضافة خدمة جديدة'; ?>
        </h2>
        <?php if ($editServ): ?>
            <a href="services.php" class="btn-secondary-action">إلغاء التعديل ✖️</a>
        <?php endif; ?>
    </div>

    <form method="POST" action="services.php">
        <input type="hidden" name="action_type" value="save_service">
        <input type="hidden" name="service_id" value="<?php echo $editServ['id'] ?? 0; ?>">

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">عنوان الخدمة (عربي) *</label>
                <input type="text" name="title_ar" class="form-input" required value="<?php echo htmlspecialchars($editServ['title_ar'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">عنوان الخدمة (إنجليزي)</label>
                <input type="text" name="title_en" class="form-input" value="<?php echo htmlspecialchars($editServ['title_en'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">شارة الخدمة (عربي)</label>
                <input type="text" name="badge_ar" class="form-input" placeholder="🎯 خدمة رئيسية" value="<?php echo htmlspecialchars($editServ['badge_ar'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">شارة الخدمة (إنجليزي)</label>
                <input type="text" name="badge_en" class="form-input" placeholder="🎯 Core Service" value="<?php echo htmlspecialchars($editServ['badge_en'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">الوصف والملخص (عربي)</label>
                <textarea name="subtitle_ar" class="form-textarea" rows="2"><?php echo htmlspecialchars($editServ['subtitle_ar'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">الوصف والملخص (إنجليزي)</label>
                <textarea name="subtitle_en" class="form-textarea" rows="2"><?php echo htmlspecialchars($editServ['subtitle_en'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">قائمة الميزات (JSON Array)</label>
                <textarea name="features_json" class="form-textarea" rows="3" placeholder='["ميزة 1", "ميزة 2"]'><?php echo htmlspecialchars($editServ['features_json'] ?? '[]'); ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">التسعير / العرض (عربي)</label>
                <input type="text" name="price_text_ar" class="form-input" placeholder="حسب نطاق المشروع" value="<?php echo htmlspecialchars($editServ['price_text_ar'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">نص زر الدعوة للإجراء CTA (عربي)</label>
                <input type="text" name="cta_text_ar" class="form-input" value="<?php echo htmlspecialchars($editServ['cta_text_ar'] ?? '📅 احجز جلسة استشارية'); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">رابط الزر (CTA Link)</label>
                <input type="text" name="cta_link" class="form-input" value="<?php echo htmlspecialchars($editServ['cta_link'] ?? 'contact.php'); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">ترتيب الظهور (sort_order)</label>
                <input type="number" name="sort_order" class="form-input" value="<?php echo $editServ['sort_order'] ?? count($services) + 1; ?>">
            </div>
        </div>

        <div style="margin-bottom: 1.2rem; display:flex; align-items:center; gap:8px;">
            <input type="checkbox" name="is_published" id="is_published_s" value="1" <?php echo (!isset($editServ) || !empty($editServ['is_published'])) ? 'checked' : ''; ?>>
            <label for="is_published_s" style="font-size:0.9rem; font-weight:700; color:#FFF; cursor:pointer;">إظهار الخدمة في صفحة الخدمات الحية للموقع</label>
        </div>

        <button type="submit" class="btn-action">💾 <?php echo $editServ ? 'حفظ التعديلات' : 'إضافة الخدمة'; ?></button>
    </form>
</div>

<!-- List of Services -->
<div class="glass-card">
    <div class="card-header-flex">
        <h2 class="card-title"><i class="fas fa-list-ul"></i> قائمة الخدمات الحالية (<?php echo count($services); ?>)</h2>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID / الترتيب</th>
                    <th>اسم الخدمة</th>
                    <th>الشارة</th>
                    <th>التسعير</th>
                    <th>الحالة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($services as $s): ?>
                    <tr>
                        <td>
                            <strong style="color: var(--admin-primary);">#<?php echo $s['id']; ?></strong>
                            <div style="font-size:0.75rem; color:var(--admin-text-muted);">ترتيب: <?php echo $s['sort_order']; ?></div>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($s['title_ar']); ?></strong>
                            <div style="font-size: 0.8rem; color: var(--admin-text-muted);"><?php echo htmlspecialchars($s['title_en']); ?></div>
                        </td>
                        <td>
                            <span style="background: rgba(212, 175, 55, 0.15); color: var(--admin-primary); padding: 4px 10px; border-radius: 12px; font-weight: 700; font-size: 0.8rem;">
                                <?php echo htmlspecialchars($s['badge_ar']); ?>
                            </span>
                        </td>
                        <td><strong><?php echo htmlspecialchars($s['price_text_ar']); ?></strong></td>
                        <td>
                            <?php if (!empty($s['is_published'])): ?>
                                <span style="color: #4ADE80; font-weight: 700; font-size: 0.82rem;">منشور 🟢</span>
                            <?php else: ?>
                                <span style="color: #F87171; font-weight: 700; font-size: 0.82rem;">مخفي 🔴</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display:flex; gap: 6px;">
                                <a href="services.php?edit_id=<?php echo $s['id']; ?>#form_section" class="btn-secondary-action" style="padding: 4px 10px; font-size: 0.8rem;">تعديل ✏️</a>
                                <form method="POST" action="services.php" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذه الخدمة؟');">
                                    <input type="hidden" name="action_type" value="delete_service">
                                    <input type="hidden" name="service_id" value="<?php echo $s['id']; ?>">
                                    <button type="submit" class="btn-danger-action">حذف 🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
