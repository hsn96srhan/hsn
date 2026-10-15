<?php
require_once __DIR__ . '/includes/header.php';

$db = getDBConnection();
$msg = '';
$msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'])) {
    $action = $_POST['action_type'];

    if ($action === 'save_course') {
        $id = (int)($_POST['course_id'] ?? 0);
        $title_ar = trim($_POST['title_ar'] ?? '');
        $title_en = trim($_POST['title_en'] ?? '');
        $badge_ar = trim($_POST['badge_ar'] ?? '');
        $badge_en = trim($_POST['badge_en'] ?? '');
        $desc_ar = trim($_POST['description_ar'] ?? '');
        $desc_en = trim($_POST['description_en'] ?? '');
        $duration_ar = trim($_POST['duration_ar'] ?? '');
        $duration_en = trim($_POST['duration_en'] ?? '');
        $price_ar = trim($_POST['price_ar'] ?? '');
        $price_en = trim($_POST['price_en'] ?? '');
        $features_json = trim($_POST['features_json'] ?? '[]');
        $image_url = trim($_POST['image_url'] ?? '');
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        $is_published = isset($_POST['is_published']) ? 1 : 0;

        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
            $newFileName = 'crs_' . time() . '_' . rand(100,999) . '.' . strtolower($ext);
            $uploadDir = __DIR__ . '/../assets/images/';
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadDir . $newFileName)) {
                $image_url = 'assets/images/' . $newFileName;
            }
        }

        if ($id > 0) {
            $stmt = $db->prepare("UPDATE courses SET title_ar=?, title_en=?, badge_ar=?, badge_en=?, description_ar=?, description_en=?, duration_ar=?, duration_en=?, price_ar=?, price_en=?, features_json=?, image_url=?, sort_order=?, is_published=? WHERE id=?");
            $stmt->execute([$title_ar, $title_en, $badge_ar, $badge_en, $desc_ar, $desc_en, $duration_ar, $duration_en, $price_ar, $price_en, $features_json, $image_url, $sort_order, $is_published, $id]);
            $msg = 'تم تحديث الكورس بنجاح! 🎓';
        } else {
            $stmt = $db->prepare("INSERT INTO courses (title_ar, title_en, badge_ar, badge_en, description_ar, description_en, duration_ar, duration_en, price_ar, price_en, features_json, image_url, sort_order, is_published) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$title_ar, $title_en, $badge_ar, $badge_en, $desc_ar, $desc_en, $duration_ar, $duration_en, $price_ar, $price_en, $features_json, $image_url, $sort_order, $is_published]);
            $msg = 'تمت إضافة الكورس الجديد بنجاح! 🚀';
        }
    } elseif ($action === 'delete_course') {
        $id = (int)$_POST['course_id'];
        $stmt = $db->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        $msg = 'تم حذف الكورس بنجاح.';
    }
}

$courses = $db->query("SELECT * FROM courses ORDER BY sort_order ASC, id ASC")->fetchAll();

$editCrs = null;
if (isset($_GET['edit_id'])) {
    $editId = (int)$_GET['edit_id'];
    $stmtEdit = $db->prepare("SELECT * FROM courses WHERE id = ?");
    $stmtEdit->execute([$editId]);
    $editCrs = $stmtEdit->fetch();
}
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.8rem;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 900; color: #FFF; margin-bottom: 4px;">إدارة الكورسات والماستر كلاس الاحترافي</h1>
        <p style="color: var(--admin-text-muted); font-size: 0.92rem;">التحكم الكامل في البرامج التدريبية، المدة، المحاور والسعر.</p>
    </div>
    <a href="courses.php#form_section" class="btn-action">➕ إضافة كورس جديد</a>
</div>

<?php if (!empty($msg)): ?>
    <div class="alert-box alert-<?php echo $msgType; ?>"><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>

