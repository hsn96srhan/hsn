<?php
/**
 * Admin & SEO Control Panel Subdomain Dashboard (dash.domain.com)
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

$db = getDBConnection();

$loginError = '';

// Handle Login
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $user = trim($_POST['username'] ?? '');
    $pass = trim($_POST['password'] ?? '');

    $stmt = $db->prepare("SELECT * FROM users WHERE username = :u OR email = :u");
    $stmt->execute([':u' => $user]);
    $userData = $stmt->fetch();

    if ($userData && password_verify($pass, $userData['password_hash'])) {
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['username'] = $userData['username'];
        $_SESSION['role'] = $userData['role'];
        header("Location: index.php");
        exit;
    } else {
        $loginError = 'اسم المستخدم أو كلمة المرور غير صحيحة!';
    }
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

$isLoggedIn = isset($_SESSION['user_id']);
$userRole = $_SESSION['role'] ?? 'seo';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم | حسن سرحان - HASSAN SARHAN</title>
    <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Readex Pro', sans-serif; background: #F8FAFC; color: #0B132B; margin: 0; padding: 0; }
        .dash-header { background: #0B132B; color: #FFF; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .dash-container { max-width: 1100px; margin: 2rem auto; padding: 0 1rem; }
        .dash-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem; margin-top: 1.5rem; }
        .dash-card { background: #FFF; border: 1px solid #E2E8F0; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(11,19,43,0.04); text-decoration: none; color: inherit; transition: transform 0.2s; }
        .dash-card:hover { transform: translateY(-4px); border-color: #0B132B; }
        .dash-card h3 { color: #0B132B; margin-top: 0; }
        .login-box { max-width: 400px; margin: 5rem auto; background: #FFF; border: 1px solid #E2E8F0; padding: 2.5rem; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .btn-dash { background: #0B132B; color: #FFF; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-family: inherit; font-weight: 700; width: 100%; margin-top: 1rem; }
        .form-input { width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px; margin-top: 0.5rem; box-sizing: border-box; }
        .role-badge { background: rgba(255,255,255,0.2); padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; }
    </style>
</head>
<body>

<?php if (!$isLoggedIn): ?>
    <div class="login-box">
        <h2 style="text-align:center; color:#0B132B; margin-top:0;">تسجيل الدخول للوحة التحكم</h2>
        <p style="text-align:center; color:#64748B; font-size:0.9rem;">Subdomain: ad.hsn.agency</p>

        <?php if (!empty($loginError)): ?>
            <div style="background:#FEE2E2; color:#991B1B; padding:10px; border-radius:8px; margin-bottom:1rem; text-align:center;">
                <?php echo $loginError; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="action" value="login">
            <div style="margin-bottom:1rem;">
                <label>اسم المستخدم / البريد الإلكتروني</label>
                <input type="text" name="username" class="form-input" required placeholder="admin / seouser">
            </div>
            <div style="margin-bottom:1rem;">
                <label>كلمة المرور</label>
                <input type="password" name="password" class="form-input" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn-dash">تسجيل الدخول</button>
        </form>
    </div>
<?php else: ?>
    <div class="dash-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <img src="../icon.jpeg" style="height:36px; border-radius:4px;">
            <h2 style="margin:0; font-size:1.2rem;">لوحة إدارة B2B | حسن سرحان - HASSAN SARHAN</h2>
            <span class="role-badge">الدور: <?php echo strtoupper($userRole); ?></span>
        </div>
        <div>
            <a href="../index.php" target="_blank" style="color:#FFF; text-decoration:none; margin-left:15px;">🌐 معاينة الموقع</a>
            <a href="?logout=1" style="color:#FCA5A5; text-decoration:none;">خروج ➔</a>
        </div>
    </div>

    <div class="dash-container">
        <h2>مرحباً بك، <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p style="color:#64748B;">تحكم كامل بالموقع الديناميكي، المقالات، وسوم التتبع، وحجوزات المواعيد.</p>

        <div class="dash-grid">
            <a href="projects.php" class="dash-card">
                <h3>📂 إدارة سابقة الأعمال (Projects)</h3>
                <p>إضافة وتعديل دراسات الحالة والترتيب الرقمي بالأولوية.</p>
            </a>

            <a href="articles.php" class="dash-card">
                <h3>✍️ المقالات والـ SEO (Articles)</h3>
                <p>إدارة مقالات LinkedIn وضبط الـ Meta وصور OG للمشاركة.</p>
            </a>

            <a href="shorts.php" class="dash-card">
                <h3>📹 ريلز يوتيوب (Highlights Feed)</h3>
                <p>إضافة وإدارة فيديوهات الريلز القصيرة في الصفحة الرئيسية.</p>
            </a>

            <a href="bookings.php" class="dash-card">
                <h3>📅 حجوزات الرزنامة (Bookings)</h3>
                <p>استعراض المواعيد المحجوزة وتفاصيل العملاء المستهدفين.</p>
            </a>

            <?php if ($userRole === 'admin'): ?>
                <a href="scripts.php" class="dash-card">
                    <h3>⚙️ حقن أكواد التتبع (GTM / Scripts)</h3>
                    <p>إدخال وحقن أكواد Header و Body فورياً وبشكل ديناميكي.</p>
                </a>

                <a href="settings.php" class="dash-card">
                    <h3>🛠️ إعدادات الموقع وإخفاء الصفحات</h3>
                    <p>مفتاح إظهار/إخفاء صفحة الكورسات وإعدادات القوائم.</p>
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

</body>
</html>
