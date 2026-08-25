<?php
require_once 'include/eventconfig.php';
require_once 'include/eventmania.php';

$eid = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($eid <= 0) {
    header("Location: events.php");
    exit;
}

$evQuery = $event->query("SELECT * FROM tbl_event WHERE id=$eid AND status=1");
if (!$evQuery || $evQuery->num_rows === 0) {
    header("Location: events.php");
    exit;
}

$ev = $evQuery->fetch_assoc();
$pageTitle = $ev['title'] . ' - ' . ($set['webname'] ?? 'ClubGo');
require_once 'includes/header.php';

$currentUid = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$sdate = date_create($ev['sdate']);
$formattedDate = date_format($sdate, 'l, d F Y');
$formattedTime = date("g:i A", strtotime($ev['stime'])) . ' to ' . date("g:i A", strtotime($ev['etime']));

// Fetch Category Names
$catTitles = [];
if (!empty($ev['cid'])) {
    $cids = array_map('intval', explode(',', $ev['cid']));
    $cidsStr = implode(',', $cids);
    $catRes = $event->query("SELECT title FROM tbl_cat WHERE id IN ($cidsStr)");
    if ($catRes) {
        while ($cRow = $catRes->fetch_assoc()) {
            $catTitles[] = $cRow['title'];
        }
    }
}

// Fetch Cover & Gallery Images
$galleryImages = [];
if (!empty($ev['cover_img'])) $galleryImages[] = $ev['cover_img'];
if (!empty($ev['img']) && !in_array($ev['img'], $galleryImages)) $galleryImages[] = $ev['img'];

$coverQuery = $event->query("SELECT img FROM tbl_cover WHERE eid=$eid AND status=1");
if ($coverQuery) {
    while ($c = $coverQuery->fetch_assoc()) {
        if (!empty($c['img']) && !in_array($c['img'], $galleryImages)) $galleryImages[] = $c['img'];
    }
}
$galQuery = $event->query("SELECT img FROM tbl_gallery WHERE eid=$eid AND status=1");
if ($galQuery) {
    while ($g = $galQuery->fetch_assoc()) {
        if (!empty($g['img']) && !in_array($g['img'], $galleryImages)) $galleryImages[] = $g['img'];
    }
}

// Fetch Ticket Types
$ticketTypes = [];
$tQuery = $event->query("SELECT * FROM tbl_type_price WHERE eid=$eid AND status=1");
$minPrice = 0;
if ($tQuery) {
    while ($t = $tQuery->fetch_assoc()) {
        $ticketTypes[] = $t;
        $prices = array_filter([(float)$t['price'], (float)$t['couple_price'], (float)$t['female_price'], (float)$t['male_price']], function($p) { return $p > 0; });
        if (!empty($prices)) {
            $lowest = min($prices);
            if ($minPrice === 0 || $lowest < $minPrice) $minPrice = $lowest;
        }
    }
}

// Sponsor
$sponsors = [];
$sponQuery = $event->query("SELECT * FROM tbl_sponsore WHERE eid=$eid AND status=1");
if ($sponQuery) {
    while ($sp = $sponQuery->fetch_assoc()) {
        $sponsors[] = $sp;
    }
}

// Bookmark check
$isBookmarked = false;
if ($currentUid > 0) {
    $fCheck = $event->query("SELECT id FROM tbl_fav WHERE uid=$currentUid AND eid=$eid");
    $isBookmarked = ($fCheck && $fCheck->num_rows > 0);
}

// Similar Events
$similarEvents = [];
if (!empty($ev['similer_event'])) {
    $simIds = array_map('intval', explode(',', $ev['similer_event']));
    $simStr = implode(',', $simIds);
    $simRes = $event->query("SELECT id, title, img, cover_img, sdate, place_name, address FROM tbl_event WHERE id IN ($simStr) AND status=1 LIMIT 3");
    if ($simRes) {
        while ($sRow = $simRes->fetch_assoc()) {
            $similarEvents[] = $sRow;
        }
    }
}
?>

