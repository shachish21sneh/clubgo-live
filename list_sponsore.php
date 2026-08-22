<?php 
include 'include/top.php';
include 'include/sidebar.php';
?>
        <div class="content-body">
            <!-- row -->
			<div class="container-fluid">
				<div class="form-head mb-4 d-flex flex-wrap align-items-center">
					<div class="me-auto">
						<h2 class="font-w600 mb-0">Sponsore  Management</h2>
						
					</div>	
					
				</div>
				<div class="row">
					
					<div class="col-xl-12 col-lg-12">
					     <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Sponsore  List</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example3" class="display" style="min-width: 845px">
                                        <thead>
                                            <tr>
											<th>Sr No.</th>
											<th>Event Name</th>
											<th>Sponsore Title</th>
											<th>Sponsore Image</th>
											
												<th>Sponsore Status</th>
												<th>Action</th>
									</tr>
                                        </thead>
                                        <tbody>
                                              <?php 
										$city = $event->query("select * from tbl_sponsore");
										$i=0;
										while($row = $city->fetch_assoc())
										{
											$i = $i + 1;
											?>
											<tr>
                                                <td>
                                                    <?php echo $i; ?>
                                                </td>
												<td>
                                                    <?php $e = $event->query("select * from tbl_event where id=".$row['eid']."")->fetch_assoc(); echo $e['title'];?>
                                                </td>
												<td>
                                                    <?php echo $row['title']; ?>
                                                </td>
                                                
                                                <td class="align-middle">
                                                   <img src="<?php echo $row['img']; ?>" width="100" height="100"/>
                                                </td>
												
                                                
                                               
												<?php if($row['status'] == 1) { ?>
												
                                                <td><span class="badge badge-success">Publish</span></td>
												<?php } else { ?>
												
												<td>
												<span class="badge badge-danger">Unpublish</span></td>
												<?php } ?>
                                                <td>
													<div class="d-flex">
														<a href="add_sponsore.php?id=<?php echo $row['id'];?>" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fa fa-pencil"></i></a>
														<a href="javascript:void(0)" data-id="<?php echo $row['id'];?>" class="btn btn-danger shadow btn-xs sharp del" data-type="sponsore_delete"><i class="fa fa-trash"></i></a>
													</div>												
												</td>												
                                            </tr>
										<?php } ?>
                                            
                                        </tbody>
                                    </table>
                                </div>
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