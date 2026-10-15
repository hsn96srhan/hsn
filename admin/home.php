<?php
require_once __DIR__ . '/includes/header.php';

$db = getDBConnection();
$msg = '';
$msgType = 'success';

// Handle Home Settings Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type']) && $_POST['action_type'] === 'update_home') {
    try {
        $stmt = $db->prepare("
            UPDATE site_settings SET 
                hero_badge_ar = ?, hero_badge_en = ?,
                hero_name_ar = ?, hero_name_en = ?,
                hero_subtitle_ar = ?, hero_subtitle_en = ?,
                hero_video_url = ?,
                kpi_exp = ?, kpi_brands = ?, kpi_certs = ?, kpi_roas = ?,
                current_role_badge_ar = ?, current_role_badge_en = ?,
                current_role_title_ar = ?, current_role_title_en = ?,
                current_role_desc_ar = ?, current_role_desc_en = ?,
                philosophy_quote_ar = ?, philosophy_quote_en = ?,
                philosophy_desc_ar = ?, philosophy_desc_en = ?
            WHERE id = 1
        ");
        $stmt->execute([
            $_POST['hero_badge_ar'], $_POST['hero_badge_en'],
            $_POST['hero_name_ar'], $_POST['hero_name_en'],
            $_POST['hero_subtitle_ar'], $_POST['hero_subtitle_en'],
            $_POST['hero_video_url'],
            $_POST['kpi_exp'], $_POST['kpi_brands'], $_POST['kpi_certs'], $_POST['kpi_roas'],
            $_POST['current_role_badge_ar'], $_POST['current_role_badge_en'],
            $_POST['current_role_title_ar'], $_POST['current_role_title_en'],
            $_POST['current_role_desc_ar'], $_POST['current_role_desc_en'],
            $_POST['philosophy_quote_ar'], $_POST['philosophy_quote_en'],
            $_POST['philosophy_desc_ar'], $_POST['philosophy_desc_en']
        ]);
        $msg = 'تم حفظ جميع تعديلات الصفحة الرئيسية والفلسفة بنجاح! ✨';
    } catch (Exception $e) {
        $msg = 'حدث خطأ أثناء الحفظ: ' . $e->getMessage();
        $msgType = 'danger';
    }
}

// Handle Add/Delete YouTube Short
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'])) {
    if ($_POST['action_type'] === 'add_short') {
        try {
            $stmt = $db->prepare("INSERT INTO youtube_shorts (title, youtube_video_id, views_count, likes_count, priority) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_POST['short_title'], $_POST['youtube_video_id'], $_POST['views_count'], $_POST['likes_count'], (int)$_POST['priority']]);
            $msg = 'تمت إضافة فيديو الشورتس بنجاح! 🎬';
        } catch (Exception $e) {
            $msg = 'خطأ في الإضافة: ' . $e->getMessage();
            $msgType = 'danger';
        }
    } elseif ($_POST['action_type'] === 'delete_short') {
        try {
            $stmt = $db->prepare("DELETE FROM youtube_shorts WHERE id = ?");
            $stmt->execute([(int)$_POST['short_id']]);
            $msg = 'تم حذف فيديو الشورتس بنجاح.';
        } catch (Exception $e) {
            $msg = 'خطأ في الحذف: ' . $e->getMessage();
            $msgType = 'danger';
        }
    }
}

// Fetch current site settings
$settings = $db->query("SELECT * FROM site_settings WHERE id = 1")->fetch();

// Fetch YouTube Shorts
$shorts = $db->query("SELECT * FROM youtube_shorts ORDER BY priority DESC, id DESC")->fetchAll();
?>

<div style="margin-bottom: 1.8rem;">
    <h1 style="font-size: 1.8rem; font-weight: 900; color: #FFF; margin-bottom: 4px;">إدارة الصفحة الرئيسية وفيديوهات Shorts</h1>
    <p style="color: var(--admin-text-muted); font-size: 0.92rem;">التحكم الكامل في فيديو الهيرو، العدادات، المنصب القيادي الحالي، وفلسفة القيادة والشورتس.</p>
</div>

<?php if (!empty($msg)): ?>
    <div class="alert-box alert-<?php echo $msgType; ?>">
        <?php echo htmlspecialchars($msg); ?>
    </div>
<?php endif; ?>

