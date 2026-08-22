<?php 
include 'include/top.php';
include 'include/sidebar.php';
?>
        <div class="content-body">
            <!-- row -->
			<div class="container-fluid">
				<div class="form-head mb-4 d-flex flex-wrap align-items-center">
					<div class="me-auto">
						<h2 class="font-w600 mb-0">Dashboard</h2>
						
					</div>	
					
				</div>
				<div class="row">
					<div class="col-xl-12">
						<div class="row">
							<div class="col-xl-3 col-xxl-6 col-lg-6 col-sm-6">
						<div class="widget-stat card bg-warning">
							<div class="card-body  p-4">
								<div class="media">
									<span class="me-3">
										<i class="fa fa-list"></i>
									</span>
									<div class="media-body text-white text-end">
										<p class="mb-1">Total Category</p>
										<h3 class="text-white"><?php echo $event->query("select * from tbl_cat")->num_rows;?></h3>
									</div>
								</div>
							</div>
						</div>
                    </div>
					<div class="col-xl-3 col-xxl-6 col-lg-6 col-sm-6">
						<div class="widget-stat card bg-info">
							<div class="card-body  p-4">
								<div class="media">
									<span class="me-3">
										<i class="flaticon-381-speaker"></i>
									</span>
									<div class="media-body text-white text-end">
										<p class="mb-1">Total Events</p>
										<h3 class="text-white"><?php echo $event->query("select * from tbl_event")->num_rows;?></h3>
									</div>
								</div>
							</div>
						</div>
                    </div>
					<div class="col-xl-3 col-xxl-6 col-lg-6 col-sm-6">
						<div class="widget-stat card bg-primary">
							<div class="card-body  p-4">
								<div class="media">
									<span class="me-3">
										<i class="fa fa-list"></i>
									</span>
									<div class="media-body text-white text-end">
										<p class="mb-1">Total Pages</p>
										<h3 class="text-white"><?php echo $event->query("select * from tbl_page")->num_rows;?></h3>
									</div>
								</div>
							</div>
						</div>
                    </div>
					<div class="col-xl-3 col-xxl-6 col-lg-6 col-sm-6">
						<div class="widget-stat card bg-danger">
							<div class="card-body  p-4">
								<div class="media">
									<span class="me-3">
										<i class="fa fa-list"></i>
									</span>
									<div class="media-body text-white text-end">
										<p class="mb-1">Total Faq Category</p>
										<h3 class="text-white"><?php echo $event->query("select * from faq_cat")->num_rows;?></h3>
									</div>
								</div>
							</div>
						</div>
                    </div>
					<div class="col-xl-3 col-xxl-6 col-lg-6 col-sm-6">
						<div class="widget-stat card bg-info">
							<div class="card-body  p-4">
								<div class="media">
									<span class="me-3">
										<i class="fa fa-question-circle"></i>
									</span>
									<div class="media-body text-white text-end">
										<p class="mb-1">Total Faq </p>
										<h3 class="text-white"><?php echo $event->query("select * from tbl_faq")->num_rows;?></h3>
									</div>
								</div>
							</div>
						</div>
                    </div>
					<div class="col-xl-3 col-xxl-6 col-lg-6 col-sm-6">
						<div class="widget-stat card bg-success">
							<div class="card-body  p-4">
								<div class="media">
									<span class="me-3">
										<i class="fa fa-users"></i>
									</span>
									<div class="media-body text-white text-end">
										<p class="mb-1">Total Users </p>
										<h3 class="text-white"><?php echo $event->query("select * from tbl_user")->num_rows;?></h3>
									</div>
								</div>
							</div>
						</div>
                    </div>
					
					<div class="col-xl-3 col-xxl-6 col-lg-6 col-sm-6">
						<div class="widget-stat card bg-warning">
							<div class="card-body  p-4">
								<div class="media">
									<span class="me-3">
										<i class="fa fa-image"></i>
									</span>
									<div class="media-body text-white text-end">
										<p class="mb-1">Total  Gallery Images</p>
										<h3 class="text-white"><?php echo $event->query("select * from tbl_gallery")->num_rows;?></h3>
									</div>
								</div>
							</div>
						</div>
                    </div>
					
					<div class="col-xl-3 col-xxl-6 col-lg-6 col-sm-6">
						<div class="widget-stat card bg-info">
							<div class="card-body  p-4">
								<div class="media">
									<span class="me-3">
										<i class="flaticon-381-gift"></i>
									</span>
									<div class="media-body text-white text-end">
										<p class="mb-1">Total  Offers</p>
										<h3 class="text-white"><?php echo $event->query("select * from tbl_coupon")->num_rows;?></h3>
									</div>
								</div>
							</div>
						</div>
                    </div>
					
					<div class="col-xl-3 col-xxl-6 col-lg-6 col-sm-6">
						<div class="widget-stat card bg-primary">
							<div class="card-body  p-4">
								<div class="media">
									<span class="me-3">
										<i class="fa fa-ticket"></i>
									</span>
									<div class="media-body text-white text-end">
										<p class="mb-1">Total  Tickets</p>
										<h3 class="text-white"><?php echo $event->query("select * from tbl_ticket where ticket_type='Completed'")->num_rows;?></h3>
									</div>
								</div>
							</div>
						</div>
                    </div>
					
					<div class="col-xl-3 col-xxl-6 col-lg-6 col-sm-6">
						<div class="widget-stat card bg-warning">
							<div class="card-body  p-4">
								<div class="media">
									<span class="me-3">
										<i class="fa fa-ticket"></i>
									</span>
									<div class="media-body text-white text-end">
										<p class="mb-1">Total  Sales</p>
										<h3 class="text-white"><?php $sales = $event->query("select sum(`total_amt`) as total_sales from tbl_ticket where ticket_type='Completed'")->fetch_assoc();
										echo empty($sales['total_sales']) ? '0'.$set['currency']: number_format((float)$sales['total_sales'], 2, '.', '').$set['currency'];
										?></h3>
									</div>
								</div>
							</div>
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