<?php
require_once 'include/eventconfig.php';
require_once 'include/eventmania.php';

$eid = isset($_GET['eid']) ? (int)$_GET['eid'] : 0;
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
$pageTitle = 'Book Tickets - ' . $ev['title'];

// Process Ticket Booking Submission
$bookingError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'confirm_booking') {
    $uid = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
    
    // If guest user submitted name/mobile, create or find user
    if ($uid <= 0) {
        $guestName = trim($_POST['guest_name'] ?? '');
        $guestMobile = trim($_POST['guest_mobile'] ?? '');
        $guestEmail = trim($_POST['guest_email'] ?? '');

        if (empty($guestName) || empty($guestMobile)) {
            $bookingError = 'Please provide your name and mobile number for the ticket booking.';
        } else {
            $escMobile = $event->real_escape_string($guestMobile);
            $uCheck = $event->query("SELECT * FROM tbl_user WHERE mobile='$escMobile' LIMIT 1");
            if ($uCheck && $uCheck->num_rows > 0) {
                $uRow = $uCheck->fetch_assoc();
                $uid = (int)$uRow['id'];
            } else {
                $escName = $event->real_escape_string($guestName);
                $escEmail = $event->real_escape_string($guestEmail ?: ($guestMobile . '@clubgo.in'));
                $myCode = rand(1000, 9999);
                $now = date('Y-m-d H:i:s');
                $event->query("INSERT INTO tbl_user (name, email, mobile, password, ccode, code, wallet, status, rdate) VALUES ('$escName', '$escEmail', '$escMobile', '" . md5('123456') . "', '+91', $myCode, '0', 1, '$now')");
                $uid = (int)$event->insert_id;
            }
            $_SESSION['user_id'] = $uid;
            $_SESSION['user_name'] = $guestName;
            $_SESSION['user_mobile'] = $guestMobile;
        }
    }

    if ($uid > 0 && empty($bookingError)) {
        $typeId = (int)($_POST['typeid'] ?? 0);
        $ticketType = trim($_POST['ticket_type_name'] ?? 'General Entry');
        $ticketPrice = (float)($_POST['ticket_price'] ?? 0);
        $totalQty = (int)($_POST['total_qty'] ?? 1);
        $subtotal = $ticketPrice * $totalQty;

        $couponAmt = (float)($_POST['coupon_discount'] ?? 0);
        $walletAmt = (float)($_POST['wallet_discount'] ?? 0);
        $taxPercent = (float)($set['tax'] ?? 0);
        $taxAmt = round(($subtotal * $taxPercent) / 100, 2);
        
        $totalAmt = max(0, ($subtotal + $taxAmt) - ($couponAmt + $walletAmt));

        $selectDate = trim($_POST['select_date'] ?? $ev['sdate']);
        $arrivalTime = trim($_POST['arrival_time'] ?? $ev['stime']);
        $guestsCouple = (int)($_POST['guests_couple'] ?? 0);
        $guestsMale = (int)($_POST['guests_male'] ?? 0);
        $guestsFemale = (int)($_POST['guests_female'] ?? 0);
        
        $pMethodId = 1; // Standard / Online / Free
        $transactionId = 'CG' . strtoupper(uniqid());

        // Insert into tbl_ticket
        $h = new Eventmania();
        $fields = ["p_method_id", "transaction_id", "eid", "type", "price", "subtotal", "cou_amt", "total_ticket", "total_amt", "uid", "wall_amt", "typeid", "tax", "select_date", "arrival_time", "guests_couple", "guests_male", "guests_female"];
        $dataVals = [$pMethodId, $transactionId, $eid, $ticketType, $ticketPrice, $subtotal, $couponAmt, $totalQty, $totalAmt, $uid, $walletAmt, $typeId, $taxAmt, $selectDate, $arrivalTime, $guestsCouple, $guestsMale, $guestsFemale];

        $ticketId = $h->eventinsertdata_Api_Id($fields, $dataVals, 'tbl_ticket');

        if ($ticketId) {
            // Update booked tickets count in tbl_type_price
            if ($typeId > 0) {
                $bookRow = $event->query("SELECT ticket_book FROM tbl_type_price WHERE eid=$eid AND id=$typeId")->fetch_assoc();
                $curBook = (int)($bookRow['ticket_book'] ?? 0) + $totalQty;
                $event->query("UPDATE tbl_type_price SET ticket_book=$curBook WHERE eid=$eid AND id=$typeId");
            }

            // Deduct wallet if used
            if ($walletAmt > 0) {
                $uData = $event->query("SELECT wallet FROM tbl_user WHERE id=$uid")->fetch_assoc();
                $curWallet = (float)($uData['wallet'] ?? 0);
                $newWallet = max(0, $curWallet - $walletAmt);
                $event->query("UPDATE tbl_user SET wallet=$newWallet WHERE id=$uid");
                
                $timestamp = date('Y-m-d H:i:s');
                $event->query("INSERT INTO wallet_report (uid, message, status, amt, tdate) VALUES ($uid, 'Wallet Used for Booking #$ticketId', 'Debit', '$walletAmt', '$timestamp')");
            }

            header("Location: ticket_view.php?id=" . $ticketId);
            exit;
        } else {
            $bookingError = 'Failed to generate ticket. Please try again.';
        }
    }
}

