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
}
else 
{
}
?>
<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	
	<!-- PAGE TITLE HERE -->
	<title><?php echo $set['webname'].' Login Page';?></title>
	
	<!-- FAVICONS ICON -->
	<link rel="shortcut icon" type="image/png" href="<?php echo $set['weblogo'];?>" />
    <link href="css/style.css" rel="stylesheet">

</head>

<body class="vh-100">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-6">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form">
									<div class="text-center mb-3">
										<img src="<?php echo $set['weblogo'];?>" width="120px" alt="">
									</div>
									 <div id="getmsg"></div>
                                    <h4 class="text-center mb-4">Validate Domain</h4>
                                    
                                        <div class="mb-3">
                                            <label class="mb-1"><strong>Enter Envato Purchase Code</strong></label>
                                            <input type="text" class="form-control" id="inputCode" placeholder="Enter Envato Purchase Code" required>
											<input type="hidden" name="type" value="login"/>
                                        </div>
                                        
                                       
                                        <div class="text-center">
                                            <button id="sub_activate" class="btn btn-primary btn-block">Activate Domain</button>
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