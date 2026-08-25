<?php
require_once 'include/eventconfig.php';
require_once 'include/eventmania.php';

$tid = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($tid <= 0) {
    header("Location: my_tickets.php");
    exit;
}

$tQuery = $event->query("SELECT t.*, e.title as event_title, e.img as event_img, e.cover_img as event_cover_img, e.sdate, e.stime, e.etime, e.place_name, e.address, u.name as user_name, u.mobile as user_mobile, u.email as user_email FROM tbl_ticket t JOIN tbl_event e ON t.eid = e.id LEFT JOIN tbl_user u ON t.uid = u.id WHERE t.id = $tid");

if (!$tQuery || $tQuery->num_rows === 0) {
    header("Location: my_tickets.php");
    exit;
}

$ticket = $tQuery->fetch_assoc();
$pageTitle = 'Digital Pass #' . $ticket['id'] . ' - ' . $ticket['event_title'];
require_once 'includes/header.php';

$sdate = date_create($ticket['sdate']);
$formattedDate = date_format($sdate, 'l, d F Y');
$formattedTime = date("g:i A", strtotime($ticket['stime'])) . ' to ' . date("g:i A", strtotime($ticket['etime']));
$passImg = !empty($ticket['event_cover_img']) ? $ticket['event_cover_img'] : $ticket['event_img'];
?>

