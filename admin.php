<?php 
include 'include/eventconfig.php';
include 'include/eventmania.php';
if(isset($_SESSION['eventname']))
{
	?>
	<script>
	window.location.href="dashboard.php";
	</script>
	<?php 
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!-- PAGE TITLE -->
	<title><?php echo htmlspecialchars($set['webname'] ?? 'ClubGo'); ?> - Admin Login</title>
	
	<!-- FAVICONS ICON -->
	<link rel="shortcut icon" type="image/png" href="<?php echo htmlspecialchars($set['weblogo'] ?? 'images/website/clubgoimg.webp'); ?>" />
    <link href="css/style.css" rel="stylesheet">
    <style>
        .authincation {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
            min-height: 100vh;
        }
        .authincation-content {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .btn-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%) !important;
            border: none !important;
            padding: 12px 24px;
            font-weight: 600;
            border-radius: 8px;
        }
        .btn-primary:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }
        .back-home {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #64748b;
            font-size: 14px;
            text-decoration: none;
            margin-top: 16px;
        }
        .back-home:hover {
            color: #4f46e5;
        }
    </style>
</head>

<body class="vh-100">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-6 col-lg-5">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form p-4 p-md-5">
									<div class="text-center mb-4">
										<img src="<?php echo htmlspecialchars(get_image_url($set['weblogo'] ?? 'images/website/logo-red.svg')); ?>" alt="ClubGo" style="max-height: 50px;">
									</div>
                                    <h4 class="text-center mb-1 font-w600" style="color: #0f172a;">Admin Portal</h4>
                                    <p class="text-center text-muted mb-4 fs-14">Enter your credentials to access the management dashboard</p>
                                    <form method="post">
                                        <div class="mb-3">
                                            <label class="mb-1 form-label"><strong>User Name</strong></label>
                                            <input type="text" class="form-control" name="username" placeholder="Enter admin username" required>
											<input type="hidden" name="type" value="login"/>
                                        </div>
                                        <div class="mb-4">
                                            <label class="mb-1 form-label"><strong>Password</strong></label>
                                            <input type="password" class="form-control" name="password" placeholder="Enter password" required>
                                        </div>
                                       
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary btn-block w-100">Sign In to Dashboard</button>
                                        </div>
                                    </form>
                                    
                                    <div class="text-center mt-3">
                                        <a href="index.php" class="back-home">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                            Back to ClubGo Website
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <?php 
   include 'include/footer.php';
   ?>
</body>
</html>
