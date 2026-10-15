<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$db = getDBConnection();

// Update Status
if (isset($_GET['status_id']) && isset($_GET['new_status'])) {
    $bId = (int)$_GET['status_id'];
    $st = $_GET['new_status'];
    $stmt = $db->prepare("UPDATE bookings SET status = :s WHERE id = :id");
    $stmt->execute([':s' => $st, ':id' => $bId]);
    header("Location: bookings.php");
    exit;
}

$bookings = $db->query("SELECT * FROM bookings ORDER BY booking_date DESC, id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة حجوزات الرزنامة | Dash</title>
    <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Readex Pro', sans-serif; background: #F8FAFC; padding: 2rem; }
        table { width: 100%; border-collapse: collapse; background: #FFF; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.04); }
        th, td { padding: 12px 16px; border-bottom: 1px solid #E2E8F0; text-align: right; }
        th { background: #0B132B; color: #FFF; }
        .badge-pending { background: #FEF3C7; color: #92400E; padding: 4px 8px; border-radius: 4px; font-weight: 700; font-size: 0.8rem; }
        .badge-confirmed { background: #DCFCE7; color: #166534; padding: 4px 8px; border-radius: 4px; font-weight: 700; font-size: 0.8rem; }
    </style>
</head>
<body>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <h2>إدارة المواعيد والاستشارات المحجوزة من الرزنامة (Bookings)</h2>
    <a href="index.php" style="color:#0B132B; font-weight:700;">⬅ العودة للوحة التحكم</a>
</div>

<table>
    <thead>
        <tr>
            <th>تاريخ الموعد</th>
            <th>الاسم الكامل</th>
            <th>الجوال / الواتساب</th>
            <th>الشركة</th>
            <th>ملاحظات المشروع</th>
            <th>الحالة</th>
            <th>الإجراء</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($bookings as $b): ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($b['booking_date']); ?></strong></td>
                <td><?php echo htmlspecialchars($b['full_name']); ?></td>
                <td><a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $b['phone']); ?>" target="_blank" style="color:#2563EB; font-weight:700;"><?php echo htmlspecialchars($b['phone']); ?> 💬</a></td>
                <td><?php echo htmlspecialchars($b['company_name']); ?></td>
                <td><small><?php echo htmlspecialchars($b['notes']); ?></small></td>
                <td>
                    <span class="<?php echo $b['status'] === 'confirmed' ? 'badge-confirmed' : 'badge-pending'; ?>">
                        <?php echo $b['status'] === 'confirmed' ? 'مؤكد' : 'قيد الانتظار'; ?>
                    </span>
                </td>
                <td>
                    <a href="?status_id=<?php echo $b['id']; ?>&new_status=confirmed" style="color:#166534; font-weight:700; margin-left:8px;">تأكيد ✅</a>
                    <a href="?status_id=<?php echo $b['id']; ?>&new_status=cancelled" style="color:#DC2626; font-weight:700;">إلغاء ❌</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
