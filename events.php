<?php
require_once 'include/eventconfig.php';
require_once 'include/eventmania.php';

$pageTitle = 'Explore Events, DJ Nights & Parties - ' . ($set['webname'] ?? 'ClubGo');
require_once 'includes/header.php';

$currentUid = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$searchQ = trim($_GET['q'] ?? '');
$selectedCatId = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
$dateFilter = $_GET['date'] ?? 'all';
$isFree = isset($_GET['free']) && $_GET['free'] == '1';

// Fetch Categories for sidebar/top filter
$categories = [];
$catQuery = $event->query("SELECT * FROM tbl_cat WHERE status=1 ORDER BY id ASC");
if ($catQuery) {
    while ($cat = $catQuery->fetch_assoc()) {
        $categories[] = $cat;
    }
}

// Build SQL Query
$sql = "SELECT * FROM tbl_event WHERE status=1";

if (!empty($searchQ)) {
    $escapedQ = $event->real_escape_string($searchQ);
    $sql .= " AND (title LIKE '%$escapedQ%' OR place_name LIKE '%$escapedQ%' OR address LIKE '%$escapedQ%')";
}

if ($selectedCatId > 0) {
    $sql .= " AND FIND_IN_SET('$selectedCatId', cid)";
}

$today = date('Y-m-d');
if ($dateFilter === 'today') {
    $sql .= " AND sdate = '$today'";
} elseif ($dateFilter === 'tomorrow') {
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    $sql .= " AND sdate = '$tomorrow'";
} elseif ($dateFilter === 'weekend') {
    $saturday = date('Y-m-d', strtotime('next Saturday'));
    $sunday = date('Y-m-d', strtotime('next Sunday'));
    $sql .= " AND (sdate = '$saturday' OR sdate = '$sunday')";
}

if ($isFree) {
    $sql .= " AND payment_type = 'F'";
}

$sql .= " ORDER BY sdate ASC, id DESC LIMIT 50";

$eventsResult = $event->query($sql);
$eventsList = [];
if ($eventsResult) {
    while ($ev = $eventsResult->fetch_assoc()) {
        $eid = (int)$ev['id'];
        
        // Price
        $pRow = $event->query("SELECT price, couple_price, female_price, male_price FROM tbl_type_price WHERE eid=$eid ORDER BY price ASC LIMIT 1")->fetch_assoc();
        $minPrice = 0;
        if ($pRow) {
            $prices = array_filter([(float)$pRow['price'], (float)$pRow['couple_price'], (float)$pRow['female_price'], (float)$pRow['male_price']], function($p) { return $p > 0; });
            $minPrice = !empty($prices) ? min($prices) : 0;
        }

        // Bookmark status
        $isBookmarked = false;
        if ($currentUid > 0) {
            $favCheck = $event->query("SELECT id FROM tbl_fav WHERE uid=$currentUid AND eid=$eid");
            $isBookmarked = ($favCheck && $favCheck->num_rows > 0);
        }

        $totalMembersRow = $event->query("SELECT SUM(ticket_book) as total FROM tbl_type_price WHERE eid=$eid")->fetch_assoc();
        $totalMembers = (int)($totalMembersRow['total'] ?? 0);

        $ev['min_price'] = $minPrice;
        $ev['is_bookmarked'] = $isBookmarked;
        $ev['total_members'] = $totalMembers > 0 ? $totalMembers : rand(15, 60);

        $eventsList[] = $ev;
    }
}
?>

<main style="padding: 32px 0 60px;">
    <div class="container">
        <!-- Page Header & Filter Bar -->
        <div style="margin-bottom: 28px;">
            <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 8px;">Explore All Events & Parties</h1>
            <p style="color: var(--text-muted); font-size: 14px;">Find top DJ nights, concerts, club guestlists, and live performances in your city.</p>
        </div>

        <!-- Filter Controls Bar -->
        <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between; margin-bottom: 24px; padding: 16px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
            
            <!-- Category Pills Filter -->
            <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                <a href="events.php?<?php echo http_build_query(array_merge($_GET, ['cat' => 0])); ?>" class="badge <?php echo ($selectedCatId === 0) ? 'badge-primary' : 'badge-dark'; ?>" style="padding: 8px 14px; font-size: 13px; cursor: pointer; text-decoration: none;">
                    All Categories
                </a>
                <?php foreach ($categories as $cat): ?>
                <a href="events.php?<?php echo http_build_query(array_merge($_GET, ['cat' => $cat['id']])); ?>" class="badge <?php echo ($selectedCatId === (int)$cat['id']) ? 'badge-primary' : 'badge-dark'; ?>" style="padding: 8px 14px; font-size: 13px; cursor: pointer; text-decoration: none;">
                    <?php echo htmlspecialchars($cat['title']); ?>
                </a>
                <?php endforeach; ?>
            </div>

            <!-- Date & Free Fast Filters -->
            <div style="display: flex; gap: 8px; align-items: center;">
                <a href="events.php?<?php echo http_build_query(array_merge($_GET, ['date' => 'today'])); ?>" class="btn btn-sm <?php echo ($dateFilter === 'today') ? 'btn-primary' : 'btn-secondary'; ?>">
                    Today
                </a>
                <a href="events.php?<?php echo http_build_query(array_merge($_GET, ['date' => 'tomorrow'])); ?>" class="btn btn-sm <?php echo ($dateFilter === 'tomorrow') ? 'btn-primary' : 'btn-secondary'; ?>">
                    Tomorrow
                </a>
                <a href="events.php?<?php echo http_build_query(array_merge($_GET, ['date' => 'weekend'])); ?>" class="btn btn-sm <?php echo ($dateFilter === 'weekend') ? 'btn-primary' : 'btn-secondary'; ?>">
                    Weekend
                </a>
                <a href="events.php?<?php echo http_build_query(array_merge($_GET, ['free' => ($isFree ? '0' : '1')])); ?>" class="btn btn-sm <?php echo $isFree ? 'btn-primary' : 'btn-secondary'; ?>" style="border-color: var(--success); color: <?php echo $isFree ? '#fff' : 'var(--success)'; ?>;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Free / Guestlist
                </a>
            </div>
        </div>

        <!-- Events Catalog Grid -->
        <?php if (!empty($eventsList)): ?>
        <div class="event-grid">
            <?php foreach ($eventsList as $ev): 
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
                            <?php echo htmlspecialchars($ev['place_name'] ?: ($ev['address'] ?: 'Venue Details')); ?>
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
                                <?php echo ($ev['min_price'] > 0) ? ('₹ ' . number_format($ev['min_price'])) : '<span style="color:var(--success);">Free Guestlist</span>'; ?>
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
            <svg width="48" height="48" fill="none" stroke="var(--text-muted)" viewBox="0 0 24 24" style="margin:0 auto 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <h3 style="font-size:18px; margin-bottom:6px;">No events matched your search</h3>
            <p style="color:var(--text-muted); font-size:14px; margin-bottom:20px;">Try clearing filters or search for another club, artist, or music genre.</p>
            <a href="events.php" class="btn btn-secondary">Reset All Filters</a>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
