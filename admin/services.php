<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/upload.php';
requireAuth();

$db = getDB();
$msg = '';
$msgType = 'success';

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $item = $db->prepare("SELECT * FROM services WHERE id = ?");
    $item->execute([$id]);
    $row = $item->fetch();
    if ($row) {
        deleteFile($row['icon'] ?? '');
        $db->prepare("DELETE FROM services WHERE id = ?")->execute([$id]);
        $msg = 'Service deleted.';
    }
}
if (isset($_GET['toggle'])) {
    $db->prepare("UPDATE services SET is_active = NOT is_active WHERE id = ?")->execute([intval($_GET['toggle'])]);
    $msg = 'Status updated.';
}

$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([intval($_GET['edit'])]);
    $editing = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id           = intval($_POST['id'] ?? 0);
    $title_fr     = trim($_POST['title_fr'] ?? '');
    $title_en     = trim($_POST['title_en'] ?? '');
    $desc_fr      = trim($_POST['description_fr'] ?? '');
    $desc_en      = trim($_POST['description_en'] ?? '');
    $price        = trim($_POST['price'] ?? '');
    $price_lbl_fr = trim($_POST['price_label_fr'] ?? '');
    $price_lbl_en = trim($_POST['price_label_en'] ?? '');
    $icon         = trim($_POST['icon'] ?? '');
    $existingIcon = $_POST['existing_icon'] ?? '';
    $is_addon     = isset($_POST['is_addon']) ? 1 : 0;
    $is_active    = isset($_POST['is_active']) ? 1 : 0;
    $sort_order   = intval($_POST['sort_order'] ?? 0);

    if (!empty($_FILES['icon_upload']['name'])) {
        $up = handleUpload($_FILES['icon_upload'], 'services', ALLOWED_ALL_TYPES);
        if ($up['success']) {
            if ($existingIcon) deleteFile($existingIcon);
            $icon = $up['path'];
        } else {
            $msg = $up['error'];
            $msgType = 'error';
        }
    }

    if (!$msg || $msgType !== 'error') {
        if ($id) {
            $db->prepare("UPDATE services SET title_fr=?,title_en=?,description_fr=?,description_en=?,price=?,price_label_fr=?,price_label_en=?,icon=?,is_addon=?,is_active=?,sort_order=? WHERE id=?")
               ->execute([$title_fr, $title_en, $desc_fr, $desc_en, $price, $price_lbl_fr, $price_lbl_en, $icon, $is_addon, $is_active, $sort_order, $id]);
            $msg = 'Service updated.';
        } else {
            $db->prepare("INSERT INTO services (title_fr,title_en,description_fr,description_en,price,price_label_fr,price_label_en,icon,is_addon,is_active,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?,?)")
               ->execute([$title_fr, $title_en, $desc_fr, $desc_en, $price, $price_lbl_fr, $price_lbl_en, $icon, $is_addon, $is_active, $sort_order]);
            $msg = 'Service added.';
        }
        header('Location: ' . SITE_URL . '/admin/services.php?msg=' . urlencode($msg));
        exit;
    }
}

if (isset($_GET['msg'])) $msg = $_GET['msg'];
$services = $db->query("SELECT * FROM services ORDER BY is_addon ASC, sort_order ASC")->fetchAll();

require_once __DIR__ . '/layout.php';
adminHeader('Services', 'services');
?>