<!-- 1. Hero & Philosophy Form -->
<form method="POST" action="home.php">
    <input type="hidden" name="action_type" value="update_home">

    <!-- Hero Section Control -->
    <div class="glass-card">
        <div class="card-header-flex">
            <h2 class="card-title"><i class="fas fa-star"></i> قسم الهيرو والتعريف الرئيسي (Hero Section)</h2>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">شارة الهيرو (عربي)</label>
                <input type="text" name="hero_badge_ar" class="form-input" value="<?php echo htmlspecialchars($settings['hero_badge_ar'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">شارة الهيرو (إنجليزي)</label>
                <input type="text" name="hero_badge_en" class="form-input" value="<?php echo htmlspecialchars($settings['hero_badge_en'] ?? ''); ?>" required>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">الاسم الكامل (عربي)</label>
                <input type="text" name="hero_name_ar" class="form-input" value="<?php echo htmlspecialchars($settings['hero_name_ar'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">الاسم الكامل (إنجليزي)</label>
                <input type="text" name="hero_name_en" class="form-input" value="<?php echo htmlspecialchars($settings['hero_name_en'] ?? ''); ?>" required>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">الوصف والملخص الإستراتيجي (عربي)</label>
                <textarea name="hero_subtitle_ar" class="form-textarea" rows="3"><?php echo htmlspecialchars($settings['hero_subtitle_ar'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">الوصف والملخص الإستراتيجي (إنجليزي)</label>
                <textarea name="hero_subtitle_en" class="form-textarea" rows="3"><?php echo htmlspecialchars($settings['hero_subtitle_en'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">رابط فيديو الهيرو (YouTube Embed URL / MP4 Direct Link)</label>
            <input type="url" name="hero_video_url" class="form-input" value="<?php echo htmlspecialchars($settings['hero_video_url'] ?? ''); ?>" placeholder="https://www.youtube.com/embed/XXXXXX">
        </div>
    </div>

    <!-- Counters & KPIs -->
    <div class="glass-card">
        <div class="card-header-flex">
            <h2 class="card-title"><i class="fas fa-chart-pie"></i> عدادات الإنجازات والـ KPIs الرئيسية</h2>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">سنوات الخبرة</label>
                <input type="text" name="kpi_exp" class="form-input" value="<?php echo htmlspecialchars($settings['kpi_exp'] ?? '+8 سنوات'); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">عدد العلامات التجارية</label>
                <input type="text" name="kpi_brands" class="form-input" value="<?php echo htmlspecialchars($settings['kpi_brands'] ?? '+50 براند'); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">عدد الاعتمادات الرسمية</label>
                <input type="text" name="kpi_certs" class="form-input" value="<?php echo htmlspecialchars($settings['kpi_certs'] ?? '18 شهادة'); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">الميزانيات المدارة</label>
                <input type="text" name="kpi_roas" class="form-input" value="<?php echo htmlspecialchars($settings['kpi_roas'] ?? '+7M SAR'); ?>">
            </div>
        </div>
    </div>

    <!-- Current Executive Role -->
    <div class="glass-card">
        <div class="card-header-flex">
            <h2 class="card-title"><i class="fas fa-building"></i> بطاقة المنصب القيادي الحالي</h2>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">شارة المنصب (عربي)</label>
                <input type="text" name="current_role_badge_ar" class="form-input" value="<?php echo htmlspecialchars($settings['current_role_badge_ar'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">شارة المنصب (إنجليزي)</label>
                <input type="text" name="current_role_badge_en" class="form-input" value="<?php echo htmlspecialchars($settings['current_role_badge_en'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">اسم الشركة / الجهة (عربي)</label>
                <input type="text" name="current_role_title_ar" class="form-input" value="<?php echo htmlspecialchars($settings['current_role_title_ar'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">اسم الشركة / الجهة (إنجليزي)</label>
                <input type="text" name="current_role_title_en" class="form-input" value="<?php echo htmlspecialchars($settings['current_role_title_en'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">تفاصيل المهام والقيادة (عربي)</label>
                <textarea name="current_role_desc_ar" class="form-textarea" rows="3"><?php echo htmlspecialchars($settings['current_role_desc_ar'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">تفاصيل المهام والقيادة (إنجليزي)</label>
                <textarea name="current_role_desc_en" class="form-textarea" rows="3"><?php echo htmlspecialchars($settings['current_role_desc_en'] ?? ''); ?></textarea>
            </div>
        </div>
    </div>

    <!-- Leadership Philosophy -->
    <div class="glass-card">
        <div class="card-header-flex">
            <h2 class="card-title"><i class="fas fa-quote-right"></i> الفلسفة الشخصية والرؤية القيادية</h2>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">المقولة الملهمة (عربي)</label>
                <input type="text" name="philosophy_quote_ar" class="form-input" value="<?php echo htmlspecialchars($settings['philosophy_quote_ar'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">المقولة الملهمة (إنجليزي)</label>
                <input type="text" name="philosophy_quote_en" class="form-input" value="<?php echo htmlspecialchars($settings['philosophy_quote_en'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">شرح الفلسفة القيادية (عربي)</label>
                <textarea name="philosophy_desc_ar" class="form-textarea" rows="3"><?php echo htmlspecialchars($settings['philosophy_desc_ar'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">شرح الفلسفة القيادية (إنجليزي)</label>
                <textarea name="philosophy_desc_en" class="form-textarea" rows="3"><?php echo htmlspecialchars($settings['philosophy_desc_en'] ?? ''); ?></textarea>
            </div>
        </div>

        <button type="submit" class="btn-action">💾 حفظ جميع تعديلات الرئيسية والرؤية</button>
    </div>
</form>

<!-- 2. YouTube Shorts Management -->
<div class="glass-card">
    <div class="card-header-flex">
        <h2 class="card-title"><i class="fab fa-youtube"></i> إدارة فيديوهات Shorts المباشرة من اليوتيوب</h2>
    </div>

    <!-- Add Short Form -->
    <form method="POST" action="home.php" style="background: rgba(255,255,255,0.03); border: 1px dashed var(--admin-border); padding: 1.2rem; border-radius: 12px; margin-bottom: 1.5rem;">
        <input type="hidden" name="action_type" value="add_short">
        <h4 style="font-size: 1rem; margin-bottom: 1rem; color: var(--admin-primary);">➕ إضافة فيديو شورتس جديد</h4>
        
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">عنوان الفيديو</label>
                <input type="text" name="short_title" class="form-input" required placeholder="استراتيجية مضاعفة الـ ROAS">
            </div>
            <div class="form-group">
                <label class="form-label">معرف فيديو يوتيوب (YouTube Video ID)</label>
                <input type="text" name="youtube_video_id" class="form-input" required placeholder="dQw4w9WgXcQ">
            </div>
            <div class="form-group">
                <label class="form-label">عدد المشاهدات</label>
                <input type="text" name="views_count" class="form-input" placeholder="120K+">
            </div>
            <div class="form-group">
                <label class="form-label">عدد الإعجابات</label>
                <input type="text" name="likes_count" class="form-input" placeholder="8.5K">
            </div>
            <div class="form-group">
                <label class="form-label">أولوية الترتيب (Sort Order)</label>
                <input type="number" name="priority" class="form-input" value="0">
            </div>
        </div>
        <button type="submit" class="btn-action" style="margin-top: 0.5rem;">🎬 نشر فيديو الشورتس</button>
    </form>

    <!-- Shorts Table -->
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>عنوان الفيديو</th>
                    <th>YouTube ID</th>
                    <th>المشاهدات</th>
                    <th>الإعجابات</th>
                    <th>الترتيب</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($shorts as $sh): ?>
                    <tr>
                        <td><strong>#<?php echo $sh['id']; ?></strong></td>
                        <td><strong><?php echo htmlspecialchars($sh['title']); ?></strong></td>
                        <td><code><?php echo htmlspecialchars($sh['youtube_video_id']); ?></code></td>
                        <td><?php echo htmlspecialchars($sh['views_count']); ?></td>
                        <td><?php echo htmlspecialchars($sh['likes_count']); ?></td>
                        <td><?php echo $sh['priority']; ?></td>
                        <td>
                            <form method="POST" action="home.php" style="display:inline;" onsubmit="return confirm('هل أنت تأكد من حذف هذا الفيديو؟');">
                                <input type="hidden" name="action_type" value="delete_short">
                                <input type="hidden" name="short_id" value="<?php echo $sh['id']; ?>">
                                <button type="submit" class="btn-danger-action">حذف 🗑️</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
