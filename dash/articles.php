<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$db = getDBConnection();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_article') {
    $titleAr = trim($_POST['title_ar']);
    $contentAr = trim($_POST['content_ar']);
    $linkedinUrl = trim($_POST['linkedin_url']);
    $ogTitle = trim($_POST['og_title']);
    $ogDesc = trim($_POST['og_description']);
    $priority = (int)$_POST['priority'];
    $slug = preg_replace('/[^a-z0-9-]+/i', '-', strtolower($titleAr)) . '-' . time();

    $stmt = $db->prepare("INSERT INTO articles (title_ar, title_en, slug, content_ar, linkedin_url, og_title, og_description, priority) VALUES (:ta, :ta, :slug, :ca, :lu, :ogt, :ogd, :prio)");
    $stmt->execute([
        ':ta' => $titleAr,
        ':slug' => $slug,
        ':ca' => $contentAr,
        ':lu' => $linkedinUrl,
        ':ogt' => $ogTitle,
        ':ogd' => $ogDesc,
        ':prio' => $priority
    ]);
    $msg = 'تم حفظ المقال وتخصيص الـ SEO والمشاركة بنجاح!';
}

if (isset($_GET['delete_id'])) {
    $delId = (int)$_GET['delete_id'];
    $stmt = $db->prepare("DELETE FROM articles WHERE id = :id");
    $stmt->execute([':id' => $delId]);
    header("Location: articles.php");
    exit;
}

$articles = $db->query("SELECT * FROM articles ORDER BY priority DESC, id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة المقالات والـ SEO | Dash</title>
    <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Readex Pro', sans-serif; background: #F8FAFC; padding: 2rem; }
        .box { background: #FFF; border: 1px solid #E2E8F0; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; }
        .input-group { margin-bottom: 1rem; }
        .input-group label { display: block; font-weight: 600; margin-bottom: 4px; }
        .form-ctrl { width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px; box-sizing: border-box; }
        .btn-save { background: #0B132B; color: #FFF; padding: 10px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 700; }
        table { width: 100%; border-collapse: collapse; background: #FFF; border-radius: 8px; overflow: hidden; }
        th, td { padding: 12px 16px; border-bottom: 1px solid #E2E8F0; text-align: right; }
        th { background: #0B132B; color: #FFF; }
    </style>
</head>
<body>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <h2>إدارة المقالات، LinkedIn Sync والـ SEO Meta</h2>
    <a href="index.php" style="color:#0B132B; font-weight:700;">⬅ العودة للوحة التحكم</a>
</div>

<?php if (!empty($msg)): ?>
    <div style="background:#DCFCE7; color:#166534; padding:12px; border-radius:8px; margin-bottom:1rem; font-weight:700;">
        <?php echo $msg; ?>
    </div>
<?php endif; ?>

<div class="box">
    <h3>إضافة مقال جديد وزر قراءة على LinkedIn</h3>
    <form method="POST">
        <input type="hidden" name="action" value="save_article">
        
        <div style="display:grid; grid-template-columns: 2fr 1fr; gap:1rem;">
            <div class="input-group">
                <label>عنوان المقال</label>
                <input type="text" name="title_ar" class="form-ctrl" required placeholder="عنوان المقال الأسبوعي">
            </div>
            <div class="input-group">
                <label>رقم أولوية الترتيب</label>
                <input type="number" name="priority" class="form-ctrl" value="1" required>
            </div>
        </div>

        <div class="input-group">
            <label>رابط المقال على LinkedIn (LinkedIn Sync Link)</label>
            <input type="url" name="linkedin_url" class="form-ctrl" placeholder="https://linkedin.com/in/hsn96srhan/article-xyz">
        </div>

        <div class="input-group">
            <label>محتوى المقال</label>
            <textarea name="content_ar" class="form-ctrl" rows="5" required></textarea>
        </div>

        <h4 style="color:#0B132B; margin-top:1.5rem;">إعدادات الـ SEO وكارت المشاركة Social Share Card</h4>

        <div class="input-group">
            <label>عنوان المشاركة (OG Title for WhatsApp & LinkedIn)</label>
            <input type="text" name="og_title" class="form-ctrl" placeholder="العنوان المحسن للمشاركة">
        </div>

        <div class="input-group">
            <label>وصف المشاركة (OG Description)</label>
            <textarea name="og_description" class="form-ctrl" rows="2" placeholder="وصف مقتضب وجذاب للمشاركة..."></textarea>
        </div>

        <button type="submit" class="btn-save">حفظ المقال وإعداده للمشاركة</button>
    </form>
</div>

<h3>المقالات الحالية</h3>
<table>
    <thead>
        <tr>
            <th>الأولوية</th>
            <th>العنوان</th>
            <th>رابط LinkedIn</th>
            <th>التحكم</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($articles as $a): ?>
            <tr>
                <td><strong><?php echo $a['priority']; ?></strong></td>
                <td><?php echo htmlspecialchars($a['title_ar']); ?></td>
                <td><a href="<?php echo htmlspecialchars($a['linkedin_url']); ?>" target="_blank" style="color:#2563EB;">رابط المقال ↗</a></td>
                <td>
                    <a href="?delete_id=<?php echo $a['id']; ?>" onclick="return confirm('حذف المقال؟');" style="color:#DC2626; font-weight:700;">حذف 🗑️</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
