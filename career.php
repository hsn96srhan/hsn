<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/db.php';

$db = getDBConnection();
$careers = [];
try {
    $stmt = $db->query("SELECT * FROM career_timeline WHERE is_published = 1 ORDER BY sort_order ASC, id ASC");
    $careers = $stmt->fetchAll();
} catch (Exception $e) {}
?>

<!-- Page Title Bar under Header -->
<div class="page-title-header-bar">
    <div class="container text-center">
        <h1 class="page-title-heading"><?php echo t('nav_career'); ?></h1>
    </div>
</div>

<section class="page-hero-banner" style="background-image:url('assets/images/hero_career.jpg');"></section>

<section style="padding:4rem 0; background:#FFF;">
    <div class="container" style="max-width:960px;">
        <?php foreach ($careers as $c): ?>
            <?php 
            $title = (SITE_LANG === 'en' && !empty($c['title_en'])) ? $c['title_en'] : $c['title_ar'];
            $company = (SITE_LANG === 'en' && !empty($c['company_en'])) ? $c['company_en'] : $c['company_ar'];
            $period = (SITE_LANG === 'en' && !empty($c['period_en'])) ? $c['period_en'] : $c['period_ar'];
            $location = (SITE_LANG === 'en' && !empty($c['location_en'])) ? $c['location_en'] : $c['location_ar'];
            $desc = (SITE_LANG === 'en' && !empty($c['description_en'])) ? $c['description_en'] : $c['description_ar'];
            $logo = !empty($c['logo_url']) ? $c['logo_url'] : 'assets/images/fluxads.png';
            ?>
            <div class="company-card" style="background:#F8FAFC; border:1px solid var(--border-color); border-radius:16px; padding:1.8rem; margin-bottom:2rem; border-right:6px solid var(--primary-navy); box-shadow:var(--shadow-sm);">
                <div class="company-card-header" style="display:flex; align-items:center; gap:14px; width:100%; margin-bottom:1.2rem; border-bottom:1px solid #E2E8F0; padding-bottom:1rem;">
                    <?php if (!empty($c['website_url'])): ?>
                        <a href="<?php echo htmlspecialchars($c['website_url']); ?>" target="_blank" rel="noopener" style="flex-shrink:0;">
                            <img src="<?php echo htmlspecialchars($logo); ?>" alt="<?php echo htmlspecialchars($company); ?>" style="width:58px; height:58px; border-radius:12px; object-fit:contain; background:#FFF; padding:5px; border:1px solid var(--border-color); box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                        </a>
                    <?php else: ?>
                        <img src="<?php echo htmlspecialchars($logo); ?>" alt="<?php echo htmlspecialchars($company); ?>" style="width:58px; height:58px; border-radius:12px; object-fit:contain; background:#FFF; padding:5px; border:1px solid var(--border-color); box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                    <?php endif; ?>
                    
                    <div style="display:flex; flex-direction:column; gap:4px; min-width:0; flex:1;">
                        <h3 style="font-size:1.25rem; color:var(--primary-navy); margin:0; font-weight:800; line-height:1.3;">
                            <span><?php echo htmlspecialchars($company); ?></span>
                        </h3>
                        <div class="badge-pill-row">
                            <span style="background:var(--primary-navy); color:#FFF; padding:4px 12px; border-radius:20px; font-size:0.82rem; font-weight:700;"><?php echo htmlspecialchars($title); ?></span>
                            <?php if (!empty($period)): ?>
                                <span style="font-size:0.8rem; font-weight:700; color:var(--primary-navy); background:#E2E8F0; padding:4px 12px; border-radius:20px;"><?php echo htmlspecialchars($period); ?></span>
                            <?php endif; ?>
                            <?php if (!empty($location)): ?>
                                <span class="company-location-badge" style="font-size:0.8rem; color:var(--text-muted); font-weight:600;"><?php echo htmlspecialchars($location); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <p style="font-size:0.96rem; color:var(--text-body); line-height:1.85; margin-bottom:1.2rem; text-align:justify; text-justify:inter-word;">
                    <?php echo htmlspecialchars($desc); ?>
                </p>

                <div class="company-btn-row">
                    <?php if (!empty($c['website_url'])): ?>
                        <a href="<?php echo htmlspecialchars($c['website_url']); ?>" target="_blank" rel="noopener" class="btn-primary" style="padding:4px 12px; font-size:0.82rem;">🌐 الموقع ↗</a>
                    <?php endif; ?>
                    <?php if (!empty($c['case_study_url'])): ?>
                        <a href="<?php echo htmlspecialchars($c['case_study_url']); ?>" class="btn-secondary" style="background:var(--bg-soft-gray); color:var(--primary-navy) !important; border:1px solid var(--border-color); padding:4px 12px; font-size:0.82rem;">📂 دراسة حالة</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