<!-- Form Section -->
<div class="glass-card" id="form_section">
    <div class="card-header-flex">
        <h2 class="card-title">
            <i class="fas fa-graduation-cap"></i> 
            <?php echo $editCrs ? 'تعديل الكورس #' . $editCrs['id'] : 'إضافة كورس / ماستر كلاس جديد'; ?>
        </h2>
        <?php if ($editCrs): ?>
            <a href="courses.php" class="btn-secondary-action">إلغاء التعديل ✖️</a>
        <?php endif; ?>
    </div>

    <form method="POST" action="courses.php" enctype="multipart/form-data">
        <input type="hidden" name="action_type" value="save_course">
        <input type="hidden" name="course_id" value="<?php echo $editCrs['id'] ?? 0; ?>">

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">عنوان الكورس (عربي) *</label>
                <input type="text" name="title_ar" class="form-input" required value="<?php echo htmlspecialchars($editCrs['title_ar'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">عنوان الكورس (إنجليزي)</label>
                <input type="text" name="title_en" class="form-input" value="<?php echo htmlspecialchars($editCrs['title_en'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">شارة الكورس (عربي)</label>
                <input type="text" name="badge_ar" class="form-input" placeholder="🏆 برنامج قيادي" value="<?php echo htmlspecialchars($editCrs['badge_ar'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">شارة الكورس (إنجليزي)</label>
                <input type="text" name="badge_en" class="form-input" placeholder="🏆 Leadership Program" value="<?php echo htmlspecialchars($editCrs['badge_en'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">المدة الزمنية (عربي)</label>
                <input type="text" name="duration_ar" class="form-input" placeholder="4 أسابيع (16 ساعة)" value="<?php echo htmlspecialchars($editCrs['duration_ar'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">السعر (عربي)</label>
                <input type="text" name="price_ar" class="form-input" placeholder="1,499 ﷼" value="<?php echo htmlspecialchars($editCrs['price_ar'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">وصف الكورس (عربي)</label>
                <textarea name="description_ar" class="form-textarea" rows="2"><?php echo htmlspecialchars($editCrs['description_ar'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">محاور ومميزات الكورس (JSON Array)</label>
                <textarea name="features_json" class="form-textarea" rows="2" placeholder='["محور 1", "محور 2"]'><?php echo htmlspecialchars($editCrs['features_json'] ?? '[]'); ?></textarea>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">رابط غلاف الكورس الحالي</label>
                <input type="text" name="image_url" class="form-input" value="<?php echo htmlspecialchars($editCrs['image_url'] ?? 'assets/images/'); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">أو رفع صورة غلاف جديدة</label>
                <input type="file" name="image_file" class="form-input" accept="image/*">
            </div>
            <div class="form-group">
                <label class="form-label">ترتيب الظهور (sort_order)</label>
                <input type="number" name="sort_order" class="form-input" value="<?php echo $editCrs['sort_order'] ?? count($courses) + 1; ?>">
            </div>
        </div>

        <div style="margin-bottom: 1.2rem; display:flex; align-items:center; gap:8px;">
            <input type="checkbox" name="is_published" id="is_published_cr" value="1" <?php echo (!isset($editCrs) || !empty($editCrs['is_published'])) ? 'checked' : ''; ?>>
            <label for="is_published_cr" style="font-size:0.9rem; font-weight:700; color:#FFF; cursor:pointer;">نشر الكورس في صفحة الكورسات المباشرة للموقع</label>
        </div>

        <button type="submit" class="btn-action">💾 <?php echo $editCrs ? 'حفظ التعديلات' : 'إضافة الكورس'; ?></button>
    </form>
</div>

<!-- List of Courses -->
<div class="glass-card">
    <div class="card-header-flex">
        <h2 class="card-title"><i class="fas fa-chalkboard-teacher"></i> قائمة الكورسات الحالية (<?php echo count($courses); ?>)</h2>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID / الترتيب</th>
                    <th>اسم الكورس</th>
                    <th>المدة</th>
                    <th>السعر</th>
                    <th>الحالة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $c): ?>
                    <tr>
                        <td>
                            <strong style="color: var(--admin-primary);">#<?php echo $c['id']; ?></strong>
                            <div style="font-size:0.75rem; color:var(--admin-text-muted);">ترتيب: <?php echo $c['sort_order']; ?></div>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($c['title_ar']); ?></strong>
                            <div style="font-size: 0.8rem; color: var(--admin-text-muted);"><?php echo htmlspecialchars($c['title_en']); ?></div>
                        </td>
                        <td><?php echo htmlspecialchars($c['duration_ar']); ?></td>
                        <td><strong style="color: #4ADE80;"><?php echo htmlspecialchars($c['price_ar']); ?></strong></td>
                        <td>
                            <?php if (!empty($c['is_published'])): ?>
                                <span style="color: #4ADE80; font-weight: 700; font-size: 0.82rem;">منشور 🟢</span>
                            <?php else: ?>
                                <span style="color: #F87171; font-weight: 700; font-size: 0.82rem;">مخفي 🔴</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display:flex; gap: 6px;">
                                <a href="courses.php?edit_id=<?php echo $c['id']; ?>#form_section" class="btn-secondary-action" style="padding: 4px 10px; font-size: 0.8rem;">تعديل ✏️</a>
                                <form method="POST" action="courses.php" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا الكورس؟');">
                                    <input type="hidden" name="action_type" value="delete_course">
                                    <input type="hidden" name="course_id" value="<?php echo $c['id']; ?>">
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