<?php if ($msg): ?>
<div class="alert alert-<?= $msgType === 'error' ? 'error' : 'success' ?>" style="margin-bottom:1.5rem;"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if ($editing || isset($_GET['add'])): ?>
<div class="admin-card" style="margin-bottom:2rem;">
    <div class="admin-card-header">
        <span class="admin-card-title"><?= $editing ? 'Edit Service' : 'Add New Service' ?></span>
        <a href="<?= SITE_URL ?>/admin/services.php" class="topbar-btn secondary">Cancel</a>
    </div>
    <div class="admin-card-body">
        <form method="POST" enctype="multipart/form-data" class="admin-form validate-form">
            <?php if ($editing): ?>
            <input type="hidden" name="id" value="<?= $editing['id'] ?>">
            <input type="hidden" name="existing_icon" value="<?= htmlspecialchars($editing['icon'] ?? '') ?>">
            <?php endif; ?>

            <div class="form-section-title">Titles</div>
            <div class="form-row">
                <div class="form-group">
                    <label>Title (French) *</label>
                    <input type="text" name="title_fr" value="<?= htmlspecialchars($editing['title_fr'] ?? '') ?>" required>
                    <div class="form-error">Required</div>
                </div>
                <div class="form-group">
                    <label>Title (English) *</label>
                    <input type="text" name="title_en" value="<?= htmlspecialchars($editing['title_en'] ?? '') ?>" required>
                    <div class="form-error">Required</div>
                </div>
            </div>

            <div class="form-section-title">Descriptions</div>
            <div class="form-row">
                <div class="form-group">
                    <label>Description (French)</label>
                    <textarea name="description_fr" rows="4"><?= htmlspecialchars($editing['description_fr'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Description (English)</label>
                    <textarea name="description_en" rows="4"><?= htmlspecialchars($editing['description_en'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="form-section-title">Pricing</div>
            <div class="form-row">
                <div class="form-group">
                    <label>Price (e.g. 1500 MAD)</label>
                    <input type="text" name="price" value="<?= htmlspecialchars($editing['price'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Icon (emoji or upload image/video)</label>
                    <input type="text" name="icon" value="<?= htmlspecialchars($editing['icon'] ?? '') ?>" placeholder="📸">
                    <small style="color:var(--text-muted);display:block;margin-top:.35rem;">You can keep an emoji, or upload an image/video to replace it.</small>
                    <input type="file" name="icon_upload" accept="image/*,video/mp4,video/webm" style="margin-top:.75rem;">
                    <?php if (!empty($editing['icon']) && assetUrl($editing['icon'])): ?>
                    <div style="margin-top:.75rem;display:flex;align-items:center;gap:.75rem;">
                        <?php if (isVideoAsset($editing['icon'])): ?>
                        <video src="<?= assetUrl($editing['icon']) ?>" controls style="width:72px;height:72px;border-radius:12px;object-fit:cover;"></video>
                        <?php else: ?>
                        <img src="<?= assetUrl($editing['icon']) ?>" alt="Current icon" style="width:72px;height:72px;border-radius:12px;object-fit:cover;">
                        <?php endif; ?>
                        <span style="color:var(--text-muted);font-size:.9rem;">Current icon file</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Price Label (French)</label>
                    <input type="text" name="price_label_fr" value="<?= htmlspecialchars($editing['price_label_fr'] ?? 'À partir de') ?>">
                </div>
                <div class="form-group">
                    <label>Price Label (English)</label>
                    <input type="text" name="price_label_en" value="<?= htmlspecialchars($editing['price_label_en'] ?? 'Starting from') ?>">
                </div>
            </div>

            <div class="form-section-title">Settings</div>
            <div class="form-row">
                <div class="form-group">
                    <label>Sort Order</label>
                    <input type="number" name="sort_order" value="<?= intval($editing['sort_order'] ?? 0) ?>" min="0">
                </div>
            </div>
            <div style="display:flex;gap:2rem;align-items:center;flex-wrap:wrap;margin-bottom:1.5rem;">
                <label class="toggle-switch">
                    <input type="checkbox" name="is_active" <?= ($editing['is_active'] ?? 1) ? 'checked' : '' ?>>
                    <div class="toggle-track"></div>
                    <span style="color:var(--text-muted);font-size:0.9rem;">Active</span>
                </label>
                <label class="toggle-switch">
                    <input type="checkbox" name="is_addon" <?= ($editing['is_addon'] ?? 0) ? 'checked' : '' ?>>
                    <div class="toggle-track"></div>
                    <span style="color:var(--text-muted);font-size:0.9rem;">Add-on service</span>
                </label>
            </div>

            <div style="display:flex;gap:1rem;">
                <button type="submit" class="topbar-btn"><?= $editing ? 'Save Changes' : 'Add Service' ?></button>
                <a href="<?= SITE_URL ?>/admin/services.php" class="topbar-btn secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="admin-card">
    <div class="admin-card-header">
        <span class="admin-card-title">All Services (<?= count($services) ?>)</span>
        <a href="?add=1" class="topbar-btn">+ Add Service</a>
    </div>
    <div class="admin-table-wrap">
        <?php if (empty($services)): ?>
        <div class="empty-state"><div class="empty-state-icon">⚡</div><p>No services yet.</p></div>
        <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Icon</th>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Type</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($services as $svc): ?>
                <tr>
                    <td style="font-size:1.5rem;">
                        <?php $svcIcon = assetUrl($svc['icon'] ?? ''); ?>
                        <?php if ($svcIcon && isVideoAsset($svc['icon'])): ?>
                            <video src="<?= $svcIcon ?>" autoplay muted loop playsinline style="width:48px;height:48px;border-radius:12px;object-fit:cover;"></video>
                        <?php elseif ($svcIcon): ?>
                            <img src="<?= $svcIcon ?>" alt="" style="width:48px;height:48px;border-radius:12px;object-fit:cover;">
                        <?php else: ?>
                            <?= htmlspecialchars($svc['icon'] ?? '⚡') ?>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($svc['title_en']) ?></td>
                    <td><?= htmlspecialchars($svc['price'] ?? '—') ?></td>
                    <td><?= $svc['is_addon'] ? '<span class="badge badge-pending">Add-on</span>' : '<span class="badge badge-active">Core</span>' ?></td>
                    <td><?= $svc['sort_order'] ?></td>
                    <td><span class="badge badge-<?= $svc['is_active'] ? 'active' : 'inactive' ?>"><?= $svc['is_active'] ? 'Active' : 'Hidden' ?></span></td>
                    <td>
                        <div class="action-btns">
                            <a href="?edit=<?= $svc['id'] ?>" class="action-btn action-btn-edit">Edit</a>
                            <a href="?toggle=<?= $svc['id'] ?>" class="action-btn action-btn-toggle"><?= $svc['is_active'] ? 'Hide' : 'Show' ?></a>
                            <a href="?delete=<?= $svc['id'] ?>" class="action-btn action-btn-delete confirm-delete">Delete</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php adminFooter(); ?>
