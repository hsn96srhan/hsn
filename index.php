<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/db.php';

$db = getDBConnection();
$siteSettings = getSiteSettings();

// Fetch YouTube Shorts from DB dynamically
$shortsList = [];
try {
    $stmtShorts = $db->query("SELECT * FROM youtube_shorts WHERE is_published = 1 ORDER BY priority DESC, id DESC");
    $shortsList = $stmtShorts->fetchAll();
} catch (Exception $e) {}
?>

<!-- Homepage Executive YouTube Video Hero Banner -->
<section class="video-hero-banner">
    <div class="container">
        <div class="video-hero-card" id="video_hero_card">
            <div class="youtube-hero-wrapper">
                <?php 
                $heroVidUrl = $siteSettings['hero_video_url'] ?? 'https://www.youtube.com/embed/dQw4w9WgXcQ';
                if (strpos($heroVidUrl, 'youtube') !== false && strpos($heroVidUrl, 'embed') === false) {
                    preg_match('/[\\?&]v=([^&#] *)/', $heroVidUrl, $matches);
                    $vidId = $matches[1] ?? 'dQw4w9WgXcQ';
                    $heroVidUrl = "https://www.youtube.com/embed/" . $vidId;
                }
                ?>
                <iframe id="youtube_hero_player" 
                        src="<?php echo htmlspecialchars($heroVidUrl); ?>?enablejsapi=1&autoplay=1&mute=1&controls=0&loop=1&playsinline=1&rel=0&modestbranding=1&iv_load_policy=3" 
                        title="Executive Hero Widescreen Demo Video" 
                        frameborder="0" 
                        allow="autoplay; encrypted-media" 
                        allowfullscreen>
                </iframe>
            </div>
            <div class="video-click-overlay" id="video_click_overlay"></div>
            <button id="video_play_btn" type="button" class="video-play-icon-btn" aria-label="تشغيل الفيديو">
                <span class="play-triangle-icon">▶</span>
            </button>
        </div>
    </div>
</section>

<!-- Hero Action Buttons Bar under Video -->
<div class="hero-cta-bar" style="background:#FFF; padding:1.8rem 0; border-bottom:1px solid var(--border-color); text-align:center;">
    <div class="container" style="display:flex; gap:16px; justify-content:center; flex-wrap:wrap;">
        <a href="contact.php" class="btn-primary" style="padding:12px 28px; font-size:1.05rem; font-weight:800; background:#3B82F6; color:#FFF !important; border-radius:8px; text-decoration:none; box-shadow:0 4px 12px rgba(59,130,246,0.25);" data-en="📅 Online Meeting"><?php echo t('btn_consultation'); ?></a>
        <a href="portfolio.php" class="btn-secondary" style="padding:12px 28px; font-size:1.05rem; background:#F8FAFC; color:var(--primary-navy) !important; border:1px solid var(--border-color); font-weight:700; border-radius:8px; text-decoration:none;" data-en="📂 View Portfolio"><?php echo t('btn_portfolio'); ?></a>
    </div>
</div>

<!-- Latest Shorts Videos Section (Dynamic DB Driven) -->
<section class="shorts-section">
    <div class="container">
        <div class="shorts-grid">
            <?php if (!empty($shortsList)): ?>
                <?php foreach ($shortsList as $idx => $sh): ?>
                    <div class="short-card" data-youtube-id="<?php echo htmlspecialchars($sh['youtube_video_id']); ?>">
                        <img src="https://img.youtube.com/vi/<?php echo htmlspecialchars($sh['youtube_video_id']); ?>/hqdefault.jpg" alt="<?php echo htmlspecialchars($sh['title']); ?>" class="short-card-poster">
                        <div class="short-card-overlay"></div>
                        <div class="short-card-header">
                            <span class="short-badge">⚡ SHORTS</span>
                            <span class="short-duration">0:50</span>
                        </div>
                        <div class="short-play-btn">
                            <svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </div>
                        <div class="short-card-footer">
                            <h3 class="short-card-title"><?php echo htmlspecialchars($sh['title']); ?></h3>
                            <div class="short-card-views">
                                <span>👁️ <?php echo htmlspecialchars($sh['views_count'] ?? '10K+'); ?></span>
                                <span data-en="views">• مشاهدة</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="short-card">
                    <div class="short-card-footer">
                        <h3 class="short-card-title">فيديوهات Shorts قريباً</h3>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- KPIs & Stats Bar (Dynamic DB Driven) -->
