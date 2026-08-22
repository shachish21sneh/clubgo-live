<?php 
include 'include/top.php';
include 'include/sidebar.php';
?>
        <div class="content-body">
            <!-- row -->
			<div class="container-fluid">
				<div class="form-head mb-4 d-flex flex-wrap align-items-center">
					<div class="me-auto">
						<h2 class="font-w600 mb-0">FAQ  Management</h2>
						
					</div>	
					
				</div>
				<div class="row">
					
					<div class="col-xl-12 col-lg-12">
					 <?php 
								if(isset($_GET['id']))
								{
									$data = $event->query("select * from tbl_faq where id=".$_GET['id']."")->fetch_assoc();
									?>
									<div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Edit FAQ </h4>
                            </div>
                            <div class="card-body">
                               
                                    <form method="post" enctype="multipart/form-data">
                                    
                                    
									<div class="form-group mb-3">
                                            <label>FAQ Category</label>
                                            <select  name="fid" class="form-control select2-single input-rounded" required>
											<option value="">Select Category</option>
											<?php 
											$list = $event->query("select * from faq_cat");
											while($row = $list->fetch_assoc())
											{
												?>
												<option value="<?php echo $row['id'];?>" <?php if($data['fid'] == $row['id']) {echo 'selected';}?> ><?php echo $row['title'];?></option>
												<?php 
											}
											?>
											</select>
                                        </div>
										
                                        <div class="form-group mb-3">
                                            <label>FAQ Question</label>
                                            <input type="text" class="form-control input-rounded" name="question" placeholder="Enter FAQ Question" value="<?php echo $data['question'];?>" name="cname" required="">
											  <input type="hidden" name="type" value="edit_faq"/>
										<input type="hidden" name="id" value="<?php echo $_GET['id'];?>"/>
                                        </div>
										
										<div class="form-group mb-3">
                                            <label>FAQ Answer</label>
                                            <input type="text" class="form-control input-rounded" name="answer" placeholder="Enter FAQ Answer" name="cname" required="" value="<?php echo $data['answer'];?>">
                                        </div>
                                        
										
										 <div class="form-group mb-3">
                                            <label>FAQ Status</label>
                                            <select name="status" name="status" class="form-control input-rounded">
											<option value="">Select Status</option>
											 <option value="1" <?php if($data['status'] == 1){echo 'selected';}?>>Publish</option>
                                        <option value="0" <?php if($data['status'] == 0){echo 'selected';}?>>Unpublish</option>
											</select>
                                        </div>
                                        
										
                                    
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-rounded btn-primary"><span class="btn-icon-start text-primary"><i class="fa fa-question-circle"></i>
                                    </span>Edit FAQ</button>
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
                                <h4 class="card-title">Add FAQ</h4>
                            </div>
                            <div class="card-body">
                               
                                    <form method="post" enctype="multipart/form-data">
                                    
									<div class="form-group mb-3">
                                            <label>FAQ Category</label>
                                            <select  name="fid" class="form-control select2-single input-rounded" required>
											<option value="" disabled selected>Select Category</option>
											<?php 
											$list = $event->query("select * from faq_cat");
											while($row = $list->fetch_assoc())
											{
												?>
												<option value="<?php echo $row['id'];?>"><?php echo $row['title'];?></option>
												<?php 
											}
											?>
											</select>
                                        </div>
                                    
                                        <div class="form-group mb-3">
                                            <label>FAQ Question</label>
                                            <input type="text" class="form-control input-rounded" name="question" placeholder="Enter Category Name" name="cname" required="">
											<input type="hidden" name="type" value="add_faq"/>
                                        </div>
										
										<div class="form-group mb-3">
                                            <label>FAQ Answer</label>
                                            <input type="text" class="form-control input-rounded" name="answer" placeholder="Enter Category Name" name="cname" required="">
                                        </div>
                                       
										 <div class="form-group mb-3">
                                            <label>FAQ Status</label>
                                            <select name="status" name="status" class="form-control input-rounded">
											<option value="">Select Status</option>
											<option value="1">Publish</option>
											<option value="0">UnPublish</option>
											</select>
                                        </div>
                                        
										
                                    
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-rounded btn-primary"><span class="btn-icon-start text-primary"><i class="fa fa-question-circle"></i>
                                    </span>Add FAQ</button>
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