<?php 
include 'include/top.php';
include 'include/sidebar.php';
?>
        <div class="content-body">
            <!-- row -->
			<div class="container-fluid">
				<div class="form-head mb-4 d-flex flex-wrap align-items-center">
					<div class="me-auto">
						<h2 class="font-w600 mb-0">Payment  Management</h2>
						
					</div>	
					
				</div>
				<div class="row">
					
					<div class="col-xl-12 col-lg-12">
					 <?php 
								if(isset($_GET['id']))
								{
									$data = $event->query("select * from tbl_payment_list where id=".$_GET['id']."")->fetch_assoc();
									?>
									<div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Edit Payment </h4>
                            </div>
                            <div class="card-body">
                               
                                     <form method="POST" enctype="multipart/form-data">
								
								<div class="form-group mb-3">
                                            <label>Payment Gateway Name</label>
                                            <input type="text" class="form-control " disabled placeholder="Enter Payment Gateway Name" value="<?php echo $data['title'];?>" name="cname" required="">
											 <input type="hidden" name="type" value="edit_payment"/>
										<input type="hidden" name="id" value="<?php echo $_GET['id'];?>"/>
                                        </div>
										
										<div class="form-group mb-3">
                                            <label>Payment Gateway SubTitle</label>
                                            <input type="text" class="form-control" placeholder="Enter Payment Gateway SubTitle" value="<?php echo $data['subtitle'];?>" name="ptitle" required="">
                                        </div>
										
                                        <div class="form-group mb-3">
                                            <label>Payment Gateway Image</label>
											<div class="custom-file">
                                                <input type="file" name="cat_img" class="custom-file-input">
                                               
                                            </div>
											<br>
											<br>
											<img src="<?php echo $data['img']?>" width="100px"/>
                                        </div>
										<div class="form-group mb-3">
                                            <label>Payment Gateway Attributes<?php if($_GET['id'] == 1){echo ' ( 1 for Live Paypal And 0 for Sendbox Paypal. )';}?></label>
                                            <input type="text" class="form-control"  data-role="tagsinput" value="<?php echo $data['attributes'];?>" name="p_attr"  required="">
                                        </div>
										
										 <div class="form-group mb-3">
                                            <label>Payment Gateway Status</label>
                                            <select name="status" class="form-control">
											<option value="1" <?php if($data['status'] == 1){echo 'selected';}?>>Publish</option>
											<option value="0" <?php if($data['status'] == 0){echo 'selected';}?> >UnPublish</option>
											</select>
                                        </div>
										
										<div class="form-group mb-3">
                                            <label>Show On Wallet?</label>
                                            <select name="p_show" class="form-control">
											<option value="1" <?php if($data['p_show'] == 1){echo 'selected';}?>>Yes</option>
											<option value="0" <?php if($data['p_show'] == 0){echo 'selected';}?> >No</option>
											</select>
                                        </div>
							
                                     <div class="form-group">
                                        <button type="submit" class="btn btn-rounded btn-primary"><span class="btn-icon-start text-primary"><i class="flaticon-381-id-card"></i>
                                    </span>Edit Payment Gateway</button>
                                    </div>
                                </form>
                               
                            </div>
                        </div>
									<?php 
								}
								else 
								{
								?>
                      <script>
					  window.location.href="payment-list.php";
					  </script>
						 <?php } ?>
					</div>
						
					
					
					
				</div>
            </div>
			
        </div>
       
	</div>
    
   <?php include 'include/footer.php';?>
   
</body>

</html>