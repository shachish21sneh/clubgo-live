<?php 
include 'include/top.php';
include 'include/sidebar.php';
?>
        <div class="content-body">
            <!-- row -->
			<div class="container-fluid">
				<div class="form-head mb-4 d-flex flex-wrap align-items-center">
					<div class="me-auto">
						<h2 class="font-w600 mb-0">Setting  Management</h2>
						
					</div>	
					
				</div>
				<div class="row">
					
					<div class="col-xl-12 col-lg-12">
				
									<div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Edit Setting </h4>
                            </div>
                            <div class="card-body">
                               
                                      <h5 class="h5_set"><i class="fa fa-gear fa-spin"></i>  General  Information</h5>
				<form method="post" enctype="multipart/form-data">
                                       <div class="row">
									    <div class="form-group col-3">
                                            <label><span class="text-danger">*</span> Website Name</label>
                                            <input type="text" class="form-control " placeholder="Enter Store Name" value="<?php echo $set['webname'];?>" name="webname" required="">
											<input type="hidden" name="type" value="edit_setting"/>
										<input type="hidden" name="id" value="1"/>
                                        </div>
										
                                      <div class="form-group col-3" style="margin-bottom: 48px;">
                                            <label><span class="text-danger">*</span> Website Image</label>
                                            <div class="custom-file">
                                                <input type="file" name="weblogo">
                                                <br>
												<br>
												<img src="<?php echo $set['weblogo'];?>" width="60" height="60"/>
                                            </div>
                                        </div>
										
										<div class="form-group col-3">
									<label for="cname">Select Timezone</label>
									<select name="timezone" class="form-control" required>
									<option value="">Select Timezone</option>
									<?php 
								$tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
								$limit =  count($tzlist);
								?>
									<?php 
									for($k=0;$k<$limit;$k++)
									{
									?>
									<option <?php echo $tzlist[$k];?> <?php if($tzlist[$k] == $set['timezone']) {echo 'selected';}?>><?php echo $tzlist[$k];?></option>
									<?php } ?>
									</select>
								</div>
										
										<div class="form-group col-3">
                                            <label><span class="text-danger">*</span> Currency</label>
                                            <input type="text" class="form-control" placeholder="Enter Currency"  value="<?php echo $set['currency'];?>" name="currency" required="">
                                        </div>
										
										
										
										
										
										
	
	<div class="form-group col-12">
										<h5 class="h5_set"><i class="fa fa-signal"></i> Onesignal Information</h5>
										</div>
										<div class="form-group col-6">
                                            <label><span class="text-danger">*</span> User App Onesignal App Id</label>
                                            <input type="text" class="form-control " placeholder="Enter User App Onesignal App Id"  value="<?php echo $set['one_key'];?>" name="one_key" required="">
                                        </div>
										
										<div class="form-group col-6">
                                            <label><span class="text-danger">*</span> User  App Onesignal Rest Api Key</label>
                                            <input type="text" class="form-control " placeholder="Enter User Boy App Onesignal Rest Api Key"  value="<?php echo $set['one_hash'];?>" name="one_hash" required="">
                                        </div>
	
										
										
										<div class="form-group col-12">
										<h5 class="h5_set"><i class="fa fa-user-plus" aria-hidden="true"></i> Refer And Earn Information</h5>
										</div>
										
										<div class="form-group col-6">
                                            <label><span class="text-danger">*</span> Sign Up Credit</label>
                                            <input type="text" class="form-control numberonly" placeholder="Enter Sign Up Credit"  value="<?php echo $set['scredit'];?>" name="scredit" required="">
                                        </div>
										
										<div class="form-group col-6">
                                            <label><span class="text-danger">*</span> Refer Credit</label>
                                            <input type="text" class="form-control numberonly" placeholder="Enter Refer Credit"  value="<?php echo $set['rcredit'];?>" name="rcredit" required="">
                                        </div>
										
										
	
										
										<div class="form-group mb-4" style="margin-top: 40px;">
                                        <button type="submit" class="btn btn-rounded btn-primary"><span class="btn-icon-start text-primary"><i class="fa fa-gear fa-spin"></i>
                                    </span>Edit Setting</button>
                                    </div>
											</div>
                                    </form> 
                               
                            </div>
                        </div>
									
					</div>
						
					
					
					
				</div>
            </div>
			
        </div>
       
	</div>
    
   <?php include 'include/footer.php';?>
   
</body>

</html>