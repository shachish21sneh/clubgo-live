<?php
require_once 'include/eventconfig.php';
require_once 'include/eventmania.php';

$pid = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$pQuery = $event->query("SELECT * FROM tbl_page WHERE id=$pid AND status=1");
if (!$pQuery || $pQuery->num_rows === 0) {
    $pQuery = $event->query("SELECT * FROM tbl_page WHERE status=1 LIMIT 1");
}

$pageData = $pQuery->fetch_assoc();
$pageTitle = ($pageData['title'] ?? 'Information') . ' - ' . ($set['webname'] ?? 'ClubGo');
require_once 'includes/header.php';
?>

<main style="padding: 36px 0 60px;">
    <div class="container" style="max-width:850px;">
        <div class="detail-info-card">
            <h1 style="font-size:26px; font-weight:800; margin-bottom:20px; border-bottom:1px solid var(--border-light); padding-bottom:16px;">
                <?php echo htmlspecialchars($pageData['title'] ?? 'Information'); ?>
            </h1>

            <div style="font-size:15px; color:var(--text-secondary); line-height:1.8;">
                <?php if (!empty($pageData['description'])): ?>
                    <?php echo $pageData['description']; ?>
                <?php else: ?>
                    <p>For inquiries, support, or partnership opportunities, please contact our helpline at <strong>support@clubgo.in</strong> or call our party concierge.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
