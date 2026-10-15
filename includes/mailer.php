<?php
/**
 * HSN SRHAN - Unified Dual Mailer Module (Admin & Client Email Notifications)
 * Handles dual email dispatch:
 * 1. Admin Email Notification (hi@hsn.agency)
 * 2. Client Confirmation Email
 */

if (!defined('ADMIN_EMAIL')) {
    define('ADMIN_EMAIL', 'hi@hsn.agency');
}

/**
 * Send Dual Emails for Bookings, Consultation Requests, Course Enrollments, & Product Orders
 * 
 * @param string $type ('booking', 'order', 'contact', 'course', 'service')
 * @param array $data (Contains full details: full_name, email, phone, title/product, notes, date, time, order_number, etc.)
 * @return bool
 */
function sendDualNotificationMail($type, $data) {
    $adminEmail = ADMIN_EMAIL;
    $clientEmail = trim($data['email'] ?? $data['customer_email'] ?? $data['client_email'] ?? '');
    $clientName = trim($data['full_name'] ?? $data['customer_name'] ?? $data['client_name'] ?? 'عزيزنا العميل');
    $phone = trim($data['phone'] ?? $data['customer_phone'] ?? $data['client_phone'] ?? 'غير محدد');
    $notes = trim($data['notes'] ?? 'لا يوجد');

    // Build Subjects & Header Titles based on Request Type
    $subjectAdmin = "🔔 إشعار طلب جديد على الموقع: ";
    $subjectClient = "✅ تم استلام طلبك بنجاح | حسن سرحان - HSSN-SRHAN";
    $typeTitleAr = "طلب استشارة وموعد جديد";

    if ($type === 'booking') {
        $subjectAdmin .= "حجز جلسة استشارية من " . $clientName;
        $subjectClient = "📅 تأكيد استلام طلب حجز الجلسة الاستشارية | حسن سرحان";
        $typeTitleAr = "حجز جلسة استشارية أونلاين";
    } elseif ($type === 'order') {
        $orderNum = $data['order_number'] ?? 'ORD-' . time();
        $subjectAdmin .= "إيصال تحويل رقمي #" . $orderNum . " - " . ($data['product_title'] ?? 'منتج رقمي');
        $subjectClient = "💳 تأكيد استلام طلب المنتج الرقمي #" . $orderNum . " | حسن سرحان";
        $typeTitleAr = "شراء منتج رقمي وتحويل بنكي D360";
    } elseif ($type === 'course') {
        $subjectAdmin .= "تسجيل في كورس " . ($data['course_title'] ?? 'ماستر كلاس');
        $subjectClient = "🎓 تأكيد التسجيل في الكورس الماستر كلاس | حسن سرحان";
        $typeTitleAr = "حجز كورس / ماستر كلاس";
    } elseif ($type === 'service') {
        $subjectAdmin .= "طلب خدمة " . ($data['service_title'] ?? 'استشارية');
        $subjectClient = "💼 تأكيد استلام طلب الخدمة الاستشارية | حسن سرحان";
        $typeTitleAr = "طلب خدمة استشارية وتطويرية";
    }

    // 1. Render HTML Content for Admin Email
    $adminBody = renderAdminEmailTemplate($typeTitleAr, $clientName, $clientEmail, $phone, $data, $notes);

    // 2. Render HTML Content for Client Email
    $clientBody = renderClientEmailTemplate($typeTitleAr, $clientName, $data, $notes);

    // Common Email Headers for UTF-8 HTML delivery
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Hassan Sarhan Platform <noreply@hsn.agency>" . "\r\n";
    $headers .= "Reply-To: " . $clientEmail . "\r\n";

    $headersClient = "MIME-Version: 1.0" . "\r\n";
    $headersClient .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headersClient .= "From: Hassan Sarhan <hi@hsn.agency>" . "\r\n";
    $headersClient .= "Reply-To: hi@hsn.agency" . "\r\n";

    // Send Mail to Admin
    $mailAdminSuccess = @mail($adminEmail, "=?UTF-8?B?" . base64_encode($subjectAdmin) . "?=", $adminBody, $headers);

    // Send Mail to Client if valid email provided
    $mailClientSuccess = false;
    if (!empty($clientEmail) && filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
        $mailClientSuccess = @mail($clientEmail, "=?UTF-8?B?" . base64_encode($subjectClient) . "?=", $clientBody, $headersClient);
    }

    return $mailAdminSuccess || $mailClientSuccess;
}

/**
 * Render Executive HTML Email for Admin Notification
 */
