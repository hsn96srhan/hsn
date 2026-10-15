<?php
/**
 * API Endpoint: Submit Digital Product Order & Bank Receipt
 * HSN SRHAN Portfolio & Strategic Platform
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$clientName  = trim($_POST['client_name'] ?? '');
$clientEmail = trim($_POST['client_email'] ?? '');
$clientPhone = trim($_POST['client_phone'] ?? '');
$productName = trim($_POST['digital_product'] ?? 'منتج رقمي');
$productPriceStr = trim($_POST['digital_price'] ?? '199');
$clientNotes = trim($_POST['client_notes'] ?? '');

if (empty($clientName) || empty($clientEmail) || empty($clientPhone)) {
    echo json_encode(['success' => false, 'message' => 'يرجى استكمال جميع البيانات المطلوبة.']);
    exit;
}

// Clean price numeric value
preg_match('/[\d\.]+/', $productPriceStr, $matches);
$amountSar = isset($matches[0]) ? (float)$matches[0] : 199.00;

// Handle file upload
$receiptUrl = '';
if (isset($_FILES['receipt_file']) && $_FILES['receipt_file']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['receipt_file']['name'], PATHINFO_EXTENSION);
    $allowedExts = ['jpg', 'jpeg', 'png', 'pdf', 'webp'];
    if (in_array(strtolower($ext), $allowedExts)) {
        $newFileName = 'receipt_' . time() . '_' . rand(1000, 9999) . '.' . strtolower($ext);
        $uploadDir = __DIR__ . '/../assets/uploads/receipts/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        if (move_uploaded_file($_FILES['receipt_file']['tmp_name'], $uploadDir . $newFileName)) {
            $receiptUrl = 'assets/uploads/receipts/' . $newFileName;
        }
    }
}

$orderNumber = 'ORD-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));

try {
    $db = getDBConnection();
    $stmt = $db->prepare("
        INSERT INTO bank_orders (order_number, customer_name, customer_email, customer_phone, product_title, amount_sar, receipt_url, notes, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    $stmt->execute([
        $orderNumber,
        $clientName,
        $clientEmail,
        $clientPhone,
        $productName,
        $amountSar,
        $receiptUrl,
        $clientNotes
    ]);

    // Dispatch dual email notifications (Admin & Client)
    require_once __DIR__ . '/../includes/mailer.php';
    sendDualNotificationMail('order', [
        'order_number' => $orderNumber,
        'customer_name' => $clientName,
        'customer_email' => $clientEmail,
        'customer_phone' => $clientPhone,
        'product_title' => $productName,
        'amount_sar' => $amountSar,
        'receipt_url' => $receiptUrl,
        'notes' => $clientNotes
    ]);

    echo json_encode([
        'success' => true,
        'order_number' => $orderNumber,
        'message' => 'تم استلام طلبك وإرفاق الإيصال بنجاح. تم إرسال إشعار وتأكيد عبر البريد الإلكتروني وسيتم توثيق الحساب وإرسال رابط التحميل المباشر فوراً.'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'حدث خطأ أثناء حفظ الطلب: ' . $e->getMessage()
    ]);
}
