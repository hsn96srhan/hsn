<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/db.php';

$db = getDBConnection();
$projects = [];
try {
    $stmt = $db->query("SELECT * FROM projects WHERE is_published = 1 ORDER BY priority DESC, id DESC");
    $projects = $stmt->fetchAll();
} catch (Exception $e) {}
?>

<!-- Page Title Bar under Header -->
<div class="page-title-header-bar">
    <div class="container text-center">
        <h1 class="page-title-heading" data-en="Portfolio"><?php echo (SITE_LANG === 'ar') ? 'معرض الأعمال ودراسات الحالة' : 'Portfolio Case Studies'; ?></h1>
    </div>
</div>

<!-- Portfolio Visual Hero Banner -->
<section class="page-hero-banner" style="background-image:url('assets/images/hero_services.jpg');"></section>

<section style="padding:4rem 0; background:#FFF;">
    <div class="container">
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(360px, 1fr)); gap:2.5rem;">
            <?php foreach ($projects as $p): ?>
                <?php 
                $title = (SITE_LANG === 'en' && !empty($p['title_en'])) ? $p['title_en'] : $p['title_ar'];
                $client = $p['client_name'];
                $problem = $p['problem_text_ar'];
                $strategy = $p['strategy_text_ar'];
                $results = json_decode($p['results_json'] ?? '{}', true) ?: [];
                $logo = !empty($p['client_logo_url']) ? $p['client_logo_url'] : 'assets/images/image7_square.png';
                $anchorId = !empty($p['slug']) ? $p['slug'] : 'project-' . $p['id'];
                ?>
                <div class="img-card" id="<?php echo htmlspecialchars($anchorId); ?>">
                    <div style="background:#F8FAFC; padding:1.2rem; display:flex; align-items:center; gap:1rem; border-bottom:1px solid var(--border-color);">
                        <img src="<?php echo htmlspecialchars($logo); ?>" alt="<?php echo htmlspecialchars($client); ?>" style="width:45px; height:45px; object-fit:contain; border-radius:6px; background:#FFF; border:1px solid var(--border-color); padding:2px; flex-shrink:0;">
                        <div style="min-width:0;">
                            <span style="font-size:0.75rem; font-weight:700; color:var(--primary-navy);"><?php echo htmlspecialchars($client); ?></span>
                            <h3 class="card-title" style="margin:0.2rem 0; font-size: 1.1rem; color: var(--primary-navy); font-weight: 800;"><?php echo htmlspecialchars($title); ?></h3>
                        </div>
                    </div>
                    
                    <div class="img-card-body" style="padding:1.4rem;">
                        <?php if (!empty($problem)): ?>
                            <div style="margin-bottom:0.8rem;">
                                <strong style="font-size:0.85rem; color:var(--primary-navy); display:block; margin-bottom:2px;">📌 التحدي والهدف:</strong>
                                <p style="font-size:0.88rem; color:var(--text-body); margin:0; line-height:1.6;"><?php echo htmlspecialchars($problem); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($strategy)): ?>
                            <div style="margin-bottom:0.8rem;">
                                <strong style="font-size:0.85rem; color:var(--primary-navy); display:block; margin-bottom:2px;">⚙️ الاستراتيجية والحل:</strong>
                                <p style="font-size:0.88rem; color:var(--text-body); margin:0; line-height:1.6;"><?php echo htmlspecialchars($strategy); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($results)): ?>
                            <div style="background: rgba(34, 197, 94, 0.08); border: 1px dashed #166534; padding: 8px 12px; border-radius: 8px; margin-top: 0.8rem;">
                                <strong style="font-size:0.82rem; color:#166534; display:block; margin-bottom:4px;">🚀 النتائج المحققة (KPIs):</strong>
                                <div style="display:flex; gap:10px; flex-wrap:wrap; font-size:0.82rem; font-weight:800; color:#166534;">
                                    <?php foreach ($results as $k => $v): ?>
                                        <span><?php echo htmlspecialchars($k); ?>: <?php echo htmlspecialchars($v); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
