<?php
require_once __DIR__ . '/includes/header.php';

$db = getDBConnection();

// Fetch summary metrics
$countProjects = $db->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$countQuals = $db->query("SELECT COUNT(*) FROM qualifications")->fetchColumn();
$countOrdersPending = $db->query("SELECT COUNT(*) FROM bank_orders WHERE status = 'pending'")->fetchColumn();
$countOrdersTotal = $db->query("SELECT COUNT(*) FROM bank_orders")->fetchColumn();
$countServices = $db->query("SELECT COUNT(*) FROM services")->fetchColumn();
$countShorts = $db->query("SELECT COUNT(*) FROM youtube_shorts")->fetchColumn();

// Fetch recent pending bank orders
$recentOrders = $db->query("SELECT * FROM bank_orders ORDER BY id DESC LIMIT 5")->fetchAll();
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 900; color: #FFF; margin-bottom: 4px;">مرحباً بك، <?php echo htmlspecialchars($currentUser['username']); ?> 👋</h1>
        <p style="color: var(--admin-text-muted); font-size: 0.92rem;">لوحة التحليل والتحكم الاستراتيجي الشاملة بالمحتوى والوظائف الحية للموقع.</p>
    </div>
    <div style="background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.4); color: #4ADE80; padding: 8px 16px; border-radius: 30px; font-weight: 700; font-size: 0.85rem; display: flex; align-items: center; gap: 8px;">
        <span style="width: 10px; height: 10px; background: #22C55E; border-radius: 50%; display: inline-block; box-shadow: 0 0 10px #22C55E;"></span>
        الموقع متصل ولايف (PDO SQLite Active)
    </div>
</div>

<!-- Key Metrics Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.2rem; margin-bottom: 2rem;">
    
    <div class="glass-card" style="margin-bottom: 0; padding: 1.4rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <span style="color: var(--admin-text-muted); font-size: 0.85rem; font-weight: 700;">طلبات D360 المعلقة</span>
            <i class="fas fa-receipt" style="color: #F59E0B; font-size: 1.4rem;"></i>
        </div>
        <div style="font-size: 2rem; font-weight: 900; color: #F59E0B;"><?php echo $countOrdersPending; ?></div>
        <div style="font-size: 0.78rem; color: var(--admin-text-muted); margin-top: 4px;">إجمالي الطلبات: <?php echo $countOrdersTotal; ?></div>
    </div>

    <div class="glass-card" style="margin-bottom: 0; padding: 1.4rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <span style="color: var(--admin-text-muted); font-size: 0.85rem; font-weight: 700;">دراسات الحالة الميدانية</span>
            <i class="fas fa-folder-open" style="color: var(--admin-primary); font-size: 1.4rem;"></i>
        </div>
        <div style="font-size: 2rem; font-weight: 900; color: var(--admin-primary);"><?php echo $countProjects; ?></div>
        <div style="font-size: 0.78rem; color: var(--admin-text-muted); margin-top: 4px;">مشاريع وحالات ناجحة</div>
    </div>

    <div class="glass-card" style="margin-bottom: 0; padding: 1.4rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <span style="color: var(--admin-text-muted); font-size: 0.85rem; font-weight: 700;">المؤهلات والاعتمادات</span>
            <i class="fas fa-award" style="color: #38BDF8; font-size: 1.4rem;"></i>
        </div>
        <div style="font-size: 2rem; font-weight: 900; color: #38BDF8;"><?php echo $countQuals; ?></div>
        <div style="font-size: 0.78rem; color: var(--admin-text-muted); margin-top: 4px;">شهادات رسمية واحترافية</div>
    </div>

    <div class="glass-card" style="margin-bottom: 0; padding: 1.4rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <span style="color: var(--admin-text-muted); font-size: 0.85rem; font-weight: 700;">فيديوهات Shorts</span>
            <i class="fab fa-youtube" style="color: #EF4444; font-size: 1.4rem;"></i>
        </div>
        <div style="font-size: 2rem; font-weight: 900; color: #EF4444;"><?php echo $countShorts; ?></div>
        <div style="font-size: 0.78rem; color: var(--admin-text-muted); margin-top: 4px;">محتوى قصيرة متزامن</div>
    </div>

