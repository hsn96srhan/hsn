<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/mailer.php';

$siteSettings = getSiteSettings();
$msg = '';
$msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type']) && $_POST['action_type'] === 'book_meeting') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $company_name = trim($_POST['company_name'] ?? '');
    $booking_date = trim($_POST['booking_date'] ?? date('Y-m-d'));
    $time_slot = trim($_POST['time_slot'] ?? '10:00 AM');
    $notes = trim($_POST['notes'] ?? '');

    if (!empty($full_name) && !empty($email) && !empty($phone)) {
        try {
            $db = getDBConnection();
            $stmt = $db->prepare("INSERT INTO bookings (full_name, email, phone, company_name, booking_date, time_slot, notes, status) VALUES (?,?,?,?,?,?,?,'pending')");
            $stmt->execute([$full_name, $email, $phone, $company_name, $booking_date, $time_slot, $notes]);

            // Dispatch dual notification emails (to Admin & Client)
            sendDualNotificationMail('booking', [
                'full_name' => $full_name,
                'email' => $email,
                'phone' => $phone,
                'company_name' => $company_name,
                'booking_date' => $booking_date,
                'time_slot' => $time_slot,
                'notes' => $notes
            ]);

            $msg = 'تم تسجيل طلب حجز الاستشارة بنجاح! تم إرسال إشعار وتأكيد عبر البريد الإلكتروني وسيتم التواصل معك فوراً عبر البريد والواتساب. 📅';
        } catch (Exception $e) {
            $msg = 'حدث خطأ أثناء حفظ الطلب: ' . $e->getMessage();
            $msgType = 'danger';
        }
    } else {
        $msg = 'يرجى ملء كافة البيانات المطلوبة.';
        $msgType = 'danger';
    }
}

$phoneNum = !empty($siteSettings['emergency_phone']) ? $siteSettings['emergency_phone'] : '+966506267542';
$waNum = !empty($siteSettings['emergency_whatsapp']) ? $siteSettings['emergency_whatsapp'] : '+966506267542';
$cleanWa = preg_replace('/[^\d]/', '', $waNum);
?>

<!-- Page Title Bar under Header -->
<div class="page-title-header-bar">
    <div class="container text-center">
        <h1 class="page-title-heading"><?php echo t('nav_contact'); ?></h1>
    </div>
</div>

<!-- Contact Visual Hero Banner -->
<section class="page-hero-banner" style="background-image:url('assets/images/hero_contact.jpg');"></section>

