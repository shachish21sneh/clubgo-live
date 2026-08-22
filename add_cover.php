<?php 
include 'include/top.php';
include 'include/sidebar.php';
?>
        <div class="content-body">
            <!-- row -->
			<div class="container-fluid">
				<div class="form-head mb-4 d-flex flex-wrap align-items-center">
					<div class="me-auto">
						<h2 class="font-w600 mb-0">Cover Image Management</h2>
						
					</div>	
					
				</div>
				<div class="row">
					
					<div class="col-xl-12 col-lg-12">
					 <?php 
								if(isset($_GET['id']))
								{
									$data = $event->query("select * from tbl_cover where id=".$_GET['id']."")->fetch_assoc();
									?>
									<div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Edit Cover Image</h4>
                            </div>
                            <div class="card-body">
                               
                                    <form method="post" enctype="multipart/form-data">
                                    
                                    
                                        <div class="form-group mb-3">
                                            <label>Select Event</label>
                                            <select name="eid" class="form-control select2-single" required>
											<option value="" disabled selected>Select Event</option>
											<?php 
											$cat = $event->query("select * from tbl_event");
											while($row = $cat->fetch_assoc())
											{
												?>
												<option value="<?php echo $row['id'];?>" <?php if($data['eid'] == $row['id']){echo 'selected';}?>><?php echo $row['title'];?></option>
												<?php 
											}
											?>
											</select>
                                        </div>
										
                                        <div class="form-group mb-3">
                                            <label>Cover Image Image</label>
                                            <div class="input-group">
                                            <div class="form-file">
                                                <input type="file" name="cat_img" class="form-file-input input-rounded form-control">
												<input type="hidden" name="type" value="edit_cover"/>
										<input type="hidden" name="id" value="<?php echo $_GET['id'];?>"/>
                                            </div>
                                        </div>
                                        </div>
										<div class="form-group">
								<img src="<?php echo $data['img'];?>" width="100px" height="100px"/>
								</div>
										
										 <div class="form-group mb-3">
                                            <label>Cover Image Status</label>
                                            <select name="status" name="status" class="form-control input-rounded" required>
											<option value="">Select Status</option>
											<option value="1" <?php if($data['status'] == 1){echo 'selected';}?>>Publish</option>
											<option value="0" <?php if($data['status'] == 0){echo 'selected';}?>>UnPublish</option>
											</select>
                                        </div>
                                        
										
                                    
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-rounded btn-primary"><span class="btn-icon-start text-primary"><i class="fa fa-list"></i>
                                    </span>Edit Category</button>
                                    </div>
                                </form>
                               
                            </div>
                        </div>
									<?php 
								}
								else 
								{
								?>
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Add Cover Image</h4>
                            </div>
                            <div class="card-body">
                               
                                    <form method="post" enctype="multipart/form-data">
                                    
                                    
                                        <div class="form-group mb-3">
                                            <label>Select Event</label>
                                            <select name="eid" class="form-control select2-single" required>
											<option value="" disabled selected>Select Event</option>
											<?php 
											$cat = $event->query("select * from tbl_event");
											while($row = $cat->fetch_assoc())
											{
												?>
												<option value="<?php echo $row['id'];?>"><?php echo $row['title'];?></option>
												<?php 
											}
											?>
											</select>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Cover Image</label>
                                            <div class="input-group">
                                            <div class="form-file">
                                                <input type="file" name="cat_img" class="form-file-input input-rounded form-control" required>
												<input type="hidden" name="type" value="add_cover"/>
                                            </div>
                                        </div>
                                        </div>
										
										 <div class="form-group mb-3">
                                            <label>Cover Image Status</label>
                                            <select name="status" name="status" class="form-control input-rounded" required>
											<option value="">Select Status</option>
											<option value="1">Publish</option>
											<option value="0">UnPublish</option>
											</select>
                                        </div>
                                        
										
                                    
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-rounded btn-primary"><span class="btn-icon-start text-primary"><i class="fa fa-list"></i>
                                    </span>Add Cover Image Management</button>
                                    </div>
                                </form>
                               
                            </div>
                        </div>
						 <?php } ?>
					</div>
						
					
					
					
				</div>
            </div>
			
        </div>
       
	</div>
    
   <?php include 'include/footer.php';?>
   
</body>

</html>