</div>

<!-- Quick Actions Banner -->
<div class="glass-card">
    <div class="card-header-flex">
        <h2 class="card-title"><i class="fas fa-bolt"></i> اختصارات التعديل المباشر</h2>
    </div>
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="home.php" class="btn-secondary-action"><i class="fas fa-video"></i> تعديل هيرو الرئيسية والـ Shorts</a>
        <a href="qualifications.php" class="btn-secondary-action"><i class="fas fa-certificate"></i> إدارة المؤهلات الـ 18</a>
        <a href="career.php" class="btn-secondary-action"><i class="fas fa-history"></i> تحديث المسار المهني</a>
        <a href="portfolio.php" class="btn-secondary-action"><i class="fas fa-briefcase"></i> إضافة/تعديل مشاريع المعرض</a>
        <a href="digital-products.php" class="btn-secondary-action"><i class="fas fa-university"></i> مراجعة تحويلات بنك D360</a>
        <a href="landing-pages.php" class="btn-secondary-action"><i class="fas fa-magic"></i> إنشاء لاندنج بيدج جديدة</a>
    </div>
</div>

<!-- Recent Orders Section -->
<div class="glass-card">
    <div class="card-header-flex">
        <h2 class="card-title"><i class="fas fa-money-check-alt"></i> أحدث طلبات المنتجات الرقمية وتحويلات بنك D360</h2>
        <a href="digital-products.php" class="btn-secondary-action">عرض جميع الطلبات الإدارية ←</a>
    </div>

    <?php if (empty($recentOrders)): ?>
        <p style="color: var(--admin-text-muted); font-size: 0.9rem; text-align: center; padding: 2rem 0;">لا توجد طلبات تحويل جديدة حتى الآن.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>العميل</th>
                        <th>الجوال / البريد</th>
                        <th>المنتج</th>
                        <th>المبلغ</th>
                        <th>الحالة</th>
                        <th>الإيصال</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $ord): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($ord['order_number'] ?? '#' . $ord['id']); ?></strong></td>
                            <td><?php echo htmlspecialchars($ord['customer_name']); ?></td>
                            <td>
                                <div><?php echo htmlspecialchars($ord['customer_phone']); ?></div>
                                <div style="font-size: 0.78rem; color: var(--admin-text-muted);"><?php echo htmlspecialchars($ord['customer_email']); ?></div>
                            </td>
                            <td><span style="color: var(--admin-primary); font-weight: 700;"><?php echo htmlspecialchars($ord['product_title']); ?></span></td>
                            <td><strong><?php echo $ord['amount_sar']; ?> ﷼</strong></td>
                            <td>
                                <?php if ($ord['status'] === 'approved'): ?>
                                    <span style="background: rgba(34, 197, 94, 0.2); color: #4ADE80; padding: 4px 10px; border-radius: 12px; font-weight: 700; font-size: 0.8rem;">مؤكد ومكتمل ✅</span>
                                <?php elseif ($ord['status'] === 'rejected'): ?>
                                    <span style="background: rgba(239, 68, 68, 0.2); color: #F87171; padding: 4px 10px; border-radius: 12px; font-weight: 700; font-size: 0.8rem;">مرفوض ❌</span>
                                <?php else: ?>
                                    <span style="background: rgba(245, 158, 11, 0.2); color: #FBBF24; padding: 4px 10px; border-radius: 12px; font-weight: 700; font-size: 0.8rem;">بانتظار التأكيد ⏳</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($ord['receipt_url'])): ?>
                                    <a href="../<?php echo htmlspecialchars($ord['receipt_url']); ?>" target="_blank" style="color: #38BDF8; font-weight: 700; text-decoration: underline;">عرض الإيصال 📎</a>
                                <?php else: ?>
                                    <span style="color: var(--admin-text-muted);">لا يوجد</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size: 0.8rem; color: var(--admin-text-muted);"><?php echo $ord['created_at']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
