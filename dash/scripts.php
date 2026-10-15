<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    die("صلاحية الوصول مقتصرة على مدير النظام (Admin) فقط.");
}

$db = getDBConnection();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_scripts') {
    $scriptHead = $_POST['script_header'] ?? '';
    $scriptBody = $_POST['script_body'] ?? '';

    $stmt = $db->prepare("UPDATE site_settings SET script_header = :sh, script_body = :sb WHERE id = 1");
    $stmt->execute([':sh' => $scriptHead, ':sb' => $scriptBody]);
    $msg = 'تم تحديث وحقن أكواد التتبع بنجاح في الموقع!';
}

$settings = getSiteSettings();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>حقن أكواد التتبع (GTM & Pixels) | Dash</title>
    <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Readex Pro', sans-serif; background: #F8FAFC; padding: 2rem; }
        .box { background: #FFF; border: 1px solid #E2E8F0; padding: 2rem; border-radius: 12px; }
        .form-ctrl { width: 100%; padding: 12px; border: 1px solid #CBD5E1; border-radius: 8px; font-family: monospace; font-size: 0.9rem; direction: ltr; box-sizing: border-box; }
        .btn-save { background: #0B132B; color: #FFF; padding: 12px 28px; border: none; border-radius: 8px; cursor: pointer; font-weight: 700; font-family: inherit; }
    </style>
</head>
<body>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <h2>حقن أكواد التتبع والإعلانات (Script Injection Engine)</h2>
    <a href="index.php" style="color:#0B132B; font-weight:700;">⬅ العودة للوحة التحكم</a>
</div>

<?php if (!empty($msg)): ?>
    <div style="background:#DCFCE7; color:#166534; padding:12px; border-radius:8px; margin-bottom:1rem; font-weight:700;">
        <?php echo $msg; ?>
    </div>
<?php endif; ?>

<div class="box">
    <form method="POST">
        <input type="hidden" name="action" value="save_scripts">

        <div style="margin-bottom:1.5rem;">
            <label style="display:block; font-weight:700; margin-bottom:0.5rem; color:#0B132B;">
                أكواد الهيدر (Header Scripts Injection - &lt;head&gt;):
            </label>
            <p style="font-size:0.85rem; color:#64748B; margin-top:0; margin-bottom:0.5rem;">
                ضع هنا كود Google Tag Manager (Head), Meta Pixel, Google Analytics, Snapchat Pixel.
            </p>
            <textarea name="script_header" class="form-ctrl" rows="8"><?php echo htmlspecialchars($settings['script_header'] ?? ''); ?></textarea>
        </div>

        <div style="margin-bottom:1.5rem;">
            <label style="display:block; font-weight:700; margin-bottom:0.5rem; color:#0B132B;">
                أكواد البودي (Body Scripts Injection - &lt;body&gt;):
            </label>
            <p style="font-size:0.85rem; color:#64748B; margin-top:0; margin-bottom:0.5rem;">
                ضع هنا كود &lt;noscript&gt; التابع لـ GTM أو أي بكسل تتبع ينفذ في جسم الصفحة.
            </p>
            <textarea name="script_body" class="form-ctrl" rows="8"><?php echo htmlspecialchars($settings['script_body'] ?? ''); ?></textarea>
        </div>

        <button type="submit" class="btn-save">حفظ وحقن الأكواد فوراً</button>
    </form>
</div>

</body>
</html>
