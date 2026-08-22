<?php 
include 'include/top.php';
include 'include/sidebar.php';
?>
        <div class="content-body">
            <!-- row -->
			<div class="container-fluid">
				<div class="form-head mb-4 d-flex flex-wrap align-items-center">
					<div class="me-auto">
						<h2 class="font-w600 mb-0">Profile  Management</h2>
						
					</div>	
					
				</div>
				<div class="row">
					
					<div class="col-xl-12 col-lg-12">
				
									<div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Edit Profile </h4>
                            </div>
                            <div class="card-body">
                               <?php 
				 $admindata = $event->query("SELECT * FROM `admin`")->fetch_assoc();
				?>
                                      <form method="post" enctype="multipart/form-data">
				
                                       <div class="form-group mb-3">
                                            <label>Username</label>
                                            <input type="text" min="1" step="1"  class="form-control" name="username" required="" value="<?php echo $admindata['username']; ?>">
											<input type="hidden" name="type" value="edit_profile"/>
										<input type="hidden" name="id" value="1"/>
                                        </div>
										 
                                        
										<div class="form-group mb-3">
                                            <label>Password</label>
                                            <input type="text" min="1" step="1"  class="form-control" name="password" value="<?php echo $admindata['password']; ?>" required="">
                                        </div>
										
										
	
										
										<div class="form-group mb-3">
                                                <button type="submit" class="btn btn-rounded btn-primary"><span class="btn-icon-start text-primary"><i class="fa fa-user"></i>
                                    </span>Edit Profile</button>
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