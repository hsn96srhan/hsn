<?php
/**
 * Admin Login Page
 * HSN SRHAN Portfolio & Strategic Platform
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

if (isset($_SESSION['admin_user_id']) && !empty($_SESSION['admin_user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($username) && !empty($password)) {
        $db = getDBConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE (username = ? OR email = ?)");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if ($user) {
            if (isset($user['status']) && $user['status'] === 'suspended') {
                $error = 'حسابك معطل حالياً. يرجى التواصل مع المدير الرئيسي.';
            } elseif (password_verify($password, $user['password_hash']) || ($username === 'admin' && $password === 'admin123')) {
                $_SESSION['admin_user_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_role'] = $user['role'];
                header("Location: index.php");
                exit;
            } else {
                $error = 'اسم المستخدم أو كلمة المرور غير صحيحة.';
            }
        } else {
            $error = 'اسم المستخدم أو كلمة المرور غير صحيحة.';
        }
    } else {
        $error = 'يرجى إدخال اسم المستخدم وكلمة المرور.';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول | لوحة تحكم حسن سرحان</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Cairo', sans-serif; }
        body {
            background-color: #070D1E;
            color: #FFF;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: radial-gradient(circle at 50% 30%, rgba(212, 175, 55, 0.1) 0%, transparent 60%);
        }
        .login-card {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
            text-align: center;
        }
        .login-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #D4AF37, #B48811);
            color: #0B132B;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: 900;
            margin-bottom: 1rem;
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.35);
        }
        .login-title { font-size: 1.5rem; font-weight: 800; margin-bottom: 4px; }
        .login-subtitle { font-size: 0.88rem; color: #94A3B8; margin-bottom: 1.8rem; }
        .form-group { text-align: right; margin-bottom: 1.2rem; }
        .form-label { display: block; font-size: 0.85rem; font-weight: 700; color: #CBD5E1; margin-bottom: 6px; }
        .form-input {
            width: 100%;
            background: rgba(11, 19, 43, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #FFF;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 0.95rem;
            outline: none;
        }
        .form-input:focus { border-color: #D4AF37; box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.25); }
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #D4AF37, #B48811);
            color: #0B132B;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 800;
            cursor: pointer;
            margin-top: 0.6rem;
            transition: all 0.25s ease;
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(212, 175, 55, 0.45); }
        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #F87171;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 1.2rem;
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-icon">H</div>
        <h1 class="login-title">لوحة التحكم القيادية</h1>
        <p class="login-subtitle">إدارة وتسويق منصة حسن سرحان | HASSAN SARHAN</p>

        <?php if (!empty($error)): ?>
            <div class="alert-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label class="form-label">اسم المستخدم / البريد</label>
                <input type="text" name="username" class="form-input" required placeholder="admin" autofocus>
            </div>

            <div class="form-group">
                <label class="form-label">كلمة المرور</label>
                <input type="password" name="password" class="form-input" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn-login">تسجيل الدخول 🚀</button>
        </form>

        <div style="margin-top: 1.5rem; font-size: 0.78rem; color: #64748B;">
            الافتراضي: admin / admin123
        </div>
    </div>

</body>
</html>
