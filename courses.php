<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/db.php';

$db = getDBConnection();
$courses = [];
try {
    $stmt = $db->query("SELECT * FROM courses WHERE is_published = 1 ORDER BY sort_order ASC, id ASC");
    $courses = $stmt->fetchAll();
} catch (Exception $e) {}
?>

<!-- Page Header Title Bar -->
<div class="page-title-header-bar">
    <div class="container">
        <h1 class="page-title-heading" data-en="Courses & Masterclasses"><?php echo t('nav_courses'); ?></h1>
    </div>
</div>

<section style="padding: 4rem 0; background: #FFFFFF;">
    <div class="container" style="max-width: 1040px;">
        <div id="courses_container" style="display: flex; flex-direction: column; gap: 2rem;">
            <?php foreach ($courses as $c): ?>
                <?php 
                $title = (SITE_LANG === 'en' && !empty($c['title_en'])) ? $c['title_en'] : $c['title_ar'];
                $badge = (SITE_LANG === 'en' && !empty($c['badge_en'])) ? $c['badge_en'] : $c['badge_ar'];
                $desc = (SITE_LANG === 'en' && !empty($c['description_en'])) ? $c['description_en'] : $c['description_ar'];
                $duration = (SITE_LANG === 'en' && !empty($c['duration_en'])) ? $c['duration_en'] : $c['duration_ar'];
                $price = (SITE_LANG === 'en' && !empty($c['price_en'])) ? $c['price_en'] : $c['price_ar'];
                $original_price = !empty($c['original_price_ar']) ? $c['original_price_ar'] : '2,999 ﷼';
                $features = json_decode($c['features_json'] ?? '[]', true) ?: [];
                $img = !empty($c['image_url']) ? $c['image_url'] : 'assets/images/image45.jpg';
                ?>
                <div class="digital-product-card">
                    <div class="product-img-wrapper">
                        <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($title); ?>">
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 0.8rem; width: 100%; min-width: 0; padding: 1.2rem;">
                        <div class="product-header-flex" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 10px; border-bottom: 1px solid #E2E8F0; padding-bottom: 0.6rem;">
                            <div style="min-width: 0; flex: 1;">
                                <?php if (!empty($badge)): ?>
                                    <span style="background: var(--primary-navy); color: #FFF; padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 800; display: inline-block; margin-bottom: 4px;"><?php echo htmlspecialchars($badge); ?></span>
                                <?php endif; ?>
                                <h3 style="font-size: 1.25rem; color: var(--primary-navy); font-weight: 800; margin: 0; line-height: 1.3;"><?php echo htmlspecialchars($title); ?></h3>
                            </div>
                            <div class="product-price-box" style="text-align: right; white-space: nowrap;">
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <span style="font-size: 1.4rem; font-weight: 800; color: #166534;"><?php echo htmlspecialchars($price); ?></span>
                                    <span style="font-size: 0.95rem; text-decoration: line-through; color: var(--text-muted);"><?php echo htmlspecialchars($original_price); ?></span>
                                    <span style="background: #FEF3C7; color: #B45309; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 800;" data-en="🔥 50% OFF">🔥 وفر 50%</span>
                                </div>
                                <?php if (!empty($duration)): ?>
                                    <div style="font-size: 0.78rem; color: var(--text-muted); font-weight: 700; margin-top:2px;"><?php echo htmlspecialchars($duration); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <p style="font-size: 0.94rem; color: var(--text-body); line-height: 1.6; margin: 0;">
                            <?php echo htmlspecialchars($desc); ?>
                        </p>

                        <?php if (!empty($features)): ?>
                            <ul style="padding-right: 0; color: var(--text-body); font-size: 0.88rem; line-height: 1.6; margin: 0; list-style: none;">
                                <?php foreach ($features as $ft): ?>
                                    <li style="margin-bottom: 4px;">🎓 <?php echo htmlspecialchars($ft); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <div style="margin-top: 0.6rem;">
                            <a href="#" class="btn-primary open-digital-modal-btn" data-product="<?php echo htmlspecialchars($c['title_ar']); ?>" data-price="<?php echo htmlspecialchars($c['price_ar']); ?>" data-product-en="<?php echo htmlspecialchars(!empty($c['title_en']) ? $c['title_en'] : $c['title_ar']); ?>" data-price-en="<?php echo htmlspecialchars(!empty($c['price_en']) ? $c['price_en'] : $c['price_ar']); ?>" style="padding: 10px 22px; font-size: 0.92rem; font-weight: 800; text-decoration: none; display: inline-block;" data-en="💳 Request Enrollment & Book Seat">
                                💳 طلب التسجيل وحجز المقعد
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Interactive Order & Bank Transfer Modal for Courses -->
<div class="image-modal-overlay" id="digital_order_modal">
    <div class="image-modal-container">
        <button class="image-modal-close" id="close_digital_modal_btn" aria-label="Close" style="position: absolute; top: 14px; left: 18px; right: auto; background: #F1F5F9; color: var(--primary-navy); border: none; font-size: 1.6rem; font-weight: 700; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10;">&times;</button>
        
        <div style="text-align: center; margin-bottom: 1.4rem; padding-bottom: 1rem; border-bottom: 1px solid #E2E8F0;">
            <span style="font-size: 2.2rem; display: block; margin-bottom: 4px;">💳</span>
            <h3 style="font-size: 1.4rem; color: var(--primary-navy); font-weight: 800; margin: 0 0 4px 0;" data-en="Course Enrollment & Bank Transfer Details">طلب التسجيل وبيانات التحويل البنكي</h3>
            <span style="background: #DCFCE7; color: #166534; font-size: 0.95rem; font-weight: 800; padding: 4px 14px; border-radius: 20px; display: inline-block; border: 1px solid #86EFAC;" id="modal_product_name_text" data-en="Selected Course">الكورس المحدد</span>
        </div>

        <form id="digital_order_form">
            <input type="hidden" id="selected_digital_product" name="digital_product" value="">
            <input type="hidden" id="selected_digital_price" name="digital_price" value="">

            <div class="modal-section-box">
                <h4 class="modal-section-title" data-en="1. Trainee Contact Details">👤 1. بيانات المتدرب لتأكيد حجز المقعد</h4>
                
                <div class="digital-form-group">
                    <label class="digital-form-label" data-en="Full Name *">الاسم الكامل *</label>
                    <input type="text" required class="digital-form-input" name="client_name" placeholder="أدخل اسمك الكريم" data-en-placeholder="Enter your full name">
                </div>

                <div class="digital-form-group">
                    <label class="digital-form-label" data-en="Email Address *">البريد الإلكتروني (لتسلم رابط الكورس عليه) *</label>
                    <input type="email" required class="digital-form-input" name="client_email" placeholder="name@domain.com" data-en-placeholder="name@domain.com">
                </div>

                <div class="digital-form-group" style="margin-bottom: 0;">
                    <label class="digital-form-label" data-en="WhatsApp Mobile Number *">رقم الجوال / الواتساب *</label>
                    <input type="tel" required class="digital-form-input" name="client_phone" placeholder="05xxxxxxxx / +966xxxxxxxx" data-en-placeholder="+966xxxxxxxxx">
                </div>
            </div>

            <div class="bank-details-box" style="margin-bottom: 1.2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; border-bottom: 1px solid #CBD5E1; padding-bottom: 8px;">
                    <span style="font-size: 0.95rem; font-weight: 800; color: var(--primary-navy);" data-en="🏦 Direct Saudi Bank Account Details">🏦 2. بيانات الحساب البنكي المباشر</span>
                    <span class="bank-name-badge" data-en="D360 Bank">D360 بنك</span>
                </div>

                <div class="bank-row-item">
                    <span class="bank-label" data-en="Bank Name:">اسم البنك:</span>
                    <span style="font-size: 0.9rem; font-weight: 800; color: var(--primary-navy);" data-en="D360 Bank">D360 بنك</span>
                </div>

                <div class="bank-row-item">
                    <span class="bank-label" data-en="Account Holder:">صاحب الحساب:</span>
                    <span style="font-size: 0.9rem; font-weight: 800; color: var(--primary-navy);" data-en="Hassan Fayez Hassan Sarhan">حسن فايز حسن سرحان</span>
                </div>

                <div class="bank-row-item">
                    <span class="bank-label" data-en="IBAN Number:">الآيبان:</span>
                    <div class="bank-val-wrapper">
                        <span class="bank-val" id="iban_val_text">SA6936036036047948932598</span>
                        <button type="button" class="btn-copy-code" id="btn_copy_iban" data-copy="SA6936036036047948932598" aria-label="Copy IBAN" title="نسخ الآيبان" style="background: transparent !important; background-color: transparent !important; border: none !important; box-shadow: none !important; outline: none !important; padding: 0 !important; margin: 0 !important; min-width: 0 !important; min-height: 0 !important; width: auto !important; height: auto !important; font-size: 1.15rem; cursor: pointer;">📋</button>
                    </div>
                </div>

                <div class="bank-row-item">
                    <span class="bank-label" data-en="SWIFT Code:">رمز السويفت:</span>
                    <div class="bank-val-wrapper">
                        <span class="bank-val" id="swift_val_text">DBAKSARIXXX</span>
                        <button type="button" class="btn-copy-code" id="btn_copy_swift" data-copy="DBAKSARIXXX" aria-label="Copy SWIFT" title="نسخ السويفت" style="background: transparent !important; background-color: transparent !important; border: none !important; box-shadow: none !important; outline: none !important; padding: 0 !important; margin: 0 !important; min-width: 0 !important; min-height: 0 !important; width: auto !important; height: auto !important; font-size: 1.15rem; cursor: pointer;">📋</button>
                    </div>
                </div>

                <div style="background: #FEF3C7; border: 1.5px solid #F59E0B; padding: 10px 12px; border-radius: 10px; margin-top: 12px; font-size: 0.85rem; color: #92400E; font-weight: 800; text-align: center;" data-en="⚠️ Important Note: Please select transfer purpose as 'Friend'.">
                    ⚠️ تنبيه هام: يرجى اختيار الغرض من التحويل "صديق" عند إجراء التحويل البنكي.
                </div>
            </div>

            <div class="modal-section-box">
                <h4 class="modal-section-title" data-en="3. Receipt Attachment & Enrollment Notes">📎 3. إرفاق إيصال التحويل وحجز المقعد</h4>
                
                <div class="digital-form-group">
                    <label class="digital-form-label" data-en="Attach Transfer Receipt Screenshot *">رفع سكرين / إيصال التحويل البنكي *</label>
                    <div class="receipt-file-input-wrapper">
                        <input type="file" required id="receipt_file_input" accept="image/*,.pdf" style="width: 100%; font-size: 0.85rem; color: var(--primary-navy);">
                        <p style="font-size: 0.76rem; color: #64748B; margin: 6px 0 0 0;" data-en="Accepts Images (JPG, PNG) or PDF receipts">يُقبل الصور (JPG, PNG) أو ملفات الـ PDF</p>
                    </div>
                </div>

                <div class="digital-form-group" style="margin-bottom: 0;">
                    <label class="digital-form-label" data-en="Additional Notes / Special Instructions">ملاحظات إضافية على التحويل أو التسجيل (اختياري)</label>
                    <textarea class="digital-form-textarea" id="client_notes_input" name="client_notes" rows="2" placeholder="أكتب أي ملاحظات أو استفسارات خاصة بطلبك هنا..." data-en-placeholder="Write any additional notes or request details here..."></textarea>
                </div>
            </div>

            <button type="submit" class="btn-submit-digital-order" data-en="🚀 Send Receipt & Confirm Enrollment via WhatsApp">
                🚀 تأكيد التحويل وإرسال طلب حجز المقعد عبر الواتساب
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
