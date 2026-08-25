<?php
require_once 'include/eventconfig.php';
require_once 'include/eventmania.php';

$vid = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($vid <= 0) {
    header("Location: venues.php");
    exit;
}

$vQuery = $event->query("SELECT v.*, c.name as city_name FROM tbl_veneu v LEFT JOIN tbl_city c ON v.loc_city_id = c.id WHERE v.loc_id=$vid AND v.loc_status='A'");
if (!$vQuery || $vQuery->num_rows === 0) {
    header("Location: venues.php");
    exit;
}

$venue = $vQuery->fetch_assoc();
$pageTitle = $venue['loc_title'] . ' - Nightclub & Lounge in ' . ($venue['city_name'] ?? 'Delhi');
require_once 'includes/header.php';

// Fetch Cuisines
$cuisinesList = [];
if (!empty($venue['loc_cuisines_id'])) {
    $cIds = array_map('intval', explode(',', $venue['loc_cuisines_id']));
    $cStr = implode(',', $cIds);
    $cRes = $event->query("SELECT name FROM tbl_cuisines WHERE id IN ($cStr)");
    if ($cRes) {
        while ($c = $cRes->fetch_assoc()) $cuisinesList[] = $c['name'];
    }
}

// Fetch Facilities
$facilitiesList = [];
if (!empty($venue['loc_facilities_id'])) {
    $fIds = array_map('intval', explode(',', $venue['loc_facilities_id']));
    $fStr = implode(',', $fIds);
    $fRes = $event->query("SELECT name FROM tbl_facilities WHERE id IN ($fStr)");
    if ($fRes) {
        while ($f = $fRes->fetch_assoc()) $facilitiesList[] = $f['name'];
    }
}

// Fetch Known For
$knownList = [];
if (!empty($venue['loc_known_for'])) {
    $kIds = array_map('intval', explode(',', $venue['loc_known_for']));
    $kStr = implode(',', $kIds);
    $kRes = $event->query("SELECT name FROM tbl_known_for WHERE id IN ($kStr)");
    if ($kRes) {
        while ($k = $kRes->fetch_assoc()) $knownList[] = $k['name'];
    }
}

// Fetch Venue Gallery
$venueGallery = [];
if (!empty($venue['loc_image'])) $venueGallery[] = $venue['loc_image'];
if (!empty($venue['banner_image']) && !in_array($venue['banner_image'], $venueGallery)) $venueGallery[] = $venue['banner_image'];

$vgRes = $event->query("SELECT img FROM tbl_venue_gallery WHERE vid=$vid AND status=1");
if ($vgRes) {
    while ($vg = $vgRes->fetch_assoc()) {
        if (!empty($vg['img']) && !in_array($vg['img'], $venueGallery)) $venueGallery[] = $vg['img'];
    }
}

// Fetch Events Hosted at this Venue
$venueEvents = [];
$escTitle = $event->real_escape_string($venue['loc_title']);
$evRes = $event->query("SELECT * FROM tbl_event WHERE status=1 AND (loc_id=$vid OR place_name LIKE '%$escTitle%') ORDER BY sdate ASC LIMIT 6");
if ($evRes) {
    while ($ev = $evRes->fetch_assoc()) {
        $venueEvents[] = $ev;
    }
}
?>

