<?php
require_once 'include/eventconfig.php';
require_once 'include/eventmania.php';

$pageTitle = 'Top Nightclubs, Lounges & Party Venues - ' . ($set['webname'] ?? 'ClubGo');
require_once 'includes/header.php';

$selectedCity = isset($_GET['city']) ? (int)$_GET['city'] : $selectedCityId;
$searchQ = trim($_GET['q'] ?? '');

$sql = "SELECT v.*, c.name as city_name FROM tbl_veneu v LEFT JOIN tbl_city c ON v.loc_city_id = c.id WHERE v.loc_status='A'";

if ($selectedCity > 0) {
    $sql .= " AND v.loc_city_id = $selectedCity";
}

if (!empty($searchQ)) {
    $esc = $event->real_escape_string($searchQ);
    $sql .= " AND (v.loc_title LIKE '%$esc%' OR v.loc_customer_headlines LIKE '%$esc%')";
}

$sql .= " ORDER BY v.loc_id DESC";
$vRes = $event->query($sql);
$venues = [];
if ($vRes) {
    while ($ve = $vRes->fetch_assoc()) {
        $venues[] = $ve;
    }
}
?>

<main style="padding: 32px 0 60px;">
    <div class="container">
        
        <div style="margin-bottom: 28px;">
            <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 8px;">Top Nightclubs, Lounges & Bars</h1>
            <p style="color: var(--text-muted); font-size: 14px;">Browse verified partner clubs, cocktail lounges, and rooftops in your city.</p>
        </div>

        <?php if (!empty($venues)): ?>
        <div class="venue-grid">
            <?php foreach ($venues as $ve): 
                $vImg = !empty($ve['loc_image']) ? $ve['loc_image'] : 'placeholder-image.jpg';
            ?>
            <div class="venue-card">
                <div class="venue-card-media">
                    <img src="<?php echo htmlspecialchars(get_image_url($vImg)); ?>" alt="<?php echo htmlspecialchars($ve['loc_title']); ?>" loading="lazy">
                    <span class="venue-city-pill"><?php echo htmlspecialchars($ve['city_name'] ?? 'ClubGo'); ?></span>
                </div>
                <div class="venue-card-content">
                    <h3 class="venue-card-title">
                        <a href="venue_detail.php?id=<?php echo $ve['loc_id']; ?>"><?php echo htmlspecialchars($ve['loc_title']); ?></a>
                    </h3>
                    <p class="venue-cuisines"><?php echo htmlspecialchars($ve['loc_customer_headlines'] ?: 'Cocktails, DJ & Nightlife'); ?></p>
                    
                    <div class="venue-timing" style="margin-bottom:12px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span><?php echo !empty($ve['loc_start_time']) ? (date("g:i A", strtotime($ve['loc_start_time'])) . ' - ' . date("g:i A", strtotime($ve['loc_end_time']))) : 'Open Tonight'; ?></span>
                    </div>

                    <a href="venue_detail.php?id=<?php echo $ve['loc_id']; ?>" class="btn btn-secondary btn-sm w-100" style="margin-top:auto;">
                        View Club Details
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div style="text-align:center; padding:60px 20px; background:var(--surface); border-radius:var(--radius-lg); border:1px solid var(--border);">
            <svg width="48" height="48" fill="none" stroke="var(--text-muted)" viewBox="0 0 24 24" style="margin:0 auto 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <h3 style="font-size:18px; margin-bottom:6px;">No venues found for this selection</h3>
            <p style="color:var(--text-muted); font-size:14px; margin-bottom:20px;">Try choosing another city from the location selector.</p>
            <button type="button" class="btn btn-primary" onclick="openModal('cityModal')">Switch City</button>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
