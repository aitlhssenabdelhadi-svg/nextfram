<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/lang.php';
require_once __DIR__ . '/upload.php';

$lang = initLang();
$settings = getSettings();

$logoPath = assetUrl($settings['logo_path'] ?? '');
$agencyName = $settings['agency_name'] ?? 'Nextfram';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($agencyName) ?> — <?= htmlspecialchars(langField($settings, 'tagline') ?: ($lang === 'fr' ? 'Agence Créative Maroc' : 'Creative Agency Morocco')) ?></title>
    <meta name="description" content="<?= htmlspecialchars($settings['hero_subtitle_' . $lang] ?? '') ?>">

    <?php if ($logoPath): ?>
    <link rel="icon" type="image/png" href="<?= $logoPath ?>">
    <?php endif; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Main stylesheets -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/animations.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/components.css">
</head>
<body>

<nav class="navbar" id="navbar">
    <div class="nav-container">
        <a href="<?= SITE_URL ?>/index.php" class="nav-logo">
            <?php if ($logoPath): ?>
                <img src="<?= $logoPath ?>" alt="<?= htmlspecialchars($agencyName) ?>" class="logo-img">
            <?php else: ?>
                <span class="logo-text"><?= htmlspecialchars($agencyName) ?></span>
            <?php endif; ?>
        </a>

        <ul class="nav-links" id="navLinks">
            <li><a href="<?= SITE_URL ?>/index.php" class="<?= $currentPage === 'index' ? 'active' : '' ?>"><?= t('nav_home') ?></a></li>
            <li><a href="<?= SITE_URL ?>/pages/services.php" class="<?= $currentPage === 'services' ? 'active' : '' ?>"><?= t('nav_services') ?></a></li>
            <li><a href="<?= SITE_URL ?>/pages/portfolio.php" class="<?= $currentPage === 'portfolio' ? 'active' : '' ?>"><?= t('nav_portfolio') ?></a></li>
            <li><a href="<?= SITE_URL ?>/pages/team.php" class="<?= $currentPage === 'team' ? 'active' : '' ?>"><?= t('nav_team') ?></a></li>
            <li><a href="<?= SITE_URL ?>/pages/contact.php" class="<?= $currentPage === 'contact' ? 'active' : '' ?>"><?= t('nav_contact') ?></a></li>
            <li><a href="<?= SITE_URL ?>/pages/booking.php" class="nav-cta <?= $currentPage === 'booking' ? 'active' : '' ?>"><?= t('nav_booking') ?></a></li>
            <li>
                <a href="?lang=<?= t('lang_switch_url') ?>" class="lang-btn">
                    <?= t('lang_switch') ?>
                </a>
            </li>
        </ul>

        <button class="hamburger" id="hamburger" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>

<?php
// Ensure social-float appears if any social URL is set.
// Some deployments may store TikTok under a different key (e.g. tiktok), so fall back gracefully.
$whatsapp = $settings['whatsapp'] ?? '';
$instagramUrl = $settings['instagram_url'] ?? '';
$facebookUrl = $settings['facebook_url'] ?? '';
$tiktokUrl = $settings['tiktok_url'] ?? ($settings['tiktok'] ?? '');
?>

<?php if (!empty($whatsapp) || !empty($instagramUrl) || !empty($facebookUrl) || !empty($tiktokUrl)): ?>
<div class="social-float">
    <?php if (!empty($whatsapp)): ?>
    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $whatsapp) ?>" class="whatsapp-float" target="_blank" rel="noopener" aria-label="WhatsApp">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    </a>
    <?php endif; ?>

    <?php if (!empty($instagramUrl)): ?>
    <a href="<?= htmlspecialchars($instagramUrl) ?>" class="instagram-float" target="_blank" rel="noopener" aria-label="Instagram" title="Instagram">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
    </a>
    <?php endif; ?>

    <?php if (!empty($facebookUrl)): ?>
    <a href="<?= htmlspecialchars($facebookUrl) ?>" class="facebook-float" target="_blank" rel="noopener" aria-label="Facebook" title="Facebook">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13.5 22v-9h3l.4-3H13.5V4.2c0-.9.3-1.5 1.6-1.5h1.7V.1C17 .1 15.9 0 14.6 0c-2.8 0-4.7 1.7-4.7 4.9V10H6.6v3h3.3v9h3.6z"/></svg>
    </a>
    <?php endif; ?>

    <?php if (!empty($tiktokUrl)): ?>
    <a href="<?= htmlspecialchars($tiktokUrl) ?>" class="tiktok-float" target="_blank" rel="noopener" aria-label="TikTok" title="TikTok">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16.5 5.1c.7-.1 1.4-.4 1.9-.8v3c-.5.3-1.2.5-1.9.6-.7.1-1.4 0-2-.1v6.7c0 2.7-2.2 4.9-4.9 4.9S5 19.2 5 16.5c0-2.6 2.1-4.8 4.7-4.9v3.1c-.9.1-1.6.9-1.6 1.8 0 1.1.9 2 2 2s2-.9 2-2V3h3.2c.1 1 .6 1.9 1.5 2.1z"/></svg>
    </a>
    <?php endif; ?>
</div>
<?php endif; ?>


