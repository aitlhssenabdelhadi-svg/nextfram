<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/lang.php';
require_once __DIR__ . '/../includes/upload.php';

$lang = initLang();
$db = getDB();
$settings = getSettings();

$coreServices = $db->query("SELECT * FROM services WHERE is_addon = 0 AND is_active = 1 ORDER BY sort_order ASC")->fetchAll();
$addons = $db->query("SELECT * FROM services WHERE is_addon = 1 AND is_active = 1 ORDER BY sort_order ASC")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="page-hero-content">
        <span class="section-label"><?= t('nav_services') ?></span>
        <h1 class="page-hero-title gradient-text"><?= t('section_services') ?></h1>
        <p class="page-hero-sub"><?= t('section_services_sub') ?></p>
    </div>
</section>

<section class="services-section" style="padding-top:2rem;">
    <div class="services-grid" style="max-width:1400px;margin:0 auto;">
        <?php foreach ($coreServices as $i => $service): ?>
        <div class="service-card reveal reveal-delay-<?= ($i % 4) + 1 ?>">
            <?php
            $iconUrl = assetUrl($service['icon'] ?? '');
            $iconIsVideo = isVideoAsset($service['icon'] ?? '');
            ?>
            <div class="service-media">
                <?php if ($iconUrl && $iconIsVideo): ?>
                    <video src="<?= $iconUrl ?>" autoplay muted loop playsinline playsinline></video>
                <?php elseif ($iconUrl): ?>
                    <img src="<?= $iconUrl ?>" alt="<?= htmlspecialchars(langField($service, 'title')) ?>">
                <?php else: ?>
                    <div class="service-fallback-emoji"><?= htmlspecialchars($service['icon'] ?? '⚡') ?></div>
                <?php endif; ?>
                <span class="service-tag"><?= $service['is_addon'] ? 'Add-on' : 'Core service' ?></span>
            </div>
            <div class="service-body">
                <h3 class="service-title"><?= htmlspecialchars(langField($service, 'title')) ?></h3>
                <p class="service-desc"><?= htmlspecialchars(langField($service, 'description')) ?></p>
                <?php if ($service['price']): ?>
                <div class="service-price">
                    <span class="price-label"><?= htmlspecialchars(langField($service, 'price_label')) ?></span>
                    <span class="price-value"><?= htmlspecialchars($service['price']) ?></span>
                </div>
                <?php endif; ?>
                <a href="<?= SITE_URL ?>/pages/booking.php?service=<?= $service['id'] ?>" class="btn btn-primary service-btn">
                    <?= t('service_book') ?>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($addons)): ?>
    <div style="max-width:1400px;margin:5rem auto 0;">
        <div class="section-header">
            <span class="section-label"><?= t('service_addon') ?>s</span>
            <h2 class="section-title"><?= $lang === 'fr' ? 'Services Complémentaires' : 'Add-On Services' ?></h2>
        </div>
        <div class="services-grid">
            <?php foreach ($addons as $i => $service): ?>
            <div class="service-card reveal reveal-delay-<?= ($i % 4) + 1 ?> service-card--addon">
                <?php
                $addonIconUrl = assetUrl($service['icon'] ?? '');
                $addonIconIsVideo = isVideoAsset($service['icon'] ?? '');
                ?>
                <div class="service-media">
                    <?php if ($addonIconUrl && $addonIconIsVideo): ?>
                        <video src="<?= $addonIconUrl ?>" autoplay muted loop playsinline></video>
                    <?php elseif ($addonIconUrl): ?>
                        <img src="<?= $addonIconUrl ?>" alt="<?= htmlspecialchars(langField($service, 'title')) ?>">
                    <?php else: ?>
                        <div class="service-fallback-emoji"><?= htmlspecialchars($service['icon'] ?? '✨') ?></div>
                    <?php endif; ?>
                    <span class="service-tag"><?= t('service_addon') ?></span>
                </div>
                <div class="service-body">
                    <span class="section-label" style="margin-bottom:0;"><?= t('service_addon') ?></span>
                    <h3 class="service-title"><?= htmlspecialchars(langField($service, 'title')) ?></h3>
                    <p class="service-desc"><?= htmlspecialchars(langField($service, 'description')) ?></p>
                    <?php if ($service['price']): ?>
                    <div class="service-price">
                        <span class="price-label"><?= htmlspecialchars(langField($service, 'price_label')) ?></span>
                        <span class="price-value"><?= htmlspecialchars($service['price']) ?></span>
                    </div>
                    <?php endif; ?>
                    <a href="<?= SITE_URL ?>/pages/booking.php?service=<?= $service['id'] ?>" class="btn btn-outline service-btn">
                        <?= t('service_book') ?>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</section>

<section class="cta-section">
    <h2 class="section-title reveal"><?= t('cta_title') ?></h2>
    <p class="cta-sub reveal"><?= t('cta_sub') ?></p>
    <div class="cta-actions reveal">
        <a href="<?= SITE_URL ?>/pages/booking.php" class="btn btn-primary"><?= t('cta_btn') ?></a>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
