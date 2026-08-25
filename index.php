<?php
require_once 'include/eventconfig.php';
require_once 'include/eventmania.php';

$pageTitle = ($set['webname'] ?? 'ClubGo') . ' - Explore Parties, Clubs & Nightlife Events';
require_once 'includes/header.php';

$currentUid = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$today = date('Y-m-d');

// 1. Fetch Featured Events for Hero Carousel
$heroEvents = [];
$heroQuery = $event->query("SELECT * FROM tbl_event WHERE status=1 AND (cover_img != '' OR img != '') ORDER BY id DESC LIMIT 5");
if ($heroQuery) {
    while ($ev = $heroQuery->fetch_assoc()) {
        $heroEvents[] = $ev;
    }
}

// 2. Fetch Active Categories
$categories = [];
$catQuery = $event->query("SELECT * FROM tbl_cat WHERE status=1 ORDER BY id ASC");
if ($catQuery) {
    while ($cat = $catQuery->fetch_assoc()) {
        $categories[] = $cat;
    }
}

// 3. Fetch Upcoming Events
$selectedCatId = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
$eventSql = "SELECT * FROM tbl_event WHERE status=1";
if ($selectedCatId > 0) {
    $eventSql .= " AND FIND_IN_SET('$selectedCatId', cid)";
}
$eventSql .= " ORDER BY id DESC LIMIT 12";
$eventsResult = $event->query($eventSql);
$upcomingEvents = [];

if ($eventsResult && $eventsResult->num_rows > 0) {
    $rawEvents = [];
    $eids = [];
    while ($ev = $eventsResult->fetch_assoc()) {
        $rawEvents[] = $ev;
        $eids[] = (int)$ev['id'];
    }

    $eidString = implode(',', $eids);
    
    // 1. Batch user bookmarks
    $userBookmarks = [];
    if ($currentUid > 0) {
        $favRes = $event->query("SELECT eid FROM tbl_fav WHERE uid=$currentUid");
        if ($favRes) while ($fr = $favRes->fetch_assoc()) $userBookmarks[(int)$fr['eid']] = true;
    }

    // 2. Batch prices
    $pricesMap = [];
    $pRes = $event->query("SELECT eid, price, couple_price, female_price, male_price FROM tbl_type_price WHERE eid IN ($eidString) ORDER BY price ASC");
    if ($pRes) {
        while ($pr = $pRes->fetch_assoc()) {
            $eIdKey = (int)$pr['eid'];
            if (!isset($pricesMap[$eIdKey])) {
                $prices = array_filter([(float)$pr['price'], (float)$pr['couple_price'], (float)$pr['female_price'], (float)$pr['male_price']], function($p) { return $p > 0; });
                $pricesMap[$eIdKey] = !empty($prices) ? min($prices) : 0;
            }
        }
    }

    // 3. Batch attendees preview
    $membersMap = [];
    $uList = $event->query("SELECT t.eid, u.pro_pic FROM tbl_ticket t JOIN tbl_user u ON t.uid = u.id WHERE t.eid IN ($eidString) AND u.pro_pic IS NOT NULL AND u.pro_pic != '' GROUP BY t.eid, t.uid");
    if ($uList) {
        while ($u = $uList->fetch_assoc()) {
            $eIdKey = (int)$u['eid'];
            if (!isset($membersMap[$eIdKey])) $membersMap[$eIdKey] = [];
            if (count($membersMap[$eIdKey]) < 3) $membersMap[$eIdKey][] = $u['pro_pic'];
        }
    }

    // 4. Batch total tickets
    $totalMap = [];
    $totRes = $event->query("SELECT eid, SUM(ticket_book) as total FROM tbl_type_price WHERE eid IN ($eidString) GROUP BY eid");
    if ($totRes) {
        while ($tr = $totRes->fetch_assoc()) {
            $totalMap[(int)$tr['eid']] = (int)($tr['total'] ?? 0);
        }
    }

    foreach ($rawEvents as $ev) {
        $eid = (int)$ev['id'];
        $ev['min_price'] = $pricesMap[$eid] ?? 0;
        $ev['is_bookmarked'] = isset($userBookmarks[$eid]);
        $ev['member_avatars'] = $membersMap[$eid] ?? [];
        $totCount = $totalMap[$eid] ?? 0;
        $ev['total_members'] = $totCount > 0 ? $totCount : rand(15, 60);
        $upcomingEvents[] = $ev;
    }
}

// 4. Fetch Top Venues & Nightclubs
$venueSql = "SELECT v.*, c.name as city_name FROM tbl_veneu v LEFT JOIN tbl_city c ON v.loc_city_id = c.id WHERE v.loc_status='A' ORDER BY v.loc_id DESC LIMIT 4";
$venuesResult = $event->query($venueSql);
$venues = [];
if ($venuesResult) {
    while ($ve = $venuesResult->fetch_assoc()) {
        $venues[] = $ve;
    }
}
?>

