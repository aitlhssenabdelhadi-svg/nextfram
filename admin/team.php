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
    $row = $db->prepare("SELECT * FROM team WHERE id = ?");
    $row->execute([$id]);
    $member = $row->fetch();
    if ($member) {
        deleteFile($member['photo']);
        $db->prepare("DELETE FROM team WHERE id = ?")->execute([$id]);
        $msg = 'Team member deleted.';
    }
}
if (isset($_GET['toggle'])) {
    $db->prepare("UPDATE team SET is_active = NOT is_active WHERE id = ?")->execute([intval($_GET['toggle'])]);
    $msg = 'Visibility updated.';
}

$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM team WHERE id = ?");
    $stmt->execute([intval($_GET['edit'])]);
    $editing = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id           = intval($_POST['id'] ?? 0);
    $full_name    = trim($_POST['full_name'] ?? '');
    $role_fr      = trim($_POST['role_fr'] ?? '');
    $role_en      = trim($_POST['role_en'] ?? '');
    $bio_fr       = trim($_POST['bio_fr'] ?? '');
    $bio_en       = trim($_POST['bio_en'] ?? '');
    $instagram    = trim($_POST['instagram_url'] ?? '');
    $sort_order   = intval($_POST['sort_order'] ?? 0);
    $is_active    = isset($_POST['is_active']) ? 1 : 0;

    $photoPath = $_POST['existing_photo'] ?? '';

    if (!empty($_FILES['photo']['name'])) {
        $up = handleUpload($_FILES['photo'], 'team', ALLOWED_IMAGE_TYPES);
        if ($up['success']) {
            if ($photoPath) deleteFile($photoPath);
            $photoPath = $up['path'];
        } else {
            $msg = $up['error'];
            $msgType = 'error';
        }
    }

    if (!$msg || $msgType !== 'error') {
        if ($id) {
            $db->prepare("UPDATE team SET full_name=?,role_fr=?,role_en=?,bio_fr=?,bio_en=?,photo=?,instagram_url=?,sort_order=?,is_active=? WHERE id=?")
               ->execute([$full_name,$role_fr,$role_en,$bio_fr,$bio_en,$photoPath,$instagram,$sort_order,$is_active,$id]);
            $msg = 'Team member updated.';
        } else {
            $db->prepare("INSERT INTO team (full_name,role_fr,role_en,bio_fr,bio_en,photo,instagram_url,sort_order,is_active) VALUES (?,?,?,?,?,?,?,?,?)")
               ->execute([$full_name,$role_fr,$role_en,$bio_fr,$bio_en,$photoPath,$instagram,$sort_order,$is_active]);
            $msg = 'Team member added.';
        }
        header('Location: ' . SITE_URL . '/admin/team.php?msg=' . urlencode($msg));
        exit;
    }
}

if (isset($_GET['msg'])) $msg = $_GET['msg'];
$team = $db->query("SELECT * FROM team ORDER BY sort_order ASC")->fetchAll();

require_once __DIR__ . '/layout.php';
adminHeader('Team', 'team');
?>

