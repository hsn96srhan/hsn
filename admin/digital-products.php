<?php
require_once __DIR__ . '/includes/header.php';

$db = getDBConnection();
$msg = '';
$msgType = 'success';

// Handle Actions (Update Bank Info, Save Product, Update Order Status, Delete Product)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'])) {
    $action = $_POST['action_type'];

    if ($action === 'update_bank_info') {
        try {
            $stmt = $db->prepare("UPDATE bank_accounts SET bank_name_ar=?, bank_name_en=?, account_name_ar=?, account_name_en=?, iban=?, swift=?, transfer_note_ar=?, transfer_note_en=? WHERE id=1");
            $stmt->execute([
                $_POST['bank_name_ar'], $_POST['bank_name_en'],
                $_POST['account_name_ar'], $_POST['account_name_en'],
                $_POST['iban'], $_POST['swift'],
                $_POST['transfer_note_ar'], $_POST['transfer_note_en']
            ]);
            $msg = 'تم تحديث بيانات الحساب البنكي (D360) بنجاح! 🏦';
        } catch (Exception $e) {
            $msg = 'خطأ أثناء تحديث البنك: ' . $e->getMessage();
            $msgType = 'danger';
        }
    } elseif ($action === 'save_product') {
        $id = (int)($_POST['product_id'] ?? 0);
        $title_ar = trim($_POST['title_ar'] ?? '');
        $title_en = trim($_POST['title_en'] ?? '');
        $badge_ar = trim($_POST['badge_ar'] ?? '');
        $badge_en = trim($_POST['badge_en'] ?? '');
        $category = trim($_POST['category'] ?? 'playbook');
        $price_sar = (float)($_POST['price_sar'] ?? 0);
        $orig_price_sar = (float)($_POST['original_price_sar'] ?? 0);
        $download_url = trim($_POST['download_url'] ?? '');
        $cover_image = trim($_POST['cover_image'] ?? '');
        $desc_ar = trim($_POST['description_ar'] ?? '');
        $desc_en = trim($_POST['description_en'] ?? '');
        $features_json = trim($_POST['features_json'] ?? '[]');
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        $is_published = isset($_POST['is_published']) ? 1 : 0;

        if (isset($_FILES['cover_file']) && $_FILES['cover_file']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['cover_file']['name'], PATHINFO_EXTENSION);
            $newFileName = 'prod_' . time() . '_' . rand(100,999) . '.' . strtolower($ext);
            $uploadDir = __DIR__ . '/../assets/images/';
            if (move_uploaded_file($_FILES['cover_file']['tmp_name'], $uploadDir . $newFileName)) {
                $cover_image = 'assets/images/' . $newFileName;
            }
        }

        if ($id > 0) {
            $stmt = $db->prepare("UPDATE digital_products SET title_ar=?, title_en=?, badge_ar=?, badge_en=?, category=?, price_sar=?, original_price_sar=?, download_url=?, cover_image=?, description_ar=?, description_en=?, features_json=?, sort_order=?, is_published=? WHERE id=?");
            $stmt->execute([$title_ar, $title_en, $badge_ar, $badge_en, $category, $price_sar, $orig_price_sar, $download_url, $cover_image, $desc_ar, $desc_en, $features_json, $sort_order, $is_published, $id]);
            $msg = 'تم تحديث بيانات المنتج الرقمي بنجاح! 💳';
        } else {
            $stmt = $db->prepare("INSERT INTO digital_products (title_ar, title_en, badge_ar, badge_en, category, price_sar, original_price_sar, download_url, cover_image, description_ar, description_en, features_json, sort_order, is_published) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$title_ar, $title_en, $badge_ar, $badge_en, $category, $price_sar, $orig_price_sar, $download_url, $cover_image, $desc_ar, $desc_en, $features_json, $sort_order, $is_published]);
            $msg = 'تمت إضافة المنتج الرقمي الجديد بنجاح! 📦';
        }
    } elseif ($action === 'update_order_status') {
        $order_id = (int)$_POST['order_id'];
        $status = $_POST['status'];
        $stmt = $db->prepare("UPDATE bank_orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $order_id]);
        $msg = 'تم تحديث حالة طلب الإيصال إلى (' . $status . ') بنجاح. ✅';
    } elseif ($action === 'delete_product') {
        $id = (int)$_POST['product_id'];
        $stmt = $db->prepare("DELETE FROM digital_products WHERE id = ?");
        $stmt->execute([$id]);
        $msg = 'تم حذف المنتج بنجاح.';
    }
}