<section style="padding:4rem 0; background:#FFF;">
    <div class="container" style="max-width:1050px;">
        
        <?php if (!empty($msg)): ?>
            <div style="padding: 1rem 1.5rem; border-radius: 12px; font-weight: 700; margin-bottom: 2rem; background: <?php echo $msgType === 'success' ? '#DCFCE7; color: #166534; border: 1px solid #86EFAC;' : '#FEE2E2; color: #991B1B; border: 1px solid #FCA5A5;'; ?>">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <div style="display:grid; grid-template-columns: 0.9fr 1.1fr; gap:2.5rem; align-items:flex-start; margin-bottom:3.5rem;">
            <div>
                <img src="assets/images/hassan_photo.jpg" alt="حسن سرحان - HASSAN SARHAN" style="width:100%; aspect-ratio:4/5; max-height:420px; border-radius:16px; object-fit:cover; object-position:top center; border:1px solid var(--border-color); box-shadow:var(--shadow-md);">
            </div>
            <div>
                <span class="case-sector-badge" style="margin-bottom:1.2rem; display:inline-block;" data-en="💼 Direct Contact & Executive Partnerships">💼 التواصل المباشر والشراكات</span>

                <div style="display:flex; flex-direction:column; gap:1.2rem;">
                    <!-- Phone / WhatsApp Card -->
                    <div class="contact-info-card">
                        <div class="contact-info-content">
                            <div class="contact-info-icon">📱</div>
                            <div class="contact-info-details">
                                <div class="contact-info-label" data-en="Mobile & WhatsApp:">رقم الجوال والواتساب:</div>
                                <div class="contact-info-value" style="direction:ltr; text-align:right; unicode-bidi:embed; display:inline-block;"><?php echo htmlspecialchars($phoneNum); ?></div>
                            </div>
                        </div>
                        <a href="https://wa.me/<?php echo $cleanWa; ?>" target="_blank" rel="noopener" class="contact-card-btn" data-en="WhatsApp Chat ↗">محادثة واتساب ↗</a>
                    </div>

                    <!-- Email Card -->
                    <div class="contact-info-card">
                        <div class="contact-info-content">
                            <div class="contact-info-icon">✉️</div>
                            <div class="contact-info-details">
                                <div class="contact-info-label" data-en="Email Address:">البريد الإلكتروني:</div>
                                <div class="contact-info-value" style="word-break:break-all;">hsn96srhan@gmail.com</div>
                            </div>
                        </div>
                        <a href="mailto:hsn96srhan@gmail.com" class="contact-card-btn" data-en="Send Email ↗">إرسال بريد ↗</a>
                    </div>

                    <!-- Digital Business Card -->
                    <div class="contact-info-card">
                        <div class="contact-info-content">
                            <div class="contact-info-icon">💳</div>
                            <div class="contact-info-details">
                                <div class="contact-info-label" data-en="Digital Business Card:">الكارت الرقمي للتواصل الذكي:</div>
                                <div class="contact-info-value" data-en="GetTap Digital Card">بطاقة GetTap للتواصل الرقمي المباشر</div>
                            </div>
                        </div>
                        <a href="https://link.gettap.co/srhan" target="_blank" rel="noopener" class="contact-card-btn" data-en="Open Card ↗">فتح الكارت ↗</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Interactive Consultation Booking Form -->
        <div style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 20px; padding: 2.5rem; box-shadow: var(--shadow-sm);" id="book">
            <h2 style="font-size: 1.6rem; color: var(--primary-navy); font-weight: 800; margin-bottom: 0.5rem;" data-en="Book an Online Strategy Session">📅 احجز جلسة استشارية أونلاين</h2>
            <p style="font-size: 0.95rem; color: var(--text-body); margin-bottom: 2rem;" data-en="Select an available date slot to discuss your brand growth strategy directly.">اختر الموعد المناسب لمناقشة استراتيجية نمو وتوسيع أعمالك مباشرة مع حسن سرحان.</p>

            <form method="POST" action="contact.php#book">
                <input type="hidden" name="action_type" value="book_meeting">
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.2rem; margin-bottom: 1.2rem;">
                    <div>
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-navy); margin-bottom: 6px;" data-en="Full Name *"><?php echo t('form_name'); ?> *</label>
                        <input type="text" required name="full_name" class="digital-form-input" placeholder="اسمك الكريم">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-navy); margin-bottom: 6px;" data-en="Email Address *"><?php echo t('form_email'); ?> *</label>
                        <input type="email" required name="email" class="digital-form-input" placeholder="name@domain.com">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-navy); margin-bottom: 6px;" data-en="Phone / WhatsApp Number *"><?php echo t('form_phone'); ?> *</label>
                        <input type="tel" required name="phone" class="digital-form-input" placeholder="05xxxxxxxx">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.2rem; margin-bottom: 1.2rem;">
                    <div>
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-navy); margin-bottom: 6px;" data-en="Company Name"><?php echo t('form_company'); ?></label>
                        <input type="text" name="company_name" class="digital-form-input" placeholder="اسم براندك أو شركتك">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-navy); margin-bottom: 6px;" data-en="Preferred Date">التاريخ المفضل</label>
                        <input type="date" name="booking_date" class="digital-form-input" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-navy); margin-bottom: 6px;" data-en="Preferred Time">الوقت المفضل</label>
                        <select name="time_slot" class="digital-form-input">
                            <option value="10:00 AM" data-en="10:00 AM">10:00 صباحاً</option>
                            <option value="02:00 PM" data-en="02:00 PM">02:00 ظهراً</option>
                            <option value="05:00 PM" data-en="05:00 PM">05:00 مساءً</option>
                            <option value="08:00 PM" data-en="08:00 PM">08:00 مساءً</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-navy); margin-bottom: 6px;" data-en="Project Notes or Details">ملاحظات أو تفاصيل المشروع</label>
                    <textarea name="notes" rows="3" class="digital-form-textarea" placeholder="أكتب نبذة مختصرة عن هدفك والاستراتيجية المطلوبة..."></textarea>
                </div>

                <button type="submit" class="btn-primary" style="padding: 12px 28px; font-size: 1rem; font-weight: 800; background: var(--primary-navy); color: #FFF; border: none; border-radius: 10px; cursor: pointer;" data-en="🚀 <?php echo (SITE_LANG === 'en') ? 'Confirm Booking' : 'Confirm Booking'; ?>">
                    🚀 <?php echo t('form_submit'); ?>
                </button>
            </form>
        </div>

    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