function renderAdminEmailTemplate($typeTitle, $name, $email, $phone, $data, $notes) {
    $dateStr = date('Y-m-d H:i');
    
    $detailsRows = "
        <tr><td style='padding:8px 12px; font-weight:bold; background:#F8FAFC; border:1px solid #E2E8F0;'>نوع الطلب:</td><td style='padding:8px 12px; border:1px solid #E2E8F0;'>{$typeTitle}</td></tr>
        <tr><td style='padding:8px 12px; font-weight:bold; background:#F8FAFC; border:1px solid #E2E8F0;'>اسم العميل:</td><td style='padding:8px 12px; border:1px solid #E2E8F0;'>{$name}</td></tr>
        <tr><td style='padding:8px 12px; font-weight:bold; background:#F8FAFC; border:1px solid #E2E8F0;'>البريد الإلكتروني:</td><td style='padding:8px 12px; border:1px solid #E2E8F0;'><a href='mailto:{$email}'>{$email}</a></td></tr>
        <tr><td style='padding:8px 12px; font-weight:bold; background:#F8FAFC; border:1px solid #E2E8F0;'>رقم الجوال / الواتساب:</td><td style='padding:8px 12px; border:1px solid #E2E8F0;'><a href='https://wa.me/" . preg_replace('/[^\d]/', '', $phone) . "' target='_blank'>{$phone} ↗</a></td></tr>
    ";

    if (!empty($data['company_name'])) {
        $detailsRows .= "<tr><td style='padding:8px 12px; font-weight:bold; background:#F8FAFC; border:1px solid #E2E8F0;'>اسم الشركة / البراند:</td><td style='padding:8px 12px; border:1px solid #E2E8F0;'>{$data['company_name']}</td></tr>";
    }
    if (!empty($data['booking_date'])) {
        $detailsRows .= "<tr><td style='padding:8px 12px; font-weight:bold; background:#F8FAFC; border:1px solid #E2E8F0;'>الموعد والوقت:</td><td style='padding:8px 12px; border:1px solid #E2E8F0;'>{$data['booking_date']} - " . ($data['time_slot'] ?? '') . "</td></tr>";
    }
    if (!empty($data['product_title'])) {
        $detailsRows .= "<tr><td style='padding:8px 12px; font-weight:bold; background:#F8FAFC; border:1px solid #E2E8F0;'>المنتج / الباقة:</td><td style='padding:8px 12px; border:1px solid #E2E8F0;'>{$data['product_title']} (" . ($data['amount_sar'] ?? '') . " ﷼)</td></tr>";
    }
    if (!empty($data['order_number'])) {
        $detailsRows .= "<tr><td style='padding:8px 12px; font-weight:bold; background:#F8FAFC; border:1px solid #E2E8F0;'>رقم الطلب:</td><td style='padding:8px 12px; border:1px solid #E2E8F0;'><code>{$data['order_number']}</code></td></tr>";
    }
    if (!empty($data['receipt_url'])) {
        $detailsRows .= "<tr><td style='padding:8px 12px; font-weight:bold; background:#F8FAFC; border:1px solid #E2E8F0;'>صورة الإيصال:</td><td style='padding:8px 12px; border:1px solid #E2E8F0;'><a href='https://hsn-srhan.com/{$data['receipt_url']}' target='_blank'>عرض الإيصال المرفق 📄</a></td></tr>";
    }

    $detailsRows .= "<tr><td style='padding:8px 12px; font-weight:bold; background:#F8FAFC; border:1px solid #E2E8F0;'>الملاحظات والرسالة:</td><td style='padding:8px 12px; border:1px solid #E2E8F0; color:#334155;'>{$notes}</td></tr>";

    return "
    <div dir='rtl' style='font-family: Arial, sans-serif; background-color: #070D1E; padding: 20px; color: #F8FAFC;'>
        <div style='max-width: 620px; margin: 0 auto; background: #FFFFFF; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.3); color: #0B132B;'>
            <div style='background: linear-gradient(135deg, #0B132B, #162447); padding: 25px; text-align: center; border-bottom: 3px solid #D4AF37;'>
                <h1 style='color: #D4AF37; margin: 0; font-size: 22px;'>🔔 إشعار طلب جديد على المنصة</h1>
                <p style='color: #E2E8F0; margin: 6px 0 0 0; font-size: 14px;'>حسن سرحان | HSSN-SRHAN</p>
            </div>
            
            <div style='padding: 25px;'>
                <p style='font-size: 15px; color: #334155; line-height: 1.6;'>تم استلام طلب جديد عبر الموقع الإلكتروني بتاريخ <strong>{$dateStr}</strong>. التفاصيل أدناه:</p>
                
                <table style='width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 14px;'>
                    {$detailsRows}
                </table>

                <div style='text-align: center; margin-top: 25px;'>
                    <a href='https://wa.me/" . preg_replace('/[^\d]/', '', $phone) . "' style='background: #22C55E; color: #FFFFFF; text-decoration: none; padding: 12px 26px; border-radius: 10px; font-weight: bold; display: inline-block; font-size: 15px;'>📲 التواصل المباشر مع العميل عبر الواتساب</a>
                </div>
            </div>
            
            <div style='background: #F8FAFC; padding: 15px; text-align: center; font-size: 12px; color: #64748B; border-top: 1px solid #E2E8F0;'>
                © " . date('Y') . " لوحة تحكم حسن سرحان القيادية | HSSN-SRHAN Platform
            </div>
        </div>
    </div>
    ";
}