// Fetch Bank Account Info
$bank = $db->query("SELECT * FROM bank_accounts WHERE id = 1")->fetch();

// Fetch Digital Products
$products = $db->query("SELECT * FROM digital_products ORDER BY sort_order ASC, id ASC")->fetchAll();

// Fetch Bank Orders
$orders = $db->query("SELECT * FROM bank_orders ORDER BY id DESC")->fetchAll();

$editProd = null;
if (isset($_GET['edit_product_id'])) {
    $editId = (int)$_GET['edit_product_id'];
    $stmtEdit = $db->prepare("SELECT * FROM digital_products WHERE id = ?");
    $stmtEdit->execute([$editId]);
    $editProd = $stmtEdit->fetch();
}
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.8rem;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 900; color: #FFF; margin-bottom: 4px;">المنتجات الرقمية وإيصالات D360</h1>
        <p style="color: var(--admin-text-muted); font-size: 0.92rem;">التحكم الكامل بالمنتجات، تفاصيل حساب D360، ومراجعة وتأكيد إيصالات التحويل البنكي المباشرة.</p>
    </div>
    <a href="digital-products.php#prod_form_section" class="btn-action">➕ إضافة منتج رقمي جديد</a>
</div>

<?php if (!empty($msg)): ?>
    <div class="alert-box alert-<?php echo $msgType; ?>"><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>

<!-- 1. D360 Bank Account Details Control -->
<div class="glass-card">
    <div class="card-header-flex">
        <h2 class="card-title"><i class="fas fa-university"></i> إعدادات حساب بنك D360 المباشر (لتحويلات المنتجات)</h2>
    </div>

    <form method="POST" action="digital-products.php">
        <input type="hidden" name="action_type" value="update_bank_info">

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">اسم البنك (عربي)</label>
                <input type="text" name="bank_name_ar" class="form-input" required value="<?php echo htmlspecialchars($bank['bank_name_ar'] ?? 'D360 بنك'); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">اسم البنك (إنجليزي)</label>
                <input type="text" name="bank_name_en" class="form-input" required value="<?php echo htmlspecialchars($bank['bank_name_en'] ?? 'D360 Bank'); ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">اسم صاحب الحساب (عربي) *</label>
                <input type="text" name="account_name_ar" class="form-input" required value="<?php echo htmlspecialchars($bank['account_name_ar'] ?? 'حسن فايز حسن سرحان'); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">اسم صاحب الحساب (إنجليزي)</label>
                <input type="text" name="account_name_en" class="form-input" required value="<?php echo htmlspecialchars($bank['account_name_en'] ?? 'Hassan Fayez Hassan Sarhan'); ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">رقم الآيبان (IBAN) *</label>
                <input type="text" name="iban" class="form-input" required value="<?php echo htmlspecialchars($bank['iban'] ?? 'SA6936036036047948932598'); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">رمز السويفت (SWIFT Code)</label>
                <input type="text" name="swift" class="form-input" value="<?php echo htmlspecialchars($bank['swift'] ?? 'DBAKSARIXXX'); ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">تنبيه الغرض من التحويل (عربي)</label>
                <input type="text" name="transfer_note_ar" class="form-input" value="<?php echo htmlspecialchars($bank['transfer_note_ar'] ?? 'يرجى اختيار الغرض من التحويل صديق'); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">تنبيه الغرض من التحويل (إنجليزي)</label>
                <input type="text" name="transfer_note_en" class="form-input" value="<?php echo htmlspecialchars($bank['transfer_note_en'] ?? 'Please select transfer purpose as Friend'); ?>">
            </div>
        </div>

        <button type="submit" class="btn-action">💾 حفظ إعدادات حساب بنك D360</button>
    </form>
</div>

