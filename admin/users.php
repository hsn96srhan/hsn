<?php
require_once __DIR__ . '/includes/header.php';
requireAdmin(); // Super Admin privilege required for managing assistant accounts

$db = getDBConnection();
$msg = '';
$msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'])) {
    $action = $_POST['action_type'];

    if ($action === 'save_user') {
        $id = (int)($_POST['user_id'] ?? 0);
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role = trim($_POST['role'] ?? 'editor');
        $status = trim($_POST['status'] ?? 'active');

        if ($id > 0) {
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE users SET username=?, email=?, password_hash=?, role=?, status=? WHERE id=?");
                $stmt->execute([$username, $email, $hash, $role, $status, $id]);
            } else {
                $stmt = $db->prepare("UPDATE users SET username=?, email=?, role=?, status=? WHERE id=?");
                $stmt->execute([$username, $email, $role, $status, $id]);
            }
            $msg = 'تم تحديث بيانات حساب المساعد بنجاح! 👤';
        } else {
            if (empty($password)) {
                $msg = 'كلمة المرور مطلوبة للحسابات الجديدة.';
                $msgType = 'danger';
            } else {
                try {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("INSERT INTO users (username, email, password_hash, role, status) VALUES (?,?,?,?,?)");
                    $stmt->execute([$username, $email, $hash, $role, $status]);
                    $msg = 'تم إضافة المساعد الجديد بنجاح! 🎉';
                } catch (Exception $e) {
                    $msg = 'اسم المستخدم أو البريد مستخدم بالفعل.';
                    $msgType = 'danger';
                }
            }
        }
    } elseif ($action === 'toggle_user_status') {
        $user_id = (int)$_POST['user_id'];
        $new_status = $_POST['new_status'];
        if ($user_id !== (int)$currentUser['id']) { // Protect current logged in user from self suspension
            $stmt = $db->prepare("UPDATE users SET status = ? WHERE id = ?");
            $stmt->execute([$new_status, $user_id]);
            $msg = 'تم تغيير حالة الحساب إلى (' . $new_status . ') بنجاح.';
        } else {
            $msg = 'لا يمكنك تعطيل حسابك الشخصي أثناء الجلسة.';
            $msgType = 'danger';
        }
    } elseif ($action === 'delete_user') {
        $user_id = (int)$_POST['user_id'];
        if ($user_id !== (int)$currentUser['id']) {
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $msg = 'تم حذف حساب المساعد بنجاح.';
        } else {
            $msg = 'لا يمكنك حذف حسابك الشخصي الحجم الحالي.';
            $msgType = 'danger';
        }
    }
}

$users = $db->query("SELECT * FROM users ORDER BY id ASC")->fetchAll();

$editUser = null;
if (isset($_GET['edit_id'])) {
    $editId = (int)$_GET['edit_id'];
    $stmtEdit = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmtEdit->execute([$editId]);
    $editUser = $stmtEdit->fetch();
}
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.8rem;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 900; color: #FFF; margin-bottom: 4px;">إدارة المساعدين والوصول المتعدد (Sub-Users & Assistants)</h1>
        <p style="color: var(--admin-text-muted); font-size: 0.92rem;">إضافة مساعدين لإدارة المنصة، تعيين الصلاحيات وتفعيل/تعطيل الحسابات على الموقع المباشر.</p>
    </div>
    <a href="users.php#user_form_section" class="btn-action">👤 إضافة مساعد جديد</a>
</div>

<?php if (!empty($msg)): ?>
    <div class="alert-box alert-<?php echo $msgType; ?>"><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>