/**
 * Render Executive HTML Email for Client Confirmation
 */
function renderClientEmailTemplate($typeTitle, $name, $data, $notes) {
    $dateStr = date('Y-m-d');
    
    $summaryDetails = "";
    if (!empty($data['booking_date'])) {
        $summaryDetails .= "<p style='margin:4px 0;'><strong>الموعد المحجوز:</strong> " . $data['booking_date'] . " (" . ($data['time_slot'] ?? '') . ")</p>";
    }
    if (!empty($data['product_title'])) {
        $summaryDetails .= "<p style='margin:4px 0;'><strong>المنتج المحدد:</strong> " . $data['product_title'] . "</p>";
    }
    if (!empty($data['order_number'])) {
        $summaryDetails .= "<p style='margin:4px 0;'><strong>رقم المرجعية / الطلب:</strong> <code>" . $data['order_number'] . "</code></p>";
    }

    return "
    <div dir='rtl' style='font-family: Arial, sans-serif; background-color: #F8FAFC; padding: 20px; color: #0B132B;'>
        <div style='max-width: 620px; margin: 0 auto; background: #FFFFFF; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,0.06); border: 1px solid #E2E8F0;'>
            
            <!-- Branded Header -->
            <div style='background: linear-gradient(135deg, #0B132B 0%, #162447 100%); padding: 30px 25px; text-align: center; border-bottom: 3px solid #D4AF37;'>
                <div style='display:inline-block; width:48px; height:48px; background:linear-gradient(135deg, #D4AF37, #B48811); border-radius:12px; color:#0B132B; font-weight:900; font-size:24px; line-height:48px; margin-bottom:10px;'>H</div>
                <h1 style='color: #FFFFFF; margin: 0; font-size: 22px; font-weight:bold;'>تم استلام طلبك بنجاح! ✨</h1>
                <p style='color: #D4AF37; margin: 6px 0 0 0; font-size: 14px; font-weight:bold;'>حسن سرحان | خبير ومستشار الشراء الإعلاني والنمو</p>
            </div>
            
            <!-- Main Content Area -->
            <div style='padding: 30px 25px; line-height: 1.7; color: #334155;'>
                <h2 style='color: #0B132B; font-size: 18px; margin-top: 0;'>أهلاً وسهلاً بك، {$name} 👋</h2>
                
                <p style='font-size: 15px;'>نشكرك على تواصلك وثقتك. تم استلام طلبك الخاص بـ <strong>({$typeTitle})</strong> بنجاح وجاري مراجعته ومتابعته من قبل فريقنا الاستراتيجي.</p>

                " . (!empty($summaryDetails) ? "
                <div style='background: #F1F5F9; border-right: 4px solid #D4AF37; padding: 15px; border-radius: 8px; margin: 20px 0; font-size: 14px; color: #0B132B;'>
                    <strong style='display:block; margin-bottom:6px; color:#0B132B;'>ملخص تفاصيل الطلب:</strong>
                    {$summaryDetails}
                </div>
                " : "") . "

                <div style='background: #ECFDF5; border: 1px solid #A7F3D0; padding: 15px; border-radius: 10px; margin: 20px 0; font-size: 14px; color: #065F46;'>
                    <strong>⚡ الخطوة القادمة:</strong><br>
                    سيتم مراجعة الطلب والتواصل معك مباشرة خلال أقل من 24 ساعة عبر البريد الإلكتروني أو الواتساب لتأكيد كافة التفاصيل وإرسال الملفات/الروابط المطلوبة.
                </div>

                <p style='font-size: 14px; color: #64748B;'>إذا كان لديك أي استفسار عاجل، يمكنك التواصل مباشرة عبر الواتساب المباشر:</p>

                <div style='text-align: center; margin: 25px 0;'>
                    <a href='https://wa.me/966506267542' style='background: linear-gradient(135deg, #D4AF37 0%, #B48811 100%); color: #0B132B; text-decoration: none; padding: 14px 30px; border-radius: 12px; font-weight: bold; display: inline-block; font-size: 15px; box-shadow: 0 4px 14px rgba(212, 175, 55, 0.3);'>💬 تواصل مباشر عبر الواتساب ↗</a>
                </div>
            </div>
            
            <!-- Footer -->
            <div style='background: #F8FAFC; padding: 20px; text-align: center; font-size: 13px; color: #64748B; border-top: 1px solid #E2E8F0;'>
                <p style='margin: 0 0 6px 0; font-weight: bold; color: #0B132B;'>حسن سرحان - HSSN-SRHAN</p>
                <p style='margin: 0;'>الرياض، المملكة العربية السعودية | البريد: hsn96srhan@gmail.com</p>
                <p style='margin: 6px 0 0 0; font-size: 11px; color: #94A3B8;'>© " . date('Y') . " جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </div>
    ";
}
