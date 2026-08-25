<?php
if (!isset($event)) {
    require_once dirname(__DIR__) . '/include/eventconfig.php';
}

// Fetch Cities
$citiesQuery = $event->query("SELECT * FROM tbl_city WHERE status='A' ORDER BY id ASC");
$cities = [];
while ($c = $citiesQuery->fetch_assoc()) {
    $cities[] = $c;
}

// Determine Current Selected City
$selectedCityId = isset($_GET['city']) ? (int)$_GET['city'] : (isset($_COOKIE['selected_city_id']) ? (int)$_COOKIE['selected_city_id'] : 1);
$selectedCityName = 'DelhiNCR';
foreach ($cities as $city) {
    if ((int)$city['id'] === $selectedCityId) {
        $selectedCityName = $city['name'];
        break;
    }
}

// Check Logged in User & refresh wallet balance
$loggedInUser = null;
if (!empty($_SESSION['user_id'])) {
    $uRes = $event->query("SELECT * FROM tbl_user WHERE id=" . (int)$_SESSION['user_id'])->fetch_assoc();
    if ($uRes) {
        $loggedInUser = $uRes;
        $_SESSION['user_wallet'] = $uRes['wallet'];
        $_SESSION['user_name'] = $uRes['name'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo htmlspecialchars($pageTitle ?? ($set['webname'] ?? 'ClubGo') . ' - Event Booking & Nightlife Management'); ?></title>
    <meta name="description" content="Discover and book the best parties, DJ nights, live music, club passes, and nightlife events in your city with ClubGo.">
    <link rel="shortcut icon" type="image/png" href="<?php echo htmlspecialchars(get_image_url($set['weblogo'] ?? 'images/website/clubgoimg.webp')); ?>" />
    
    <!-- CSS Design System -->
    <link rel="stylesheet" href="css/frontend.css?v=<?php echo time(); ?>">
</head>
<body>

    <!-- Main Navigation Header -->
    <header class="site-header">
        <div class="container header-container">
            <!-- Left: Brand Logo & Location -->
            <div class="header-left">
                <a href="index.php" class="brand-logo" id="siteLogo">
                    <img src="<?php echo htmlspecialchars(get_image_url($set['weblogo'] ?? 'images/website/logo-red.svg')); ?>" alt="ClubGo">
                    <span style="display:none; font-weight:800; letter-spacing:-0.5px;">Club<span style="color:var(--primary);">Go</span></span>
                </a>

                <!-- City Selector Pill -->
                <button type="button" class="city-picker-btn" onclick="openModal('cityModal')" id="cityPickerTrigger">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span id="currentCityLabel"><?php echo htmlspecialchars($selectedCityName); ?></span>
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
            </div>

            <!-- Center: Search Bar -->
            <div class="header-search">
                <svg class="search-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="globalSearchInput" placeholder="Search events, clubs, artists, or venues..." autocomplete="off">
                <div class="search-suggestions" id="searchSuggestions"></div>
            </div>

            <!-- Right: Nav Links & User Actions -->
            <div class="header-right">
                <nav class="nav-links">
                    <a href="index.php" class="nav-link">Home</a>
                    <a href="events.php" class="nav-link">Explore Events</a>
                    <a href="venues.php" class="nav-link">Clubs & Venues</a>
                    <a href="events.php?free=1" class="nav-link">Offers & Guestlist</a>
                </nav>

                <?php if ($loggedInUser): ?>
                    <!-- Wallet Pill -->
                    <a href="wallet.php" class="wallet-badge-btn" title="Your ClubGo Wallet">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span>₹ <?php echo number_format($loggedInUser['wallet'] ?? 0); ?></span>
                    </a>

                    <!-- User Profile Dropdown -->
                    <div class="user-dropdown">
                        <div class="user-profile-trigger">
                            <img src="<?php echo htmlspecialchars(get_image_url($loggedInUser['pro_pic'] ?? 'images/profile/pic1.jpg')); ?>" alt="<?php echo htmlspecialchars($loggedInUser['name']); ?>">
                            <span style="font-weight:600; font-size:13px; max-width:90px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                <?php echo htmlspecialchars(explode(' ', $loggedInUser['name'])[0]); ?>
                            </span>
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                        <div class="user-dropdown-menu">
                            <div style="padding:10px 14px; border-bottom:1px solid var(--border-light);">
                                <div style="font-weight:700; font-size:14px;"><?php echo htmlspecialchars($loggedInUser['name']); ?></div>
                                <div style="font-size:12px; color:var(--text-muted);"><?php echo htmlspecialchars($loggedInUser['mobile']); ?></div>
                            </div>
                            <a href="my_tickets.php" class="user-menu-item">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                My Bookings / Passes
                            </a>
                            <a href="wallet.php" class="user-menu-item">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                ClubGo Wallet
                            </a>
                            <a href="bookmarks.php" class="user-menu-item">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                Saved Events
                            </a>
                            <a href="user_profile.php" class="user-menu-item">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Profile Settings
                            </a>
                            <div class="user-menu-divider"></div>
                            <a href="javascript:void(0);" onclick="fetch('auth_handler.php?action=logout').then(() => window.location.reload());" class="user-menu-item text-danger">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Sign Out
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <button type="button" class="btn btn-primary btn-sm btn-pill" onclick="openModal('authModal')" id="loginTriggerBtn">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        Sign In / Register
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- City Selection Modal -->
    <div class="modal-backdrop" id="cityModal">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-title">Select Your City</div>
                <button type="button" class="modal-close-btn" onclick="closeModal('cityModal')">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="modal-body">
                <p style="font-size:13px; color:var(--text-muted); margin-bottom:16px;">Choose your city to discover upcoming events, clubs, and parties near you.</p>
                <div class="city-grid">
                    <?php foreach ($cities as $ct): ?>
                        <button type="button" class="city-card-btn <?php echo ((int)$ct['id'] === $selectedCityId) ? 'active' : ''; ?>" data-city-id="<?php echo $ct['id']; ?>" data-city-name="<?php echo htmlspecialchars($ct['name']); ?>">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span><?php echo htmlspecialchars($ct['name']); ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Auth Modal (Sign In & Sign Up) -->
    <div class="modal-backdrop" id="authModal">
        <div class="modal-box">
            <div class="modal-header" style="border-bottom:none; padding-bottom:0;">
                <div style="display:flex; gap:16px;">
                    <button type="button" class="tab-btn auth-tab-btn active" data-target="loginPanel">Sign In</button>
                    <button type="button" class="tab-btn auth-tab-btn" data-target="registerPanel">Create Account</button>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeModal('authModal')">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="modal-body">
                <!-- Login Panel -->
                <div class="auth-form-panel" id="loginPanel">
                    <div id="loginErrorMsg" style="display:none; padding:10px 14px; background:var(--danger-subtle); color:var(--danger); border-radius:var(--radius-sm); font-size:13px; font-weight:600; margin-bottom:14px;"></div>
                    <form id="userLoginForm">
                        <div class="form-group">
                            <label class="form-label">Mobile Number or Email</label>
                            <input type="text" name="mobile" class="form-control" placeholder="e.g. 9876543210" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block w-100" style="margin-top:8px;">Sign In</button>
                    </form>
                </div>

                <!-- Register Panel -->
                <div class="auth-form-panel" id="registerPanel" style="display:none;">
                    <div id="registerErrorMsg" style="display:none; padding:10px 14px; background:var(--danger-subtle); color:var(--danger); border-radius:var(--radius-sm); font-size:13px; font-weight:600; margin-bottom:14px;"></div>
                    <form id="userRegisterForm">
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Alex Sharma" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="e.g. alex@example.com" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Mobile Number</label>
                            <input type="tel" name="mobile" class="form-control" placeholder="e.g. 9876543210" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Create Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Referral Code (Optional)</label>
                            <input type="text" name="referral" class="form-control" placeholder="Enter friend's invite code">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block w-100" style="margin-top:8px;">Create Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
