<?php
require_once __DIR__ . '/includes/header.php';

$db = getDBConnection();
$msg = '';
$msgType = 'success';

// Handle Emergency Contact Save & Booking Status Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'])) {
    $action = $_POST['action_type'];

    if ($action === 'update_emergency') {
        try {
            $stmt = $db->prepare("UPDATE site_settings SET emergency_phone=?, emergency_whatsapp=?, emergency_title_ar=?, emergency_title_en=?, max_daily_bookings=? WHERE id=1");
            $stmt->execute([
                $_POST['emergency_phone'],
                $_POST['emergency_whatsapp'],
                $_POST['emergency_title_ar'],
                $_POST['emergency_title_en'],
                (int)$_POST['max_daily_bookings']
            ]);
            $msg = 'تم حفظ بيانات الاتصال المباشر والطوارئ بنجاح! 📞';
        } catch (Exception $e) {
            $msg = 'خطأ أثناء الحفظ: ' . $e->getMessage();
            $msgType = 'danger';
        }
    } elseif ($action === 'update_booking_status') {
        $booking_id = (int)$_POST['booking_id'];
        $status = $_POST['status'];
        $stmt = $db->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->execute([$status, $booking_id]);
        $msg = 'تم تحديث حالة طلب الحجز إلى (' . $status . ') بنجاح. ✅';
    }
}

$settings = $db->query("SELECT * FROM site_settings WHERE id = 1")->fetch();
$bookings = $db->query("SELECT * FROM bookings ORDER BY id DESC")->fetchAll();
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.8rem;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 900; color: #FFF; margin-bottom: 4px;">إدارة اتصالات الطوارئ والحجوزات</h1>
        <p style="color: var(--admin-text-muted); font-size: 0.92rem;">التحكم في أرقام الاتصال المباشر للجوال والواتساب ومتابعة طلبات استشارات العملاء.</p>
    </div>
</div>

<?php if (!empty($msg)): ?>
    <div class="alert-box alert-<?php echo $msgType; ?>"><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>

<!-- Emergency Contact Card -->
<div class="glass-card">
    <div class="card-header-flex">
        <h2 class="card-title"><i class="fas fa-phone-alt"></i> إعدادات الاتصال السريع والواتساب والطوارئ</h2>
    </div>

    <form method="POST" action="contact-settings.php">
        <input type="hidden" name="action_type" value="update_emergency">

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">رقم الجوال الشخصي المباشر *</label>
                <input type="text" name="emergency_phone" class="form-input" required value="<?php echo htmlspecialchars($settings['emergency_phone'] ?? '+966506267542'); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">رقم الواتساب الرسمي *</label>
                <input type="text" name="emergency_whatsapp" class="form-input" required value="<?php echo htmlspecialchars($settings['emergency_whatsapp'] ?? '+966506267542'); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">الحد الأقصى للحجوزات اليومية</label>
                <input type="number" name="max_daily_bookings" class="form-input" value="<?php echo $settings['max_daily_bookings'] ?? 3; ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">عنوان شريط الطوارئ (عربي)</label>
                <input type="text" name="emergency_title_ar" class="form-input" value="<?php echo htmlspecialchars($settings['emergency_title_ar'] ?? 'للطوارئ والظروف العاجلة فقط:'); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">عنوان شريط الطوارئ (إنجليزي)</label>
                <input type="text" name="emergency_title_en" class="form-input" value="<?php echo htmlspecialchars($settings['emergency_title_en'] ?? 'For Urgent Matters Only:'); ?>">
            </div>
        </div>

        <button type="submit" class="btn-action">💾 حفظ إعدادات الاتصال والطوارئ</button>
    </form>
</div>

<!-- Customer Bookings Table -->
<div class="glass-card">
    <div class="card-header-flex">
        <h2 class="card-title"><i class="fas fa-calendar-check"></i> قائمة طلبات الاستشارات والمقابلات المباشرة (<?php echo count($bookings); ?>)</h2>
    </div>

    <?php if (empty($bookings)): ?>
        <p style="color: var(--admin-text-muted); font-size: 0.9rem; text-align: center; padding: 2rem 0;">لا توجد طلبات استشارات محجوزة حتى الآن.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>اسم العميل</th>
                        <th>الشركة</th>
                        <th>التواصل (إيميل/واتس)</th>
                        <th>تاريخ الحجز المفضل</th>
                        <th>ملاحظات</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $b): ?>
                        <tr>
                            <td><strong>#<?php echo $b['id']; ?></strong></td>
                            <td><strong><?php echo htmlspecialchars($b['full_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($b['company_name'] ?? 'فردي / غير محدد'); ?></td>
                            <td>
                                <div>📱 <?php echo htmlspecialchars($b['phone']); ?></div>
                                <div style="font-size:0.78rem; color:var(--admin-text-muted);">✉️ <?php echo htmlspecialchars($b['email']); ?></div>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($b['booking_date']); ?></strong>
                                <div style="font-size:0.75rem; color:var(--admin-primary);"><?php echo htmlspecialchars($b['time_slot'] ?? 'غير محدد'); ?></div>
                            </td>
                            <td style="font-size:0.8rem; max-width: 200px;"><?php echo htmlspecialchars($b['notes'] ?? 'لا يوجد'); ?></td>
                            <td>
                                <form method="POST" action="contact-settings.php" style="display:flex; gap:6px;">
                                    <input type="hidden" name="action_type" value="update_booking_status">
                                    <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                                    <select name="status" class="form-select" style="padding:4px 8px; font-size:0.8rem;" onchange="this.form.submit()">
                                        <option value="pending" <?php echo $b['status'] === 'pending' ? 'selected' : ''; ?>>⏳ قيد الانتظار</option>
                                        <option value="confirmed" <?php echo $b['status'] === 'confirmed' ? 'selected' : ''; ?>>✅ تم التأكيد والمقابلة</option>
                                        <option value="cancelled" <?php echo $b['status'] === 'cancelled' ? 'selected' : ''; ?>>❌ ملغي</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
