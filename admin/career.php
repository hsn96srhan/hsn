<?php
require_once __DIR__ . '/includes/header.php';

$db = getDBConnection();
$msg = '';
$msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'])) {
    $action = $_POST['action_type'];

    if ($action === 'save_career') {
        $id = (int)($_POST['career_id'] ?? 0);
        $title_ar = trim($_POST['title_ar'] ?? '');
        $title_en = trim($_POST['title_en'] ?? '');
        $company_ar = trim($_POST['company_ar'] ?? '');
        $company_en = trim($_POST['company_en'] ?? '');
        $period_ar = trim($_POST['period_ar'] ?? '');
        $period_en = trim($_POST['period_en'] ?? '');
        $location_ar = trim($_POST['location_ar'] ?? '');
        $location_en = trim($_POST['location_en'] ?? '');
        $desc_ar = trim($_POST['description_ar'] ?? '');
        $desc_en = trim($_POST['description_en'] ?? '');
        $website_url = trim($_POST['website_url'] ?? '');
        $case_study_url = trim($_POST['case_study_url'] ?? '');
        $logo_url = trim($_POST['logo_url'] ?? '');
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        $is_published = isset($_POST['is_published']) ? 1 : 0;

        if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['logo_file']['name'], PATHINFO_EXTENSION);
            $newFileName = 'logo_' . time() . '_' . rand(100,999) . '.' . strtolower($ext);
            $uploadDir = __DIR__ . '/../assets/images/';
            if (move_uploaded_file($_FILES['logo_file']['tmp_name'], $uploadDir . $newFileName)) {
                $logo_url = 'assets/images/' . $newFileName;
            }
        }

        if ($id > 0) {
            $stmt = $db->prepare("UPDATE career_timeline SET title_ar=?, title_en=?, company_ar=?, company_en=?, period_ar=?, period_en=?, location_ar=?, location_en=?, description_ar=?, description_en=?, website_url=?, case_study_url=?, logo_url=?, sort_order=?, is_published=? WHERE id=?");
            $stmt->execute([$title_ar, $title_en, $company_ar, $company_en, $period_ar, $period_en, $location_ar, $location_en, $desc_ar, $desc_en, $website_url, $case_study_url, $logo_url, $sort_order, $is_published, $id]);
            $msg = 'تم تحديث بيانات المحطة المهنية بنجاح! 💼';
        } else {
            $stmt = $db->prepare("INSERT INTO career_timeline (title_ar, title_en, company_ar, company_en, period_ar, period_en, location_ar, location_en, description_ar, description_en, website_url, case_study_url, logo_url, sort_order, is_published) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$title_ar, $title_en, $company_ar, $company_en, $period_ar, $period_en, $location_ar, $location_en, $desc_ar, $desc_en, $website_url, $case_study_url, $logo_url, $sort_order, $is_published]);
            $msg = 'تمت إضافة المحطة المهنية الجديدة بنجاح! 🚀';
        }
    } elseif ($action === 'delete_career') {
        $id = (int)$_POST['career_id'];
        $stmt = $db->prepare("DELETE FROM career_timeline WHERE id = ?");
        $stmt->execute([$id]);
        $msg = 'تم حذف المحطة المهنية بنجاح.';
    }
}

$careers = $db->query("SELECT * FROM career_timeline ORDER BY sort_order ASC, id ASC")->fetchAll();

$editCareer = null;
if (isset($_GET['edit_id'])) {
    $editId = (int)$_GET['edit_id'];
    $stmtEdit = $db->prepare("SELECT * FROM career_timeline WHERE id = ?");
    $stmtEdit->execute([$editId]);
    $editCareer = $stmtEdit->fetch();
}
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.8rem;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 900; color: #FFF; margin-bottom: 4px;">إدارة السيرة والمسار الوظيفي</h1>
        <p style="color: var(--admin-text-muted); font-size: 0.92rem;">التحكم الكامل والترتيب الفوري لمحطات الخبرات القيادية والشركات.</p>
    </div>
    <a href="career.php#form_section" class="btn-action">➕ إضافة محطة مهنية جديدة</a>
</div>

<?php if (!empty($msg)): ?>
    <div class="alert-box alert-<?php echo $msgType; ?>"><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>

