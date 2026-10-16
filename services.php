<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/db.php';

$db = getDBConnection();
$services = [];
try {
    $stmt = $db->query("SELECT * FROM services WHERE is_published = 1 ORDER BY sort_order ASC, id ASC");
    $services = $stmt->fetchAll();
} catch (Exception $e) {}
?>

<!-- Page Header Title Bar -->
<div class="page-title-header-bar">
    <div class="container">
        <h1 class="page-title-heading" data-en="Strategic Services"><?php echo t('nav_services'); ?></h1>
    </div>
</div>

<section class="page-hero-banner" style="background-image:url('assets/images/hero_services.jpg');"></section>

<section style="padding: 4rem 0; background: #FFFFFF;">
    <div class="container" style="max-width: 1040px;">
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            <?php foreach ($services as $s): ?>
                <?php 
                $title = (SITE_LANG === 'en' && !empty($s['title_en'])) ? $s['title_en'] : $s['title_ar'];
                $badge = (SITE_LANG === 'en' && !empty($s['badge_en'])) ? $s['badge_en'] : $s['badge_ar'];
                $subtitle = (SITE_LANG === 'en' && !empty($s['subtitle_en'])) ? $s['subtitle_en'] : $s['subtitle_ar'];
                $price = (SITE_LANG === 'en' && !empty($s['price_text_en'])) ? $s['price_text_en'] : $s['price_text_ar'];
                $cta = (SITE_LANG === 'en' && !empty($s['cta_text_en'])) ? $s['cta_text_en'] : $s['cta_text_ar'];
                $features = json_decode($s['features_json'] ?? '[]', true) ?: [];
                ?>
                <div class="digital-product-card" style="border-right: 6px solid var(--primary-navy);">
                    <div style="display: flex; flex-direction: column; gap: 0.8rem; width: 100%; min-width: 0; padding: 1.5rem;">
                        <div class="product-header-flex" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 10px; border-bottom: 1px solid #E2E8F0; padding-bottom: 0.6rem;">
                            <div style="min-width: 0; flex: 1;">
                                <?php if (!empty($badge)): ?>
                                    <span style="background: var(--primary-navy); color: #FFF; padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 800; display: inline-block; margin-bottom: 4px;"><?php echo htmlspecialchars($badge); ?></span>
                                <?php endif; ?>
                                <h3 style="font-size: 1.3rem; color: var(--primary-navy); font-weight: 800; margin: 0; line-height: 1.3;"><?php echo htmlspecialchars($title); ?></h3>
                            </div>
                            <?php if (!empty($price)): ?>
                                <div class="product-price-box" style="text-align: right; white-space: nowrap;">
                                    <span style="font-size: 1.2rem; font-weight: 800; color: #166534;"><?php echo htmlspecialchars($price); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <p style="font-size: 0.96rem; color: var(--text-body); line-height: 1.6; margin: 0;">
                            <?php echo htmlspecialchars($subtitle); ?>
                        </p>

                        <?php if (!empty($features)): ?>
                            <ul style="padding-right: 0; color: var(--text-body); font-size: 0.9rem; line-height: 1.7; margin: 0.5rem 0 0 0; list-style: none;">
                                <?php foreach ($features as $ft): ?>
                                    <li style="margin-bottom: 4px;">🎯 <?php echo htmlspecialchars($ft); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <div style="margin-top: 1rem;">
                            <a href="<?php echo htmlspecialchars(!empty($s['cta_link']) ? $s['cta_link'] : 'contact.php'); ?>" class="btn-primary" style="padding: 10px 22px; font-size: 0.92rem; font-weight: 800; text-decoration: none; display: inline-block;" data-en="<?php echo htmlspecialchars(!empty($s['cta_text_en']) ? $s['cta_text_en'] : '📅 Book Consultation Session'); ?>">
                                <?php echo htmlspecialchars(!empty($cta) ? $cta : '📅 احجز جلسة استشارية'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
