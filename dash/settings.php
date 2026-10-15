<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    die("صلاحية الوصول مقتصرة على مدير النظام فقط.");
}

$db = getDBConnection();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_settings') {
    $siteTitleAr = trim($_POST['site_title_ar']);
    $siteTitleEn = trim($_POST['site_title_en']);
    $coursesEnabled = isset($_POST['courses_page_enabled']) ? 1 : 0;
    $maxBookings = (int)$_POST['max_daily_bookings'];

    $stmt = $db->prepare("UPDATE site_settings SET site_title_ar = :sta, site_title_en = :ste, courses_page_enabled = :cpe, max_daily_bookings = :mdb WHERE id = 1");
    $stmt->execute([
        ':sta' => $siteTitleAr,
        ':ste' => $siteTitleEn,
        ':cpe' => $coursesEnabled,
        ':mdb' => $maxBookings
    ]);
    $msg = 'تم حفظ الإعدادات وحالة القوائم بنجاح!';
}

$settings = getSiteSettings();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إعدادات القوائم والموقع | Dash</title>
    <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Readex Pro', sans-serif; background: #F8FAFC; padding: 2rem; }
        .box { background: #FFF; border: 1px solid #E2E8F0; padding: 2rem; border-radius: 12px; }
        .input-group { margin-bottom: 1.2rem; }
        .input-group label { display: block; font-weight: 600; margin-bottom: 4px; }
        .form-ctrl { width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px; box-sizing: border-box; }
        .btn-save { background: #0B132B; color: #FFF; padding: 10px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 700; }
    </style>
</head>
<body>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <h2>إعدادات القوائم وإخفاء الصفحات</h2>
    <a href="index.php" style="color:#0B132B; font-weight:700;">⬅ العودة للوحة التحكم</a>
</div>

<?php if (!empty($msg)): ?>
    <div style="background:#DCFCE7; color:#166534; padding:12px; border-radius:8px; margin-bottom:1rem; font-weight:700;">
        <?php echo $msg; ?>
    </div>
<?php endif; ?>

<div class="box">
    <form method="POST">
        <input type="hidden" name="action" value="save_settings">

        <div class="input-group">
            <label>عنوان الموقع (بالعربية)</label>
            <input type="text" name="site_title_ar" class="form-ctrl" value="<?php echo htmlspecialchars($settings['site_title_ar'] ?? ''); ?>">
        </div>

        <div class="input-group">
            <label>Site Title (English)</label>
            <input type="text" name="site_title_en" class="form-ctrl" value="<?php echo htmlspecialchars($settings['site_title_en'] ?? ''); ?>">
        </div>

        <div class="input-group">
            <label>الحد الأقصى للمواعيد اليومية في الرزنامة</label>
            <input type="number" name="max_daily_bookings" class="form-ctrl" value="<?php echo (int)($settings['max_daily_bookings'] ?? 3); ?>">
        </div>

        <div class="input-group" style="margin-top:1.5rem;">
            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                <input type="checkbox" name="courses_page_enabled" value="1" <?php echo !empty($settings['courses_page_enabled']) ? 'checked' : ''; ?> style="width:20px; height:20px;">
                <span>إظهار صفحة "الكورسات" في القائمة الرئيسية للموقع (مفتاح الإظهار / الإخفاء الديناميكي)</span>
            </label>
        </div>

        <button type="submit" class="btn-save" style="margin-top:1.5rem;">حفظ الإعدادات</button>
    </form>
</div>

</body>
</html>
