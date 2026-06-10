<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/lang.php';
require_once __DIR__ . '/includes/upload.php';

$lang = initLang();
$db = getDB();
$settings = getSettings();

// Featured portfolio
$portfolio = $db->query("SELECT * FROM portfolio WHERE is_featured = 1 AND is_active = 1 ORDER BY created_at DESC LIMIT 6")->fetchAll();

// Services (core only)
$services = $db->query("SELECT * FROM services WHERE is_addon = 0 AND is_active = 1 ORDER BY sort_order ASC LIMIT 4")->fetchAll();

// Team
$team = $db->query("SELECT * FROM team WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();

// Testimonials
$testimonials = [
    [
        'name' => 'Youssef El Haddad',
        'city' => 'Casablanca',
        'role' => $lang === 'fr' ? 'Directeur marketing' : 'Marketing Director',
        'comment' => $lang === 'fr'
            ? 'Le rendu visuel est incroyable, très moderne et parfaitement aligné avec notre image de marque.'
            : 'The visual result is stunning, modern, and perfectly aligned with our brand identity.',
        'avatar' => 'YE',
        'rating' => 5
    ],
    [
        'name' => 'Laila Benali',
        'city' => 'Marrakech',
        'role' => $lang === 'fr' ? 'Fondatrice de boutique' : 'Boutique Founder',
        'comment' => $lang === 'fr'
            ? 'Le design est propre, élégant et vraiment mémorable. Les clients ont immédiatement remarqué la différence.'
            : 'The design feels clean, elegant, and highly memorable. Clients immediately noticed the difference.',
        'avatar' => 'LB',
        'rating' => 5
    ],
    [
        'name' => 'Karim Tazi',
        'city' => 'Rabat',
        'role' => $lang === 'fr' ? 'Chef de produit' : 'Product Lead',
        'comment' => $lang === 'fr'
            ? 'Un excellent équilibre entre créativité, rapidité et professionnalisme. Tout est fluide et moderne.'
            : 'It balances creativity, speed, and professionalism beautifully. Everything feels smooth and modern.',
        'avatar' => 'KT',
        'rating' => 5
    ],
    [
        'name' => 'Nora Chafik',
        'city' => 'Tangier',
        'role' => $lang === 'fr' ? 'Consultante brand' : 'Brand Consultant',
        'comment' => $lang === 'fr'
            ? 'Le site a donné à notre présence en ligne un vrai coup de jeune, avec un style premium et très professionnel.'
            : 'The site gave our online presence a fresh upgrade with a premium and highly professional style.',
        'avatar' => 'NC',
        'rating' => 5
    ],
];

// Stats
$stats = [
    ['number' => 120, 'suffix' => '+', 'label' => t('stat_projects')],
    ['number' => 85, 'suffix' => '+', 'label' => t('stat_clients')],
    ['number' => 5, 'suffix' => '', 'label' => t('stat_years')],
    ['number' => 12, 'suffix' => '', 'label' => t('stat_cities')],
];

$showreelPath = assetUrl($settings['showreel_path'] ?? '');
$agencyName = $settings['agency_name'] ?? 'Nextfram';
$tagline = $settings['tagline_' . $lang] ?? '';
$heroSub = $settings['hero_subtitle_' . $lang] ?? '';
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="page-loader"><div class="loader-logo"><?= htmlspecialchars($agencyName) ?></div></div>

<!-- HERO -->
<section class="hero">
    <?php if ($showreelPath): ?>
    <div class="hero-video-wrap">
        <video autoplay muted loop playsinline>
            <source src="<?= $showreelPath ?>" type="video/mp4">
        </video>
    </div>
    <?php else: ?>
    <div class="hero-fallback">
        <div class="hero-particles"></div>
    </div>
    <?php endif; ?>

    <div class="hero-content">
        <div class="hero-eyebrow">Creative Agency — Morocco</div>
        <h1 class="hero-title gradient-text"><?= htmlspecialchars($agencyName) ?></h1>
        <?php if ($tagline): ?>
        <p class="hero-subtitle"><?= htmlspecialchars($tagline) ?></p>
        <?php endif; ?>
        <div class="hero-actions">
            <a href="<?= SITE_URL ?>/pages/booking.php" class="btn btn-primary">
                <?= t('hero_cta') ?>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
            <a href="<?= SITE_URL ?>/pages/portfolio.php" class="btn btn-outline"><?= t('nav_portfolio') ?></a>
        </div>
    </div>
    <div class="hero-scroll">
        <div class="scroll-line"></div>
        <span><?= t('hero_scroll') ?></span>
    </div>
</section>

<!-- STATS -->
<section class="stats-section">
    <div class="stats-grid">
        <?php foreach ($stats as $i => $stat): ?>
        <div class="stat-item reveal reveal-delay-<?= $i + 1 ?>">
            <span class="stat-number" data-target="<?= $stat['number'] ?>" data-suffix="<?= $stat['suffix'] ?>">0</span>
            <div class="stat-label"><?= htmlspecialchars($stat['label']) ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- SERVICES -->
<section class="services-section">
    <div class="section-header reveal">
        <span class="section-label"><?= t('nav_services') ?></span>
        <h2 class="section-title"><?= t('section_services') ?></h2>
        <p class="section-sub"><?= t('section_services_sub') ?></p>
    </div>
    <div class="services-grid">
        <?php foreach ($services as $i => $service): ?>
        <div class="service-card reveal reveal-delay-<?= ($i % 4) + 1 ?>">
            <?php
            $iconUrl = assetUrl($service['icon'] ?? '');
            $iconIsVideo = isVideoAsset($service['icon'] ?? '');
            ?>
            <div class="service-media">
                <?php if ($iconUrl && $iconIsVideo): ?>
                    <video src="<?= $iconUrl ?>" autoplay muted loop playsinline></video>
                <?php elseif ($iconUrl): ?>
                    <img src="<?= $iconUrl ?>" alt="<?= htmlspecialchars(langField($service, 'title')) ?>">
                <?php else: ?>
                    <div class="service-fallback-emoji"><?= htmlspecialchars($service['icon'] ?? '⚡') ?></div>
                <?php endif; ?>
                <span class="service-tag">Visual service</span>
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
                <a href="<?= SITE_URL ?>/pages/booking.php?service=<?= $service['id'] ?>" class="btn btn-outline service-btn">
                    <?= t('service_book') ?>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- PORTFOLIO -->
<?php if (!empty($portfolio)): ?>
<section class="portfolio-section">
    <div class="section-header reveal">
        <span class="section-label"><?= t('nav_portfolio') ?></span>
        <h2 class="section-title"><?= t('section_portfolio') ?></h2>
        <p class="section-sub"><?= t('section_portfolio_sub') ?></p>
    </div>
    <div class="portfolio-grid">
        <?php foreach ($portfolio as $item): ?>
        <?php
        $mediaUrl = assetUrl($item['media_url'] ?? '');
        $thumbUrl = assetUrl($item['thumbnail'] ?? '');
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
    <div style="text-align:center;margin-top:3rem;">
        <a href="<?= SITE_URL ?>/pages/portfolio.php" class="btn btn-outline"><?= t('filter_all') ?> →</a>
    </div>
</section>
<?php endif; ?>

<!-- TEAM -->
<?php if (!empty($team)): ?>
<section class="team-section">
    <div class="section-header reveal">
        <span class="section-label"><?= t('nav_team') ?></span>
        <h2 class="section-title"><?= t('section_team') ?></h2>
        <p class="section-sub"><?= t('section_team_sub') ?></p>
    </div>
    <div class="team-grid">
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
</section>
<?php endif; ?>

<!-- TESTIMONIALS -->
<section class="testimonials-section">
    <div class="section-header reveal">
        <span class="section-label">Testimonials</span>
        <h2 class="section-title">What our clients say</h2>
        <p class="section-sub">A modern carousel of authentic feedback from Moroccan brands and creative partners.</p>
    </div>

    <div class="testimonials-shell reveal">
        <button class="testimonial-nav testimonial-prev" type="button" aria-label="Previous testimonial">←</button>
        <div class="testimonials-carousel" id="testimonialsCarousel">
            <div class="testimonial-track" id="testimonialTrack">
                <?php foreach ($testimonials as $index => $item): ?>
                <article class="testimonial-card" data-index="<?= $index ?>">
                    <div class="testimonial-top">
                        <div class="testimonial-avatar"><?= htmlspecialchars($item['avatar']) ?></div>
                        <div>
                            <h3 class="testimonial-name"><?= htmlspecialchars($item['name']) ?></h3>
                            <p class="testimonial-role"><?= htmlspecialchars($item['role']) ?> · <?= htmlspecialchars($item['city']) ?></p>
                        </div>
                    </div>
                    <div class="testimonial-stars" aria-label="5 out of 5 stars">★★★★★</div>
                    <p class="testimonial-comment">“<?= htmlspecialchars($item['comment']) ?>”</p>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
        <button class="testimonial-nav testimonial-next" type="button" aria-label="Next testimonial">→</button>
    </div>

    <div class="testimonial-dots" aria-label="Testimonial navigation">
        <?php foreach ($testimonials as $index => $item): ?>
        <button class="testimonial-dot <?= $index === 0 ? 'active' : '' ?>" type="button" data-slide="<?= $index ?>" aria-label="Go to testimonial <?= $index + 1 ?>"></button>
        <?php endforeach; ?>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <h2 class="section-title reveal"><?= t('cta_title') ?></h2>
    <p class="cta-sub reveal"><?= t('cta_sub') ?></p>
    <div class="cta-actions reveal">
        <a href="<?= SITE_URL ?>/pages/booking.php" class="btn btn-primary"><?= t('cta_btn') ?></a>
        <a href="<?= SITE_URL ?>/pages/contact.php" class="btn btn-outline"><?= t('nav_contact') ?></a>
    </div>
</section>

<!-- LIGHTBOX -->
<div class="lightbox" id="lightbox">
    <button class="lightbox-close" id="lightboxClose">✕</button>
    <div class="lightbox-content" id="lightboxContent"></div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
