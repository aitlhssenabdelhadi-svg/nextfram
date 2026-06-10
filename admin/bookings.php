<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/upload.php';
requireAuth();

$db = getDB();
$msg = '';

// Update status
if (isset($_GET['status']) && isset($_GET['id'])) {
    $status = $_GET['status'];
    $id     = intval($_GET['id']);
    if (in_array($status, ['pending', 'confirmed', 'cancelled'])) {
        $db->prepare("UPDATE bookings SET status = ? WHERE id = ?")->execute([$status, $id]);
        $msg = 'Booking status updated.';
    }
}

// Delete
if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM bookings WHERE id = ?")->execute([intval($_GET['delete'])]);
    header('Location: ' . SITE_URL . '/admin/bookings.php?msg=Booking+deleted.');
    exit;
}

if (isset($_GET['msg'])) $msg = $_GET['msg'];

// Filter
$filter = $_GET['filter'] ?? 'all';
$where = $filter !== 'all' ? "WHERE b.status = " . $db->quote($filter) : '';
$bookings = $db->query("SELECT b.*, s.title_en as service_name FROM bookings b LEFT JOIN services s ON b.service_id = s.id $where ORDER BY b.created_at DESC")->fetchAll();

// Detail view
$viewing = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT b.*, s.title_en as service_name, s.title_fr as service_name_fr FROM bookings b LEFT JOIN services s ON b.service_id = s.id WHERE b.id = ?");
    $stmt->execute([intval($_GET['edit'])]);
    $viewing = $stmt->fetch();
}

require_once __DIR__ . '/layout.php';
adminHeader('Bookings', 'bookings');
?>

<?php if ($msg): ?>
<div class="alert alert-success" style="margin-bottom:1.5rem;"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if ($viewing): ?>
<!-- DETAIL VIEW -->
<div class="admin-card" style="max-width:700px;">
    <div class="admin-card-header">
        <span class="admin-card-title">Booking #<?= $viewing['id'] ?></span>
        <a href="<?= SITE_URL ?>/admin/bookings.php" class="topbar-btn secondary">← Back</a>
    </div>
    <div class="admin-card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:2rem;">
            <div>
                <div class="form-section-title">Client Info</div>
                <p style="color:var(--text-muted);font-size:0.85rem;margin-bottom:0.3rem;">Name</p>
                <p style="color:var(--white);margin-bottom:1rem;"><?= htmlspecialchars($viewing['client_name']) ?></p>
                <p style="color:var(--text-muted);font-size:0.85rem;margin-bottom:0.3rem;">Email</p>
                <p style="margin-bottom:1rem;"><a href="mailto:<?= htmlspecialchars($viewing['client_email']) ?>" style="color:var(--lavender);"><?= htmlspecialchars($viewing['client_email']) ?></a></p>
                <p style="color:var(--text-muted);font-size:0.85rem;margin-bottom:0.3rem;">Phone</p>
                <p><a href="tel:<?= htmlspecialchars($viewing['client_phone']) ?>" style="color:var(--lavender);"><?= htmlspecialchars($viewing['client_phone']) ?></a></p>
            </div>
            <div>
                <div class="form-section-title">Booking Details</div>
                <p style="color:var(--text-muted);font-size:0.85rem;margin-bottom:0.3rem;">Service</p>
                <p style="color:var(--white);margin-bottom:1rem;"><?= htmlspecialchars($viewing['service_name'] ?? '—') ?></p>
                <p style="color:var(--text-muted);font-size:0.85rem;margin-bottom:0.3rem;">Type</p>
                <p style="color:var(--white);margin-bottom:1rem;"><?= ucfirst($viewing['booking_type']) ?></p>
                <p style="color:var(--text-muted);font-size:0.85rem;margin-bottom:0.3rem;">Preferred Date</p>
                <p style="color:var(--white);"><?= $viewing['preferred_date'] ? date('d/m/Y', strtotime($viewing['preferred_date'])) : '—' ?></p>
            </div>
        </div>

        <?php if ($viewing['message']): ?>
        <div class="form-section-title">Message</div>
        <div style="background:var(--bg);padding:1.2rem;border-radius:8px;color:var(--text-muted);font-size:0.9rem;line-height:1.6;margin-bottom:2rem;">
            <?= nl2br(htmlspecialchars($viewing['message'])) ?>
        </div>
        <?php endif; ?>

        <div class="form-section-title">Update Status</div>
        <div style="display:flex;gap:1rem;flex-wrap:wrap;">
            <a href="?edit=<?= $viewing['id'] ?>&status=pending&id=<?= $viewing['id'] ?>" 
               class="action-btn <?= $viewing['status'] === 'pending' ? 'action-btn-toggle' : 'action-btn-edit' ?>" style="padding:10px 20px;font-size:0.9rem;">
                ⏳ Pending
            </a>
            <a href="?edit=<?= $viewing['id'] ?>&status=confirmed&id=<?= $viewing['id'] ?>" 
               class="action-btn action-btn-edit" style="padding:10px 20px;font-size:0.9rem;<?= $viewing['status'] === 'confirmed' ? 'background:rgba(52,211,153,0.25);' : '' ?>">
                ✅ Confirmed
            </a>
            <a href="?edit=<?= $viewing['id'] ?>&status=cancelled&id=<?= $viewing['id'] ?>" 
               class="action-btn action-btn-delete" style="padding:10px 20px;font-size:0.9rem;<?= $viewing['status'] === 'cancelled' ? 'background:rgba(255,107,107,0.25);' : '' ?>">
                ❌ Cancelled
            </a>
        </div>

        <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid var(--border);display:flex;gap:1rem;">
            <a href="mailto:<?= htmlspecialchars($viewing['client_email']) ?>" class="topbar-btn">✉️ Email Client</a>
            <?php if ($viewing['client_phone']): ?>
            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $viewing['client_phone']) ?>" target="_blank" class="topbar-btn secondary">💬 WhatsApp</a>
            <?php endif; ?>
            <a href="?delete=<?= $viewing['id'] ?>" class="topbar-btn secondary confirm-delete" style="margin-left:auto;background:rgba(255,107,107,0.12);color:#FF6B6B;border:none;">🗑 Delete</a>
        </div>
    </div>
