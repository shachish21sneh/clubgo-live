    <!-- Mobile Bottom App Bar -->
    <nav class="mobile-bottom-nav">
        <a href="index.php" class="bottom-nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Home</span>
        </a>
        <a href="events.php" class="bottom-nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>Events</span>
        </a>
        <a href="venues.php" class="bottom-nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <span>Venues</span>
        </a>
        <a href="<?php echo !empty($_SESSION['user_id']) ? 'bookmarks.php' : 'javascript:openModal(\'authModal\');'; ?>" class="bottom-nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            <span>Saved</span>
        </a>
        <a href="<?php echo !empty($_SESSION['user_id']) ? 'my_tickets.php' : 'javascript:openModal(\'authModal\');'; ?>" class="bottom-nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
            <span>Tickets</span>
        </a>
    </nav>

    <!-- Footer Component -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Brand Summary -->
                <div class="footer-brand">
                    <a href="index.php" class="brand-logo" style="color:#fff;">
                        <img src="<?php echo htmlspecialchars($set['weblogo'] ?? 'images/website/logo-red.svg'); ?>" alt="ClubGo" style="max-height:36px; filter:brightness(0) invert(1);">
                    </a>
                    <p>India's premier party and nightlife booking platform. Instant table reservations, free club guestlists, DJ concerts, and nightlife passes in top venues.</p>
                    <div style="margin-top:18px; display:flex; gap:12px;">
                        <a href="https://instagram.com/clubgoapp" target="_blank" class="btn-icon btn-secondary" style="color:#fff; background:rgba(255,255,255,0.1); border:none; display:flex; align-items:center; justify-content:center;" title="Instagram">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <a href="https://facebook.com/clubgoapp" target="_blank" class="btn-icon btn-secondary" style="color:#fff; background:rgba(255,255,255,0.1); border:none; display:flex; align-items:center; justify-content:center;" title="Facebook">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8H6v4h3v12h5V12h3.642L18 8h-4V6.333C14 5.374 14.5 5 15.5 5H18V0h-3.808C10.595 0 9 1.583 9 4.615V8z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Event Categories -->
                <div>
                    <h4 class="footer-heading">Top Categories</h4>
                    <ul class="footer-links">
                        <li><a href="events.php?cat=1" class="footer-link">DJ Nights</a></li>
                        <li><a href="events.php?cat=7" class="footer-link">Bollywood Parties</a></li>
                        <li><a href="events.php?cat=3" class="footer-link">Live Music Bands</a></li>
                        <li><a href="events.php?cat=5" class="footer-link">Techno & EDM</a></li>
                        <li><a href="events.php?cat=6" class="footer-link">Ladies Night Specials</a></li>
                    </ul>
                </div>

                <!-- Quick Navigation -->
                <div>
                    <h4 class="footer-heading">Explore</h4>
                    <ul class="footer-links">
                        <li><a href="events.php" class="footer-link">All Upcoming Events</a></li>
                        <li><a href="venues.php" class="footer-link">Popular Clubs & Lounges</a></li>
                        <li><a href="events.php?free=1" class="footer-link">Free Guestlist Passes</a></li>
                        <li><a href="wallet.php" class="footer-link">Refer & Earn Rewards</a></li>
                        <li><a href="page.php?id=3" class="footer-link">Contact & Support</a></li>
                    </ul>
                </div>

                <!-- Legal & Admin -->
                <div>
                    <h4 class="footer-heading">Legal & Admin</h4>
                    <ul class="footer-links">
                        <li><a href="page.php?id=1" class="footer-link">Privacy Policy</a></li>
                        <li><a href="page.php?id=2" class="footer-link">Terms & Conditions</a></li>
                        <li><a href="page.php?id=4" class="footer-link">Help & FAQs</a></li>
                        <li style="margin-top:10px;">
                            <a href="admin.php" class="footer-link" style="color:var(--accent); font-weight:600; display:inline-flex; align-items:center; gap:6px;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Admin Portal
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div>© <?php echo date('Y'); ?> <?php echo htmlspecialchars($set['webname'] ?? 'ClubGo'); ?>. All rights reserved.</div>
                <div style="display:flex; gap:16px;">
                    <a href="page.php?id=1" class="footer-link">Privacy</a>
                    <span>•</span>
                    <a href="page.php?id=2" class="footer-link">Terms</a>
                    <span>•</span>
                    <a href="page.php?id=3" class="footer-link">Support</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript Scripts -->
    <script src="js/frontend.js?v=<?php echo time(); ?>"></script>
</body>
</html>