<main style="padding: 24px 0 60px;">
    <div class="container">
        
        <div style="margin-bottom: 20px;">
            <a href="venues.php" style="display:inline-flex; align-items:center; gap:6px; font-size:14px; font-weight:600; color:var(--text-muted);">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Venues
            </a>
        </div>

        <div class="detail-layout">
            <!-- Left Main Content -->
            <div class="detail-main">
                <!-- Gallery Cover -->
                <div class="detail-gallery">
                    <img src="<?php echo htmlspecialchars(get_image_url($venueGallery[0] ?? $venue['loc_image'])); ?>" alt="<?php echo htmlspecialchars($venue['loc_title']); ?>" id="mainVenueImg">
                </div>

                <?php if (count($venueGallery) > 1): ?>
                <div style="display:flex; gap:12px; overflow-x:auto; padding-bottom:6px;">
                    <?php foreach ($venueGallery as $vgImg): ?>
                    <img src="<?php echo htmlspecialchars(get_image_url($vgImg)); ?>" alt="Venue photo" style="width:84px; height:60px; object-fit:cover; border-radius:var(--radius-sm); cursor:pointer;" onclick="document.getElementById('mainVenueImg').src = this.src;">
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Venue Info Card -->
                <div class="detail-info-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
                        <div>
                            <span class="badge badge-primary" style="margin-bottom:8px;"><?php echo htmlspecialchars($venue['city_name'] ?? 'Nightlife'); ?></span>
                            <h1 style="font-size:26px; font-weight:800;"><?php echo htmlspecialchars($venue['loc_title']); ?></h1>
                            <p style="color:var(--text-muted); font-size:14px; margin-top:4px;"><?php echo htmlspecialchars($venue['loc_customer_headlines'] ?: 'Cocktail Bar & Club'); ?></p>
                        </div>
                    </div>

                    <!-- Meta Grid -->
                    <div class="detail-meta-grid">
                        <div class="detail-meta-item">
                            <div class="meta-icon-box">
                                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <div style="font-size:12px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Party Timings</div>
                                <div style="font-weight:700; font-size:14px;">
                                    <?php echo !empty($venue['loc_start_time']) ? (date("g:i A", strtotime($venue['loc_start_time'])) . ' - ' . date("g:i A", strtotime($venue['loc_end_time']))) : 'Open Tonight'; ?>
                                </div>
                                <div style="font-size:12px; color:var(--success); font-weight:600;">Active Guestlist Available</div>
                            </div>
                        </div>

                        <div class="detail-meta-item">
                            <div class="meta-icon-box">
                                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <div style="font-size:12px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">City & Location</div>
                                <div style="font-weight:700; font-size:14px;"><?php echo htmlspecialchars($venue['city_name'] ?? 'DelhiNCR'); ?></div>
                                <div style="font-size:12px; color:var(--text-muted);">Verified Club Partner</div>
                            </div>
                        </div>
                    </div>

                    <!-- Known For & Facilities -->
                    <?php if (!empty($knownList) || !empty($facilitiesList)): ?>
                    <div style="margin-top:20px; padding-top:20px; border-top:1px solid var(--border-light);">
                        <h4 style="font-size:14px; font-weight:700; color:var(--text-muted); text-transform:uppercase; margin-bottom:10px;">Highlights & Amenities</h4>
                        <div style="display:flex; flex-wrap:wrap; gap:8px;">
                            <?php foreach ($knownList as $kItem): ?>
                                <span class="badge badge-accent"><?php echo htmlspecialchars($kItem); ?></span>
                            <?php endforeach; ?>
                            <?php foreach ($facilitiesList as $fItem): ?>
                                <span class="badge badge-dark"><?php echo htmlspecialchars($fItem); ?></span>
                            <?php endforeach; ?>
                            <?php foreach ($cuisinesList as $cItem): ?>
                                <span class="badge badge-primary"><?php echo htmlspecialchars($cItem); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Description -->
                <?php if (!empty($venue['loc_description'])): ?>
                <div class="detail-info-card">
                    <h3 style="font-size:18px; font-weight:800; margin-bottom:12px;">About the Venue</h3>
                    <div style="font-size:15px; color:var(--text-secondary); line-height:1.7;">
                        <?php echo $venue['loc_description']; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Upcoming Events at Venue -->
                <div>
                    <h3 style="font-size:20px; font-weight:800; margin-bottom:16px;">Upcoming Parties at <?php echo htmlspecialchars($venue['loc_title']); ?></h3>
                    <?php if (!empty($venueEvents)): ?>
                    <div class="event-grid" style="grid-template-columns:repeat(auto-fit, minmax(260px, 1fr));">
                        <?php foreach ($venueEvents as $vev): 
                            $date = date_create($vev['sdate']);
                        ?>
                        <div class="event-card">
                            <div class="event-card-media">
                                <img src="<?php echo htmlspecialchars(get_image_url(!empty($vev['cover_img']) ? $vev['cover_img'] : $vev['img'])); ?>" alt="<?php echo htmlspecialchars($vev['title']); ?>">
                                <div class="event-date-badge">
                                    <span class="month"><?php echo date_format($date, 'M'); ?></span>
                                    <span class="day"><?php echo date_format($date, 'd'); ?></span>
                                </div>
                            </div>
                            <div class="event-card-content">
                                <h4 class="event-card-title">
                                    <a href="event_detail.php?id=<?php echo $vev['id']; ?>"><?php echo htmlspecialchars($vev['title']); ?></a>
                                </h4>
                                <a href="event_detail.php?id=<?php echo $vev['id']; ?>" class="btn btn-primary btn-sm btn-pill" style="margin-top:auto;">
                                    Book Passes
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p style="color:var(--text-muted); font-size:14px;">No public events currently listed for this venue. Check back soon for weekend party announcements.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div>
                <div class="booking-sidebar">
                    <h3 style="font-size:18px; font-weight:800;">Reserve a Table / Guestlist</h3>
                    <p style="font-size:13px; color:var(--text-muted);">Get free couple guestlist entry or reserve VIP tables directly with priority assistance.</p>
                    
                    <hr style="border:none; border-top:1px solid var(--border);">

                    <div style="font-size:13px; display:flex; flex-direction:column; gap:8px;">
                        <div><strong>Open Days:</strong> <?php echo htmlspecialchars($venue['loc_days'] ?: 'Wednesday to Sunday'); ?></div>
                        <div><strong>Music Style:</strong> Commercial, Bollywood, EDM & Techno</div>
                        <div><strong>Dress Code:</strong> Smart Casuals / Party Wear</div>
                    </div>

                    <?php if (!empty($venueEvents)): ?>
                    <a href="event_detail.php?id=<?php echo $venueEvents[0]['id']; ?>" class="btn btn-primary btn-lg w-100">
                        View Tonight's Party
                    </a>
                    <?php else: ?>
                    <a href="events.php" class="btn btn-primary btn-lg w-100">
                        Explore All Parties
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
