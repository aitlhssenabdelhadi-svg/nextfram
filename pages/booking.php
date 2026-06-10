<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/lang.php';
require_once __DIR__ . '/../includes/upload.php';

$lang = initLang();
$db = getDB();
$settings = getSettings();
$services = $db->query("SELECT * FROM services WHERE is_active = 1 ORDER BY is_addon ASC, sort_order ASC")->fetchAll();

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $service = intval($_POST['service'] ?? 0);
    $type    = $_POST['booking_type'] ?? '';
    $date    = $_POST['preferred_date'] ?? '';
    $message = trim($_POST['message'] ?? '');

    if (!$name) $errors['name'] = t('val_required');
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = t('val_email');
    if (!$phone) $errors['phone'] = t('val_required');
    if (!in_array($type, ['consultation', 'service'])) $errors['booking_type'] = t('val_required');

    if (empty($errors)) {
        $stmt = $db->prepare("INSERT INTO bookings (client_name, client_email, client_phone, service_id, booking_type, preferred_date, message, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$name, $email, $phone, $service ?: null, $type, $date ?: null, $message]);
        $success = true;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="page-hero-content">
        <span class="section-label"><?= t('nav_booking') ?></span>
        <h1 class="page-hero-title gradient-text"><?= t('booking_title') ?></h1>
        <p class="page-hero-sub"><?= t('booking_sub') ?></p>
    </div>
</section>

<section class="booking-section">
    <div class="booking-container">
        <?php if ($success): ?>
        <div class="alert alert-success"><?= t('booking_success') ?></div>
        <?php endif; ?>

        <div class="form-card reveal">
            <form method="POST" class="validate-form" novalidate>
                <div class="form-row">
                    <div class="form-group <?= isset($errors['name']) ? 'error' : '' ?>">
                        <label><?= t('booking_name') ?> *</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                        <div class="form-error"><?= $errors['name'] ?? t('val_required') ?></div>
                    </div>
                    <div class="form-group <?= isset($errors['email']) ? 'error' : '' ?>">
                        <label><?= t('booking_email') ?> *</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        <div class="form-error"><?= $errors['email'] ?? t('val_email') ?></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group <?= isset($errors['phone']) ? 'error' : '' ?>">
                        <label><?= t('booking_phone') ?> *</label>
                        <input type="tel" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required data-type="phone">
                        <div class="form-error"><?= $errors['phone'] ?? t('val_phone') ?></div>
                    </div>
                    <div class="form-group">
                        <label><?= t('booking_date') ?></label>
                        <input type="date" name="preferred_date" value="<?= htmlspecialchars($_POST['preferred_date'] ?? '') ?>" min="<?= date('Y-m-d') ?>">
                        <div class="form-error"></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label><?= t('booking_service') ?></label>
                        <select name="service" id="service">
                            <option value=""><?= t('booking_select_service') ?></option>
                            <?php foreach ($services as $svc): ?>
                            <option value="<?= $svc['id'] ?>" <?= (isset($_POST['service']) && $_POST['service'] == $svc['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars(langField($svc, 'title')) ?>
                                <?php if ($svc['price']): ?> — <?= htmlspecialchars($svc['price']) ?><?php endif; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-error"></div>
                    </div>
                    <div class="form-group <?= isset($errors['booking_type']) ? 'error' : '' ?>">
                        <label><?= t('booking_type') ?> *</label>
                        <select name="booking_type" required>
                            <option value=""><?= $lang === 'fr' ? 'Sélectionner...' : 'Select...' ?></option>
                            <option value="consultation" <?= (($_POST['booking_type'] ?? '') === 'consultation') ? 'selected' : '' ?>><?= t('booking_type_consultation') ?></option>
                            <option value="service" <?= (($_POST['booking_type'] ?? '') === 'service') ? 'selected' : '' ?>><?= t('booking_type_service') ?></option>
                        </select>
                        <div class="form-error"><?= $errors['booking_type'] ?? t('val_required') ?></div>
                    </div>
                </div>

                <div class="form-group">
                    <label><?= t('booking_message') ?></label>
                    <textarea name="message" rows="5" placeholder="<?= $lang === 'fr' ? 'Décrivez votre projet...' : 'Describe your project...' ?>"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                    <div class="form-error"></div>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                    <?= t('booking_submit') ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
            </form>
        </div>

        <!-- Contact alternatives -->
        <div style="margin-top:2rem;display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <?php if (!empty($settings['whatsapp'])): ?>
            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $settings['whatsapp']) ?>" target="_blank" class="contact-info-item" style="text-decoration:none;">
                <div class="contact-icon">
                    <svg viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </div>
                <div>
                    <div class="contact-info-label">WhatsApp</div>
                    <div class="contact-info-value"><?= htmlspecialchars($settings['whatsapp']) ?></div>
                </div>
            </a>
            <?php endif; ?>
            <?php if (!empty($settings['phone'])): ?>
            <a href="tel:<?= preg_replace('/[^0-9+]/', '', $settings['phone']) ?>" class="contact-info-item" style="text-decoration:none;">
                <div class="contact-icon">
                    <svg viewBox="0 0 24 24" fill="white"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                </div>
                <div>
                    <div class="contact-info-label"><?= t('contact_phone') ?></div>
                    <div class="contact-info-value"><?= htmlspecialchars($settings['phone']) ?></div>
                </div>
            </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
