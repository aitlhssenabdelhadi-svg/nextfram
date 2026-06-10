<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/upload.php';
requireAuth();

$db = getDB();
$msg = '';
$msgType = 'success';

// DELETE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $item = $db->prepare("SELECT * FROM portfolio WHERE id = ?");
    $item->execute([$id]);
    $row = $item->fetch();
    if ($row) {
        deleteFile($row['thumbnail']);
        deleteFile($row['media_url']);
        $db->prepare("DELETE FROM portfolio WHERE id = ?")->execute([$id]);
        $msg = 'Item deleted successfully.';
    }
}

// TOGGLE ACTIVE
if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $db->prepare("UPDATE portfolio SET is_active = NOT is_active WHERE id = ?")->execute([$id]);
    $msg = 'Visibility updated.';
}

// TOGGLE FEATURED
if (isset($_GET['feature'])) {
    $id = intval($_GET['feature']);
    $db->prepare("UPDATE portfolio SET is_featured = NOT is_featured WHERE id = ?")->execute([$id]);
    $msg = 'Featured status updated.';
}

// SAVE (add or edit)
$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM portfolio WHERE id = ?");
    $stmt->execute([intval($_GET['edit'])]);
    $editing = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = intval($_POST['id'] ?? 0);
    $title_fr    = trim($_POST['title_fr'] ?? '');
    $title_en    = trim($_POST['title_en'] ?? '');
    $desc_fr     = trim($_POST['description_fr'] ?? '');
    $desc_en     = trim($_POST['description_en'] ?? '');
    $category    = $_POST['category'] ?? 'photo';
    $client      = trim($_POST['client_name'] ?? '');
    $featured    = isset($_POST['is_featured']) ? 1 : 0;
    $active      = isset($_POST['is_active']) ? 1 : 0;

    $thumbnailPath = $_POST['existing_thumbnail'] ?? '';
    $mediaPath     = $_POST['existing_media'] ?? '';

    if (!empty($_FILES['thumbnail']['name'])) {
        $up = handleUpload($_FILES['thumbnail'], 'portfolio', ALLOWED_ALL_TYPES);
        if ($up['success']) {
            if ($thumbnailPath) deleteFile($thumbnailPath);
            $thumbnailPath = $up['path'];
        } else { $msg = $up['error']; $msgType = 'error'; }
    }
    if (!empty($_FILES['media_url']['name'])) {
        $up = handleUpload($_FILES['media_url'], 'portfolio', ALLOWED_ALL_TYPES);
        if ($up['success']) {
            if ($mediaPath) deleteFile($mediaPath);
            $mediaPath = $up['path'];
        } else { $msg = $up['error']; $msgType = 'error'; }
    }

    if (!$msg || $msgType !== 'error') {
        if ($id) {
            $db->prepare("UPDATE portfolio SET title_fr=?, title_en=?, description_fr=?, description_en=?, category=?, thumbnail=?, media_url=?, client_name=?, is_featured=?, is_active=? WHERE id=?")
               ->execute([$title_fr, $title_en, $desc_fr, $desc_en, $category, $thumbnailPath, $mediaPath, $client, $featured, $active, $id]);
            $msg = 'Portfolio item updated.';
        } else {
            $db->prepare("INSERT INTO portfolio (title_fr, title_en, description_fr, description_en, category, thumbnail, media_url, client_name, is_featured, is_active) VALUES (?,?,?,?,?,?,?,?,?,?)")
               ->execute([$title_fr, $title_en, $desc_fr, $desc_en, $category, $thumbnailPath, $mediaPath, $client, $featured, $active]);
            $msg = 'Portfolio item added.';
        }
        $editing = null;
        header('Location: ' . SITE_URL . '/admin/portfolio.php?msg=' . urlencode($msg));
        exit;
    }
}

if (isset($_GET['msg'])) { $msg = $_GET['msg']; }

$items = $db->query("SELECT * FROM portfolio ORDER BY created_at DESC")->fetchAll();

require_once __DIR__ . '/layout.php';
adminHeader('Portfolio', 'portfolio');
?>

