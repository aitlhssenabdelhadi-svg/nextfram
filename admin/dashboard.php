<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/upload.php';
requireAuth();

$db = getDB();

$totalPortfolio  = $db->query("SELECT COUNT(*) FROM portfolio")->fetchColumn();
$totalServices   = $db->query("SELECT COUNT(*) FROM services")->fetchColumn();
$totalTeam       = $db->query("SELECT COUNT(*) FROM team")->fetchColumn();
$totalBookings   = $db->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$pendingBookings = $db->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
$recentBookings  = $db->query("SELECT b.*, s.title_en as service_name FROM bookings b LEFT JOIN services s ON b.service_id = s.id ORDER BY b.created_at DESC LIMIT 10")->fetchAll();

require_once __DIR__ . '/layout.php';
adminHeader('Dashboard', 'dashboard');
?>

<div class="stats-cards">
    <div class="stat-card">
        <div class="stat-card-icon">📷</div>
        <div class="stat-card-number"><?= $totalPortfolio ?></div>
        <div class="stat-card-label">Portfolio Items</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon">⚡</div>
        <div class="stat-card-number"><?= $totalServices ?></div>
        <div class="stat-card-label">Services</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon">👥</div>
        <div class="stat-card-number"><?= $totalTeam ?></div>
        <div class="stat-card-label">Team Members</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon">📅</div>
        <div class="stat-card-number"><?= $totalBookings ?></div>
        <div class="stat-card-label">Total Bookings <span style="color:var(--lavender);font-size:1rem;">(<?= $pendingBookings ?> pending)</span></div>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <span class="admin-card-title">Recent Bookings</span>
        <a href="<?= SITE_URL ?>/admin/bookings.php" class="topbar-btn secondary" style="font-size:0.8rem;padding:7px 16px;">View All</a>
    </div>
    <div class="admin-table-wrap">
        <?php if (empty($recentBookings)): ?>
        <div class="empty-state"><div class="empty-state-icon">📭</div><p>No bookings yet.</p></div>
        <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Email</th>
                    <th>Service</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentBookings as $b): ?>
                <tr>
                    <td><?= htmlspecialchars($b['client_name']) ?></td>
                    <td><?= htmlspecialchars($b['client_email']) ?></td>
                    <td><?= htmlspecialchars($b['service_name'] ?? '—') ?></td>
                    <td><?= ucfirst($b['booking_type']) ?></td>
                    <td><?= $b['preferred_date'] ? date('d/m/Y', strtotime($b['preferred_date'])) : '—' ?></td>
                    <td><span class="badge badge-<?= $b['status'] ?>"><?= ucfirst($b['status']) ?></span></td>
                    <td>
                        <a href="<?= SITE_URL ?>/admin/bookings.php?edit=<?= $b['id'] ?>" class="action-btn action-btn-edit">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php adminFooter(); ?>
