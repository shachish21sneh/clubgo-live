<?php
require_once 'include/eventconfig.php';
require_once 'include/eventmania.php';

$pageTitle = 'ClubGo Wallet & Cashbacks - ' . ($set['webname'] ?? 'ClubGo');
require_once 'includes/header.php';

$uid = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$userWallet = 0;
$userCode = '';
$transactions = [];

if ($uid > 0) {
    $uData = $event->query("SELECT * FROM tbl_user WHERE id=$uid")->fetch_assoc();
    if ($uData) {
        $userWallet = (float)($uData['wallet'] ?? 0);
        $userCode = $uData['code'] ?? '';
    }

    $wRes = $event->query("SELECT * FROM wallet_report WHERE uid=$uid ORDER BY id DESC LIMIT 20");
    if ($wRes) {
        while ($row = $wRes->fetch_assoc()) {
            $transactions[] = $row;
        }
    }
}
?>

<main style="padding: 32px 0 60px;">
    <div class="container" style="max-width:800px;">
        
        <div class="section-header">
            <div class="section-title-wrap">
                <span class="section-tag">Rewards & Cashbacks</span>
                <h1 class="section-title">ClubGo Wallet</h1>
            </div>
        </div>

        <?php if ($uid <= 0): ?>
            <div style="text-align:center; padding:60px 20px; background:var(--surface); border-radius:var(--radius-lg); border:1px solid var(--border);">
                <svg width="48" height="48" fill="none" stroke="var(--primary)" viewBox="0 0 24 24" style="margin:0 auto 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <h2 style="font-size:20px; font-weight:800; margin-bottom:8px;">Sign in to access your wallet</h2>
                <p style="color:var(--text-muted); font-size:14px; margin-bottom:24px;">Earn wallet cashback on every ticket booking and friend referral.</p>
                <button type="button" class="btn btn-primary" onclick="openModal('authModal')">Sign In</button>
            </div>
        <?php else: ?>

            <!-- Wallet Card Balance Banner -->
            <div style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%); border-radius:var(--radius-xl); padding:32px; color:#fff; box-shadow:var(--shadow-lg); margin-bottom:28px; position:relative; overflow:hidden;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <div>
                        <span style="font-size:13px; font-weight:600; color:#c7d2fe; text-transform:uppercase; letter-spacing:0.5px;">Available Balance</span>
                        <div style="font-size:36px; font-weight:800; margin-top:4px;">₹ <?php echo number_format($userWallet, 2); ?></div>
                    </div>
                    <div style="width:48px; height:48px; border-radius:var(--radius-pill); background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center;">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                </div>

                <div style="margin-top:24px; padding-top:20px; border-top:1px solid rgba(255,255,255,0.15); display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:16px;">
                    <div>
                        <span style="font-size:12px; color:#c7d2fe;">Your Unique Referral Code:</span>
                        <div style="font-weight:800; font-size:18px; letter-spacing:1px; color:#fbbf24;"><?php echo htmlspecialchars($userCode ?: 'CLUBGO50'); ?></div>
                    </div>
                    <button type="button" class="btn btn-sm" style="background:rgba(255,255,255,0.2); color:#fff; border:1px solid rgba(255,255,255,0.3); backdrop-filter:blur(8px);" onclick="navigator.clipboard.writeText('<?php echo htmlspecialchars($userCode); ?>'); alert('Referral code copied to clipboard!');">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                        Copy Code
                    </button>
                </div>
            </div>

            <!-- Transaction History -->
            <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg); padding:24px; box-shadow:var(--shadow-sm);">
                <h3 style="font-size:18px; font-weight:800; margin-bottom:18px;">Transaction Activity</h3>

                <?php if (!empty($transactions)): ?>
                    <div style="display:flex; flex-direction:column; gap:12px;">
                        <?php foreach ($transactions as $tr): 
                            $isCredit = (strtolower($tr['status']) === 'credit');
                        ?>
                        <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 16px; background:var(--bg-alt); border-radius:var(--radius-md);">
                            <div style="display:flex; align-items:center; gap:12px;">
                                <div style="width:36px; height:36px; border-radius:var(--radius-pill); background:<?php echo $isCredit ? 'var(--success-subtle)' : 'var(--danger-subtle)'; ?>; color:<?php echo $isCredit ? 'var(--success)' : 'var(--danger)'; ?>; display:flex; align-items:center; justify-content:center;">
                                    <?php if ($isCredit): ?>
                                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                                    <?php else: ?>
                                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div style="font-weight:700; font-size:14px;"><?php echo htmlspecialchars($tr['message']); ?></div>
                                    <div style="font-size:12px; color:var(--text-muted);"><?php echo date('d M Y, h:i A', strtotime($tr['tdate'])); ?></div>
                                </div>
                            </div>
                            <div style="font-weight:800; font-size:16px; color:<?php echo $isCredit ? 'var(--success)' : 'var(--danger)'; ?>;">
                                <?php echo $isCredit ? '+' : '-'; ?> ₹ <?php echo number_format((float)$tr['amt'], 2); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="color:var(--text-muted); font-size:14px; text-align:center; padding:30px 0;">No wallet transactions recorded yet.</p>
                <?php endif; ?>
            </div>

        <?php endif; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
