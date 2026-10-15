<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

$settings = getSiteSettings();
$currentLang = SITE_LANG;
$otherLang = ($currentLang === 'ar') ? 'en' : 'ar';
$otherLangLabel = t('other_lang_label');

$siteTitle = ($currentLang === 'ar') 
    ? ($settings['site_title_ar'] ?? 'حسن سرحان | مدير تسويق')
    : ($settings['site_title_en'] ?? 'HASSAN SARHAN | Marketing Director');

$coursesEnabled = (int)($settings['courses_page_enabled'] ?? 1);
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="<?php echo $currentLang; ?>" dir="<?php echo IS_RTL ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($siteTitle); ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="icon.png">
    <link rel="apple-touch-icon" href="assets/images/hassan_photo.jpg">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Readex+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Master CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Script Header Injection -->
    <?php if (!empty($settings['script_header'])): ?>
        <?php echo $settings['script_header']; ?>
    <?php endif; ?>
</head>
<body>

<header class="site-header">
    <div class="container nav-wrapper">
        <a href="index.php" class="brand-logo">
            <img src="web.jpeg" alt="HASSAN SARHAN" class="logo-img">
        </a>

        <!-- Header Nav Links with strict translated dictionary keys -->
        <ul class="nav-links">
            <li><a href="qualifications.php" class="<?php echo $currentPage == 'qualifications.php' ? 'active' : ''; ?>"><?php echo t('nav_qualifications'); ?></a></li>
            <li><a href="career.php" class="<?php echo $currentPage == 'career.php' ? 'active' : ''; ?>"><?php echo t('nav_career'); ?></a></li>
            <li><a href="services.php" class="<?php echo $currentPage == 'services.php' ? 'active' : ''; ?>"><?php echo t('nav_services'); ?></a></li>
            <?php if ($coursesEnabled): ?>
                <li><a href="courses.php" class="<?php echo $currentPage == 'courses.php' ? 'active' : ''; ?>"><?php echo t('nav_courses'); ?></a></li>
            <?php endif; ?>
            <li><a href="blog.php" class="<?php echo $currentPage == 'blog.php' ? 'active' : ''; ?>"><?php echo t('nav_blog'); ?></a></li>
            <li><a href="contact.php" class="<?php echo $currentPage == 'contact.php' ? 'active' : ''; ?>"><?php echo t('nav_contact'); ?></a></li>
        </ul>

        <div class="nav-actions">
            <!-- Strict Language Switcher -->
            <a href="?lang=<?php echo $otherLang; ?>" class="btn-lang">
                <?php echo $otherLangLabel; ?>
            </a>
            <a href="services.php#book" class="btn-primary btn-header-cta">
                📅 <?php echo t('book_free_meeting'); ?>
            </a>
        </div>

        <div class="mobile-header-controls">
            <a href="?lang=<?php echo $otherLang; ?>" class="mobile-lang-btn">
                <?php echo $otherLangLabel; ?>
            </a>
            <button class="mobile-nav-toggle" type="button" aria-label="Toggle Navigation Menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>
