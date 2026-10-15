<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/db.php';

$db = getDBConnection();
$quals = [];
try {
    $stmt = $db->query("SELECT * FROM qualifications WHERE is_published = 1 ORDER BY sort_order ASC, id ASC");
    $quals = $stmt->fetchAll();
} catch (Exception $e) {}
?>

<!-- Page Title Bar under Header -->
<div class="page-title-header-bar">
    <div class="container text-center">
        <h1 class="page-title-heading"><?php echo t('nav_qualifications'); ?></h1>
    </div>
</div>

<section class="page-hero-banner" style="background-image:url('assets/images/hero_qualifications.jpg');"></section>

<section style="padding:4rem 0; background:#FFF;">
    <div class="container">
        <!-- ALL CERTIFICATES GRID (Dynamic DB Driven) -->
        <div class="cert-grid-3x6">
            <?php foreach ($quals as $q): ?>
                <?php 
                $title = (SITE_LANG === 'en' && !empty($q['title_en'])) ? $q['title_en'] : $q['title_ar'];
                $issuer = (SITE_LANG === 'en' && !empty($q['issuer_en'])) ? $q['issuer_en'] : $q['issuer_ar'];
                $desc = (SITE_LANG === 'en' && !empty($q['description_en'])) ? $q['description_en'] : $q['description_ar'];
                $img = !empty($q['image_url']) ? $q['image_url'] : 'assets/images/image65.jpg';
                ?>
                <div class="img-card">
                    <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($title); ?>" class="img-card-cover zoomable-img">
                    <div class="img-card-body">
                        <span style="font-size:0.78rem; font-weight:700; color:var(--primary-navy);"><?php echo htmlspecialchars($issuer); ?></span>
                        <h4 style="font-size:1.02rem; margin:0.4rem 0; font-weight:700;"><?php echo htmlspecialchars($title); ?></h4>
                        <?php if (!empty($desc)): ?>
                            <p style="font-size:0.86rem; line-height:1.6; color:var(--text-body);"><?php echo htmlspecialchars($desc); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
