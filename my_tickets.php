<?php
require_once 'include/eventconfig.php';
require_once 'include/eventmania.php';

$pageTitle = 'My Bookings & Passes - ' . ($set['webname'] ?? 'ClubGo');
require_once 'includes/header.php';

$uid = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$today = date('Y-m-d');

$tickets = [];
if ($uid > 0) {
    $tRes = $event->query("SELECT t.*, e.title as event_title, e.img as event_img, e.cover_img as event_cover_img, e.sdate, e.stime, e.place_name, e.address FROM tbl_ticket t JOIN tbl_event e ON t.eid = e.id WHERE t.uid = $uid ORDER BY t.id DESC");
    if ($tRes) {
        while ($row = $tRes->fetch_assoc()) {
            $tickets[] = $row;
        }
    }
}
?>

<main style="padding: 32px 0 60px;">
    <div class="container">
        
        <div class="section-header">
            <div class="section-title-wrap">
                <span class="section-tag">Pass Management</span>
                <h1 class="section-title">My Bookings & Passes</h1>
            </div>
        </div>

        <?php if ($uid <= 0): ?>
            <div style="text-align:center; padding:60px 20px; background:var(--surface); border-radius:var(--radius-lg); border:1px solid var(--border);">
                <svg width="48" height="48" fill="none" stroke="var(--primary)" viewBox="0 0 24 24" style="margin:0 auto 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                <h2 style="font-size:20px; font-weight:800; margin-bottom:8px;">Sign in to view your tickets</h2>
                <p style="color:var(--text-muted); font-size:14px; margin-bottom:24px;">Log in with your mobile number to access all your confirmed party passes, VIP table receipts, and guestlists.</p>
                <button type="button" class="btn btn-primary" onclick="openModal('authModal')">Sign In to Account</button>
            </div>
        <?php else: ?>

            <?php if (!empty($tickets)): ?>
            <div style="display:grid; grid-template-columns:1fr; gap:20px; max-width:850px; margin:0 auto;">
                <?php foreach ($tickets as $t): 
                    $tDate = date_create($t['sdate']);
                    $isPast = ($t['sdate'] < $today);
                    $passImg = !empty($t['event_cover_img']) ? $t['event_cover_img'] : $t['event_img'];
                ?>
                <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg); padding:20px; box-shadow:var(--shadow-sm); display:flex; flex-direction:column; gap:16px;">
                    <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:flex-start; gap:12px;">
                        <div style="display:flex; gap:16px; align-items:center;">
                            <img src="<?php echo htmlspecialchars(get_image_url($passImg)); ?>" alt="event" style="width:70px; height:70px; border-radius:var(--radius-md); object-fit:cover;">
                            <div>
                                <span class="badge <?php echo ($t['ticket_type'] === 'Cancelled') ? 'badge-danger' : ($isPast ? 'badge-dark' : 'badge-success'); ?>" style="margin-bottom:6px;">
                                    <?php echo ($t['ticket_type'] === 'Cancelled') ? 'Cancelled' : ($isPast ? 'Completed Night' : 'Confirmed Pass'); ?>
                                </span>
                                <h3 style="font-size:17px; font-weight:700;">
                                    <a href="event_detail.php?id=<?php echo $t['eid']; ?>"><?php echo htmlspecialchars($t['event_title']); ?></a>
                                </h3>
                                <div style="font-size:13px; color:var(--text-muted);">
                                    <?php echo date_format($tDate, 'd M Y'); ?> • <?php echo htmlspecialchars($t['place_name'] ?: 'Venue'); ?>
                                </div>
                            </div>
                        </div>

                        <div style="text-align:right;">
                            <span class="price-label">Booking ID #</span>
                            <div style="font-weight:700; font-size:14px; color:var(--primary);">CG-<?php echo str_pad($t['id'], 6, '0', STR_PAD_LEFT); ?></div>
                            <div style="font-weight:800; font-size:16px; margin-top:4px;">
                                <?php echo ($t['total_amt'] > 0) ? ('₹ ' . number_format($t['total_amt'], 2)) : '<span style="color:var(--success);">Free</span>'; ?>
                            </div>
                        </div>
                    </div>

                    <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; border-top:1px solid var(--border-light); padding-top:14px; gap:12px;">
                        <div style="font-size:13px; color:var(--text-muted);">
                            Pass Type: <strong style="color:var(--text-main);"><?php echo htmlspecialchars($t['type'] ?: 'Entry Pass'); ?></strong> (<?php echo $t['total_ticket']; ?> Guests)
                        </div>
                        <a href="ticket_view.php?id=<?php echo $t['id']; ?>" class="btn btn-primary btn-sm btn-pill">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            View Digital Pass
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div style="text-align:center; padding:60px 20px; background:var(--surface); border-radius:var(--radius-lg); border:1px solid var(--border);">
                <svg width="48" height="48" fill="none" stroke="var(--text-muted)" viewBox="0 0 24 24" style="margin:0 auto 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                <h3 style="font-size:18px; margin-bottom:6px;">No bookings found yet</h3>
                <p style="color:var(--text-muted); font-size:14px; margin-bottom:20px;">Explore trending parties and club guestlists in your city.</p>
                <a href="events.php" class="btn btn-primary">Discover Events</a>
            </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
