<?php 
require_once 'include/eventconfig.php';
require_once 'include/eventmania.php';

$loginError = '';

if (isset($_SESSION['eventname'])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($username) && !empty($password)) {
        $escUser = $event->real_escape_string($username);
        $escPass = $event->real_escape_string($password);
        
        $adminCheck = $event->query("SELECT * FROM admin WHERE username='$escUser' AND password='$escPass' LIMIT 1");
        if ($adminCheck && $adminCheck->num_rows > 0) {
            $_SESSION['eventname'] = $username;
            header("Location: dashboard.php");
            exit;
        } else {
            $loginError = 'Invalid admin username or password!';
        }
    } else {
        $loginError = 'Please enter both username and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($set['webname'] ?? 'ClubGo'); ?> - Admin Login</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="<?php echo htmlspecialchars(get_image_url($set['weblogo'] ?? 'images/website/clubgoimg.webp')); ?>" />
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 50% 20%, #1e1b4b 0%, #0f172a 60%, #020617 100%);
            padding: 24px;
            color: #0f172a;
        }
        .login-card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 20px;
            padding: 40px 36px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.1);
            animation: fadeIn 0.3s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .logo-wrap {
            text-align: center;
            margin-bottom: 24px;
        }
        .logo-wrap img {
            max-height: 52px;
            width: auto;
            object-fit: contain;
        }
        .card-header-text {
            text-align: center;
            margin-bottom: 28px;
        }
        .card-title {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }
        .card-subtitle {
            font-size: 14px;
            color: #64748b;
        }
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 8px;
        }
        .form-input {
            width: 100%;
            height: 50px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 0 16px;
            font-size: 15px;
            color: #0f172a;
            font-weight: 500;
            transition: all 0.2s ease;
            outline: none;
        }
        .form-input::placeholder {
            color: #94a3b8;
        }
        .form-input:focus {
            background: #ffffff;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }
        .btn-submit {
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
            margin-top: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-submit:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.45);
        }
        .btn-submit:active {
            transform: translateY(0);
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            margin-top: 24px;
            transition: color 0.15s ease;
        }
        .back-link:hover {
            color: #4f46e5;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <!-- Brand Logo -->
        <div class="logo-wrap">
            <img src="<?php echo htmlspecialchars(get_image_url($set['weblogo'] ?? 'images/website/logo-red.svg')); ?>" alt="ClubGo">
        </div>

        <div class="card-header-text">
            <h1 class="card-title">Admin Management Portal</h1>
            <p class="card-subtitle">Sign in to access your event dashboard</p>
        </div>

        <?php if (!empty($loginError)): ?>
            <div class="alert-error">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span><?php echo htmlspecialchars($loginError); ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="admin.php">
            <div class="form-group">
                <label class="form-label" for="usernameInput">Username</label>
                <input type="text" id="usernameInput" name="username" class="form-input" placeholder="Enter admin username" required autofocus autocomplete="username">
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label class="form-label" for="passwordInput">Password</label>
                <input type="password" id="passwordInput" name="password" class="form-input" placeholder="Enter admin password" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn-submit">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                Sign In to Dashboard
            </button>
        </form>

        <div style="text-align: center;">
            <a href="index.php" class="back-link">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Website
            </a>
        </div>
    </div>

</body>
</html>