<!-- Form Section -->
<div class="glass-card" id="user_form_section">
    <div class="card-header-flex">
        <h2 class="card-title">
            <i class="fas fa-user-plus"></i> 
            <?php echo $editUser ? 'تعديل بيانات المساعد #' . $editUser['id'] : 'إضافة حساب مساعد جديد'; ?>
        </h2>
        <?php if ($editUser): ?>
            <a href="users.php" class="btn-secondary-action">إلغاء التعديل ✖️</a>
        <?php endif; ?>
    </div>

    <form method="POST" action="users.php">
        <input type="hidden" name="action_type" value="save_user">
        <input type="hidden" name="user_id" value="<?php echo $editUser['id'] ?? 0; ?>">

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">اسم المستخدم (Username) *</label>
                <input type="text" name="username" class="form-input" required value="<?php echo htmlspecialchars($editUser['username'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">البريد الإلكتروني *</label>
                <input type="email" name="email" class="form-input" required value="<?php echo htmlspecialchars($editUser['email'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">كلمة المرور <?php echo $editUser ? '(اتركها فارغة للتعديل بدون تغيير)' : '*'; ?></label>
                <input type="password" name="password" class="form-input" <?php echo $editUser ? '' : 'required'; ?> placeholder="••••••••">
            </div>
            <div class="form-group">
                <label class="form-label">صلاحية الحساب (Role) *</label>
                <select name="role" class="form-select">
                    <option value="editor" <?php echo ($editUser['role'] ?? '') === 'editor' ? 'selected' : ''; ?>>محرر محتوى (Editor)</option>
                    <option value="seo" <?php echo ($editUser['role'] ?? '') === 'seo' ? 'selected' : ''; ?>>مسؤول الـ SEO والمقالات</option>
                    <option value="sales" <?php echo ($editUser['role'] ?? '') === 'sales' ? 'selected' : ''; ?>>مسؤول مبيعات وإيصالات D360</option>
                    <option value="admin" <?php echo ($editUser['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>مدير كامل (Super Admin)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">حالة الحساب *</label>
                <select name="status" class="form-select">
                    <option value="active" <?php echo ($editUser['status'] ?? '') !== 'suspended' ? 'selected' : ''; ?>>مفعل (Active)</option>
                    <option value="suspended" <?php echo ($editUser['status'] ?? '') === 'suspended' ? 'selected' : ''; ?>>معطل (Suspended)</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn-action">💾 <?php echo $editUser ? 'حفظ التعديلات' : 'إضافة الحساب'; ?></button>
    </form>
</div>

<!-- Users List Table -->
<div class="glass-card">
    <div class="card-header-flex">
        <h2 class="card-title"><i class="fas fa-users"></i> قائمة المساعدين والمستخدمين (<?php echo count($users); ?>)</h2>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>اسم المستخدم</th>
                    <th>البريد الإلكتروني</th>
                    <th>الصلاحية</th>
                    <th>الحالة</th>
                    <th>إجراءات والتفعيل</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><strong>#<?php echo $u['id']; ?></strong></td>
                        <td>
                            <strong><?php echo htmlspecialchars($u['username']); ?></strong>
                            <?php if ($u['id'] === $currentUser['id']): ?>
                                <span style="background: rgba(212, 175, 55, 0.2); color: var(--admin-primary); padding: 2px 6px; border-radius: 6px; font-size: 0.72rem;">أنت</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td>
                            <span class="badge-role" style="font-size:0.78rem;">
                                <?php echo htmlspecialchars($u['role']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if (($u['status'] ?? 'active') === 'active'): ?>
                                <span style="color: #4ADE80; font-weight: 700; font-size: 0.82rem;">مفعل 🟢</span>
                            <?php else: ?>
                                <span style="color: #F87171; font-weight: 700; font-size: 0.82rem;">معطل 🔴</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display:flex; gap: 6px; align-items:center;">
                                <a href="users.php?edit_id=<?php echo $u['id']; ?>#user_form_section" class="btn-secondary-action" style="padding: 4px 10px; font-size: 0.8rem;">تعديل ✏️</a>

                                <?php if ($u['id'] !== $currentUser['id']): ?>
                                    <form method="POST" action="users.php" style="display:inline;">
                                        <input type="hidden" name="action_type" value="toggle_user_status">
                                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                        <input type="hidden" name="new_status" value="<?php echo ($u['status'] ?? 'active') === 'active' ? 'suspended' : 'active'; ?>">
                                        <button type="submit" class="btn-secondary-action" style="padding: 4px 10px; font-size: 0.8rem;">
                                            <?php echo ($u['status'] ?? 'active') === 'active' ? 'تعطيل ⏸️' : 'تفعيل ▶️'; ?>
                                        </button>
                                    </form>

                                    <form method="POST" action="users.php" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف حساب هذا المساعد؟');">
                                        <input type="hidden" name="action_type" value="delete_user">
                                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                        <button type="submit" class="btn-danger-action">حذف 🗑️</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