<main style="padding: 32px 0 60px;">
    <div class="container">
        
        <div style="display:flex; justify-content:space-between; align-items:center; max-width:600px; margin:0 auto 20px;">
            <a href="my_tickets.php" style="display:inline-flex; align-items:center; gap:6px; font-size:14px; font-weight:600; color:var(--text-muted);">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                All Bookings
            </a>

            <button type="button" onclick="window.print()" class="btn btn-secondary btn-sm" style="display:inline-flex; gap:6px;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print / Save PDF
            </button>
        </div>

        <!-- Digital Ticket Pass -->
        <div class="ticket-pass-container">
            <div class="ticket-pass" id="printableTicket">
                <!-- Header Banner -->
                <div class="ticket-pass-header" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                        <img src="<?php echo htmlspecialchars(get_image_url($set['weblogo'] ?? 'images/website/logo-red.svg')); ?>" alt="ClubGo" style="height:28px; filter:brightness(0) invert(1);">
                        <span class="badge badge-success" style="font-size:12px; padding:6px 12px;">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Booking Confirmed
                        </span>
                    </div>

                    <h2 style="font-size:22px; font-weight:800; color:#fff; margin-bottom:8px; line-height:1.3;"><?php echo htmlspecialchars($ticket['event_title']); ?></h2>
                    <div style="font-size:13px; color:#c7d2fe; display:flex; align-items:center; gap:6px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span><?php echo htmlspecialchars($ticket['place_name'] ?: ($ticket['address'] ?: 'Venue')); ?></span>
                    </div>
                </div>

                <!-- Perforated Divider -->
                <div class="ticket-pass-divider"></div>

                <!-- Body Details -->
                <div class="ticket-pass-body">
                    <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:16px; margin-bottom:20px;">
                        <div>
                            <span class="price-label">Pass Holder</span>
                            <div style="font-weight:700; font-size:15px;"><?php echo htmlspecialchars($ticket['user_name'] ?: 'Guest'); ?></div>
                            <div style="font-size:12px; color:var(--text-muted);"><?php echo htmlspecialchars($ticket['user_mobile']); ?></div>
                        </div>

                        <div>
                            <span class="price-label">Booking ID #</span>
                            <div style="font-weight:800; font-size:15px; color:var(--primary);">CG-<?php echo str_pad($ticket['id'], 6, '0', STR_PAD_LEFT); ?></div>
                            <div style="font-size:12px; color:var(--text-muted);"><?php echo date('d M Y, h:i A', strtotime($ticket['created_at'])); ?></div>
                        </div>

                        <div>
                            <span class="price-label">Date & Timing</span>
                            <div style="font-weight:700; font-size:14px;"><?php echo $formattedDate; ?></div>
                            <div style="font-size:12px; color:var(--text-muted);"><?php echo $formattedTime; ?></div>
                        </div>

                        <div>
                            <span class="price-label">Pass Type & Quantity</span>
                            <div style="font-weight:700; font-size:14px;"><?php echo htmlspecialchars($ticket['type'] ?: 'Entry Pass'); ?></div>
                            <div style="font-size:12px; font-weight:600; color:var(--success);"><?php echo $ticket['total_ticket']; ?> Pass(es)</div>
                        </div>
                    </div>

                    <!-- Payment Summary Box -->
                    <div style="padding:14px; background:var(--bg-alt); border-radius:var(--radius-md); display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <span class="price-label">Amount Paid</span>
                            <div style="font-size:18px; font-weight:800; color:var(--text-main);">
                                <?php echo ($ticket['total_amt'] > 0) ? ('₹ ' . number_format($ticket['total_amt'], 2)) : '<span style="color:var(--success);">Free Entry</span>'; ?>
                            </div>
                        </div>
                        <div style="text-align:right;">
                            <span class="price-label">Payment Ref</span>
                            <div style="font-size:12px; font-weight:600; font-family:monospace; color:var(--text-muted);"><?php echo htmlspecialchars($ticket['transaction_id']); ?></div>
                        </div>
                    </div>

                    <!-- QR Code Pass Section -->
                    <div class="ticket-pass-qr">
                        <!-- Simulated SVG QR Code -->
                        <svg viewBox="0 0 100 100" width="140" height="140">
                            <rect width="100" height="100" fill="#ffffff" rx="8"/>
                            <!-- Top Left Finder Pattern -->
                            <rect x="10" y="10" width="24" height="24" fill="#0f172a" rx="3"/>
                            <rect x="14" y="14" width="16" height="16" fill="#ffffff"/>
                            <rect x="18" y="18" width="8" height="8" fill="#0f172a"/>
                            <!-- Top Right Finder Pattern -->
                            <rect x="66" y="10" width="24" height="24" fill="#0f172a" rx="3"/>
                            <rect x="70" y="14" width="16" height="16" fill="#ffffff"/>
                            <rect x="74" y="18" width="8" height="8" fill="#0f172a"/>
                            <!-- Bottom Left Finder Pattern -->
                            <rect x="10" y="66" width="24" height="24" fill="#0f172a" rx="3"/>
                            <rect x="14" y="70" width="16" height="16" fill="#ffffff"/>
                            <rect x="18" y="74" width="8" height="8" fill="#0f172a"/>
                            <!-- QR Data Noise Blocks -->
                            <rect x="40" y="12" width="6" height="6" fill="#0f172a"/>
                            <rect x="52" y="12" width="6" height="6" fill="#0f172a"/>
                            <rect x="44" y="24" width="8" height="8" fill="#0f172a"/>
                            <rect x="12" y="44" width="6" height="6" fill="#0f172a"/>
                            <rect x="24" y="48" width="8" height="6" fill="#0f172a"/>
                            <rect x="40" y="40" width="20" height="20" fill="#4f46e5" rx="2"/>
                            <rect x="68" y="44" width="6" height="10" fill="#0f172a"/>
                            <rect x="80" y="50" width="8" height="6" fill="#0f172a"/>
                            <rect x="42" y="68" width="8" height="8" fill="#0f172a"/>
                            <rect x="56" y="74" width="6" height="12" fill="#0f172a"/>
                            <rect x="72" y="68" width="14" height="6" fill="#0f172a"/>
                            <rect x="80" y="80" width="8" height="8" fill="#0f172a"/>
                        </svg>

                        <div style="font-size:12px; font-weight:700; color:var(--text-muted); margin-top:8px; letter-spacing:1px;">
                            SCAN AT CLUB ENTRANCE GATE
                        </div>
                    </div>

                    <div style="margin-top:20px; font-size:12px; color:var(--text-muted); line-height:1.6; text-align:center;">
                        Please present this digital pass or booking SMS along with a valid physical Govt Photo ID (21+) at the club reception desk for entry wristbands.
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
