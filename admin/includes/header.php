<?php
require_once __DIR__ . '/auth.php';
requireAuth();
$currentUser = getCurrentUser();
$db = getDBConnection();

// Fetch unverified pending bank orders count
$pendingOrdersCount = 0;
try {
    $stmtCount = $db->query("SELECT COUNT(*) FROM bank_orders WHERE status = 'pending'");
    $pendingOrdersCount = (int)$stmtCount->fetchColumn();
} catch (Exception $e) {}

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم والقيادة الاستراتيجية | حسن سرحان</title>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --admin-bg: #070D1E;
            --admin-card-bg: rgba(15, 23, 42, 0.75);
            --admin-border: rgba(255, 255, 255, 0.12);
            --admin-primary: #D4AF37; /* Luxury Gold */
            --admin-navy: #0B132B;
            --admin-accent: #38BDF8;
            --admin-text: #F8FAFC;
            --admin-text-muted: #94A3B8;
            --admin-success: #22C55E;
            --admin-danger: #EF4444;
            --admin-warning: #F59E0B;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', system-ui, -apple-system, sans-serif;
        }

        body {
            background-color: var(--admin-bg);
            color: var(--admin-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: 
                radial-gradient(circle at 15% 15%, rgba(212, 175, 55, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 85% 85%, rgba(56, 189, 248, 0.05) 0%, transparent 40%);
        }

        /* Top Navbar */
        .admin-topbar {
            height: 70px;
            background: rgba(11, 19, 43, 0.9);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--admin-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .admin-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #FFF;
        }

        .admin-brand-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #D4AF37, #B48811);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            color: #0B132B;
            font-size: 1.2rem;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }

        .admin-brand-title {
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .admin-user-menu {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .badge-role {
            background: rgba(212, 175, 55, 0.15);
            color: var(--admin-primary);
            border: 1px solid rgba(212, 175, 55, 0.3);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .btn-live-site {
            background: rgba(255, 255, 255, 0.08);
            color: #FFF;
            border: 1px solid var(--admin-border);
            padding: 6px 14px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 700;
            transition: all 0.2s ease;
        }

        .btn-live-site:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: #FFF;
        }

        .btn-logout {
            background: rgba(239, 68, 68, 0.15);
            color: #EF4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
            padding: 6px 14px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 700;
            transition: all 0.2s ease;
        }

        .btn-logout:hover {
            background: #EF4444;
            color: #FFF;
        }

        /* Layout Container */
        .admin-wrapper {
            display: flex;
            flex: 1;
        }

        /* Sidebar Navigation */
        .admin-sidebar {
            width: 260px;
            background: rgba(11, 19, 43, 0.6);
            backdrop-filter: blur(12px);
            border-left: 1px solid var(--admin-border);
            padding: 1.5rem 1rem;
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex-shrink: 0;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            color: var(--admin-text-muted);
            text-decoration: none;
            border-radius: 10px;
            font-size: 0.92rem;
            font-weight: 700;
            transition: all 0.25s ease;
            position: relative;
        }

        .sidebar-link i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .sidebar-link:hover {
            color: #FFF;
            background: rgba(255, 255, 255, 0.06);
        }

        .sidebar-link.active {
            color: #0B132B;
            background: linear-gradient(135deg, #D4AF37, #E5C158);
            font-weight: 800;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.25);
        }

        .sidebar-badge {
            background: var(--admin-danger);
            color: #FFF;
            font-size: 0.72rem;
            padding: 2px 7px;
            border-radius: 10px;
            margin-right: auto;
            font-weight: 800;
        }

        /* Main Content */
        .admin-content {
            flex: 1;
            padding: 2rem;
            max-width: 1300px;
            width: 100%;
        }

        /* UI Component Styles */
        .glass-card {
            background: var(--admin-card-bg);
            backdrop-filter: blur(16px);
            border: 1px solid var(--admin-border);
            border-radius: 16px;
            padding: 1.8rem;
            margin-bottom: 1.8rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.37);
        }

        .card-header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.4rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            padding-bottom: 1rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: #FFF;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title i {
            color: var(--admin-primary);
        }

        /* Form Controls */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.2rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 1.2rem;
        }

        .form-label {
            font-size: 0.88rem;
            font-weight: 700;
            color: var(--admin-text-muted);
        }

        .form-input, .form-select, .form-textarea {
            background: rgba(15, 23, 42, 0.9);
            border: 1px solid var(--admin-border);
            color: #FFF;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 0.92rem;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
        }

        .btn-action {
            background: linear-gradient(135deg, #D4AF37, #B48811);
            color: #0B132B;
            border: none;
            padding: 10px 22px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 0.92rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.45);
        }

        .btn-secondary-action {
            background: rgba(255, 255, 255, 0.1);
            color: #FFF;
            border: 1px solid var(--admin-border);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-danger-action {
            background: rgba(239, 68, 68, 0.2);
            color: #EF4444;
            border: 1px solid rgba(239, 68, 68, 0.4);
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-danger-action:hover {
            background: #EF4444;
            color: #FFF;
        }

        /* Data Tables */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            text-align: right;
            font-size: 0.9rem;
        }

        .admin-table th {
            background: rgba(255, 255, 255, 0.05);
            color: var(--admin-primary);
            padding: 12px 14px;
            font-weight: 800;
            border-bottom: 1px solid var(--admin-border);
        }

        .admin-table td {
            padding: 12px 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: var(--admin-text);
            vertical-align: middle;
        }

        .admin-table tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        .alert-box {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid rgba(34, 197, 94, 0.4);
            color: #4ADE80;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #F87171;
        }
    </style>
</head>
<body>

    <!-- Top Navigation Bar -->
    <header class="admin-topbar">
        <a href="index.php" class="admin-brand">
            <div class="admin-brand-icon">H</div>
            <div>
                <div class="admin-brand-title">لوحة التحكم القيادية</div>
                <div style="font-size:0.75rem; color:var(--admin-primary);">حسن سرحان | HSSN-SRHAN</div>
            </div>
        </a>

        <div class="admin-user-menu">
            <a href="../index.php" target="_blank" class="btn-live-site">
                <i class="fas fa-external-link-alt"></i> المعاينة الحية للموقع
            </a>
            <span class="badge-role">
                <i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($currentUser['username'] ?? 'مستخدم'); ?> (<?php echo htmlspecialchars($currentUser['role'] ?? 'editor'); ?>)
            </span>
            <a href="logout.php" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> خروج
            </a>
        </div>
    </header>

    <div class="admin-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar">
            <a href="index.php" class="sidebar-link <?php echo $currentPage === 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> لوحة الأداء العام
            </a>
            <a href="home.php" class="sidebar-link <?php echo $currentPage === 'home.php' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> الرئيسية والشورتس
            </a>
            <a href="qualifications.php" class="sidebar-link <?php echo $currentPage === 'qualifications.php' ? 'active' : ''; ?>">
                <i class="fas fa-award"></i> المؤهلات (18 شهادة)
            </a>
            <a href="career.php" class="sidebar-link <?php echo $currentPage === 'career.php' ? 'active' : ''; ?>">
                <i class="fas fa-briefcase"></i> السيرة المهنية
            </a>
            <a href="portfolio.php" class="sidebar-link <?php echo $currentPage === 'portfolio.php' ? 'active' : ''; ?>">
                <i class="fas fa-folder-open"></i> معرض الأعمال (13 حالة)
            </a>
            <a href="services.php" class="sidebar-link <?php echo $currentPage === 'services.php' ? 'active' : ''; ?>">
                <i class="fas fa-concierge-bell"></i> الخدمات والحلول
            </a>
            <a href="courses.php" class="sidebar-link <?php echo $currentPage === 'courses.php' ? 'active' : ''; ?>">
                <i class="fas fa-graduation-cap"></i> الكورسات والماستر كلاس
            </a>
            <a href="digital-products.php" class="sidebar-link <?php echo $currentPage === 'digital-products.php' ? 'active' : ''; ?>">
                <i class="fas fa-credit-card"></i> المنتجات الرقمية وإيصالات D360
                <?php if ($pendingOrdersCount > 0): ?>
                    <span class="sidebar-badge"><?php echo $pendingOrdersCount; ?></span>
                <?php endif; ?>
            </a>
            <a href="blog.php" class="sidebar-link <?php echo $currentPage === 'blog.php' ? 'active' : ''; ?>">
                <i class="fas fa-blog"></i> المقالات والـ SEO
            </a>
            <a href="contact-settings.php" class="sidebar-link <?php echo $currentPage === 'contact-settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-headset"></i> التواصل والحجوزات
            </a>
            <a href="landing-pages.php" class="sidebar-link <?php echo $currentPage === 'landing-pages.php' ? 'active' : ''; ?>">
                <i class="fas fa-layer-group"></i> لاندنج بيدج بيلدر
            </a>
            <a href="users.php" class="sidebar-link <?php echo $currentPage === 'users.php' ? 'active' : ''; ?>">
                <i class="fas fa-users-cog"></i> المساعدون والصلاحيات
            </a>
        </aside>

        <!-- Main Workspace -->
        <main class="admin-content">
