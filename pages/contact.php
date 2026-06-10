<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/lang.php';
require_once __DIR__ . '/../includes/upload.php';

$lang = initLang();
$db = getDB();
$settings = getSettings();

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name)    $errors['name'] = t('val_required');
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = t('val_email');
    if (!$message) $errors['message'] = t('val_required');

    if (empty($errors)) {
        // Save to DB (optional: can add a contact_messages table later)
        $success = true;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="page-hero-content">
        <span class="section-label"><?= t('nav_contact') ?></span>
        <h1 class="page-hero-title gradient-text"><?= t('contact_title') ?></h1>
        <p class="page-hero-sub"><?= t('section_contact_sub') ?></p>
    </div>
</section>

<section class="contact-section">
    <div class="contact-grid" style="max-width:1100px;margin:0 auto;">
        <!-- Info -->
        <div>
            <?php if (!empty($settings['phone'])): ?>
            <div class="contact-info-item reveal">
                <div class="contact-icon">
                    <svg viewBox="0 0 24 24" fill="white"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                </div>
                <div>
                    <div class="contact-info-label"><?= t('contact_phone') ?></div>
                    <a href="tel:<?= preg_replace('/[^0-9+]/', '', $settings['phone']) ?>" class="contact-info-value"><?= htmlspecialchars($settings['phone']) ?></a>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($settings['email'])): ?>
            <div class="contact-info-item reveal">
                <div class="contact-icon">
                    <svg viewBox="0 0 24 24" fill="white"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                </div>
                <div>
                    <div class="contact-info-label"><?= t('contact_email') ?></div>
                    <a href="mailto:<?= htmlspecialchars($settings['email']) ?>" class="contact-info-value"><?= htmlspecialchars($settings['email']) ?></a>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($settings['address'])): ?>
            <div class="contact-info-item reveal">
                <div class="contact-icon">
                    <svg viewBox="0 0 24 24" fill="white"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                </div>
                <div>
                    <div class="contact-info-label">Adresse</div>
                    <div class="contact-info-value"><?= htmlspecialchars($settings['address']) ?></div>
                </div>
            </div>
            <?php endif; ?>

            <div class="reveal" style="margin-top:2rem;">
                <div class="contact-info-label" style="margin-bottom:1rem;font-size:0.75rem;font-weight:700;letter-spacing:0.15em;text-transform:uppercase;color:var(--lavender);"><?= t('contact_follow') ?></div>
                <div class="social-links">
                    <?php if (!empty($settings['instagram_url'])): ?>
                    <a href="<?= htmlspecialchars($settings['instagram_url']) ?>" target="_blank" rel="noopener" class="social-link">
                        <svg viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        Instagram
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($settings['facebook_url'])): ?>
                    <a href="<?= htmlspecialchars($settings['facebook_url']) ?>" target="_blank" rel="noopener" class="social-link">
                        <svg viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        Facebook
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($settings['whatsapp'])): ?>
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $settings['whatsapp']) ?>" target="_blank" rel="noopener" class="social-link">
                        <svg viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        WhatsApp
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="form-card reveal">
            <?php if ($success): ?>
            <div class="alert alert-success"><?= t('contact_success') ?></div>
            <?php endif; ?>
            <form method="POST" class="validate-form" novalidate>
                <div class="form-group <?= isset($errors['name']) ? 'error' : '' ?>">
                    <label><?= t('contact_name') ?> *</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                    <div class="form-error"><?= $errors['name'] ?? t('val_required') ?></div>
                </div>
                <div class="form-group <?= isset($errors['email']) ? 'error' : '' ?>">
                    <label><?= t('contact_email') ?> *</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    <div class="form-error"><?= $errors['email'] ?? t('val_email') ?></div>
                </div>
                <div class="form-group <?= isset($errors['message']) ? 'error' : '' ?>">
                    <label><?= t('contact_message') ?> *</label>
                    <textarea name="message" rows="6" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                    <div class="form-error"><?= $errors['message'] ?? t('val_required') ?></div>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                    <?= t('contact_send') ?>
                </button>
            </form>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
