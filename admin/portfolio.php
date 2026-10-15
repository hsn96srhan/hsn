<?php
require_once __DIR__ . '/includes/header.php';

$db = getDBConnection();
$msg = '';
$msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'])) {
    $action = $_POST['action_type'];

    if ($action === 'save_project') {
        $id = (int)($_POST['project_id'] ?? 0);
        $title_ar = trim($_POST['title_ar'] ?? '');
        $title_en = trim($_POST['title_en'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $client_name = trim($_POST['client_name'] ?? '');
        $sector = trim($_POST['sector'] ?? 'e-commerce');
        $problem_text_ar = trim($_POST['problem_text_ar'] ?? '');
        $strategy_text_ar = trim($_POST['strategy_text_ar'] ?? '');
        $results_json = trim($_POST['results_json'] ?? '{}');
        $client_logo_url = trim($_POST['client_logo_url'] ?? '');
        $priority = (int)($_POST['priority'] ?? 0);
        $is_published = isset($_POST['is_published']) ? 1 : 0;

        if (empty($slug)) {
            $slug = 'project-' . time();
        }

        if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['logo_file']['name'], PATHINFO_EXTENSION);
            $newFileName = 'prj_' . time() . '_' . rand(100,999) . '.' . strtolower($ext);
            $uploadDir = __DIR__ . '/../assets/images/';
            if (move_uploaded_file($_FILES['logo_file']['tmp_name'], $uploadDir . $newFileName)) {
                $client_logo_url = 'assets/images/' . $newFileName;
            }
        }

        if ($id > 0) {
            $stmt = $db->prepare("UPDATE projects SET title_ar=?, title_en=?, slug=?, client_name=?, client_logo_url=?, sector=?, problem_text_ar=?, strategy_text_ar=?, results_json=?, priority=?, is_published=? WHERE id=?");
            $stmt->execute([$title_ar, $title_en, $slug, $client_name, $client_logo_url, $sector, $problem_text_ar, $strategy_text_ar, $results_json, $priority, $is_published, $id]);
            $msg = 'تم تحديث دراسة الحالة بنجاح! 📂';
        } else {
            $stmt = $db->prepare("INSERT INTO projects (title_ar, title_en, slug, client_name, client_logo_url, sector, problem_text_ar, strategy_text_ar, results_json, priority, is_published) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$title_ar, $title_en, $slug, $client_name, $client_logo_url, $sector, $problem_text_ar, $strategy_text_ar, $results_json, $priority, $is_published]);
            $msg = 'تمت إضافة دراسة الحالة الجديدة بنجاح! 🎉';
        }
    } elseif ($action === 'delete_project') {
        $id = (int)$_POST['project_id'];
        $stmt = $db->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $msg = 'تم حذف المشروع بنجاح.';
    }
}

$projects = $db->query("SELECT * FROM projects ORDER BY priority DESC, id DESC")->fetchAll();

$editPrj = null;
if (isset($_GET['edit_id'])) {
    $editId = (int)$_GET['edit_id'];
    $stmtEdit = $db->prepare("SELECT * FROM projects WHERE id = ?");
    $stmtEdit->execute([$editId]);
    $editPrj = $stmtEdit->fetch();
}
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.8rem;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 900; color: #FFF; margin-bottom: 4px;">إدارة معرض الأعمال ودراسات الحالة (13 دراسة)</h1>
        <p style="color: var(--admin-text-muted); font-size: 0.92rem;">التحكم الكامل في تفاصيل المشاريع، الأرقام المحققة ROAS، الاستراتيجيات، وشعارات البراندات.</p>
    </div>
    <a href="portfolio.php#form_section" class="btn-action">➕ إضافة دراسة حالة جديدة</a>
</div>

<?php if (!empty($msg)): ?>
    <div class="alert-box alert-<?php echo $msgType; ?>"><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>

