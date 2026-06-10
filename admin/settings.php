<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/upload.php';
requireAuth();

$db = getDB();
$msg = '';
$msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'general';

    if ($action === 'general') {
        $fields = ['agency_name', 'phone', 'email', 'whatsapp', 'address',
                   'instagram_url', 'facebook_url', 'tiktok_url',
                   'tagline_fr', 'tagline_en',
                   'hero_subtitle_fr', 'hero_subtitle_en'];
        foreach ($fields as $field) {
            $val = trim($_POST[$field] ?? '');
            $db->prepare("INSERT INTO settings (`key`, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = ?")
               ->execute([$field, $val, $val]);
        }
        $msg = 'Settings saved successfully.';
    }

    if ($action === 'logo') {
        if (!empty($_FILES['logo']['name'])) {
            $up = handleUpload($_FILES['logo'], 'settings', ALLOWED_IMAGE_TYPES);
            if ($up['success']) {
                $old = getSetting('logo_path');
                if ($old) deleteFile($old);
                $db->prepare("INSERT INTO settings (`key`, value) VALUES ('logo_path', ?) ON DUPLICATE KEY UPDATE value = ?")
                   ->execute([$up['path'], $up['path']]);
                $msg = 'Logo updated successfully. Refresh the page to see it.';
            } else { $msg = $up['error']; $msgType = 'error'; }
        } else { $msg = 'Please select a logo file.'; $msgType = 'error'; }
    }

    if ($action === 'showreel') {
        if (!empty($_FILES['showreel']['name'])) {
            $up = handleUpload($_FILES['showreel'], 'settings', ALLOWED_VIDEO_TYPES);
            if ($up['success']) {
                $old = getSetting('showreel_path');
                if ($old) deleteFile($old);
                $db->prepare("INSERT INTO settings (`key`, value) VALUES ('showreel_path', ?) ON DUPLICATE KEY UPDATE value = ?")
                   ->execute([$up['path'], $up['path']]);
                $msg = 'Showreel uploaded. It will now play as the homepage hero background.';
            } else { $msg = $up['error']; $msgType = 'error'; }
        } else { $msg = 'Please select a video file.'; $msgType = 'error'; }
    }

    if ($action === 'password') {
        $current  = $_POST['current_password'] ?? '';
        $new      = $_POST['new_password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$_SESSION['admin_user']]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($current, $user['password'])) {
            $msg = 'Current password is incorrect.'; $msgType = 'error';
        } elseif (strlen($new) < 6) {
            $msg = 'New password must be at least 6 characters.'; $msgType = 'error';
        } elseif ($new !== $confirm) {
            $msg = 'Passwords do not match.'; $msgType = 'error';
        } else {
            $hash = password_hash($new, PASSWORD_BCRYPT);
            $db->prepare("UPDATE admin_users SET password = ? WHERE username = ?")
               ->execute([$hash, $_SESSION['admin_user']]);
            $msg = 'Password changed successfully.';
        }
    }
}

$settings = getSettings();
$logoUrl     = assetUrl($settings['logo_path'] ?? '');
$showreelUrl = assetUrl($settings['showreel_path'] ?? '');

require_once __DIR__ . '/layout.php';
adminHeader('Settings', 'settings');
?>

<?php if ($msg): ?>
<div class="alert alert-<?= $msgType === 'error' ? 'error' : 'success' ?>" style="margin-bottom:1.5rem;"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<!-- LOGO & SHOWREEL -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">

    <!-- Logo Upload -->
    <div class="admin-card">
        <div class="admin-card-header">
            <span class="admin-card-title">🖼️ Logo & Favicon</span>
        </div>
        <div class="admin-card-body">
            <?php if ($logoUrl): ?>
            <div class="current-file" style="margin-bottom:1.5rem;padding:1rem;border-radius:8px;background:var(--bg);">
                <img src="<?= $logoUrl ?>" alt="Current logo" style="height:50px;width:auto;object-fit:contain;">
                <span>Current logo — appears in navbar, footer & favicon</span>
            </div>
            <?php else: ?>
            <p style="color:var(--text-muted);font-size:0.85rem;margin-bottom:1.5rem;">No logo uploaded yet. Upload one to appear across the entire site.</p>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="logo">
                <div class="form-group">
                    <div class="upload-zone" onclick="document.getElementById('logo_input').click()">
                        <div class="upload-zone-icon">📤</div>
                        <div class="upload-zone-text">Click to upload <strong>logo</strong><br><small>PNG recommended (transparent background) — max 50MB</small></div>
                    </div>
                    <input type="file" id="logo_input" name="logo" accept="image/*" style="display:none" onchange="previewLogo(this)">
                    <div id="logo_preview" class="upload-preview"></div>
                </div>
                <button type="submit" class="topbar-btn" style="width:100%;justify-content:center;">Upload Logo</button>
            </form>
        </div>
    </div>

    <!-- Showreel Upload -->
    <div class="admin-card">
        <div class="admin-card-header">
            <span class="admin-card-title">🎬 Hero Showreel Video</span>
        </div>
        <div class="admin-card-body">
            <?php if ($showreelUrl): ?>
            <div style="margin-bottom:1.5rem;padding:1rem;border-radius:8px;background:var(--bg);">
                <video src="<?= $showreelUrl ?>" style="width:100%;max-height:120px;object-fit:cover;border-radius:6px;" muted></video>
                <p style="color:var(--text-dim);font-size:0.8rem;margin-top:0.5rem;">Current showreel — plays as homepage background</p>
            </div>
            <?php else: ?>
            <p style="color:var(--text-muted);font-size:0.85rem;margin-bottom:1.5rem;">No showreel uploaded. A gradient fallback is shown. Upload a video to make the homepage come alive.</p>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="showreel">
                <div class="form-group">
                    <div class="upload-zone" onclick="document.getElementById('showreel_input').click()">
                        <div class="upload-zone-icon">🎥</div>
                        <div class="upload-zone-text">Click to upload <strong>showreel</strong><br><small>MP4 recommended — max 50MB</small></div>
                    </div>
                    <input type="file" id="showreel_input" name="showreel" accept="video/mp4,video/webm" style="display:none" onchange="previewShowreel(this)">
                    <div id="showreel_preview" class="upload-preview"></div>
                </div>
                <button type="submit" class="topbar-btn" style="width:100%;justify-content:center;">Upload Showreel</button>
            </form>
        </div>
    </div>
