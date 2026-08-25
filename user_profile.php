<?php
require_once 'include/eventconfig.php';
require_once 'include/eventmania.php';

$uid = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
if ($uid <= 0) {
    header("Location: index.php");
    exit;
}

$msg = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($name) && !empty($email)) {
        $escName = $event->real_escape_string($name);
        $escEmail = $event->real_escape_string($email);

        $updateSql = "UPDATE tbl_user SET name='$escName', email='$escEmail'";
        if (!empty($password)) {
            $updateSql .= ", password='" . md5($password) . "'";
        }
        $updateSql .= " WHERE id=$uid";

        if ($event->query($updateSql)) {
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $msg = 'Profile updated successfully!';
            $msgType = 'success';
        } else {
            $msg = 'Failed to update profile. Please try again.';
            $msgType = 'danger';
        }
    }
}

$user = $event->query("SELECT * FROM tbl_user WHERE id=$uid")->fetch_assoc();
$pageTitle = 'Profile Settings - ' . ($set['webname'] ?? 'ClubGo');
require_once 'includes/header.php';
?>

<main style="padding: 32px 0 60px;">
    <div class="container" style="max-width:640px;">
        
        <div class="section-header">
            <div class="section-title-wrap">
                <span class="section-tag">Account Settings</span>
                <h1 class="section-title">My Profile</h1>
            </div>
        </div>

        <?php if (!empty($msg)): ?>
            <div style="padding:14px 18px; background:var(--<?php echo $msgType; ?>-subtle); color:var(--<?php echo $msgType; ?>); border-radius:var(--radius-md); font-weight:600; margin-bottom:20px;">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <div class="detail-info-card">
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Mobile Number</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['mobile'] ?? ''); ?>" disabled style="opacity:0.75; background:var(--border);">
                    <small style="color:var(--text-muted); font-size:12px;">Mobile number is linked to your booking identity and cannot be changed directly.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">New Password (leave blank to keep current)</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter new password">
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100" style="margin-top:10px;">
                    Save Profile Changes
                </button>
            </form>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