<!-- Add / Edit Form -->
<div class="glass-card" id="form_section">
    <div class="card-header-flex">
        <h2 class="card-title">
            <i class="fas fa-briefcase"></i> 
            <?php echo $editCareer ? 'تعديل المحطة المهنية #' . $editCareer['id'] : 'إضافة خبرة ومحطة وظيفية جديدة'; ?>
        </h2>
        <?php if ($editCareer): ?>
            <a href="career.php" class="btn-secondary-action">إلغاء التعديل ✖️</a>
        <?php endif; ?>
    </div>

    <form method="POST" action="career.php" enctype="multipart/form-data">
        <input type="hidden" name="action_type" value="save_career">
        <input type="hidden" name="career_id" value="<?php echo $editCareer['id'] ?? 0; ?>">

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">المسمى الوظيفي (عربي) *</label>
                <input type="text" name="title_ar" class="form-input" required value="<?php echo htmlspecialchars($editCareer['title_ar'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">المسمى الوظيفي (إنجليزي)</label>
                <input type="text" name="title_en" class="form-input" value="<?php echo htmlspecialchars($editCareer['title_en'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">اسم الشركة / الجهة (عربي) *</label>
                <input type="text" name="company_ar" class="form-input" required value="<?php echo htmlspecialchars($editCareer['company_ar'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">اسم الشركة / الجهة (إنجليزي)</label>
                <input type="text" name="company_en" class="form-input" value="<?php echo htmlspecialchars($editCareer['company_en'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">الفترة الزمنية (عربي)</label>
                <input type="text" name="period_ar" class="form-input" placeholder="2025 - الحالي" value="<?php echo htmlspecialchars($editCareer['period_ar'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">الفترة الزمنية (إنجليزي)</label>
                <input type="text" name="period_en" class="form-input" placeholder="2025 - Present" value="<?php echo htmlspecialchars($editCareer['period_en'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">الموقع الجغرافي (عربي)</label>
                <input type="text" name="location_ar" class="form-input" placeholder="الرياض، المملكة العربية السعودية" value="<?php echo htmlspecialchars($editCareer['location_ar'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">الموقع الجغرافي (إنجليزي)</label>
                <input type="text" name="location_en" class="form-input" placeholder="Riyadh, Saudi Arabia" value="<?php echo htmlspecialchars($editCareer['location_en'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">رابط الموقع الرسمي للشركة</label>
                <input type="url" name="website_url" class="form-input" placeholder="https://fluxads.sa" value="<?php echo htmlspecialchars($editCareer['website_url'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">رابط دراسة الحالة بالمعرض</label>
                <input type="text" name="case_study_url" class="form-input" placeholder="portfolio.php#flux" value="<?php echo htmlspecialchars($editCareer['case_study_url'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">رابط شعار الشركة الحالي</label>
                <input type="text" name="logo_url" class="form-input" value="<?php echo htmlspecialchars($editCareer['logo_url'] ?? 'assets/images/'); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">أو رفع شعار جديد من الجهاز</label>
                <input type="file" name="logo_file" class="form-input" accept="image/*">
            </div>
            <div class="form-group">
                <label class="form-label">ترتيب الظهور (sort_order)</label>
                <input type="number" name="sort_order" class="form-input" value="<?php echo $editCareer['sort_order'] ?? count($careers) + 1; ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">وصف الإنجازات والمسؤوليات (عربي)</label>
                <textarea name="description_ar" class="form-textarea" rows="3"><?php echo htmlspecialchars($editCareer['description_ar'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">وصف الإنجازات والمسؤوليات (إنجليزي)</label>
                <textarea name="description_en" class="form-textarea" rows="3"><?php echo htmlspecialchars($editCareer['description_en'] ?? ''); ?></textarea>
            </div>
        </div>

        <div style="margin-bottom: 1.2rem; display:flex; align-items:center; gap:8px;">
            <input type="checkbox" name="is_published" id="is_published_c" value="1" <?php echo (!isset($editCareer) || !empty($editCareer['is_published'])) ? 'checked' : ''; ?>>
            <label for="is_published_c" style="font-size:0.9rem; font-weight:700; color:#FFF; cursor:pointer;">إظهار هذه المحطة الوظيفية في الصفحة المباشرة</label>
        </div>

        <button type="submit" class="btn-action">💾 <?php echo $editCareer ? 'حفظ التعديلات' : 'إضافة المحطة المهنية'; ?></button>
    </form>
</div>

<!-- List of Careers -->
<div class="glass-card">
    <div class="card-header-flex">
        <h2 class="card-title"><i class="fas fa-history"></i> محطات المسار المهني الحالية (<?php echo count($careers); ?>)</h2>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID / الترتيب</th>
                    <th>الشعار</th>
                    <th>الشركة / الجهة</th>
                    <th>المسمى الوظيفي</th>
                    <th>الفترة</th>
                    <th>الحالة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($careers as $c): ?>
                    <tr>
                        <td>
                            <strong style="color: var(--admin-primary);">#<?php echo $c['id']; ?></strong>
                            <div style="font-size:0.75rem; color:var(--admin-text-muted);">ترتيب: <?php echo $c['sort_order']; ?></div>
                        </td>
                        <td>
                            <?php if (!empty($c['logo_url'])): ?>
                                <img src="../<?php echo htmlspecialchars($c['logo_url']); ?>" alt="" style="width: 42px; height: 42px; object-fit: contain; background: #FFF; border-radius: 8px; padding: 2px;">
                            <?php else: ?>
                                <span style="color: var(--admin-text-muted);">بدون شعار</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($c['company_ar']); ?></strong>
                            <div style="font-size: 0.8rem; color: var(--admin-text-muted);"><?php echo htmlspecialchars($c['company_en']); ?></div>
                        </td>
                        <td>
                            <span style="color: var(--admin-primary); font-weight: 700;"><?php echo htmlspecialchars($c['title_ar']); ?></span>
                        </td>
                        <td><?php echo htmlspecialchars($c['period_ar']); ?></td>
                        <td>
                            <?php if (!empty($c['is_published'])): ?>
                                <span style="color: #4ADE80; font-weight: 700; font-size: 0.82rem;">ظاهر 🟢</span>
                            <?php else: ?>
                                <span style="color: #F87171; font-weight: 700; font-size: 0.82rem;">مخفي 🔴</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display:flex; gap: 6px;">
                                <a href="career.php?edit_id=<?php echo $c['id']; ?>#form_section" class="btn-secondary-action" style="padding: 4px 10px; font-size: 0.8rem;">تعديل ✏️</a>
                                <form method="POST" action="career.php" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذه المحطة الوظيفية؟');">
                                    <input type="hidden" name="action_type" value="delete_career">
                                    <input type="hidden" name="career_id" value="<?php echo $c['id']; ?>">
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