<main>
    <!-- 1. Hero Carousel Banner -->
    <?php if (!empty($heroEvents)): ?>
    <section class="hero-section">
        <div class="container">
            <div class="hero-carousel">
                <div class="hero-slider-track">
                    <?php foreach ($heroEvents as $index => $hev): 
                        $sdate = date_create($hev['sdate']);
                        $heroImg = !empty($hev['cover_img']) ? $hev['cover_img'] : $hev['img'];
                    ?>
                    <div class="hero-slide">
                        <img src="<?php echo htmlspecialchars(get_image_url($heroImg)); ?>" alt="<?php echo htmlspecialchars($hev['title']); ?>">
                        <div class="hero-overlay">
                            <div class="hero-badge-row">
                                <span class="badge badge-accent">Featured Night</span>
                                <span class="badge badge-dark"><?php echo date_format($sdate, 'D, d M Y'); ?></span>
                            </div>
                            <h1 class="hero-title"><?php echo htmlspecialchars($hev['title']); ?></h1>
                            <div class="hero-meta">
                                <?php if (!empty($hev['place_name'])): ?>
                                <div class="hero-meta-item">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span><?php echo htmlspecialchars($hev['place_name']); ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="hero-meta-item">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span><?php echo date("g:i A", strtotime($hev['stime'])); ?> onwards</span>
                                </div>
                            </div>
                            <div style="display:flex; gap:12px;">
                                <a href="event_detail.php?id=<?php echo $hev['id']; ?>" class="btn btn-primary btn-lg">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                    Book Passes Now
                                </a>
                                <a href="event_detail.php?id=<?php echo $hev['id']; ?>" class="btn btn-secondary btn-lg" style="background:rgba(255,255,255,0.2); color:#fff; border-color:rgba(255,255,255,0.3); backdrop-filter:blur(8px);">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Navigation Controls -->
                <div class="hero-controls">
                    <button type="button" class="hero-nav-btn" id="heroPrevBtn" aria-label="Previous Slide">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button type="button" class="hero-nav-btn" id="heroNextBtn" aria-label="Next Slide">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- 2. Category Filter Chips -->
    <section class="category-section">
        <div class="container">
            <div class="category-scroll">
                <a href="index.php" class="category-chip <?php echo ($selectedCatId === 0) ? 'active' : ''; ?>">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span>All Events</span>
                </a>
                <?php foreach ($categories as $cat): ?>
                <a href="index.php?cat=<?php echo $cat['id']; ?>" class="category-chip <?php echo ($selectedCatId === (int)$cat['id']) ? 'active' : ''; ?>">
                    <?php if (!empty($cat['img'])): ?>
                        <img src="<?php echo htmlspecialchars(get_image_url($cat['img'])); ?>" alt="<?php echo htmlspecialchars($cat['title']); ?>">
                    <?php endif; ?>
                    <span><?php echo htmlspecialchars($cat['title']); ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- 3. Upcoming Events Section -->
    <section style="padding: 24px 0 40px;">
        <div class="container">
            <div class="section-header">
                <div class="section-title-wrap">
                    <span class="section-tag">Happening in <?php echo htmlspecialchars($selectedCityName); ?></span>
                    <h2 class="section-title">Upcoming Nightlife & Events</h2>
                </div>
                <a href="events.php" class="section-link">
                    <span>See All Events</span>
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <?php if (!empty($upcomingEvents)): ?>
            <div class="event-grid">
                <?php foreach ($upcomingEvents as $ev): 
                    $date = date_create($ev['sdate']);
                    $eventImg = !empty($ev['cover_img']) ? $ev['cover_img'] : $ev['img'];
                ?>
                <div class="event-card">
                    <div class="event-card-media">
                        <img src="<?php echo htmlspecialchars(get_image_url($eventImg)); ?>" alt="<?php echo htmlspecialchars($ev['title']); ?>" loading="lazy">
                        
                        <!-- Date Badge -->
                        <div class="event-date-badge">
                            <span class="month"><?php echo date_format($date, 'M'); ?></span>
                            <span class="day"><?php echo date_format($date, 'd'); ?></span>
                        </div>

                        <!-- Bookmark Button -->
                        <button type="button" class="bookmark-btn <?php echo $ev['is_bookmarked'] ? 'active' : ''; ?>" data-eid="<?php echo $ev['id']; ?>" aria-label="Save Event">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </button>
                    </div>

                    <div class="event-card-content">
                        <h3 class="event-card-title">
                            <a href="event_detail.php?id=<?php echo $ev['id']; ?>"><?php echo htmlspecialchars($ev['title']); ?></a>
                        </h3>

                        <div class="event-card-venue">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                <?php echo htmlspecialchars($ev['place_name'] ?: ($ev['address'] ?: 'Venue TBA')); ?>
                            </span>
                        </div>

                        <!-- Attendees Row -->
                        <div class="event-card-members">
                            <div class="member-avatars">
                                <img src="images/profile/pic1.jpg" class="member-avatar" alt="attendee">
                                <img src="images/profile/pic1.jpg" class="member-avatar" alt="attendee">
                                <img src="images/profile/pic1.jpg" class="member-avatar" alt="attendee">
                            </div>
                            <span class="member-count-text">+<?php echo $ev['total_members']; ?> party lovers going</span>
                        </div>

                        <!-- Card Footer -->
                        <div class="event-card-footer">
                            <div class="event-price-box">
                                <span class="price-label"><?php echo ($ev['min_price'] > 0) ? 'Starting from' : 'Entry Status'; ?></span>
                                <span class="price-amount">
                                    <?php echo ($ev['min_price'] > 0) ? ('₹ ' . number_format($ev['min_price'])) : '<span style="color:var(--success);">Free / Guestlist</span>'; ?>
                                </span>
                            </div>
                            <a href="event_detail.php?id=<?php echo $ev['id']; ?>" class="btn btn-primary btn-sm btn-pill">
                                Book Now
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div style="text-align:center; padding:60px 20px; background:var(--surface); border-radius:var(--radius-lg); border:1px solid var(--border);">
                <svg width="48" height="48" fill="none" stroke="var(--text-muted)" viewBox="0 0 24 24" style="margin:0 auto 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <h3 style="font-size:18px; margin-bottom:6px;">No events found in this category</h3>
                <p style="color:var(--text-muted); font-size:14px; margin-bottom:20px;">Try selecting another category or explore all parties happening in your city.</p>
                <a href="index.php" class="btn btn-secondary">View All Events</a>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- 4. Promo / Refer & Earn Banner -->
    <section class="promo-section">
        <div class="container">
            <div class="promo-banner">
                <div class="promo-content">
                    <span class="badge badge-accent" style="margin-bottom:12px;">ClubGo Rewards</span>
                    <h2 class="promo-title">Earn ₹500 In Wallet For Every Friend You Invite!</h2>
                    <p class="promo-subtitle">Share your referral link with friends. When they register and book their first event pass, both of you earn instant wallet cashback for free drinks & passes.</p>
                </div>
                <div class="promo-actions">
                    <a href="wallet.php" class="btn btn-primary btn-lg" style="background:#fff; color:var(--primary); box-shadow:0 6px 20px rgba(0,0,0,0.15);">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                        Invite Friends Now
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. Top Clubs & Venues Section -->
    <?php if (!empty($venues)): ?>
    <section style="padding: 32px 0 60px;">
        <div class="container">
            <div class="section-header">
                <div class="section-title-wrap">
                    <span class="section-tag">Premium Destinations</span>
                    <h2 class="section-title">Popular Clubs & Lounges</h2>
                </div>
                <a href="venues.php" class="section-link">
                    <span>Explore All Clubs</span>
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="venue-grid">
                <?php foreach ($venues as $ve): 
                    $vImg = !empty($ve['loc_image']) ? $ve['loc_image'] : 'placeholder-image.jpg';
                ?>
                <div class="venue-card">
                    <div class="venue-card-media">
                        <img src="<?php echo htmlspecialchars(get_image_url($vImg)); ?>" alt="<?php echo htmlspecialchars($ve['loc_title']); ?>" loading="lazy">
                        <span class="venue-city-pill"><?php echo htmlspecialchars($ve['city_name'] ?? 'ClubGo Venue'); ?></span>
                    </div>
                    <div class="venue-card-content">
                        <h3 class="venue-card-title">
                            <a href="venue_detail.php?id=<?php echo $ve['loc_id']; ?>"><?php echo htmlspecialchars($ve['loc_title']); ?></a>
                        </h3>
                        <p class="venue-cuisines"><?php echo htmlspecialchars($ve['loc_customer_headlines'] ?? 'Nightclub & Cocktail Lounge'); ?></p>
                        
                        <div class="venue-timing">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span><?php echo !empty($ve['loc_start_time']) ? (date("g:i A", strtotime($ve['loc_start_time'])) . ' - ' . date("g:i A", strtotime($ve['loc_end_time']))) : 'Open Tonight'; ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- 6. Trust & Perks Highlights -->
    <section style="background:var(--surface); border-top:1px solid var(--border); padding:56px 0;">
        <div class="container">
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:32px; text-align:center;">
                <div>
                    <div style="width:56px; height:56px; border-radius:var(--radius-pill); background:var(--primary-subtle); color:var(--primary); display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                        <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004 11a8 8 0 00.99 7.132"/></svg>
                    </div>
                    <h3 style="font-size:17px; margin-bottom:6px;">Instant QR Passes</h3>
                    <p style="font-size:13px; color:var(--text-muted);">Get instant digital tickets & boarding passes on your phone without physical line waiting.</p>
                </div>

                <div>
                    <div style="width:56px; height:56px; border-radius:var(--radius-pill); background:var(--accent-light); color:var(--accent); display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                        <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    </div>
                    <h3 style="font-size:17px; margin-bottom:6px;">Free Guestlists & Drinks</h3>
                    <p style="font-size:13px; color:var(--text-muted);">Exclusive couple and ladies free drinks offers verified directly with partner clubs.</p>
                </div>

                <div>
                    <div style="width:56px; height:56px; border-radius:var(--radius-pill); background:var(--success-subtle); color:var(--success); display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                        <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 style="font-size:17px; margin-bottom:6px;">100% Guaranteed Entry</h3>
                    <p style="font-size:13px; color:var(--text-muted);">Hassle-free club entry backed by ClubGo customer helpline and venue management team.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>