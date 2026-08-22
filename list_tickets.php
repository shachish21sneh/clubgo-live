<?php 
include 'include/top.php';
include 'include/sidebar.php';
?>
        <div class="content-body">
            <!-- row -->
			<div class="container-fluid">
				<div class="form-head mb-4 d-flex flex-wrap align-items-center">
					<div class="me-auto">
						<h2 class="font-w600 mb-0">Ticket Management</h2>
						
					</div>	
					
				</div>
				<div class="row">
					
					<div class="col-xl-12 col-lg-12">
					     <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Ticket List</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example3" class="display" style="min-width: 845px">
                                        <thead>
                                            <tr>
                                                <th>Sr No.</th>
												<th>Ticket <br>Id.</th>
                                                <th>Event<br> Name</th>
                                                <th>Customer<br> Name</th>
                                                <th>Event <br>Type</th>
                                                <th>Event<br> Price</th>
                                                <th>Event <br>Subtotal</th>
												<th>Event <br>Coupon <br>Amount</th>
												<th>Total <br>Tickets</th>
                                                <th>Tax</th>
												<th>Wallet<br> Amount</th>
												 <th>Total<br> Amount</th>
												<th>Payment?</th>
												<th>Status</th>
                                                <th>Cancel<br>Comment</th>
                                                <th>Review</th>
                                            </tr>
                                        </thead>
                                        <tbody>
										<?php 
										$city = $event->query("select * from tbl_ticket where eid=".$_GET['id']."");
										$i=0;
										while($row = $city->fetch_assoc())
										{
											$i = $i + 1;
											$eve = $event->query("SELECT * FROM `tbl_event` where id=".$row['eid']."")->fetch_assoc();
	$user = $event->query("SELECT * FROM `tbl_user` where id=".$row['uid']."")->fetch_assoc();
	$pdata = $event->query("select * from tbl_payment_list where id=".$row['p_method_id']."")->fetch_assoc();
											?>
                                            <tr>
                                                <td>
                                                    <?php echo $i; ?>
                                                </td>
												<td> <?php echo $row['id']; ?></td>
												<td> <?php echo $eve['title']; ?></td>
                                                <td> <?php echo $user['name']; ?></td>
												<td> <?php echo $row['type']; ?></td>
												<td> <strong><?php echo $row['price'].$set['currency']; ?></strong></td>
												<td> <strong><?php echo $row['subtotal'].$set['currency']; ?></strong></td>
												<td> <strong><?php echo $row['cou_amt'].$set['currency']; ?></strong></td>
												<td> <strong><?php echo $row['total_ticket'].$set['currency']; ?></strong></td>
												<td> <strong><?php echo $row['tax'].$set['currency']; ?></strong></td>
												<td> <strong><?php echo $row['wall_amt'].$set['currency']; ?></strong></td>
												<td> <strong><?php echo $row['total_amt'].$set['currency']; ?></strong></td>
												<td> <?php echo $pdata['title'].'<br><strong>'.$row['transaction_id'].'</strong>'; ?></td>
												
                                              
												
                                                <td><span class="badge badge-success"><?php echo $row['ticket_type']; ?></span></td>
												<td> <?php echo $row['cancle_comment']; ?></td>
												<?php if($row['is_review'] == 0) {
													?>
													<td> <?php echo 'Review Not Done!'; ?></td>
													<?php 
												}else 
													{?>
												<td> <?php echo $row['total_star'].'-'.$row['review_comment']; ?></td>
													<?php } ?>												
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