<!-- 2. Live Bank Receipts & Orders Hub -->
<div class="glass-card">
    <div class="card-header-flex">
        <h2 class="card-title"><i class="fas fa-receipt"></i> مركز مراجعة وتأكيد إيصالات التحويل البنكي (<?php echo count($orders); ?>)</h2>
    </div>

    <?php if (empty($orders)): ?>
        <p style="color: var(--admin-text-muted); font-size: 0.9rem; text-align: center; padding: 2rem 0;">لا توجد طلبات تحويل مسجلة حتى الآن.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>اسم العميل</th>
                        <th>التواصل (جوال/إيميل)</th>
                        <th>المنتج المطلوب</th>
                        <th>المبلغ</th>
                        <th>الإيصال المرفق</th>
                        <th>الحالة والتغيير</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($o['order_number'] ?? '#' . $o['id']); ?></strong></td>
                            <td><strong><?php echo htmlspecialchars($o['customer_name']); ?></strong></td>
                            <td>
                                <div>📱 <?php echo htmlspecialchars($o['customer_phone']); ?></div>
                                <div style="font-size:0.78rem; color:var(--admin-text-muted);">✉️ <?php echo htmlspecialchars($o['customer_email']); ?></div>
                            </td>
                            <td><span style="color: var(--admin-primary); font-weight: 700;"><?php echo htmlspecialchars($o['product_title']); ?></span></td>
                            <td><strong><?php echo $o['amount_sar']; ?> ﷼</strong></td>
                            <td>
                                <?php if (!empty($o['receipt_url'])): ?>
                                    <a href="../<?php echo htmlspecialchars($o['receipt_url']); ?>" target="_blank" class="btn-secondary-action" style="padding:4px 8px; font-size:0.78rem;">عرض الإيصال 📎</a>
                                <?php else: ?>
                                    <span style="color:var(--admin-text-muted);">بدون مرفق</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="POST" action="digital-products.php" style="display:flex; gap:6px; align-items:center;">
                                    <input type="hidden" name="action_type" value="update_order_status">
                                    <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                    <select name="status" class="form-select" style="padding:4px 8px; font-size:0.8rem;" onchange="this.form.submit()">
                                        <option value="pending" <?php echo $o['status'] === 'pending' ? 'selected' : ''; ?>>⏳ بانتظار التأكيد</option>
                                        <option value="approved" <?php echo $o['status'] === 'approved' ? 'selected' : ''; ?>>✅ مؤكد ومكتمل</option>
                                        <option value="rejected" <?php echo $o['status'] === 'rejected' ? 'selected' : ''; ?>>❌ مرفوض</option>
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

