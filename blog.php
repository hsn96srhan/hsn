<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/db.php';

$db = getDBConnection();
$articles = [];
try {
    $stmt = $db->query("SELECT * FROM articles WHERE is_published = 1 ORDER BY priority DESC, id DESC");
    $articles = $stmt->fetchAll();
} catch (Exception $e) {}
?>

<!-- Page Header Title Bar -->
<div class="page-title-header-bar">
    <div class="container">
        <h1 class="page-title-heading" data-en="Marketing Articles"><?php echo t('nav_blog'); ?></h1>
    </div>
</div>

<section style="padding: 4rem 0; background: #FFFFFF;">
    <div class="container" style="max-width: 960px;">
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            <?php foreach ($articles as $a): ?>
                <?php 
                $title = (SITE_LANG === 'en' && !empty($a['title_en'])) ? $a['title_en'] : $a['title_ar'];
                $content = (SITE_LANG === 'en' && !empty($a['content_en'])) ? $a['content_en'] : $a['content_ar'];
                ?>
                <div class="digital-product-card" style="padding: 2rem; border-right: 6px solid var(--primary-navy);">
                    <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                        <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 700;">📅 <?php echo htmlspecialchars($a['created_at']); ?></span>
                        <h2 style="font-size: 1.4rem; color: var(--primary-navy); font-weight: 800; margin: 0; line-height: 1.3;"><?php echo htmlspecialchars($title); ?></h2>
                        <p style="font-size: 0.96rem; color: var(--text-body); line-height: 1.8; margin: 0.5rem 0;">
                            <?php echo nl2br(htmlspecialchars($content)); ?>
                        </p>

                        <?php if (!empty($a['linkedin_url'])): ?>
                            <div style="margin-top: 0.8rem;">
                                <a href="<?php echo htmlspecialchars($a['linkedin_url']); ?>" target="_blank" rel="noopener" class="btn-primary" style="padding: 8px 18px; font-size: 0.85rem; text-decoration: none; display: inline-block;" data-en="🔗 Read Full Post on LinkedIn ↗">
                                    🔗 قراءة المنشور الكامل على LinkedIn ↗
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
