<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

$db = getDBConnection();

$action = $_SERVER['REQUEST_METHOD'];

if ($action === 'GET') {
    $date = $_GET['date'] ?? '';
    if (!$date) {
        echo json_encode(['status' => 'error', 'message' => 'Date is required']);
        exit;
    }

    $stmt = $db->prepare("SELECT time_slot FROM bookings WHERE booking_date = :bdate AND status != 'cancelled'");
    $stmt->execute([':bdate' => $date]);
    $slots = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode(['status' => 'success', 'date' => $date, 'booked_slots' => $slots]);
    exit;
}

if ($action === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    $fullName = trim($input['full_name'] ?? '');
    $email = trim($input['email'] ?? '');
    $phone = trim($input['phone'] ?? '');
    $companyName = trim($input['company_name'] ?? '');
    $notes = trim($input['notes'] ?? '');
    $bookingDate = trim($input['booking_date'] ?? '');
    $timeSlot = trim($input['time_slot'] ?? '');

    if (!$bookingDate || !$timeSlot || !$fullName || !$phone || !$email) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'جميع الحقول المطلوبة يجب تعبئتها.',
            'message_en' => 'All required fields must be filled.'
        ]);
        exit;
    }

    // Check if slot is already booked
    $stmtCheck = $db->prepare("SELECT COUNT(*) FROM bookings WHERE booking_date = :bdate AND time_slot = :tslot AND status != 'cancelled'");
    $stmtCheck->execute([':bdate' => $bookingDate, ':tslot' => $timeSlot]);
    $alreadyBooked = $stmtCheck->fetchColumn() > 0;

    if ($alreadyBooked) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'الموعد محجوز، اختر موعد آخر',
            'message_en' => 'This slot is booked, please select another time'
        ]);
        exit;
    }

    // Check daily max 3 bookings limit
    $stmtDaily = $db->prepare("SELECT COUNT(*) FROM bookings WHERE booking_date = :bdate AND status != 'cancelled'");
    $stmtDaily->execute([':bdate' => $bookingDate]);
    $dailyCount = $stmtDaily->fetchColumn();

    if ($dailyCount >= 3) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => '⚠️ عذراً، تم اكتمال كافة المواعيد المتاحة لهذا اليوم (الحد الأقصى 3 مواعيد يومياً). يرجى اختيار تاريخ آخر.',
            'message_en' => '⚠️ Sorry, all 3 available slots for this date are fully booked. Please select another date.'
        ]);
        exit;
    }

    // Insert booking into DB
    $stmtInsert = $db->prepare("INSERT INTO bookings (full_name, email, phone, company_name, notes, booking_date, time_slot, status) VALUES (:fn, :em, :ph, :cn, :nt, :bd, :ts, 'pending')");
    $stmtInsert->execute([
        ':fn' => $fullName,
        ':em' => $email,
        ':ph' => $phone,
        ':cn' => $companyName,
        ':nt' => $notes,
        ':bd' => $bookingDate,
        ':ts' => $timeSlot
    ]);

    // Dispatch dual emails (Admin & Client)
    require_once __DIR__ . '/../includes/mailer.php';
    sendDualNotificationMail('booking', [
        'full_name' => $fullName,
        'email' => $email,
        'phone' => $phone,
        'company_name' => $companyName,
        'notes' => $notes,
        'booking_date' => $bookingDate,
        'time_slot' => $timeSlot
    ]);

    echo json_encode([
        'status' => 'success',
        'message' => "✅ تم تأكيد حجزك بنجاح يوم {$bookingDate} الساعة {$timeSlot}! تم إرسال إشعار وتأكيد عبر البريد الإلكتروني وسيتم التواصل معك لتأكيد التفاصيل.",
        'message_en' => "✅ Your appointment has been booked successfully for {$bookingDate} at {$timeSlot}! A confirmation email has been sent."
    ]);
    exit;
}
