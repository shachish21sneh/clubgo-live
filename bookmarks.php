<?php
require_once 'include/eventconfig.php';
require_once 'include/eventmania.php';

$pageTitle = 'Saved Events & Favorites - ' . ($set['webname'] ?? 'ClubGo');
require_once 'includes/header.php';

$uid = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$favEvents = [];
$favVenues = [];

if ($uid > 0) {
    // Events
    $eRes = $event->query("SELECT e.* FROM tbl_fav f JOIN tbl_event e ON f.eid = e.id WHERE f.uid = $uid AND e.status = 1 ORDER BY f.id DESC");
    if ($eRes) {
        while ($ev = $eRes->fetch_assoc()) {
            $favEvents[] = $ev;
        }
    }

    // Venues
    $vRes = $event->query("SELECT v.* FROM tbl_fav_venue fv JOIN tbl_veneu v ON fv.vid = v.loc_id WHERE fv.uid = $uid AND v.loc_status = 'A' ORDER BY fv.id DESC");
    if ($vRes) {
        while ($ve = $vRes->fetch_assoc()) {
            $favVenues[] = $ve;
        }
    }
}
?>

<main style="padding: 32px 0 60px;">
    <div class="container">
        
        <div class="section-header">
            <div class="section-title-wrap">
                <span class="section-tag">Saved Collection</span>
                <h1 class="section-title">Your Favorite Parties & Clubs</h1>
            </div>
        </div>

        <?php if ($uid <= 0): ?>
            <div style="text-align:center; padding:60px 20px; background:var(--surface); border-radius:var(--radius-lg); border:1px solid var(--border);">
                <svg width="48" height="48" fill="none" stroke="var(--danger)" viewBox="0 0 24 24" style="margin:0 auto 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                <h2 style="font-size:20px; font-weight:800; margin-bottom:8px;">Sign in to view saved items</h2>
                <p style="color:var(--text-muted); font-size:14px; margin-bottom:24px;">Keep track of upcoming weekend DJ sets, concert dates, and favorite club venues.</p>
                <button type="button" class="btn btn-primary" onclick="openModal('authModal')">Sign In</button>
            </div>
        <?php else: ?>

            <?php if (!empty($favEvents)): ?>
            <div style="margin-bottom:40px;">
                <h2 style="font-size:20px; font-weight:800; margin-bottom:18px;">Saved Events</h2>
                <div class="event-grid">
                    <?php foreach ($favEvents as $ev): 
                        $date = date_create($ev['sdate']);
                        $eventImg = !empty($ev['cover_img']) ? $ev['cover_img'] : $ev['img'];
                    ?>
                    <div class="event-card">
                        <div class="event-card-media">
                            <img src="<?php echo htmlspecialchars(get_image_url($eventImg)); ?>" alt="<?php echo htmlspecialchars($ev['title']); ?>">
                            <div class="event-date-badge">
                                <span class="month"><?php echo date_format($date, 'M'); ?></span>
                                <span class="day"><?php echo date_format($date, 'd'); ?></span>
                            </div>
                            <button type="button" class="bookmark-btn active" data-eid="<?php echo $ev['id']; ?>" aria-label="Save Event">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </button>
                        </div>
                        <div class="event-card-content">
                            <h3 class="event-card-title">
                                <a href="event_detail.php?id=<?php echo $ev['id']; ?>"><?php echo htmlspecialchars($ev['title']); ?></a>
                            </h3>
                            <div class="event-card-venue">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span><?php echo htmlspecialchars($ev['place_name'] ?: 'Venue'); ?></span>
                            </div>
                            <div class="event-card-footer" style="margin-top:auto;">
                                <a href="event_detail.php?id=<?php echo $ev['id']; ?>" class="btn btn-primary btn-sm btn-pill w-100">
                                    View Event
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($favVenues)): ?>
            <div>
                <h2 style="font-size:20px; font-weight:800; margin-bottom:18px;">Saved Clubs & Lounges</h2>
                <div class="venue-grid">
                    <?php foreach ($favVenues as $ve): ?>
                    <div class="venue-card">
                        <div class="venue-card-media">
                            <img src="<?php echo htmlspecialchars(get_image_url($ve['loc_image'] ?: 'placeholder-image.jpg')); ?>" alt="<?php echo htmlspecialchars($ve['loc_title']); ?>">
                        </div>
                        <div class="venue-card-content">
                            <h3 class="venue-card-title">
                                <a href="venue_detail.php?id=<?php echo $ve['loc_id']; ?>"><?php echo htmlspecialchars($ve['loc_title']); ?></a>
                            </h3>
                            <a href="venue_detail.php?id=<?php echo $ve['loc_id']; ?>" class="btn btn-secondary btn-sm w-100" style="margin-top:auto;">
                                View Club
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (empty($favEvents) && empty($favVenues)): ?>
            <div style="text-align:center; padding:60px 20px; background:var(--surface); border-radius:var(--radius-lg); border:1px solid var(--border);">
                <svg width="48" height="48" fill="none" stroke="var(--text-muted)" viewBox="0 0 24 24" style="margin:0 auto 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                <h3 style="font-size:18px; margin-bottom:6px;">No saved events yet</h3>
                <p style="color:var(--text-muted); font-size:14px; margin-bottom:20px;">Click the heart icon on any party or club card to save it for later.</p>
                <a href="events.php" class="btn btn-primary">Discover Events</a>
            </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