<?php if ($msg): ?>
<div class="alert alert-<?= $msgType === 'error' ? 'error' : 'success' ?>" style="margin-bottom:1.5rem;"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if ($editing || isset($_GET['add'])): ?>
<div class="admin-card" style="margin-bottom:2rem;">
    <div class="admin-card-header">
        <span class="admin-card-title"><?= $editing ? 'Edit Team Member' : 'Add Team Member' ?></span>
        <a href="<?= SITE_URL ?>/admin/team.php" class="topbar-btn secondary">Cancel</a>
    </div>
    <div class="admin-card-body">
        <form method="POST" enctype="multipart/form-data" class="admin-form validate-form">
            <?php if ($editing): ?>
            <input type="hidden" name="id" value="<?= $editing['id'] ?>">
            <input type="hidden" name="existing_photo" value="<?= htmlspecialchars($editing['photo'] ?? '') ?>">
            <?php endif; ?>

            <div class="form-section-title">Profile Photo</div>
            <div class="form-group" style="max-width:400px;">
                <div class="upload-zone" onclick="document.getElementById('photo_input').click()">
                    <div class="upload-zone-icon">👤</div>
                    <div class="upload-zone-text">Click or drag <strong>profile photo</strong> here<br><small>JPG, PNG, WEBP — max 50MB</small></div>
                </div>
                <input type="file" id="photo_input" name="photo" accept="image/*" style="display:none" onchange="previewPhoto(this)">
                <div class="upload-preview" id="photo_preview">
                    <?php if (!empty($editing['photo']) && assetUrl($editing['photo'])): ?>
                    <div class="current-file">
                        <img src="<?= assetUrl($editing['photo']) ?>" alt="" style="width:60px;height:60px;object-fit:cover;border-radius:50%;">
                        Current photo
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-section-title">Personal Info</div>
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($editing['full_name'] ?? '') ?>" required>
                <div class="form-error">Required</div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Role (French) *</label>
                    <input type="text" name="role_fr" value="<?= htmlspecialchars($editing['role_fr'] ?? '') ?>" required placeholder="Directeur Créatif">
                    <div class="form-error">Required</div>
                </div>
                <div class="form-group">
                    <label>Role (English) *</label>
                    <input type="text" name="role_en" value="<?= htmlspecialchars($editing['role_en'] ?? '') ?>" required placeholder="Creative Director">
                    <div class="form-error">Required</div>
                </div>
            </div>

            <div class="form-section-title">Bio</div>
            <div class="form-row">
                <div class="form-group">
                    <label>Bio (French)</label>
                    <textarea name="bio_fr" rows="4"><?= htmlspecialchars($editing['bio_fr'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Bio (English)</label>
                    <textarea name="bio_en" rows="4"><?= htmlspecialchars($editing['bio_en'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="form-section-title">Social & Settings</div>
            <div class="form-row">
                <div class="form-group">
                    <label>Instagram URL</label>
                    <input type="url" name="instagram_url" value="<?= htmlspecialchars($editing['instagram_url'] ?? '') ?>" placeholder="https://instagram.com/username">
                </div>
                <div class="form-group">
                    <label>Sort Order</label>
                    <input type="number" name="sort_order" value="<?= intval($editing['sort_order'] ?? 0) ?>" min="0">
                </div>
            </div>
            <label class="toggle-switch" style="margin-bottom:2rem;">
                <input type="checkbox" name="is_active" <?= ($editing['is_active'] ?? 1) ? 'checked' : '' ?>>
                <div class="toggle-track"></div>
                <span style="color:var(--text-muted);font-size:0.9rem;">Active (visible on site)</span>
            </label>

            <div style="display:flex;gap:1rem;margin-top:1rem;">
                <button type="submit" class="topbar-btn"><?= $editing ? 'Save Changes' : 'Add Member' ?></button>
                <a href="<?= SITE_URL ?>/admin/team.php" class="topbar-btn secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
function previewPhoto(input) {
    const preview = document.getElementById('photo_preview');
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        preview.innerHTML = `<img src="${e.target.result}" style="width:100px;height:100px;object-fit:cover;border-radius:50%;margin-top:0.5rem;">`;
    };
    reader.readAsDataURL(file);
}
</script>

<?php else: ?>
<div class="admin-card">
    <div class="admin-card-header">
        <span class="admin-card-title">Team Members (<?= count($team) ?>)</span>
        <a href="?add=1" class="topbar-btn">+ Add Member</a>
    </div>
    <div class="admin-table-wrap">
        <?php if (empty($team)): ?>
        <div class="empty-state"><div class="empty-state-icon">👥</div><p>No team members yet.</p></div>
        <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($team as $member): ?>
                <?php $photo = assetUrl($member['photo'] ?? ''); ?>
                <tr>
                    <td>
                        <?php if ($photo): ?>
                            <img src="<?= $photo ?>" class="thumb-round" alt="">
                        <?php else: ?>
                            <div style="width:44px;height:44px;background:var(--bg-elevated);border-radius:50%;display:flex;align-items:center;justify-content:center;">👤</div>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($member['full_name']) ?></td>
                    <td><?= htmlspecialchars($member['role_en']) ?></td>
                    <td><?= $member['sort_order'] ?></td>
                    <td><span class="badge badge-<?= $member['is_active'] ? 'active' : 'inactive' ?>"><?= $member['is_active'] ? 'Active' : 'Hidden' ?></span></td>
                    <td>
                        <div class="action-btns">
                            <a href="?edit=<?= $member['id'] ?>" class="action-btn action-btn-edit">Edit</a>
                            <a href="?toggle=<?= $member['id'] ?>" class="action-btn action-btn-toggle"><?= $member['is_active'] ? 'Hide' : 'Show' ?></a>
                            <a href="?delete=<?= $member['id'] ?>" class="action-btn action-btn-delete confirm-delete">Delete</a>
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
