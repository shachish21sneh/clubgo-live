<?php 
include 'include/top.php';
include 'include/sidebar.php';
?>
        <div class="content-body">
            <!-- row -->
			<div class="container-fluid">
				<div class="form-head mb-4 d-flex flex-wrap align-items-center">
					<div class="me-auto">
						<h2 class="font-w600 mb-0">Page  Management</h2>
						
					</div>	
					
				</div>
				<div class="row">
					
					<div class="col-xl-12 col-lg-12">
					 <?php 
								if(isset($_GET['id']))
								{
									$data = $event->query("select * from tbl_page where id=".$_GET['id']."")->fetch_assoc();
									?>
									<div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Edit Page </h4>
                            </div>
                            <div class="card-body">
                               
                                    <form method="post" enctype="multipart/form-data" onsubmit="return postForm()">
                                    
                                    
									<div class="form-group mb-3">
                                            <label>Page Title</label>
                                            <input type="text" class="form-control input-rounded" value="<?php echo $data['title'];?>" name="ctitle" placeholder="Enter Category Name" name="cname" required="">
                                        <input type="hidden" name="type" value="edit_page"/>
										<input type="hidden" name="id" value="<?php echo $_GET['id'];?>"/>
										</div>
										
										<div class="form-group mb-3">
                                            <label>Page Description</label>
                                           <textarea class="form-control" rows="5" id="cdesc" name="cdesc" style="resize: none;"><?php echo $data['description'];?></textarea>
                                        </div>
                                       
										 <div class="form-group mb-3">
                                            <label>Page Status</label>
                                            <select  name="cstatus" class="form-control input-rounded">
											<option value="">Select Status</option>
											<option value="1" <?php if($data['status'] == 1){echo 'selected';}?>>Publish</option>
                                        <option value="0" <?php if($data['status'] == 0){echo 'selected';}?>>Unpublish</option>
											</select>
                                        </div>
                                        
										
                                    
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-rounded btn-primary"><span class="btn-icon-start text-primary"><i class="fa fa-list"></i>
                                    </span>Edit Page</button>
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
                                <h4 class="card-title">Add Page</h4>
                            </div>
                            <div class="card-body">
                               
                                    <form method="post" enctype="multipart/form-data" onsubmit="return postForm()">
                                    
									
                                    
                                        <div class="form-group mb-3">
                                            <label>Page Title</label>
                                            <input type="text" class="form-control input-rounded" name="ctitle" placeholder="Enter Category Name" name="cname" required="">
											<input type="hidden" name="type" value="add_page"/>
                                        </div>
										
										<div class="form-group mb-3">
                                            <label>Page Description</label>
                                            <textarea class="form-control" rows="5" id="cdesc" name="cdesc" style="resize: none;"></textarea>
                                        </div>
                                       
										 <div class="form-group mb-3">
                                            <label>Page Status</label>
                                            <select  name="cstatus" class="form-control input-rounded" required>
											<option value="">Select Status</option>
											<option value="1">Publish</option>
											<option value="0">UnPublish</option>
											</select>
                                        </div>
                                        
										
                                    
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-rounded btn-primary"><span class="btn-icon-start text-primary"><i class="fa fa-list"></i>
                                    </span>Add Page</button>
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