require_once 'includes/header.php';

// Fetch Ticket Types for this event
$ticketTypes = [];
$tQuery = $event->query("SELECT * FROM tbl_type_price WHERE eid=$eid AND status=1");
if ($tQuery) {
    while ($t = $tQuery->fetch_assoc()) {
        $ticketTypes[] = $t;
    }
}
$firstType = !empty($ticketTypes) ? $ticketTypes[0] : null;
$defaultPrice = 0;
if ($firstType) {
    $prices = array_filter([(float)$firstType['price'], (float)$firstType['couple_price'], (float)$firstType['female_price'], (float)$firstType['male_price']], function($p) { return $p > 0; });
    $defaultPrice = !empty($prices) ? min($prices) : 0;
}

$userWallet = !empty($loggedInUser) ? (float)($loggedInUser['wallet'] ?? 0) : 0;
$taxRate = (float)($set['tax'] ?? 0);
?>

<main style="padding: 32px 0 60px;">
    <div class="container">
        
        <div style="margin-bottom: 24px;">
            <a href="event_detail.php?id=<?php echo $eid; ?>" style="display:inline-flex; align-items:center; gap:6px; font-size:14px; font-weight:600; color:var(--text-muted);">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Event Details
            </a>
        </div>

        <div class="section-header" style="margin-bottom: 24px;">
            <div class="section-title-wrap">
                <span class="section-tag">Checkout & Ticket Pass</span>
                <h1 style="font-size: 26px; font-weight: 800;"><?php echo htmlspecialchars($ev['title']); ?></h1>
            </div>
        </div>

        <?php if (!empty($bookingError)): ?>
            <div style="padding:14px 18px; background:var(--danger-subtle); color:var(--danger); border-radius:var(--radius-md); font-weight:600; margin-bottom:24px;">
                <?php echo htmlspecialchars($bookingError); ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="checkoutForm">
            <input type="hidden" name="action" value="confirm_booking">
            <input type="hidden" name="typeid" id="selectedTypeId" value="<?php echo $firstType ? $firstType['id'] : 0; ?>">
            <input type="hidden" name="ticket_type_name" id="selectedTypeName" value="<?php echo $firstType ? htmlspecialchars($firstType['name']) : 'General Entry'; ?>">
            <input type="hidden" name="ticket_price" id="selectedTicketPrice" value="<?php echo $defaultPrice; ?>">
            <input type="hidden" name="total_qty" id="selectedTotalQty" value="1">
            <input type="hidden" name="coupon_discount" id="selectedCouponDiscount" value="0">
            <input type="hidden" name="wallet_discount" id="selectedWalletDiscount" value="0">

            <div class="detail-layout">
                <!-- Left: Ticket Tier & Guest Selection -->
                <div class="detail-main">
                    
                    <!-- 1. Select Ticket Pass Type -->
                    <div class="detail-info-card">
                        <h2 style="font-size:18px; font-weight:800; margin-bottom:16px;">1. Select Ticket / Entry Pass Type</h2>
                        
                        <?php if (!empty($ticketTypes)): ?>
                            <div style="display:flex; flex-direction:column; gap:12px;">
                                <?php foreach ($ticketTypes as $index => $tt): 
                                    $tPrice = (float)$tt['price'];
                                    $cPrice = (float)$tt['couple_price'];
                                    $fPrice = (float)$tt['female_price'];
                                    $mPrice = (float)$tt['male_price'];
                                    $effectivePrice = $tPrice > 0 ? $tPrice : ($cPrice > 0 ? $cPrice : ($fPrice > 0 ? $fPrice : ($mPrice > 0 ? $mPrice : 0)));
                                    $isSelected = ($index === 0);
                                ?>
                                <div class="booking-ticket-type <?php echo $isSelected ? 'selected' : ''; ?>" 
                                     data-type-id="<?php echo $tt['id']; ?>"
                                     data-type-name="<?php echo htmlspecialchars($tt['name'] ?: 'Pass'); ?>"
                                     data-type-price="<?php echo $effectivePrice; ?>"
                                     onclick="selectTicketTier(this)">
                                    
                                    <div class="ticket-header">
                                        <div>
                                            <div class="ticket-name"><?php echo htmlspecialchars($tt['name'] ?: 'Entry Pass'); ?></div>
                                            <?php if (!empty($tt['details'])): ?>
                                                <div style="font-size:13px; color:var(--text-muted);"><?php echo htmlspecialchars($tt['details']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ticket-price-val" style="font-size:18px;">
                                            <?php echo ($effectivePrice > 0) ? ('₹ ' . number_format($effectivePrice)) : '<span style="color:var(--success);">Free Entry</span>'; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="booking-ticket-type selected" data-type-id="0" data-type-name="Standard Entry" data-type-price="0">
                                <div class="ticket-header">
                                    <div>
                                        <div class="ticket-name">Standard Club Guestlist</div>
                                        <div style="font-size:13px; color:var(--text-muted);">Free club entry. Cover charge applies at venue gate.</div>
                                    </div>
                                    <div class="ticket-price-val" style="color:var(--success);">Free</div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 2. Quantity & Date/Time -->
                    <div class="detail-info-card">
                        <h2 style="font-size:18px; font-weight:800; margin-bottom:16px;">2. Quantity & Arrival Details</h2>
                        
                        <div style="display:flex; justify-content:space-between; align-items:center; padding:16px; background:var(--bg-alt); border-radius:var(--radius-md); margin-bottom:18px;">
                            <div>
                                <div style="font-weight:700; font-size:15px;">Number of Passes</div>
                                <div style="font-size:13px; color:var(--text-muted);">Max 10 passes per booking</div>
                            </div>
                            <div class="ticket-qty-control">
                                <button type="button" class="qty-btn" onclick="updateQty(-1)">-</button>
                                <span class="qty-display" id="qtyDisplay">1</span>
                                <button type="button" class="qty-btn" onclick="updateQty(1)">+</button>
                            </div>
                        </div>

                        <div class="detail-meta-grid" style="margin:0;">
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label">Date of Event</label>
                                <input type="date" name="select_date" class="form-control" value="<?php echo htmlspecialchars($ev['sdate']); ?>" required>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label">Estimated Arrival Time</label>
                                <input type="time" name="arrival_time" class="form-control" value="<?php echo htmlspecialchars($ev['stime']); ?>" required>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Guest Information (if not logged in) -->
                    <?php if (!$loggedInUser): ?>
                    <div class="detail-info-card">
                        <h2 style="font-size:18px; font-weight:800; margin-bottom:16px;">3. Primary Guest Details</h2>
                        <div class="detail-meta-grid" style="margin:0 0 14px 0;">
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="guest_name" class="form-control" placeholder="e.g. Rahul Sharma" required>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label">Mobile Number</label>
                                <input type="tel" name="guest_mobile" class="form-control" placeholder="e.g. 9876543210" required>
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Email Address (for ticket receipt)</label>
                            <input type="email" name="guest_email" class="form-control" placeholder="e.g. rahul@example.com">
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Right: Price Breakdown & Checkout Summary -->
                <div>
                    <div class="booking-sidebar">
                        <h3 style="font-size:18px; font-weight:800; margin-bottom:14px;">Order Summary</h3>

                        <!-- Promo Code Input -->
                        <div style="margin-bottom:14px;">
                            <label class="form-label">Have a Promo / Coupon Code?</label>
                            <div style="display:flex; gap:8px;">
                                <input type="text" id="couponCodeInput" class="form-control" placeholder="e.g. WELCOME5" style="text-transform:uppercase;">
                                <button type="button" class="btn btn-secondary" onclick="applyCoupon()">Apply</button>
                            </div>
                            <div id="couponStatusMsg" style="font-size:12px; margin-top:6px;"></div>
                        </div>

                        <!-- Wallet Balance Option -->
                        <?php if ($userWallet > 0): ?>
                        <div style="padding:14px; background:linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border:1px solid #f59e0b; border-radius:var(--radius-md); margin-bottom:14px;">
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <div>
                                    <div style="font-weight:700; font-size:13px; color:#b45309;">Use ClubGo Wallet Balance</div>
                                    <div style="font-size:12px; color:#92400e;">Available: ₹ <?php echo number_format($userWallet, 2); ?></div>
                                </div>
                                <input type="checkbox" id="useWalletCheckbox" onchange="toggleWalletUsage()" style="width:18px; height:18px; cursor:pointer;">
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Breakdown Table -->
                        <div style="display:flex; flex-direction:column; gap:10px; font-size:14px; padding:14px; background:var(--bg-alt); border-radius:var(--radius-md);">
                            <div style="display:flex; justify-content:space-between;">
                                <span style="color:var(--text-muted);">Pass Price (<span id="summaryQty">1</span>x)</span>
                                <span style="font-weight:600;" id="summarySubtotal">₹ <?php echo number_format($defaultPrice, 2); ?></span>
                            </div>
                            <div style="display:flex; justify-content:space-between;">
                                <span style="color:var(--text-muted);">Taxes & Fees (<?php echo $taxRate; ?>%)</span>
                                <span style="font-weight:600;" id="summaryTax">₹ <?php echo number_format(($defaultPrice * $taxRate) / 100, 2); ?></span>
                            </div>
                            <div style="display:flex; justify-content:space-between; color:var(--success); display:none;" id="couponRow">
                                <span>Coupon Discount</span>
                                <span style="font-weight:600;" id="summaryCoupon">- ₹ 0.00</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; color:var(--accent); display:none;" id="walletRow">
                                <span>Wallet Balance Applied</span>
                                <span style="font-weight:600;" id="summaryWallet">- ₹ 0.00</span>
                            </div>
                            <hr style="border:none; border-top:1px solid var(--border);">
                            <div style="display:flex; justify-content:space-between; font-size:16px; font-weight:800;">
                                <span>Total Payable</span>
                                <span style="color:var(--primary);" id="summaryGrandTotal">₹ <?php echo number_format($defaultPrice + ($defaultPrice * $taxRate) / 100, 2); ?></span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100" style="margin-top:10px;">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Confirm & Generate Pass
                        </button>

                        <div style="font-size:12px; text-align:center; color:var(--text-muted);">
                            By clicking confirm, you agree to ClubGo Booking Terms & Club Entry Policies.
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>

<script>
let currentPrice = <?php echo (float)$defaultPrice; ?>;
let currentQty = 1;
let couponDiscount = 0;
let walletDiscount = 0;
const userWalletBal = <?php echo (float)$userWallet; ?>;
const taxPercent = <?php echo (float)$taxRate; ?>;

function selectTicketTier(el) {
    document.querySelectorAll('.booking-ticket-type').forEach(card => card.classList.remove('selected'));
    el.classList.add('selected');

    const typeId = el.dataset.typeId;
    const typeName = el.dataset.typeName;
    const price = parseFloat(el.dataset.typePrice) || 0;

    document.getElementById('selectedTypeId').value = typeId;
    document.getElementById('selectedTypeName').value = typeName;
    document.getElementById('selectedTicketPrice').value = price;
    currentPrice = price;

    recalcTotal();
}

function updateQty(delta) {
    currentQty = Math.max(1, Math.min(10, currentQty + delta));
    document.getElementById('qtyDisplay').innerText = currentQty;
    document.getElementById('selectedTotalQty').value = currentQty;
    document.getElementById('summaryQty').innerText = currentQty;
    recalcTotal();
}

function applyCoupon() {
    const code = document.getElementById('couponCodeInput').value.trim();
    const statusMsg = document.getElementById('couponStatusMsg');
    const subtotal = currentPrice * currentQty;

    if (!code) {
        statusMsg.innerHTML = '<span style="color:var(--danger);">Please enter a coupon code.</span>';
        return;
    }

    fetch('auth_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'check_coupon', code, amount: subtotal })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            couponDiscount = parseFloat(data.discount) || 0;
            statusMsg.innerHTML = `<span style="color:var(--success); font-weight:600;">${data.message}</span>`;
            document.getElementById('couponRow').style.display = 'flex';
            document.getElementById('summaryCoupon').innerText = `- ₹ ${couponDiscount.toFixed(2)}`;
            document.getElementById('selectedCouponDiscount').value = couponDiscount;
            recalcTotal();
        } else {
            couponDiscount = 0;
            statusMsg.innerHTML = `<span style="color:var(--danger);">${data.message}</span>`;
            document.getElementById('couponRow').style.display = 'none';
            document.getElementById('selectedCouponDiscount').value = 0;
            recalcTotal();
        }
    })
    .catch(() => {
        statusMsg.innerHTML = '<span style="color:var(--danger);">Failed to apply coupon.</span>';
    });
}