<div class="kpis-bar" style="background: #0B132B; color: #FFF; padding: 2.5rem 0; text-align: center;">
    <div class="container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem;">
        <div>
            <div class="kpi-number" style="font-size: 2.5rem; font-weight: 800; color: #60A5FA;"><?php echo htmlspecialchars($siteSettings['kpi_exp'] ?? '+8 سنوات'); ?></div>
            <div style="font-size: 0.95rem; opacity: 0.9; margin-top: 0.3rem;" data-en="Years Leadership Experience"><?php echo t('kpi_exp'); ?></div>
        </div>
        <div>
            <div class="kpi-number" style="font-size: 2.5rem; font-weight: 800; color: #60A5FA;"><?php echo htmlspecialchars($siteSettings['kpi_brands'] ?? '+50 براند'); ?></div>
            <div style="font-size: 0.95rem; opacity: 0.9; margin-top: 0.3rem;" data-en="Global & Regional Brands"><?php echo t('kpi_brands'); ?></div>
        </div>
        <div>
            <div class="kpi-number" style="font-size: 2.5rem; font-weight: 800; color: #60A5FA;"><?php echo htmlspecialchars($siteSettings['kpi_certs'] ?? '18 شهادة'); ?></div>
            <div style="font-size: 0.95rem; opacity: 0.9; margin-top: 0.3rem;" data-en="Official Accreditations & Certs"><?php echo t('kpi_certs'); ?></div>
        </div>
        <div>
            <div class="kpi-number" style="font-size: 2.5rem; font-weight: 800; color: #60A5FA;"><?php echo htmlspecialchars($siteSettings['kpi_roas'] ?? '+7M SAR'); ?></div>
            <div style="font-size: 0.95rem; opacity: 0.9; margin-top: 0.3rem;" data-en="Ad Budget Managed over 8 Years"><?php echo t('kpi_roas'); ?></div>
        </div>
    </div>
</div>

<!-- Current Role Section (Dynamic DB Driven) -->
<section style="padding: 3rem 0; background: #FFF;">
    <div class="container">
        <div class="company-card" style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 16px; padding: 1.8rem; border-right: 6px solid var(--primary-navy); box-shadow: var(--shadow-sm);">
            <div class="company-card-header" style="display:flex; align-items:center; gap:14px; width:100%; margin-bottom:1.2rem; border-bottom:1px solid #E2E8F0; padding-bottom:1rem;">
                <a href="https://fluxads.sa" target="_blank" rel="noopener" style="flex-shrink:0;" title="الموقع الرسمي لشركة فلوكس آدز">
                    <img src="assets/images/fluxads.png" alt="FLUX Ads Logo" style="width:58px; height:58px; border-radius:12px; object-fit:contain; background:#FFF; padding:5px; border:1px solid var(--border-color); box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                </a>
                <div style="display:flex; flex-direction:column; gap:4px; min-width:0; flex:1;">
                    <a href="https://fluxads.sa" target="_blank" rel="noopener" style="text-decoration:none;" title="الموقع الرسمي لشركة فلوكس آدز">
                        <h3 style="font-size:1.25rem; color:var(--primary-navy); margin:0; font-weight:800; line-height:1.3;">
                            <span><?php echo htmlspecialchars((SITE_LANG === 'en') ? ($siteSettings['current_role_title_en'] ?? 'FLUX Ads Agency') : ($siteSettings['current_role_title_ar'] ?? 'شركة فلوكس آدز للتسويق')); ?></span>
                        </h3>
                    </a>
                    <div class="badge-pill-row">
                        <span style="background:var(--primary-navy); color:#FFF; padding:4px 12px; border-radius:20px; font-size:0.82rem; font-weight:700;"><?php echo htmlspecialchars((SITE_LANG === 'en') ? ($siteSettings['current_role_badge_en'] ?? 'Executive Role') : ($siteSettings['current_role_badge_ar'] ?? 'المنصب القيادي الحالي')); ?></span>
                    </div>
                </div>
            </div>
            <p style="font-size:0.96rem; color:var(--text-body); line-height:1.85; margin:0; text-align:justify; text-justify:inter-word;">
                <?php echo htmlspecialchars((SITE_LANG === 'en') ? ($siteSettings['current_role_desc_en'] ?? '') : ($siteSettings['current_role_desc_ar'] ?? '')); ?>
            </p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