<?php if ($msg): ?>
<div class="alert alert-<?= $msgType === 'error' ? 'error' : 'success' ?>" style="margin-bottom:1.5rem;"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if ($editing || isset($_GET['add'])): ?>
<!-- FORM -->
<div class="admin-card" style="margin-bottom:2rem;">
    <div class="admin-card-header">
        <span class="admin-card-title"><?= $editing ? 'Edit Portfolio Item' : 'Add New Item' ?></span>
        <a href="<?= SITE_URL ?>/admin/portfolio.php" class="topbar-btn secondary">Cancel</a>
    </div>
    <div class="admin-card-body">
        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <?php if ($editing): ?>
            <input type="hidden" name="id" value="<?= $editing['id'] ?>">
            <input type="hidden" name="existing_thumbnail" value="<?= htmlspecialchars($editing['thumbnail'] ?? '') ?>">
            <input type="hidden" name="existing_media" value="<?= htmlspecialchars($editing['media_url'] ?? '') ?>">
            <?php endif; ?>

            <div class="form-section-title">Basic Info</div>
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
            <div class="form-row">
                <div class="form-group">
                    <label>Description (French)</label>
                    <textarea name="description_fr" rows="3"><?= htmlspecialchars($editing['description_fr'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Description (English)</label>
                    <textarea name="description_en" rows="3"><?= htmlspecialchars($editing['description_en'] ?? '') ?></textarea>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Category *</label>
                    <select name="category">
                        <option value="photo" <?= ($editing['category'] ?? '') === 'photo' ? 'selected' : '' ?>>Photography</option>
                        <option value="video" <?= ($editing['category'] ?? '') === 'video' ? 'selected' : '' ?>>Video</option>
                        <option value="animation" <?= ($editing['category'] ?? '') === 'animation' ? 'selected' : '' ?>>Animation</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Client Name</label>
                    <input type="text" name="client_name" value="<?= htmlspecialchars($editing['client_name'] ?? '') ?>">
                </div>
            </div>

            <div class="form-section-title">Media</div>
            <div class="form-row">
                <div class="form-group">
                    <label>Thumbnail Image / Video</label>
                    <div class="upload-zone" onclick="document.getElementById('thumb_input').click()">
                        <div class="upload-zone-icon">🖼️</div>
                        <div class="upload-zone-text">Click or drag <strong>image or video</strong> here<br><small>JPG, PNG, WEBP, MP4, WEBM — max 50MB</small></div>
                    </div>
                    <input type="file" id="thumb_input" name="thumbnail" accept="image/*,video/mp4,video/webm" style="display:none" onchange="previewFile(this,'thumb_preview')">
                    <div class="upload-preview" id="thumb_preview">
                        <?php if (!empty($editing['thumbnail']) && assetUrl($editing['thumbnail'])): ?>
                        <div class="current-file"><img src="<?= assetUrl($editing['thumbnail']) ?>" alt=""> Current thumbnail</div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Main Media (image or video)</label>
                    <div class="upload-zone" onclick="document.getElementById('media_input').click()">
                        <div class="upload-zone-icon">🎬</div>
                        <div class="upload-zone-text">Click or drag <strong>image/video</strong> here<br><small>JPG, PNG, MP4, WEBP — max 50MB</small></div>
                    </div>
                    <input type="file" id="media_input" name="media_url" accept="image/*,video/mp4,video/webm" style="display:none" onchange="previewFile(this,'media_preview')">
                    <div class="upload-preview" id="media_preview">
                        <?php if (!empty($editing['media_url']) && assetUrl($editing['media_url'])): ?>
                        <div class="current-file">📎 Current media file saved</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="form-section-title">Settings</div>
            <div style="display:flex;gap:2rem;align-items:center;flex-wrap:wrap;">
                <label class="toggle-switch">
                    <input type="checkbox" name="is_active" <?= ($editing['is_active'] ?? 1) ? 'checked' : '' ?>>
                    <div class="toggle-track"></div>
                    <span style="color:var(--text-muted);font-size:0.9rem;">Active (visible on site)</span>
                </label>
                <label class="toggle-switch">
                    <input type="checkbox" name="is_featured" <?= ($editing['is_featured'] ?? 0) ? 'checked' : '' ?>>
                    <div class="toggle-track"></div>
                    <span style="color:var(--text-muted);font-size:0.9rem;">Featured on homepage</span>
                </label>
            </div>

            <div style="margin-top:2rem;display:flex;gap:1rem;">
                <button type="submit" class="topbar-btn"><?= $editing ? 'Save Changes' : 'Add Item' ?></button>
                <a href="<?= SITE_URL ?>/admin/portfolio.php" class="topbar-btn secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
function previewFile(input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        if (file.type.startsWith('video/')) {
            preview.innerHTML = `<video src="${e.target.result}" controls style="max-width:200px;border-radius:8px;"></video>`;
        } else {
            preview.innerHTML = `<img src="${e.target.result}" style="max-width:200px;height:150px;object-fit:cover;border-radius:8px;">`;
        }
    };
    reader.readAsDataURL(file);
}
</script>

<?php else: ?>
<!-- LIST -->
<div class="admin-card">
    <div class="admin-card-header">
        <span class="admin-card-title">All Portfolio Items (<?= count($items) ?>)</span>
        <a href="?add=1" class="topbar-btn">+ Add Item</a>
    </div>
    <div class="admin-table-wrap">
        <?php if (empty($items)): ?>
        <div class="empty-state"><div class="empty-state-icon">📂</div><p>No portfolio items yet. Add your first one!</p></div>
        <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Thumbnail</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Client</th>
                    <th>Featured</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <?php $thumb = assetUrl($item['thumbnail'] ?? ''); ?>
                <tr>
                    <td>
                        <?php if ($thumb): ?>
                            <img src="<?= $thumb ?>" class="thumb-sm" alt="">
                        <?php else: ?>
                            <div style="width:48px;height:48px;background:var(--bg-elevated);border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;">📷</div>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($item['title_en']) ?></td>
                    <td><?= ucfirst($item['category']) ?></td>
                    <td><?= htmlspecialchars($item['client_name'] ?? '—') ?></td>
                    <td><?= $item['is_featured'] ? '⭐' : '—' ?></td>
                    <td><span class="badge badge-<?= $item['is_active'] ? 'active' : 'inactive' ?>"><?= $item['is_active'] ? 'Active' : 'Hidden' ?></span></td>
                    <td>
                        <div class="action-btns">
                            <a href="?edit=<?= $item['id'] ?>" class="action-btn action-btn-edit">Edit</a>
                            <a href="?toggle=<?= $item['id'] ?>" class="action-btn action-btn-toggle"><?= $item['is_active'] ? 'Hide' : 'Show' ?></a>
                            <a href="?feature=<?= $item['id'] ?>" class="action-btn action-btn-toggle"><?= $item['is_featured'] ? 'Unfeature' : 'Feature' ?></a>
                            <a href="?delete=<?= $item['id'] ?>" class="action-btn action-btn-delete confirm-delete">Delete</a>
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
