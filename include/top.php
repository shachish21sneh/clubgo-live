<?php 
include 'eventconfig.php';
include 'eventmania.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	
	<!-- PAGE TITLE HERE -->
	<title><?php echo $set['webname'].' Dashboard';?></title>
	
	<!-- FAVICONS ICON -->
	<link rel="shortcut icon" type="image/png" href="<?php echo $set['weblogo'];?>" />
	
	<link href="vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
	<link href="vendor/jquery-nice-select/css/nice-select.css" rel="stylesheet">
	<link href="vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
	<link rel="stylesheet" href="vendor/pickadate/themes/default.css">
	 <link href="vendor/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet" type="text/css">
	  <link href="vendor/select2/select2.min.css" rel="stylesheet" type="text/css">
	  <link href="vendor/summernote/summernote-bs4.css" rel="stylesheet">
	  <link href="vendor/summernote/summernote.css" rel="stylesheet">
    <link rel="stylesheet" href="vendor/pickadate/themes/default.date.css">
	    <link href="vendor/clockpicker/css/bootstrap-clockpicker.min.css" rel="stylesheet">
	
	<!-- Style css -->
    <link href="css/style.css" rel="stylesheet">
	
</head>
<body>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="loader">
            <div class="loader--dot"></div>
            <div class="loader--dot"></div>
            <div class="loader--dot"></div>
            <div class="loader--dot"></div>
            <div class="loader--dot"></div>
            <div class="loader--dot"></div>
            <div class="loader--text"></div>
        </div>
    </div>
    <script>
        (function() {
            function hidePreloader() {
                var p = document.getElementById('preloader');
                if (p) p.style.display = 'none';
                var w = document.getElementById('main-wrapper');
                if (w) w.classList.add('show');
            }
            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                setTimeout(hidePreloader, 100);
            } else {
                document.addEventListener('DOMContentLoaded', hidePreloader);
            }
            window.addEventListener('load', hidePreloader);
            setTimeout(hidePreloader, 400);
        })();
    </script>
    <!--*******************
        Preloader end
    ********************-->

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <!--**********************************
            Nav header start
        ***********************************-->
        <div class="nav-header">
            <a href="#" class="brand-logo">
               <img class="logo-abbr" src="<?php echo $set['weblogo'];?>" alt="ClubGo">
				<?php /*<div class="brand-title" style="font-size:18px;"><?php echo $set['webname'];?></div> */ ?> 
				
            </a>
            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>
        <!--**********************************
            Nav header end
        ***********************************-->
		
		
		
		<!--**********************************
            Header start
        ***********************************-->
        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
							
                        </div>
                        <ul class="navbar-nav header-right">
                            
							
							
                            <li class="nav-item dropdown header-profile">
                                <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
									<div class="header-info me-3">
										<span class="fs-16 font-w600 ">Admin</span>
										<small class="text-end fs-14 font-w400">Super Admin</small>
									</div>
                                    <img src="images/profile/pic1.jpg" width="20" alt=""/>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="profile.php" class="dropdown-item ai-icon">
                                        <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                        <span class="ms-2">Profile </span>
                                    </a>
                                    
                                    <a href="logout.php" class="dropdown-item ai-icon">
                                        <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                        <span class="ms-2">Logout </span>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
				</nav>
			</div>
		</div>