</div>

<!-- GENERAL SETTINGS -->
<div class="admin-card" style="margin-bottom:1.5rem;">
    <div class="admin-card-header">
        <span class="admin-card-title">⚙️ General Settings</span>
    </div>
    <div class="admin-card-body">
        <form method="POST" class="admin-form">
            <input type="hidden" name="action" value="general">

            <div class="form-section-title">Agency</div>
            <div class="form-row">
                <div class="form-group">
                    <label>Agency Name</label>
                    <input type="text" name="agency_name" value="<?= htmlspecialchars($settings['agency_name'] ?? 'Nextfram') ?>">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" value="<?= htmlspecialchars($settings['address'] ?? '') ?>">
                </div>
            </div>

            <div class="form-section-title">Tagline</div>
            <div class="form-row">
                <div class="form-group">
                    <label>Tagline (French)</label>
                    <input type="text" name="tagline_fr" value="<?= htmlspecialchars($settings['tagline_fr'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Tagline (English)</label>
                    <input type="text" name="tagline_en" value="<?= htmlspecialchars($settings['tagline_en'] ?? '') ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Hero Subtitle (French)</label>
                    <textarea name="hero_subtitle_fr" rows="2"><?= htmlspecialchars($settings['hero_subtitle_fr'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Hero Subtitle (English)</label>
                    <textarea name="hero_subtitle_en" rows="2"><?= htmlspecialchars($settings['hero_subtitle_en'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="form-section-title">Contact</div>
            <div class="form-row">
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($settings['phone'] ?? '') ?>" placeholder="+212 6XX XXX XXX">
                </div>
                <div class="form-group">
                    <label>WhatsApp Number (digits only)</label>
                    <input type="text" name="whatsapp" value="<?= htmlspecialchars($settings['whatsapp'] ?? '') ?>" placeholder="212639797751">
                </div>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($settings['email'] ?? '') ?>">
            </div>

            <div class="form-section-title">Social Media</div>
            <div class="form-row">
                <div class="form-group">
                    <label>Instagram URL</label>
                    <input type="url" name="instagram_url" value="<?= htmlspecialchars($settings['instagram_url'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Facebook URL</label>
                    <input type="url" name="facebook_url" value="<?= htmlspecialchars($settings['facebook_url'] ?? '') ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>TikTok URL</label>
                    <input type="url" name="tiktok_url" value="<?= htmlspecialchars($settings['tiktok_url'] ?? '') ?>" placeholder="https://www.tiktok.com/@username">
                </div>
                <div class="form-group" style="visibility:hidden;"></div>
            </div>

            <button type="submit" class="topbar-btn" style="margin-top:0.5rem;">Save All Settings</button>
        </form>
    </div>
</div>

<!-- PASSWORD -->
<div class="admin-card" style="max-width:500px;">
    <div class="admin-card-header">
        <span class="admin-card-title">🔒 Change Password</span>
    </div>
    <div class="admin-card-body">
        <form method="POST" class="admin-form">
            <input type="hidden" name="action" value="password">
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" required>
            </div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" required>
            </div>
            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" required>
            </div>
            <button type="submit" class="topbar-btn">Change Password</button>
        </form>
    </div>
</div>

<script>
function previewLogo(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('logo_preview').innerHTML = `<img src="${e.target.result}" style="max-height:60px;width:auto;object-fit:contain;border-radius:4px;margin-top:0.5rem;">`;
    };
    reader.readAsDataURL(file);
}
function previewShowreel(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('showreel_preview').innerHTML = `<video src="${e.target.result}" style="max-width:200px;border-radius:8px;margin-top:0.5rem;" muted controls></video>`;
    };
    reader.readAsDataURL(file);
}
</script>

<?php adminFooter(); ?>
