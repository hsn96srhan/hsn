<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$db = getDBConnection();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_short') {
    $title = trim($_POST['title']);
    $videoId = trim($_POST['youtube_video_id']);
    $views = trim($_POST['views_count']);
    $likes = trim($_POST['likes_count']);
    $priority = (int)$_POST['priority'];

    $stmt = $db->prepare("INSERT INTO youtube_shorts (title, youtube_video_id, views_count, likes_count, priority) VALUES (:t, :vid, :v, :l, :prio)");
    $stmt->execute([
        ':t' => $title,
        ':vid' => $videoId,
        ':v' => $views,
        ':l' => $likes,
        ':prio' => $priority
    ]);
    $msg = 'تمت إضافة فيديو الريلز للكاروسيل بنجاح!';
}

if (isset($_GET['delete_id'])) {
    $delId = (int)$_GET['delete_id'];
    $stmt = $db->prepare("DELETE FROM youtube_shorts WHERE id = :id");
    $stmt->execute([':id' => $delId]);
    header("Location: shorts.php");
    exit;
}

$shorts = $db->query("SELECT * FROM youtube_shorts ORDER BY priority DESC, id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة ريلز يوتيوب | Dash</title>
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
    <h2>إدارة ريلز يوتيوب الشورتس (Highlights Feed)</h2>
    <a href="index.php" style="color:#0B132B; font-weight:700;">⬅ العودة للوحة التحكم</a>
</div>

<?php if (!empty($msg)): ?>
    <div style="background:#DCFCE7; color:#166534; padding:12px; border-radius:8px; margin-bottom:1rem; font-weight:700;">
        <?php echo $msg; ?>
    </div>
<?php endif; ?>

<div class="box">
    <h3>إضافة فيديو ريلز جديد للكاروسيل</h3>
    <form method="POST">
        <input type="hidden" name="action" value="save_short">
        <div style="display:grid; grid-template-columns: 2fr 1fr; gap:1rem;">
            <div class="input-group">
                <label>عنوان الفيديو</label>
                <input type="text" name="title" class="form-ctrl" required placeholder="مثال: استراتيجية ROAS مضاعفة">
            </div>
            <div class="input-group">
                <label>YouTube Video ID (معرّف الفيديو)</label>
                <input type="text" name="youtube_video_id" class="form-ctrl" required placeholder="مثال: dQw4w9WgXcQ">
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:1rem;">
            <div class="input-group">
                <label>عدد المشاهدات التقديري</label>
                <input type="text" name="views_count" class="form-ctrl" value="100K+">
            </div>
            <div class="input-group">
                <label>عدد الإعجابات التقديري</label>
                <input type="text" name="likes_count" class="form-ctrl" value="5.4K">
            </div>
            <div class="input-group">
                <label>رقم الأولوية في الكاروسيل</label>
                <input type="number" name="priority" class="form-ctrl" value="1">
            </div>
        </div>

        <button type="submit" class="btn-save">إضافة الفيديو للكاروسيل</button>
    </form>
</div>

<h3>فيديوهات الريلز الحالية</h3>
<table>
    <thead>
        <tr>
            <th>الأولوية</th>
            <th>العنوان</th>
            <th>Video ID</th>
            <th>المشاهدات</th>
            <th>التحكم</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($shorts as $s): ?>
            <tr>
                <td><strong><?php echo $s['priority']; ?></strong></td>
                <td><?php echo htmlspecialchars($s['title']); ?></td>
                <td><code><?php echo htmlspecialchars($s['youtube_video_id']); ?></code></td>
                <td><?php echo htmlspecialchars($s['views_count']); ?></td>
                <td>
                    <a href="?delete_id=<?php echo $s['id']; ?>" onclick="return confirm('حذف الفيديو؟');" style="color:#DC2626; font-weight:700;">حذف 🗑️</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
