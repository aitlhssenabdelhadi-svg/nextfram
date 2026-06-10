<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/lang.php';
require_once __DIR__ . '/../includes/upload.php';

$lang = initLang();
$db = getDB();
$settings = getSettings();

$portfolio = $db->query("SELECT * FROM portfolio WHERE is_active = 1 ORDER BY created_at DESC")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="page-hero-content">
        <span class="section-label"><?= t('nav_portfolio') ?></span>
        <h1 class="page-hero-title gradient-text"><?= t('section_portfolio') ?></h1>
        <p class="page-hero-sub"><?= t('section_portfolio_sub') ?></p>
    </div>
</section>

<section class="portfolio-section" style="padding-top:2rem;">
    <div class="portfolio-filters reveal">
        <button class="filter-btn active" data-filter="all"><?= t('filter_all') ?></button>
        <button class="filter-btn" data-filter="photo"><?= t('filter_photo') ?></button>
        <button class="filter-btn" data-filter="video"><?= t('filter_video') ?></button>
        <button class="filter-btn" data-filter="animation"><?= t('filter_animation') ?></button>
    </div>

    <?php if (empty($portfolio)): ?>
    <div class="empty-state">
        <div class="empty-state-icon">📂</div>
        <p><?= $lang === 'fr' ? 'Le portfolio sera bientôt disponible.' : 'Portfolio coming soon.' ?></p>
    </div>
    <?php else: ?>
    <div class="portfolio-grid" style="max-width:1400px;margin:0 auto;">
        <?php foreach ($portfolio as $item): ?>
        <?php
        $thumbUrl = assetUrl($item['thumbnail'] ?? '');
        $mediaUrl = assetUrl($item['media_url'] ?? '');
        $displayUrl = $thumbUrl ?: $mediaUrl;
        $displayIsVideo = $item['category'] === 'video' || isVideoAsset($thumbUrl) || isVideoAsset($mediaUrl);
        $playbackUrl = isVideoAsset($mediaUrl) ? $mediaUrl : (isVideoAsset($thumbUrl) ? $thumbUrl : $mediaUrl);
        $catIcons = ['photo' => '📷', 'video' => '🎬', 'animation' => '✨'];
        ?>
        <div class="portfolio-item reveal"
             data-category="<?= htmlspecialchars($item['category']) ?>"
             data-media="<?= $playbackUrl ?: '' ?>"
             data-type="<?= $displayIsVideo ? 'video' : 'image' ?>"
             data-title="<?= htmlspecialchars(langField($item, 'title')) ?>">
            <?php if ($displayIsVideo && $playbackUrl): ?>
                <video src="<?= $playbackUrl ?>" class="portfolio-thumb" autoplay muted loop playsinline preload="metadata"></video>
            <?php elseif ($displayUrl): ?>
                <img src="<?= $displayUrl ?>" alt="<?= htmlspecialchars(langField($item, 'title')) ?>" class="portfolio-thumb" loading="lazy">
            <?php else: ?>
                <div class="portfolio-placeholder"><?= $catIcons[$item['category']] ?? '📷' ?></div>
            <?php endif; ?>
            <?php if ($displayIsVideo): ?>
            <div class="portfolio-play">
                <svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
            </div>
            <?php endif; ?>
            <div class="portfolio-overlay">
                <div class="portfolio-cat"><?= htmlspecialchars($item['category']) ?></div>
                <div class="portfolio-title"><?= htmlspecialchars(langField($item, 'title')) ?></div>
                <?php if ($item['client_name']): ?>
                <div class="portfolio-client"><?= htmlspecialchars($item['client_name']) ?></div>
                <?php endif; ?>
                <div class="portfolio-cta">View Project →</div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<div class="lightbox" id="lightbox">
    <button class="lightbox-close" id="lightboxClose">✕</button>
    <div class="lightbox-content" id="lightboxContent"></div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
