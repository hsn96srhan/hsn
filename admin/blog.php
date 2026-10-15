<?php
require_once __DIR__ . '/includes/header.php';

$db = getDBConnection();
$msg = '';
$msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'])) {
    $action = $_POST['action_type'];

    if ($action === 'save_article') {
        $id = (int)($_POST['article_id'] ?? 0);
        $title_ar = trim($_POST['title_ar'] ?? '');
        $title_en = trim($_POST['title_en'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $content_ar = trim($_POST['content_ar'] ?? '');
        $content_en = trim($_POST['content_en'] ?? '');
        $linkedin_url = trim($_POST['linkedin_url'] ?? '');
        $og_title = trim($_POST['og_title'] ?? '');
        $og_description = trim($_POST['og_description'] ?? '');
        $og_image_url = trim($_POST['og_image_url'] ?? '');
        $priority = (int)($_POST['priority'] ?? 0);
        $is_published = isset($_POST['is_published']) ? 1 : 0;

        if (empty($slug)) {
            $slug = 'article-' . time();
        }

        if ($id > 0) {
            $stmt = $db->prepare("UPDATE articles SET title_ar=?, title_en=?, slug=?, content_ar=?, content_en=?, linkedin_url=?, og_title=?, og_description=?, og_image_url=?, priority=?, is_published=? WHERE id=?");
            $stmt->execute([$title_ar, $title_en, $slug, $content_ar, $content_en, $linkedin_url, $og_title, $og_description, $og_image_url, $priority, $is_published, $id]);
            $msg = 'تم تحديث المقال بنجاح! 📝';
        } else {
            $stmt = $db->prepare("INSERT INTO articles (title_ar, title_en, slug, content_ar, content_en, linkedin_url, og_title, og_description, og_image_url, priority, is_published) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$title_ar, $title_en, $slug, $content_ar, $content_en, $linkedin_url, $og_title, $og_description, $og_image_url, $priority, $is_published]);
            $msg = 'تم نشر المقال الجديد بنجاح! 🚀';
        }
    } elseif ($action === 'delete_article') {
        $id = (int)$_POST['article_id'];
        $stmt = $db->prepare("DELETE FROM articles WHERE id = ?");
        $stmt->execute([$id]);
        $msg = 'تم حذف المقال بنجاح.';
    }
}

$articles = $db->query("SELECT * FROM articles ORDER BY priority DESC, id DESC")->fetchAll();

$editArt = null;
if (isset($_GET['edit_id'])) {
    $editId = (int)$_GET['edit_id'];
    $stmtEdit = $db->prepare("SELECT * FROM articles WHERE id = ?");
    $stmtEdit->execute([$editId]);
    $editArt = $stmtEdit->fetch();
}
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.8rem;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 900; color: #FFF; margin-bottom: 4px;">إدارة المقالات والأفكار التسويقية CMS</h1>
        <p style="color: var(--admin-text-muted); font-size: 0.92rem;">كتابة وإدارة مقالات الـ SEO والنشر المباشر مع دعم روابط لينكدإن و Open Graph.</p>
    </div>
    <a href="blog.php#form_section" class="btn-action">✍️ إضافة مقال جديد</a>
</div>

<?php if (!empty($msg)): ?>
    <div class="alert-box alert-<?php echo $msgType; ?>"><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>

<!-- Form Section -->
<div class="glass-card" id="form_section">
    <div class="card-header-flex">
        <h2 class="card-title">
            <i class="fas fa-newspaper"></i> 
            <?php echo $editArt ? 'تعديل المقال #' . $editArt['id'] : 'كتابة مقال تسويقي جديد'; ?>
        </h2>
        <?php if ($editArt): ?>
            <a href="blog.php" class="btn-secondary-action">إلغاء التعديل ✖️</a>
        <?php endif; ?>
    </div>

    <form method="POST" action="blog.php">
        <input type="hidden" name="action_type" value="save_article">
        <input type="hidden" name="article_id" value="<?php echo $editArt['id'] ?? 0; ?>">

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">عنوان المقال (عربي) *</label>
                <input type="text" name="title_ar" class="form-input" required value="<?php echo htmlspecialchars($editArt['title_ar'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">عنوان المقال (إنجليزي)</label>
                <input type="text" name="title_en" class="form-input" value="<?php echo htmlspecialchars($editArt['title_en'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">المعرف الفريد (Slug) *</label>
                <input type="text" name="slug" class="form-input" required placeholder="cro-strategies-gcc" value="<?php echo htmlspecialchars($editArt['slug'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">رابط المنشور على LinkedIn</label>
                <input type="url" name="linkedin_url" class="form-input" placeholder="https://linkedin.com/in/hsn96srhan" value="<?php echo htmlspecialchars($editArt['linkedin_url'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">محتوى المقال الكامل (عربي) *</label>
            <textarea name="content_ar" class="form-textarea" rows="6" required><?php echo htmlspecialchars($editArt['content_ar'] ?? ''); ?></textarea>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">عنوان الـ SEO / OpenGraph Title</label>
                <input type="text" name="og_title" class="form-input" value="<?php echo htmlspecialchars($editArt['og_title'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">وصف الـ SEO / OpenGraph Description</label>
                <input type="text" name="og_description" class="form-input" value="<?php echo htmlspecialchars($editArt['og_description'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">الأولوية (Priority)</label>
                <input type="number" name="priority" class="form-input" value="<?php echo $editArt['priority'] ?? 0; ?>">
            </div>
        </div>

        <div style="margin-bottom: 1.2rem; display:flex; align-items:center; gap:8px;">
            <input type="checkbox" name="is_published" id="is_published_a" value="1" <?php echo (!isset($editArt) || !empty($editArt['is_published'])) ? 'checked' : ''; ?>>
            <label for="is_published_a" style="font-size:0.9rem; font-weight:700; color:#FFF; cursor:pointer;">نشر المقال على مدونة الموقع المباشرة</label>
        </div>

        <button type="submit" class="btn-action">💾 <?php echo $editArt ? 'حفظ التعديلات' : 'نشر المقال'; ?></button>
    </form>
</div>

<!-- Article List -->
<div class="glass-card">
    <div class="card-header-flex">
        <h2 class="card-title"><i class="fas fa-blog"></i> المقالات المنشورة والحالية (<?php echo count($articles); ?>)</h2>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>عنوان المقال</th>
                    <th>Slug</th>
                    <th>رابط LinkedIn</th>
                    <th>الحالة</th>
                    <th>تاريخ النشر</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $a): ?>
                    <tr>
                        <td><strong>#<?php echo $a['id']; ?></strong></td>
                        <td>
                            <strong><?php echo htmlspecialchars($a['title_ar']); ?></strong>
                        </td>
                        <td><code><?php echo htmlspecialchars($a['slug']); ?></code></td>
                        <td>
                            <?php if (!empty($a['linkedin_url'])): ?>
                                <a href="<?php echo htmlspecialchars($a['linkedin_url']); ?>" target="_blank" style="color: #38BDF8; font-weight: 700;">لينكدإن ↗</a>
                            <?php else: ?>
                                <span style="color:var(--admin-text-muted);">غير متوفر</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($a['is_published'])): ?>
                                <span style="color: #4ADE80; font-weight: 700; font-size: 0.82rem;">منشور 🟢</span>
                            <?php else: ?>
                                <span style="color: #F87171; font-weight: 700; font-size: 0.82rem;">مسودة 🔴</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size: 0.8rem; color: var(--admin-text-muted);"><?php echo $a['created_at']; ?></td>
                        <td>
                            <div style="display:flex; gap: 6px;">
                                <a href="blog.php?edit_id=<?php echo $a['id']; ?>#form_section" class="btn-secondary-action" style="padding: 4px 10px; font-size: 0.8rem;">تعديل ✏️</a>
                                <form method="POST" action="blog.php" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا المقال؟');">
                                    <input type="hidden" name="action_type" value="delete_article">
                                    <input type="hidden" name="article_id" value="<?php echo $a['id']; ?>">
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