<!-- Form Section -->
<div class="glass-card" id="form_section">
    <div class="card-header-flex">
        <h2 class="card-title">
            <i class="fas fa-folder-plus"></i> 
            <?php echo $editPrj ? 'تعديل دراسة الحالة #' . $editPrj['id'] : 'إضافة مشروع / دراسة حالة جديدة'; ?>
        </h2>
        <?php if ($editPrj): ?>
            <a href="portfolio.php" class="btn-secondary-action">إلغاء التعديل ✖️</a>
        <?php endif; ?>
    </div>

    <form method="POST" action="portfolio.php" enctype="multipart/form-data">
        <input type="hidden" name="action_type" value="save_project">
        <input type="hidden" name="project_id" value="<?php echo $editPrj['id'] ?? 0; ?>">

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">عنوان المشروع (عربي) *</label>
                <input type="text" name="title_ar" class="form-input" required value="<?php echo htmlspecialchars($editPrj['title_ar'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">عنوان المشروع (إنجليزي)</label>
                <input type="text" name="title_en" class="form-input" value="<?php echo htmlspecialchars($editPrj['title_en'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">اسم العميل / العلامة التجارية *</label>
                <input type="text" name="client_name" class="form-input" required value="<?php echo htmlspecialchars($editPrj['client_name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">المعرف الفريد (Slug / Link Anchor) *</label>
                <input type="text" name="slug" class="form-input" required placeholder="arabiva-fragrances" value="<?php echo htmlspecialchars($editPrj['slug'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">القطاع (Sector)</label>
                <select name="sector" class="form-select">
                    <option value="e-commerce" <?php echo ($editPrj['sector'] ?? '') === 'e-commerce' ? 'selected' : ''; ?>>تجارة إلكترونية (E-Commerce)</option>
                    <option value="medical" <?php echo ($editPrj['sector'] ?? '') === 'medical' ? 'selected' : ''; ?>>قطاع طبي وعيادات (Medical)</option>
                    <option value="b2b_logistics" <?php echo ($editPrj['sector'] ?? '') === 'b2b_logistics' ? 'selected' : ''; ?>>خدمات وتطبيقات B2B/Logistics</option>
                </select>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">التحدي والمشكلة (Problem Statement)</label>
                <textarea name="problem_text_ar" class="form-textarea" rows="2"><?php echo htmlspecialchars($editPrj['problem_text_ar'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">الاستراتيجية والحل (Execution Strategy)</label>
                <textarea name="strategy_text_ar" class="form-textarea" rows="2"><?php echo htmlspecialchars($editPrj['strategy_text_ar'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">نتائج الإنجاز (JSON Format)</label>
                <textarea name="results_json" class="form-textarea" rows="2" placeholder='{"ROAS": "25x", "Growth": "+65%"}'><?php echo htmlspecialchars($editPrj['results_json'] ?? '{"ROAS": "10x"}'); ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">رابط شعار العميل</label>
                <input type="text" name="client_logo_url" class="form-input" value="<?php echo htmlspecialchars($editPrj['client_logo_url'] ?? 'assets/images/'); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">أو رفع شعار العميل</label>
                <input type="file" name="logo_file" class="form-input" accept="image/*">
            </div>
            <div class="form-group">
                <label class="form-label">الأولوية والترتيب (Priority)</label>
                <input type="number" name="priority" class="form-input" value="<?php echo $editPrj['priority'] ?? 10; ?>">
            </div>
        </div>

        <div style="margin-bottom: 1.2rem; display:flex; align-items:center; gap:8px;">
            <input type="checkbox" name="is_published" id="is_published_p" value="1" <?php echo (!isset($editPrj) || !empty($editPrj['is_published'])) ? 'checked' : ''; ?>>
            <label for="is_published_p" style="font-size:0.9rem; font-weight:700; color:#FFF; cursor:pointer;">نشر دراسة الحالة في المعرض الحي للموقع</label>
        </div>

        <button type="submit" class="btn-action">💾 <?php echo $editPrj ? 'حفظ التعديلات' : 'إضافة دراسة الحالة'; ?></button>
    </form>
</div>

<!-- Project Table -->
<div class="glass-card">
    <div class="card-header-flex">
        <h2 class="card-title"><i class="fas fa-tasks"></i> قائمة دراسات الحالة الحالية (<?php echo count($projects); ?>)</h2>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID / الأولوية</th>
                    <th>العميل</th>
                    <th>عنوان دراسة الحالة</th>
                    <th>القطاع</th>
                    <th>النتائج المحققة</th>
                    <th>الحالة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $p): ?>
                    <tr>
                        <td>
                            <strong style="color: var(--admin-primary);">#<?php echo $p['id']; ?></strong>
                            <div style="font-size:0.75rem; color:var(--admin-text-muted);">أولوية: <?php echo $p['priority']; ?></div>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($p['client_name']); ?></strong>
                            <div style="font-size:0.75rem; color:var(--admin-text-muted);">Slug: <?php echo htmlspecialchars($p['slug']); ?></div>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($p['title_ar']); ?></strong>
                        </td>
                        <td>
                            <span style="background: rgba(56, 189, 248, 0.15); color: #38BDF8; padding: 4px 10px; border-radius: 12px; font-weight: 700; font-size: 0.8rem;">
                                <?php echo htmlspecialchars($p['sector']); ?>
                            </span>
                        </td>
                        <td>
                            <code style="font-size:0.78rem; color:#4ADE80;"><?php echo htmlspecialchars($p['results_json']); ?></code>
                        </td>
                        <td>
                            <?php if (!empty($p['is_published'])): ?>
                                <span style="color: #4ADE80; font-weight: 700; font-size: 0.82rem;">منشور 🟢</span>
                            <?php else: ?>
                                <span style="color: #F87171; font-weight: 700; font-size: 0.82rem;">مخفي 🔴</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display:flex; gap: 6px;">
                                <a href="portfolio.php?edit_id=<?php echo $p['id']; ?>#form_section" class="btn-secondary-action" style="padding: 4px 10px; font-size: 0.8rem;">تعديل ✏️</a>
                                <form method="POST" action="portfolio.php" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذه الدراسة؟');">
                                    <input type="hidden" name="action_type" value="delete_project">
                                    <input type="hidden" name="project_id" value="<?php echo $p['id']; ?>">
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