</div>

<?php else: ?>
<!-- LIST -->
<div class="admin-card">
    <div class="admin-card-header">
        <span class="admin-card-title">All Bookings (<?= count($bookings) ?>)</span>
        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
            <?php foreach (['all','pending','confirmed','cancelled'] as $f): ?>
            <a href="?filter=<?= $f ?>" class="action-btn <?= $filter === $f ? 'action-btn-edit' : '' ?>" style="padding:7px 16px;">
                <?= ucfirst($f) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="admin-table-wrap">
        <?php if (empty($bookings)): ?>
        <div class="empty-state"><div class="empty-state-icon">📭</div><p>No bookings <?= $filter !== 'all' ? 'with status "' . $filter . '"' : 'yet' ?>.</p></div>
        <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Client</th>
                    <th>Service</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Submitted</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $b): ?>
                <tr>
                    <td style="color:var(--text-dim);">#<?= $b['id'] ?></td>
                    <td>
                        <?= htmlspecialchars($b['client_name']) ?>
                        <div style="font-size:0.8rem;color:var(--text-dim);"><?= htmlspecialchars($b['client_email']) ?></div>
                    </td>
                    <td><?= htmlspecialchars($b['service_name'] ?? '—') ?></td>
                    <td><?= ucfirst($b['booking_type']) ?></td>
                    <td><?= $b['preferred_date'] ? date('d/m/Y', strtotime($b['preferred_date'])) : '—' ?></td>
                    <td><?= date('d/m/Y', strtotime($b['created_at'])) ?></td>
                    <td><span class="badge badge-<?= $b['status'] ?>"><?= ucfirst($b['status']) ?></span></td>
                    <td>
                        <div class="action-btns">
                            <a href="?edit=<?= $b['id'] ?>" class="action-btn action-btn-edit">View</a>
                            <?php if ($b['status'] !== 'confirmed'): ?>
                            <a href="?status=confirmed&id=<?= $b['id'] ?>" class="action-btn action-btn-edit" style="background:rgba(52,211,153,0.15);color:#34D399;">✓</a>
                            <?php endif; ?>
                            <?php if ($b['status'] !== 'cancelled'): ?>
                            <a href="?status=cancelled&id=<?= $b['id'] ?>" class="action-btn action-btn-delete">✕</a>
                            <?php endif; ?>
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