<!-- 3. Add/Edit Digital Product Form -->
<div class="glass-card" id="prod_form_section">
    <div class="card-header-flex">
        <h2 class="card-title">
            <i class="fas fa-box-open"></i> 
            <?php echo $editProd ? 'تعديل المنتج الرقمي #' . $editProd['id'] : 'إضافة منتج رقمي / دليل إستراتيجي جديد'; ?>
        </h2>
        <?php if ($editProd): ?>
            <a href="digital-products.php" class="btn-secondary-action">إلغاء التعديل ✖️</a>
        <?php endif; ?>
    </div>

    <form method="POST" action="digital-products.php" enctype="multipart/form-data">
        <input type="hidden" name="action_type" value="save_product">
        <input type="hidden" name="product_id" value="<?php echo $editProd['id'] ?? 0; ?>">

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">عنوان المنتج (عربي) *</label>
                <input type="text" name="title_ar" class="form-input" required value="<?php echo htmlspecialchars($editProd['title_ar'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">عنوان المنتج (إنجليزي)</label>
                <input type="text" name="title_en" class="form-input" value="<?php echo htmlspecialchars($editProd['title_en'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">شارة المنتج (عربي)</label>
                <input type="text" name="badge_ar" class="form-input" placeholder="🥇 الأكثر مبيعاً" value="<?php echo htmlspecialchars($editProd['badge_ar'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">السعر الحالي (SAR) *</label>
                <input type="number" step="0.01" name="price_sar" class="form-input" required value="<?php echo $editProd['price_sar'] ?? '199'; ?>">
            </div>
            <div class="form-group">
                <label class="form-label">السعر السابق لخصم (SAR)</label>
                <input type="number" step="0.01" name="original_price_sar" class="form-input" value="<?php echo $editProd['original_price_sar'] ?? '499'; ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">وصف المنتج (عربي)</label>
                <textarea name="description_ar" class="form-textarea" rows="2"><?php echo htmlspecialchars($editProd['description_ar'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">الميزات والمحتويات (JSON Array)</label>
                <textarea name="features_json" class="form-textarea" rows="2" placeholder='["ميزة 1", "ميزة 2"]'><?php echo htmlspecialchars($editProd['features_json'] ?? '[]'); ?></textarea>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">رابط صورة الغلاف الحالي</label>
                <input type="text" name="cover_image" class="form-input" value="<?php echo htmlspecialchars($editProd['cover_image'] ?? 'assets/images/'); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">أو رفع غلاف جديد</label>
                <input type="file" name="cover_file" class="form-input" accept="image/*">
            </div>
            <div class="form-group">
                <label class="form-label">رابط التحميل / تسليم الملف (PDF/Zip)</label>
                <input type="text" name="download_url" class="form-input" value="<?php echo htmlspecialchars($editProd['download_url'] ?? 'assets/files/playbook.pdf'); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">ترتيب الظهور (sort_order)</label>
                <input type="number" name="sort_order" class="form-input" value="<?php echo $editProd['sort_order'] ?? count($products) + 1; ?>">
            </div>
        </div>

        <div style="margin-bottom: 1.2rem; display:flex; align-items:center; gap:8px;">
            <input type="checkbox" name="is_published" id="is_published_dp" value="1" <?php echo (!isset($editProd) || !empty($editProd['is_published'])) ? 'checked' : ''; ?>>
            <label for="is_published_dp" style="font-size:0.9rem; font-weight:700; color:#FFF; cursor:pointer;">إظهار هذا المنتج في كاتالوج المنتجات الرقمية الحي</label>
        </div>

        <button type="submit" class="btn-action">💾 <?php echo $editProd ? 'حفظ التعديلات' : 'إضافة المنتج الرقمي'; ?></button>
    </form>
</div>

<!-- 4. Digital Products Catalog List -->
<div class="glass-card">
    <div class="card-header-flex">
        <h2 class="card-title"><i class="fas fa-shopping-bag"></i> قائمة المنتجات الرقمية الحالية (<?php echo count($products); ?>)</h2>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID / الترتيب</th>
                    <th>اسم المنتج</th>
                    <th>السعر</th>
                    <th>الشارة</th>
                    <th>الحالة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td>
                            <strong style="color: var(--admin-primary);">#<?php echo $p['id']; ?></strong>
                            <div style="font-size:0.75rem; color:var(--admin-text-muted);">ترتيب: <?php echo $p['sort_order']; ?></div>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($p['title_ar']); ?></strong>
                            <div style="font-size: 0.8rem; color: var(--admin-text-muted);"><?php echo htmlspecialchars($p['title_en']); ?></div>
                        </td>
                        <td>
                            <strong style="color: #4ADE80;"><?php echo $p['price_sar']; ?> ﷼</strong>
                            <span style="font-size:0.78rem; text-decoration:line-through; color:var(--admin-text-muted);"><?php echo $p['original_price_sar']; ?> ﷼</span>
                        </td>
                        <td>
                            <span style="background: rgba(212, 175, 55, 0.15); color: var(--admin-primary); padding: 4px 10px; border-radius: 12px; font-weight: 700; font-size: 0.8rem;">
                                <?php echo htmlspecialchars($p['badge_ar']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($p['is_published'])): ?>
                                <span style="color: #4ADE80; font-weight: 700; font-size: 0.82rem;">منشور 🟢</span>
                            <?php else: ?>
                                <span style="color: #F87171; font-weight: 700; font-size: 0.82rem;">مخفي 🔴</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display:flex; gap: 6px;">
                                <a href="digital-products.php?edit_product_id=<?php echo $p['id']; ?>#prod_form_section" class="btn-secondary-action" style="padding: 4px 10px; font-size: 0.8rem;">تعديل ✏️</a>
                                <form method="POST" action="digital-products.php" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا المنتج الرقمي؟');">
                                    <input type="hidden" name="action_type" value="delete_product">
                                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                    <button type="submit" class="btn-danger-action">حذف 🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