<main style="padding: 24px 0 60px;">
    <div class="container">
        
        <!-- Breadcrumb / Back Link -->
        <div style="margin-bottom: 20px;">
            <a href="events.php" style="display:inline-flex; align-items:center; gap:6px; font-size:14px; font-weight:600; color:var(--text-muted);">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Events
            </a>
        </div>

        <div class="detail-layout">
            <!-- Left: Main Event Details -->
            <div class="detail-main">
                
                <!-- Main Cover Media -->
                <div class="detail-gallery">
                    <img src="<?php echo htmlspecialchars(get_image_url($galleryImages[0] ?? $ev['img'])); ?>" alt="<?php echo htmlspecialchars($ev['title']); ?>" id="mainDetailImg">
                </div>

                <!-- Gallery Thumbnails if available -->
                <?php if (count($galleryImages) > 1): ?>
                <div style="display:flex; gap:12px; overflow-x:auto; padding-bottom:6px;">
                    <?php foreach ($galleryImages as $gImg): ?>
                    <img src="<?php echo htmlspecialchars(get_image_url($gImg)); ?>" alt="Gallery photo" style="width:84px; height:60px; object-fit:cover; border-radius:var(--radius-sm); cursor:pointer; border:2px solid transparent;" onclick="document.getElementById('mainDetailImg').src = this.src;">
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Event Primary Info Card -->
                <div class="detail-info-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; margin-bottom:12px;">
                        <div>
                            <div style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:10px;">
                                <?php foreach ($catTitles as $cTitle): ?>
                                    <span class="badge badge-primary"><?php echo htmlspecialchars($cTitle); ?></span>
                                <?php endforeach; ?>
                                <?php if ($ev['payment_type'] === 'F'): ?>
                                    <span class="badge badge-success">Free Entry / Guestlist</span>
                                <?php endif; ?>
                            </div>
                            <h1 style="font-size:26px; font-weight:800; line-height:1.3;"><?php echo htmlspecialchars($ev['title']); ?></h1>
                        </div>

                        <!-- Bookmark Button -->
                        <button type="button" class="bookmark-btn <?php echo $isBookmarked ? 'active' : ''; ?>" data-eid="<?php echo $ev['id']; ?>" style="position:static; flex-shrink:0;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </button>
                    </div>

                    <!-- Meta Grid (Date, Time, Location) -->
                    <div class="detail-meta-grid">
                        <div class="detail-meta-item">
                            <div class="meta-icon-box">
                                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <div style="font-size:12px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Date & Time</div>
                                <div style="font-weight:700; font-size:14px;"><?php echo $formattedDate; ?></div>
                                <div style="font-size:13px; color:var(--text-muted);"><?php echo $formattedTime; ?></div>
                            </div>
                        </div>

                        <div class="detail-meta-item">
                            <div class="meta-icon-box">
                                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <div style="font-size:12px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Location & Venue</div>
                                <div style="font-weight:700; font-size:14px;"><?php echo htmlspecialchars($ev['place_name'] ?: 'Venue Details'); ?></div>
                                <div style="font-size:13px; color:var(--text-muted);"><?php echo htmlspecialchars($ev['address'] ?: ''); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Sponsor / Host Row if available -->
                    <?php if (!empty($sponsors)): ?>
                    <div style="display:flex; align-items:center; gap:12px; padding-top:16px; border-top:1px solid var(--border-light);">
                        <span style="font-size:13px; font-weight:600; color:var(--text-muted);">Organized by:</span>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <?php foreach ($sponsors as $sp): ?>
                                <?php if (!empty($sp['img'])): ?>
                                    <img src="<?php echo htmlspecialchars(get_image_url($sp['img'])); ?>" alt="<?php echo htmlspecialchars($sp['title']); ?>" style="height:28px; width:auto; border-radius:var(--radius-xs); object-fit:contain;">
                                <?php endif; ?>
                                <span style="font-weight:700; font-size:14px;"><?php echo htmlspecialchars($sp['title']); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Tabbed Sections: Overview, Lineup, Dress Code, Terms -->
                <div class="detail-info-card">
                    <div class="tabs-nav">
                        <button type="button" class="tab-btn active" data-tab="tabAbout">About Event</button>
                        <?php if (!empty($ev['dress_img'])): ?>
                        <button type="button" class="tab-btn" data-tab="tabDress">Dress Code</button>
                        <?php endif; ?>
                        <?php if (!empty($ev['floor_img'])): ?>
                        <button type="button" class="tab-btn" data-tab="tabFloor">Floor Plan</button>
                        <?php endif; ?>
                        <button type="button" class="tab-btn" data-tab="tabTerms">Terms & Guidelines</button>
                    </div>

                    <!-- Tab: About Event -->
                    <div class="tab-content active" id="tabAbout">
                        <div style="font-size:15px; color:var(--text-secondary); line-height:1.7;">
                            <?php echo !empty($ev['description']) ? $ev['description'] : 'Join us for an unforgettable party night with high-energy music, refreshing drinks, and the finest nightlife vibes.'; ?>
                        </div>
                    </div>

                    <!-- Tab: Dress Code -->
                    <?php if (!empty($ev['dress_img'])): ?>
                    <div class="tab-content" id="tabDress">
                        <h4 style="margin-bottom:12px;">Venue Dress Code Guidelines</h4>
                        <img src="<?php echo htmlspecialchars(get_image_url($ev['dress_img'])); ?>" alt="Dress Code" style="max-width:100%; border-radius:var(--radius-md);">
                    </div>
                    <?php endif; ?>

                    <!-- Tab: Floor Plan -->
                    <?php if (!empty($ev['floor_img'])): ?>
                    <div class="tab-content" id="tabFloor">
                        <h4 style="margin-bottom:12px;">Venue Layout & VIP Tables</h4>
                        <img src="<?php echo htmlspecialchars(get_image_url($ev['floor_img'])); ?>" alt="Floor Plan" style="max-width:100%; border-radius:var(--radius-md);">
                    </div>
                    <?php endif; ?>

                    <!-- Tab: Terms & Guidelines -->
                    <div class="tab-content" id="tabTerms">
                        <h4 style="margin-bottom:12px;">Entry Policy & Terms</h4>
                        <div style="font-size:14px; color:var(--text-secondary); line-height:1.7;">
                            <?php if (!empty($ev['term_and_condition'])): ?>
                                <?php echo $ev['term_and_condition']; ?>
                            <?php else: ?>
                                <ul style="padding-left:20px; display:flex; flex-direction:column; gap:8px;">
                                    <li>Valid physical Govt ID proof is mandatory for age verification (21+).</li>
                                    <li>Club management reserves the right of admission.</li>
                                    <li>Stag entries are subject to venue profile screening at entry gate.</li>
                                    <li>Tickets once purchased are non-refundable.</li>
                                </ul>
                            <?php endif; ?>

                            <?php if (!empty($ev['disclaimer'])): ?>
                                <div style="margin-top:16px; padding:14px; background:var(--bg-alt); border-radius:var(--radius-sm); font-size:13px;">
                                    <strong>Disclaimer:</strong> <?php echo $ev['disclaimer']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Similar Events Recommendation -->
                <?php if (!empty($similarEvents)): ?>
                <div>
                    <h3 style="font-size:20px; font-weight:800; margin-bottom:16px;">You Might Also Like</h3>
                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
                        <?php foreach ($similarEvents as $sim): 
                            $simDate = date_create($sim['sdate']);
                        ?>
                        <div class="event-card">
                            <div class="event-card-media" style="aspect-ratio:16/9;">
                                <img src="<?php echo htmlspecialchars(get_image_url(!empty($sim['cover_img']) ? $sim['cover_img'] : $sim['img'])); ?>" alt="<?php echo htmlspecialchars($sim['title']); ?>">
                            </div>
                            <div class="event-card-content" style="padding:14px;">
                                <div style="font-size:12px; font-weight:700; color:var(--primary); margin-bottom:4px;"><?php echo date_format($simDate, 'd M Y'); ?></div>
                                <h4 style="font-size:15px; font-weight:700; margin-bottom:6px;">
                                    <a href="event_detail.php?id=<?php echo $sim['id']; ?>"><?php echo htmlspecialchars($sim['title']); ?></a>
                                </h4>
                                <div style="font-size:12px; color:var(--text-muted); margin-top:auto;"><?php echo htmlspecialchars($sim['place_name'] ?: 'Venue'); ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right: Sticky Booking Drawer -->
            <div>
                <div class="booking-sidebar">
                    <div style="display:flex; justify-content:space-between; align-items:flex-end;">
                        <div>
                            <span class="price-label">Ticket Starting From</span>
                            <div class="price-amount" style="font-size:24px; color:var(--primary);">
                                <?php echo ($minPrice > 0) ? ('₹ ' . number_format($minPrice)) : '<span style="color:var(--success);">Free Guestlist</span>'; ?>
                            </div>
                        </div>
                        <span class="badge badge-success">Instant Confirmation</span>
                    </div>

                    <hr style="border:none; border-top:1px solid var(--border);">

                    <div>
                        <h4 style="font-size:15px; font-weight:700; margin-bottom:10px;">Available Passes & Tickets</h4>
                        <?php if (!empty($ticketTypes)): ?>
                            <?php foreach ($ticketTypes as $tt): 
                                $tPrice = (float)$tt['price'];
                                $cPrice = (float)$tt['couple_price'];
                                $fPrice = (float)$tt['female_price'];
                                $mPrice = (float)$tt['male_price'];
                            ?>
                            <div class="booking-ticket-type">
                                <div class="ticket-header">
                                    <div class="ticket-name"><?php echo htmlspecialchars($tt['name'] ?: 'General Entry'); ?></div>
                                    <div class="ticket-price-val">
                                        <?php if ($tPrice > 0): ?>
                                            ₹ <?php echo number_format($tPrice); ?>
                                        <?php elseif ($cPrice > 0): ?>
                                            ₹ <?php echo number_format($cPrice); ?> <small style="font-size:11px; font-weight:500;">(Couple)</small>
                                        <?php else: ?>
                                            <span style="color:var(--success);">Free Pass</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if (!empty($tt['details'])): ?>
                                    <p style="font-size:12px; color:var(--text-muted); margin-bottom:4px;"><?php echo htmlspecialchars($tt['details']); ?></p>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="font-size:13px; color:var(--text-muted);">Standard club guestlist and cover charges apply at gate.</p>
                        <?php endif; ?>
                    </div>

                    <a href="book_ticket.php?eid=<?php echo $eid; ?>" class="btn btn-primary btn-lg w-100" style="margin-top:8px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        Select Tickets & Book
                    </a>

                    <div style="font-size:12px; text-align:center; color:var(--text-muted); display:flex; align-items:center; justify-content:center; gap:6px;">
                        <svg width="14" height="14" fill="none" stroke="var(--success)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        100% Safe & Verified Club Booking
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
