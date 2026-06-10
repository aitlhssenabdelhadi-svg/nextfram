<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/lang.php';
require_once __DIR__ . '/../includes/upload.php';

$lang = initLang();
$db = getDB();
$settings = getSettings();
$team = $db->query("SELECT * FROM team WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="page-hero-content">
        <span class="section-label"><?= t('nav_team') ?></span>
        <h1 class="page-hero-title gradient-text"><?= t('section_team') ?></h1>
        <p class="page-hero-sub"><?= t('section_team_sub') ?></p>
    </div>
</section>

<section class="team-section" style="padding-top:2rem;">
    <?php if (empty($team)): ?>
    <div class="empty-state">
        <div class="empty-state-icon">👥</div>
        <p><?= $lang === 'fr' ? 'L\'équipe sera présentée bientôt.' : 'Team coming soon.' ?></p>
    </div>
    <?php else: ?>
    <div class="team-grid" style="max-width:1200px;margin:0 auto;">
        <?php foreach ($team as $i => $member): ?>
        <?php $photoUrl = assetUrl($member['photo'] ?? ''); ?>
        <div class="team-card reveal reveal-delay-<?= ($i % 3) + 1 ?>">
            <div class="team-photo-wrap">
                <?php if ($photoUrl): ?>
                    <img src="<?= $photoUrl ?>" alt="<?= htmlspecialchars($member['full_name']) ?>" loading="lazy">
                <?php else: ?>
                    <div class="team-photo-placeholder">👤</div>
                <?php endif; ?>
            </div>
            <div class="team-info">
                <div class="team-name"><?= htmlspecialchars($member['full_name']) ?></div>
                <div class="team-role"><?= htmlspecialchars(langField($member, 'role')) ?></div>
                <?php if (langField($member, 'bio')): ?>
                <p class="team-bio"><?= htmlspecialchars(langField($member, 'bio')) ?></p>
                <?php endif; ?>
                <?php if ($member['instagram_url']): ?>
                <a href="<?= htmlspecialchars($member['instagram_url']) ?>" target="_blank" rel="noopener" class="team-instagram">
                    <svg viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    Instagram
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
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
