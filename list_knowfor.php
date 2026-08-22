<?php
include 'include/top.php';
include 'include/sidebar.php';
?>
<div class="content-body">
	<!-- row -->
	<div class="container-fluid">
		<div class="form-head mb-4 d-flex flex-wrap align-items-center">
			<div class="me-auto">
				<h2 class="font-w600 mb-0">Know For Management</h2>

			</div>

		</div>
		<div class="row">

			<div class="col-xl-12 col-lg-12">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title">Know For List</h4>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table id="example3" class="display" style="min-width: 845px">
								<thead>
									<tr>
										<th>Sr No.</th>
										<th>Know For Title</th>
										<th>Know For Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$city = $event->query("select * from tbl_known_for");
									$i = 0;
									while ($row = $city->fetch_assoc()) {
										$i = $i + 1;
										?>
										<tr>
											<td>
												<?php echo $i; ?>
											</td>

											<td>
												<?php echo $row['name']; ?>
											</td>




											<?php if ($row['status'] == "ACTIVE") { ?>

												<td><span class="badge badge-success">ACTIVE</span></td>
											<?php } else { ?>

												<td>
													<span class="badge badge-danger">INACTIVE</span>
												</td>
											<?php } ?>
											<td>
												<div class="d-flex">
													<a href="add_knowfor.php?id=<?php echo $row['id']; ?>"
														class="btn btn-primary shadow btn-xs sharp me-1"><i class="fa fa-pencil"></i></a>

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

<?php include 'include/footer.php'; ?>

</body>

</html>