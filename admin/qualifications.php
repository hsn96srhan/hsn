<?php
require_once __DIR__ . '/includes/header.php';

$db = getDBConnection();
$msg = '';
$msgType = 'success';

// Handle Add / Edit / Delete Qualifications
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'])) {
    $action = $_POST['action_type'];
    
    if ($action === 'save_qual') {
        $id = (int)($_POST['qual_id'] ?? 0);
        $title_ar = trim($_POST['title_ar'] ?? '');
        $title_en = trim($_POST['title_en'] ?? '');
        $issuer_ar = trim($_POST['issuer_ar'] ?? '');
        $issuer_en = trim($_POST['issuer_en'] ?? '');
        $desc_ar = trim($_POST['description_ar'] ?? '');
        $desc_en = trim($_POST['description_en'] ?? '');
        $image_url = trim($_POST['image_url'] ?? '');
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        $is_published = isset($_POST['is_published']) ? 1 : 0;

        // File upload handler if provided
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
            $newFileName = 'cert_' . time() . '_' . rand(100,999) . '.' . strtolower($ext);
            $uploadDir = __DIR__ . '/../assets/images/';
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadDir . $newFileName)) {
                $image_url = 'assets/images/' . $newFileName;
            }
        }

        if ($id > 0) {
            $stmt = $db->prepare("UPDATE qualifications SET title_ar=?, title_en=?, issuer_ar=?, issuer_en=?, description_ar=?, description_en=?, image_url=?, sort_order=?, is_published=? WHERE id=?");
            $stmt->execute([$title_ar, $title_en, $issuer_ar, $issuer_en, $desc_ar, $desc_en, $image_url, $sort_order, $is_published, $id]);
            $msg = 'تم تحديث بيانات الشهادة بنجاح! ✨';
        } else {
            $stmt = $db->prepare("INSERT INTO qualifications (title_ar, title_en, issuer_ar, issuer_en, description_ar, description_en, image_url, sort_order, is_published) VALUES (?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$title_ar, $title_en, $issuer_ar, $issuer_en, $desc_ar, $desc_en, $image_url, $sort_order, $is_published]);
            $msg = 'تمت إضافة الشهادة الجديدة بنجاح! 🎓';
        }
    } elseif ($action === 'delete_qual') {
        $id = (int)$_POST['qual_id'];
        $stmt = $db->prepare("DELETE FROM qualifications WHERE id = ?");
        $stmt->execute([$id]);
        $msg = 'تم حذف الشهادة بنجاح.';
    }
}

// Fetch all qualifications
$qualifications = $db->query("SELECT * FROM qualifications ORDER BY sort_order ASC, id ASC")->fetchAll();

// Check if editing a specific qualification
$editQual = null;
if (isset($_GET['edit_id'])) {
    $editId = (int)$_GET['edit_id'];
    $stmtEdit = $db->prepare("SELECT * FROM qualifications WHERE id = ?");
    $stmtEdit->execute([$editId]);
    $editQual = $stmtEdit->fetch();
}
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.8rem;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 900; color: #FFF; margin-bottom: 4px;">إدارة المؤهلات والاعتمادات الرسمية (18 شهادة)</h1>
        <p style="color: var(--admin-text-muted); font-size: 0.92rem;">التحكم الكامل والترتيب الديناميكي لجميع شهادات واعتمادات حسن سرحان الميدانية.</p>
    </div>
    <a href="qualifications.php#form_section" class="btn-action">➕ إضافة شهادة جديدة</a>
</div>

<?php if (!empty($msg)): ?>
    <div class="alert-box alert-<?php echo $msgType; ?>"><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>

