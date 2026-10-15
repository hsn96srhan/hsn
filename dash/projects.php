<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$db = getDBConnection();
$msg = '';

// Add / Update Project
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save_project') {
        $titleAr = trim($_POST['title_ar']);
        $clientName = trim($_POST['client_name']);
        $sector = trim($_POST['sector']);
        $problem = trim($_POST['problem_text_ar']);
        $strategy = trim($_POST['strategy_text_ar']);
        $resultsJson = trim($_POST['results_json']);
        $priority = (int)$_POST['priority'];
        $slug = preg_replace('/[^a-z0-9-]+/i', '-', strtolower($titleAr)) . '-' . time();

        $stmt = $db->prepare("INSERT INTO projects (title_ar, title_en, slug, client_name, sector, problem_text_ar, strategy_text_ar, results_json, priority) VALUES (:ta, :ta, :slug, :cn, :sec, :prob, :strat, :res, :prio)");
        $stmt->execute([
            ':ta' => $titleAr,
            ':slug' => $slug,
            ':cn' => $clientName,
            ':sec' => $sector,
            ':prob' => $problem,
            ':strat' => $strategy,
            ':res' => $resultsJson,
            ':prio' => $priority
        ]);
        $msg = 'تم حفظ المشروع بنجاح!';
    }
}

// Delete Project
if (isset($_GET['delete_id'])) {
    $delId = (int)$_GET['delete_id'];
    $stmt = $db->prepare("DELETE FROM projects WHERE id = :id");
    $stmt->execute([':id' => $delId]);
    header("Location: projects.php");
    exit;
}

$projects = $db->query("SELECT * FROM projects ORDER BY priority DESC, id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة سابقة الأعمال | Dash</title>
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
    <h2>إدارة سابقة الأعمال ودراسات الحالة (WOW Case Studies)</h2>
    <a href="index.php" style="color:#0B132B; font-weight:700;">⬅ العودة للوحة التحكم</a>
</div>

<?php if (!empty($msg)): ?>
    <div style="background:#DCFCE7; color:#166534; padding:12px; border-radius:8px; margin-bottom:1rem; font-weight:700;">
        <?php echo $msg; ?>
    </div>
<?php endif; ?>

<div class="box">
    <h3>إضافة مشروع / دراسة حالة جديدة</h3>
    <form method="POST">
        <input type="hidden" name="action" value="save_project">
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
            <div class="input-group">
                <label>عنوان المشروع</label>
                <input type="text" name="title_ar" class="form-ctrl" required placeholder="مثال: حملة التوسع لعطور أرابيفا">
            </div>
            <div class="input-group">
                <label>اسم العميل / العلامة التجارية</label>
                <input type="text" name="client_name" class="form-ctrl" required placeholder="عطور أرابيفا">
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
            <div class="input-group">
                <label>القطاع</label>
                <select name="sector" class="form-ctrl">
                    <option value="e-commerce">التجارة الإلكترونية والعطور (E-Commerce)</option>
                    <option value="medical">القطاع الطبي والعيادات (Medical)</option>
                    <option value="b2b_logistics">B2B والخدمات اللوجستية (Logistics)</option>
                </select>
            </div>
            <div class="input-group">
                <label>رقم أولوية الترتيب (الأرقام الأكبر تظهر في البداية)</label>
                <input type="number" name="priority" class="form-ctrl" value="1" required>
            </div>
        </div>

        <div class="input-group">
            <label>التحدي / الهدف</label>
            <textarea name="problem_text_ar" class="form-ctrl" rows="2" required></textarea>
        </div>

        <div class="input-group">
            <label>الاستراتيجية والتنفيذ</label>
            <textarea name="strategy_text_ar" class="form-ctrl" rows="2" required></textarea>
        </div>

        <div class="input-group">
            <label>النتائج بالأرقام (JSON Format)</label>
            <input type="text" name="results_json" class="form-ctrl" value='{"ROAS":"25x", "Growth":"+70%"}' required>
        </div>

        <button type="submit" class="btn-save">حفظ المشروع الفوري</button>
    </form>
</div>

<h3>المشاريع الحالية بالموقع</h3>
<table>
    <thead>
        <tr>
            <th>الأولوية</th>
            <th>عنوان المشروع</th>
            <th>العميل</th>
            <th>القطاع</th>
            <th>التحكم</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($projects as $p): ?>
            <tr>
                <td><strong><?php echo $p['priority']; ?></strong></td>
                <td><?php echo htmlspecialchars($p['title_ar']); ?></td>
                <td><?php echo htmlspecialchars($p['client_name']); ?></td>
                <td><?php echo htmlspecialchars($p['sector']); ?></td>
                <td>
                    <a href="?delete_id=<?php echo $p['id']; ?>" onclick="return confirm('هل أنت تأكد من حذف هذا المشروع؟');" style="color:#DC2626; font-weight:700;">حذف 🗑️</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