function toggleWalletUsage() {
    const isChecked = document.getElementById('useWalletCheckbox')?.checked;
    if (isChecked) {
        const subtotal = currentPrice * currentQty;
        const tax = (subtotal * taxPercent) / 100;
        const netBeforeWallet = Math.max(0, (subtotal + tax) - couponDiscount);
        walletDiscount = Math.min(userWalletBal, netBeforeWallet);
        document.getElementById('walletRow').style.display = 'flex';
        document.getElementById('summaryWallet').innerText = `- ₹ ${walletDiscount.toFixed(2)}`;
    } else {
        walletDiscount = 0;
        if (document.getElementById('walletRow')) {
            document.getElementById('walletRow').style.display = 'none';
        }
    }
    document.getElementById('selectedWalletDiscount').value = walletDiscount;
    recalcTotal();
}

function recalcTotal() {
    const subtotal = currentPrice * currentQty;
    const tax = (subtotal * taxPercent) / 100;
    
    // Check wallet if active
    if (document.getElementById('useWalletCheckbox')?.checked) {
        const netBeforeWallet = Math.max(0, (subtotal + tax) - couponDiscount);
        walletDiscount = Math.min(userWalletBal, netBeforeWallet);
        document.getElementById('summaryWallet').innerText = `- ₹ ${walletDiscount.toFixed(2)}`;
        document.getElementById('selectedWalletDiscount').value = walletDiscount;
    }

    const grandTotal = Math.max(0, (subtotal + tax) - (couponDiscount + walletDiscount));

    document.getElementById('summarySubtotal').innerText = `₹ ${subtotal.toFixed(2)}`;
    document.getElementById('summaryTax').innerText = `₹ ${tax.toFixed(2)}`;
    document.getElementById('summaryGrandTotal').innerText = `₹ ${grandTotal.toFixed(2)}`;
}
</script>

<?php require_once 'includes/footer.php'; ?>
