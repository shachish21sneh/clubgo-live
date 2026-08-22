<?php 
include 'include/top.php';
include 'include/sidebar.php';
?>
        <div class="content-body">
            <!-- row -->
			<div class="container-fluid">
				<div class="form-head mb-4 d-flex flex-wrap align-items-center">
					<div class="me-auto">
						<h2 class="font-w600 mb-0">Coupon Code Management</h2>
						
					</div>	
					
				</div>
				<div class="row">
					
					<div class="col-xl-12 col-lg-12">
					 <?php 
								if(isset($_GET['id']))
								{
									$sels = $event->query("select * from tbl_coupon where id=".$_GET['id']."")->fetch_assoc();
									?>
									<div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Edit Coupon Code</h4>
                            </div>
                            <div class="card-body">
                               
                                   <form method="post" enctype="multipart/form-data" onsubmit="return postForm()">
                                    
                                    
                                        
                                        <div class="row">
<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">

								<div class="form-group mb-3">
									<label>Coupon Image</label>
									
									 <div class="custom-file">
                                                <input type="file" name="f_up" class="custom-file-input" >
                                                
												    <input type="hidden" name="type" value="edit_coupon"/>
										<input type="hidden" name="id" value="<?php echo $_GET['id'];?>"/>
										<br>
									<img src="<?php echo $sels['c_img'];?>" width="50" height="50"/>
                                            </div>
								</div>
								</div>
								
								<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">

								<div class="form-group mb-3">
									<label>Coupon Expiry Date</label>
									<input type="date" name="cdate" value="<?php echo $sels['cdate'];?>" class="form-control" id="projectinput8" required>
								</div>
								</div>
								
								
								
								<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
								<div class="form-group mb-3">
								
									<label for="cname">Coupon Code </label>
									<div class="row">
								<div class="col-md-8 col-lg-8 col-xs-12 col-sm-12">
									<input type="text" id="ccode" value="<?php echo $sels['c_title'];?>" class="form-control" onkeypress="return isNumberKey(event)" 
    maxlength="8" name="ccode" required  oninput="this.value = this.value.toUpperCase()">
									</div>
									
								<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
									<button id="gen_code" class="btn btn-success"><i class="fa fa-refresh" aria-hidden="true"></i></button>
									</div>
									</div>
								</div>
								</div>
								
								
                             
							
							 <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
								<div class="form-group mb-3">
									<label for="cname">Coupon title </label>
									<input type="text"  class="form-control"  value="<?php echo $sels['ctitle'];?>" name="ctitle" required >
								</div>
							</div>

<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
								<div class="form-group mb-3">
									<label for="cname">Coupon subtitle </label>
									<input type="text"  class="form-control"  value="<?php echo $sels['subtitle'];?>" name="subtitle" required >
								</div>
							</div>
  	


								
 <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
 <div class="form-group mb-3">
									<label for="cname">Coupon Status </label>
									<select name="cstatus" class="form-control" required>
									<option value="">Select Coupon Status</option>
									<option value="1" <?php if($sels['status'] == 1){echo 'selected';}?>>Publish</option>
									<option value="0" <?php if($sels['status'] == 0){echo 'selected';}?>>Unpublish</option>
									
									</select>
								</div>
 <div class="form-group mb-3">
									<label>Coupon Min Order Amount</label>
									<input type="number" id="cname"  value="<?php echo $sels['min_amt'];?>" class="form-control"  name="minamt" step="1"
                  onkeypress="return event.charCode >= 48 && event.charCode <= 57" required >
								</div>
								<div class="form-group mb-3">
									<label for="cname">Coupon Value</label>
									<input type="number" id="cname" value="<?php echo $sels['c_value'];?>" class="form-control"  name="cvalue" step="1"
                  onkeypress="return event.charCode >= 48 && event.charCode <= 57" required >
								</div>
							</div>

									   
<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
								<div class="form-group mb-3">
									<label for="cname">Coupon Description </label>
									<textarea class="form-control" rows="5" id="cdesc" name="cdesc" style="resize: none;" required><?php echo $sels['c_desc'];?></textarea>
								</div>
							</div>							
								
							</div>
                                        
										
                                    
                                   <div class="form-group">
                                        <button type="submit" class="btn btn-rounded btn-primary"><span class="btn-icon-start text-primary"><i class="flaticon-381-gift"></i>
                                    </span>Edit Coupon Code</button>
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
                                <h4 class="card-title">Add Coupon</h4>
                            </div>
                            <div class="card-body">
                               
                                     <form method="post" enctype="multipart/form-data" onsubmit="return postForm()">
                                    
                                    
                                        
                                        <div class="row">
<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">

								<div class="form-group mb-3">
									<label>Coupon Image</label>
									
									 <div class="custom-file">
                                                <input type="file" name="f_up" class="custom-file-input" required>
                                                
												<input type="hidden" name="type" value="add_coupon"/>
                                            </div>
								</div>
								</div>
								
								<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">

								<div class="form-group mb-3">
									<label>Coupon Expiry Date</label>
									<input type="date" name="cdate" class="form-control" id="projectinput8" required>
								</div>
								</div>
								
								
								
								<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
								<div class="form-group mb-3">
								
									<label for="cname">Coupon Code </label>
									<div class="row">
								<div class="col-md-8 col-lg-8 col-xs-12 col-sm-12">
									<input type="text" id="ccode" class="form-control" onkeypress="return isNumberKey(event)" 
    maxlength="8" name="ccode" required  oninput="this.value = this.value.toUpperCase()">
									</div>
									
								<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
									<button id="gen_code" class="btn btn-success"><i class="fa fa-refresh" aria-hidden="true"></i></button>
									</div>
									</div>
								</div>
								</div>
								
								
                             
							
							 <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
								<div class="form-group mb-3">
									<label for="cname">Coupon title </label>
									<input type="text"  class="form-control"  name="ctitle" required >
								</div>
							</div>

<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
								<div class="form-group mb-3">
									<label for="cname">Coupon subtitle </label>
									<input type="text"  class="form-control"   name="subtitle" required >
								</div>
							</div>
  	


								
 <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
 <div class="form-group mb-3">
									<label for="cname">Coupon Status </label>
									<select name="cstatus" class="form-control" required>
									<option value="">Select Coupon Status</option>
									<option value="1">Publish</option>
									<option value="0">Unpublish</option>
									
									</select>
								</div>
 <div class="form-group mb-3">
									<label>Coupon Min Order Amount</label>
									<input type="number" id="cname"  class="form-control"  name="minamt" step="1"
                  onkeypress="return event.charCode >= 48 && event.charCode <= 57" required >
								</div>
								
								<div class="form-group mb-3">
									<label for="cname">Coupon Value</label>
									<input type="number" id="cname" class="form-control"  name="cvalue" step="1"
                  onkeypress="return event.charCode >= 48 && event.charCode <= 57" required >
								</div>
							</div>
 
									   
<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
								<div class="form-group mb-3">
									<label for="cname">Coupon Description </label>
									<textarea class="form-control" rows="5" id="cdesc" name="cdesc" style="resize: none;" required></textarea>
								</div>
							</div>							
								
							</div>
                                        
										
                                    
                                   <div class="form-group">
                                        <button type="submit" class="btn btn-rounded btn-primary"><span class="btn-icon-start text-primary"><i class="flaticon-381-gift"></i>
                                    </span>Add Coupon Code</button>
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