<!-- Qualification Add / Edit Form -->
<div class="glass-card" id="form_section">
    <div class="card-header-flex">
        <h2 class="card-title">
            <i class="fas fa-certificate"></i> 
            <?php echo $editQual ? 'تعديل الشهادة #' . $editQual['id'] : 'إضافة شهادة جديدة / اعتماد مهني'; ?>
        </h2>
        <?php if ($editQual): ?>
            <a href="qualifications.php" class="btn-secondary-action">إلغاء التعديل ✖️</a>
        <?php endif; ?>
    </div>

    <form method="POST" action="qualifications.php" enctype="multipart/form-data">
        <input type="hidden" name="action_type" value="save_qual">
        <input type="hidden" name="qual_id" value="<?php echo $editQual['id'] ?? 0; ?>">

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">اسم الشهادة (عربي) *</label>
                <input type="text" name="title_ar" class="form-input" required value="<?php echo htmlspecialchars($editQual['title_ar'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">اسم الشهادة (إنجليزي)</label>
                <input type="text" name="title_en" class="form-input" value="<?php echo htmlspecialchars($editQual['title_en'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">الجهة المانحة (عربي) *</label>
                <input type="text" name="issuer_ar" class="form-input" required value="<?php echo htmlspecialchars($editQual['issuer_ar'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">الجهة المانحة (إنجليزي)</label>
                <input type="text" name="issuer_en" class="form-input" value="<?php echo htmlspecialchars($editQual['issuer_en'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">وصف تفصيلي للشهادة (عربي)</label>
                <textarea name="description_ar" class="form-textarea" rows="2"><?php echo htmlspecialchars($editQual['description_ar'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">وصف تفصيلي للشهادة (إنجليزي)</label>
                <textarea name="description_en" class="form-textarea" rows="2"><?php echo htmlspecialchars($editQual['description_en'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">رابط صورة الشهادة الحالي</label>
                <input type="text" name="image_url" class="form-input" value="<?php echo htmlspecialchars($editQual['image_url'] ?? 'assets/images/'); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">أو رفع صورة شهادة جديدة من الجهاز</label>
                <input type="file" name="image_file" class="form-input" accept="image/*">
            </div>
            <div class="form-group">
                <label class="form-label">ترتيب الظهور (sort_order)</label>
                <input type="number" name="sort_order" class="form-input" value="<?php echo $editQual['sort_order'] ?? count($qualifications) + 1; ?>">
            </div>
        </div>

        <div style="margin-bottom: 1.2rem; display:flex; align-items:center; gap:8px;">
            <input type="checkbox" name="is_published" id="is_published" value="1" <?php echo (!isset($editQual) || !empty($editQual['is_published'])) ? 'checked' : ''; ?>>
            <label for="is_published" style="font-size:0.9rem; font-weight:700; color:#FFF; cursor:pointer;">نشر واظهار الشهادة على الفرونت ايند المباشر للموقع</label>
        </div>

        <button type="submit" class="btn-action">💾 <?php echo $editQual ? 'حفظ التعديلات' : 'إضافة الشهادة للشبكة'; ?></button>
    </form>
</div>

<!-- Qualifications List Grid/Table -->
<div class="glass-card">
    <div class="card-header-flex">
        <h2 class="card-title"><i class="fas fa-list-ol"></i> قائمة الشهادات الحالية (<?php echo count($qualifications); ?>)</h2>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID / الترتيب</th>
                    <th>معاينة الصورة</th>
                    <th>اسم الشهادة (عربي/EN)</th>
                    <th>الجهة المانحة</th>
                    <th>الحالة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($qualifications as $q): ?>
                    <tr>
                        <td>
                            <strong style="color: var(--admin-primary);">#<?php echo $q['id']; ?></strong>
                            <div style="font-size:0.75rem; color:var(--admin-text-muted);">ترتيب: <?php echo $q['sort_order']; ?></div>
                        </td>
                        <td>
                            <?php if (!empty($q['image_url'])): ?>
                                <img src="../<?php echo htmlspecialchars($q['image_url']); ?>" alt="" style="width: 60px; height: 42px; object-fit: contain; background: #FFF; border-radius: 6px; padding: 2px;">
                            <?php else: ?>
                                <span style="color: var(--admin-text-muted);">بدون صورة</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($q['title_ar']); ?></strong>
                            <div style="font-size: 0.8rem; color: var(--admin-text-muted);"><?php echo htmlspecialchars($q['title_en']); ?></div>
                        </td>
                        <td>
                            <span style="background: rgba(255,255,255,0.08); padding: 4px 10px; border-radius: 12px; font-size: 0.82rem; font-weight: 700;">
                                <?php echo htmlspecialchars($q['issuer_ar']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($q['is_published'])): ?>
                                <span style="color: #4ADE80; font-weight: 700; font-size: 0.82rem;">منشور 🟢</span>
                            <?php else: ?>
                                <span style="color: #F87171; font-weight: 700; font-size: 0.82rem;">مخفي 🔴</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display:flex; gap: 6px;">
                                <a href="qualifications.php?edit_id=<?php echo $q['id']; ?>#form_section" class="btn-secondary-action" style="padding: 4px 10px; font-size: 0.8rem;">تعديل ✏️</a>
                                <form method="POST" action="qualifications.php" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذه الشهادة؟');">
                                    <input type="hidden" name="action_type" value="delete_qual">
                                    <input type="hidden" name="qual_id" value="<?php echo $q['id']; ?>">
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
