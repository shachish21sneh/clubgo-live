<?php
include 'include/top.php';
include 'include/sidebar.php';
?>
<div class="content-body">
	<!-- row -->
	<div class="container-fluid">
		<div class="form-head mb-4 d-flex flex-wrap align-items-center">
			<div class="me-auto">
				<h2 class="font-w600 mb-0">Event Management</h2>

			</div>

		</div>
		<div class="row">

			<div class="col-xl-12 col-lg-12">
				<?php
				if (isset($_GET['id'])) {
					$data = $event->query("select * from tbl_event where id=" . $_GET['id'] . "")->fetch_assoc();
					?>
					<div class="card">
						<div class="card-header">
							<h4 class="card-title">Edit Event</h4>
						</div>
						<div class="card-body">

							<form method="post" enctype="multipart/form-data">
								<div class="row">
									<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">

										<div class="form-group mb-3">
											<label>Event Name</label>
											<input type="text" class="form-control" name="title"
												value="<?php echo $data['title']; ?>" placeholder="Enter Event Name"
												required="">
											<input type="hidden" name="type" value="edit_event" />
											<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
										</div>
									</div>
									<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
										<div class="form-group mb-3">
											<label>Event Image</label>
											<div class="input-group">

												<input type="file" name="cat_img">

												<img src="<?php echo $data['img']; ?>" width="100" height="100" />


											</div>
										</div>
									</div>
									<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
										<div class="form-group mb-3">
											<label>Event Cover Image</label>
											<div class="input-group">

												<input type="file" name="cover_img">

												<img src="<?php echo $data['cover_img']; ?>" width="100" height="100" />

											</div>
										</div>
									</div>
									<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
										<div class="form-group mb-3">
											<label>Event Start Date</label>
											<input type="date" name="sdate" class="form-control"
												value="<?php echo $data['sdate']; ?>" placeholder="Select Date" required>
										</div>
									</div>
									<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
										<div class="form-group mb-3">
											<label>Event End Date</label>
											<input type="date" name="edate" class="form-control"
												value="<?php echo $data['edate']; ?>" placeholder="Select Date" required>
										</div>
									</div>
									<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
										<div class="form-group mb-3">
											<label class="form-label">Event Start Time</label>
											<div class="input-group">
												<input type="time" name="stime" class="form-control"
													value="<?php echo $data['stime']; ?>" required>
											</div>
										</div>
									</div>
									<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
										<div class="form-group mb-3">
											<label class="form-label">Event End Time</label>
											<div class="input-group">
												<input type="time" name="etime" class="form-control"
													value="<?php echo $data['etime']; ?>" required>
											</div>
										</div>
									</div>
									<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
										<div class="form-group mb-3">
											<label class="form-label">Event Latitude</label>
											<input type="text" class="form-control " name="latitude"
												value="<?php echo $data['latitude']; ?>" placeholder="Enter Latitude"
												required="">
										</div>
									</div>
									<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
										<div class="form-group mb-3">
											<label class="form-label">Event Longtitude</label>
											<input type="text" class="form-control " name="longtitude"
												value="<?php echo $data['longtitude']; ?>" placeholder="Enter Longtitude"
												required="">
										</div>
									</div>

									<div class="row">

										<div class="col-md-6">
											<div class="form-group mb-3">
												<label>Event Venue</label>
												<select id="venueDropdown" name="loc_id"
													class="form-control select2-single">
													<option value="">Select Venue</option>
													<?php
													$venues = $event->query("SELECT * FROM tbl_veneu"); // Ensure correct table name
													while ($venue = $venues->fetch_assoc()) {
														$isSelected = ($venue['loc_id'] == $data["loc_id"]) ? 'selected' : '';
														echo '<option value="' . $venue['loc_id'] . '" 
                                                          data-address="' . $venue['loc_open_close'] . '" 
                                                            data-place-name="' . $venue['loc_title'] . '" ' . $isSelected . '>' .
															$venue['loc_title'] . '</option>';
													}
													?>
												</select>
											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group mb-3">
												<label>Select Similer event</label>
												<div class="dropdown">
													<button class="btn btn-outline-primary dropdown-toggle w-100"
														type="button" data-bs-toggle="dropdown">Select Similer
														event</button>
													<ul class="dropdown-menu p-3"
														style="max-height: 200px; overflow-y: auto;">
														<?php
														$selected_event = explode(',', $data['similer_event']);
														$events = $event->query("SELECT * FROM `tbl_event` WHERE event_status='Pending'");
														while ($row = $events->fetch_assoc()) {
															$checked = in_array($row['id'], $selected_event) ? 'checked' : '';
															echo "<li><label class='dropdown-item'><input type='checkbox' name='smiler_event_id[]' value='{$row['id']}' $checked> " . htmlspecialchars($row['title']) . "</label></li>";
														}
														?>

													</ul>
												</div>
											</div>
										</div>
									</div>




									<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">

										<div class="form-group mb-3">
											<label class="form-label">Event Place Name</label>
											<input type="text" class="form-control " name="pname"
												value="<?php echo $data['place_name']; ?>" placeholder="Enter Place Name"
												required="">
										</div>

										<div class="form-group mb-3">
											<label class="form-label">Event Full Address</label>
											<textarea class="form-control" rows="7" name="address" style="resize:none;"
												required><?php echo $data['address']; ?></textarea>
										</div>
									</div>
									<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
										<div class="form-group mb-3">
											<label>Event Status</label>
											<select name="status" class="form-control " required>
												<option value="">Select Status</option>
												<option value="1" <?php if ($data['status'] == 1) {
													echo 'selected';
												} ?>>Publish
												</option>
												<option value="0" <?php if ($data['status'] == 0) {
													echo 'selected';
												} ?>>Unpublish
												</option>
											</select>
										</div>
										
                                                  <div class="form-group mb-3">
                                                <label>Event Category</label>
                                                <div class="dropdown">
                                                    <button class="btn btn-outline-primary dropdown-toggle w-100"
                                                        type="button" data-bs-toggle="dropdown">Select Category</button>
                                                    <ul class="dropdown-menu p-3"
                                                        style="max-height: 200px; overflow-y: auto;">
                                                        <?php
                                                        $selected_cat = explode(',', $data['cid']);
                                                        $cat = $event->query("SELECT * FROM `tbl_cat`");
                                                        while ($row = $cat->fetch_assoc()) {
                                                            $checked = in_array($row['id'], $selected_cat) ? 'checked' : '';
                                                            echo "<li><label class='dropdown-item'><input type='checkbox' name='cid[]' value='{$row['id']}' $checked> " . htmlspecialchars($row['title']) . "</label></li>";
                                                        }
                                                        ?>

                                                    </ul>
                                                </div>
                                            </div>
										

									</div>
									</div>
									<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
										<div class="form-group mb-3">
											<label>Event Description</label>
											<textarea class="form-control" rows="5" id="cdesc" name="cdesc"
												style="resize: none;"
												required><?php echo $data['description']; ?></textarea>
										</div>
									</div>

									<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
										<div class="form-group mb-3">
											<label>Event Disclaimer</label>
											<textarea class="form-control" rows="5" id="disclaimer" name="disclaimer"
												style="resize: none;" required><?php echo $data['disclaimer']; ?></textarea>
										</div>
									</div>

									<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
										<div class="form-group mb-3">
											<label>Dress Code</label>
											<div class="input-group">

												<input type="file" name="dress_img">

												<img src="<?php echo $data['dress_img']; ?>" width="300" height="100" />


											</div>
										</div>
									</div>
									<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
										<div class="form-group mb-3">
											<label>Floor Plan</label>
											<div class="input-group">

												<input type="file" name="floor_img">

												<img src="<?php echo $data['floor_img']; ?>" width="300" height="100" />

											</div>
										</div>
									</div>



									


									<!-- Price Information -->
									<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
										<div class="form-group mb-3">
											<h5>
												<h5>
													<h5>Price Information</h5>
													<label>Status <span style="color: red;">*</span></label>
													<div>
														<input type="radio" id="price_free" name="price_status" value="F"
															<?php echo (!isset($data['payment_type']) || $data['payment_type'] == 'F') ? 'checked' : ''; ?>
															onclick="toggleDiv(true)" required>
														<label for="price_free">Free</label>

														<input type="radio" id="price_paid" name="price_status" value="P"
															<?php echo (!isset($data['payment_type']) || $data['payment_type'] == 'P') ? 'checked' : ''; ?>
															onclick="toggleDiv(true)">
														<label for="price_paid">Paid</label>
													</div>
										</div>
									</div>

									<!-- Non Booking -->
									<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
										<div class="form-group mb-3">
											<h5>
												<h5>
													<h5>Non Booking</h5>
													<label>Status <span style="color: red;">*</span></label>
													<div>
														<input type="radio" id="nonbooking_yes" name="non_booking"
															value="TRUE" <?php echo (!isset($data['non_booking']) || $data['non_booking'] == 'TRUE') ? 'checked' : ''; ?>
															onclick="toggleDiv(true)" onclick="toggleDiv(true)" required>
														<label for="nonbooking_yes">Yes</label>

														<input type="radio" id="nonbooking_no" name="non_booking" <?php echo (!isset($data['non_booking']) || $data['non_booking'] == 'FALSE') ? 'checked' : ''; ?> onclick="toggleDiv(true)"
															onclick="toggleDiv(true)" value="FALSE"
															onclick="toggleDiv(false)">
														<label for="nonbooking_no">No</label>
													</div>
										</div>
									</div>

									
									<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
										<div class="form-group mb-3">
											<label>Menu Description</label>
											<textarea class="form-control" rows="5" id="menudesc" name="menudesc"
												style="resize: none;"
												required><?php  if(isset($data['menu_description'])){
												echo $data['menu_description'];
												}else{
													echo "";
												} ?></textarea>
										</div>
									</div>



									<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
									<div class="form-group mb-3">
										<label>Term And Condition</label>
										<textarea class="form-control" rows="5" id="terms" name="terms"
												style="resize: none;"
												required><?php  if(isset($data['term_and_condition'])){
												echo $data['term_and_condition'];
												}else{
													echo "";
												} ?></textarea>
									</div>
								</div>

										<!----custom headline --->

										<h5>Custom Headline</h5>

										<div class="row align-items-end">  <!-- Added align-items-end for vertical alignment -->
										<!-- Title Input -->
										<div class="col-md-5 col-lg-5 col-xs-12 col-sm-12">
										<div class="form-group mb-3">
										<label class="form-label">Headline Title</label>
										<input type="text" class="form-control" name="headline_title" id="headline_title"
										placeholder="Enter Title">
										</div>
										</div>

										<!-- Description Input -->
										<div class="col-md-5 col-lg-5 col-xs-12 col-sm-12">
										<div class="form-group mb-3">
										<label class="form-label">Headline Description</label>
										<input type="text" class="form-control" name="headline_desc" id="headline_desc"
										placeholder="Enter Description">
										</div>
										</div>

										<!-- Add Button (now smaller) -->
										<div class="col-md-2 col-lg-2 col-xs-12 col-sm-12">
										<div class="form-group mb-3">
										<button type="button" class="btn btn-sm btn-secondary" onclick="addHeadline()">
										Add Headline
										</button>
										</div>
										</div>
										</div>
										

										<h4 id="headlineListHeading" style="display: none;">Headline List</h4>
										<div id="headlineList"></div>

										<!-- Hidden field to store headline list as JSON -->
										<input type="hidden" name="headline_list_json" id="headline_list_json" value="<?php 
										$savedJson= $data['headline_json'];
										echo htmlspecialchars($savedJson); ?>">


										<!----end headline------>


									<!-- Div to Show/Hide -->
									<div class="form-group mb-3" id="extraFields" style="display: none;">
										<div class="form-group">
											<label>Number</label>
											<input type="text" class="form-control clear-on-hide" name="user_number"
												value="<?php echo $data['user_number']; ?>" placeholder="Number">
										</div>
										</br>
										<div class="form-group">
											<label>Link</label>
											<input type="text" class="form-control clear-on-hide" name="user_link"
												value="<?php echo $data['user_link']; ?>" placeholder="Link">
										</div>
									</div>



									<div class="form-group">
										<button type="submit" class="btn btn-rounded btn-primary"><span
												class="btn-icon-start text-primary"><i class="flaticon-381-speaker"></i>
											</span>Edit Event</button>
									</div>
								</div>
							</form>
						</div>


					</div>
				</div>
				<?php
				} else {
					?>
				<div class="card">
					<div class="card-header">
						<h4 class="card-title">Add Event</h4>
					</div>
					<div class="card-body">

						<form method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">

									<div class="form-group mb-3">
										<label>Event Name</label>
										<input type="text" class="form-control" name="title" placeholder="Enter Event Name"
											required="">
										<input type="hidden" name="type" value="add_events" />
									</div>
								</div>
								<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
									<div class="form-group mb-3">
										<label>Event Image</label>
										<div class="input-group">

											<input type="file" name="cat_img" required>

										</div>
									</div>
								</div>
								<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
									<div class="form-group mb-3">
										<label>Event Cover Image</label>
										<div class="input-group">

											<input type="file" name="cover_img" required>

										</div>
									</div>
								</div>
								<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
									<div class="form-group mb-3">
										<label>Event Start Date</label>
										<input type="date" name="sdate" class="form-control" placeholder="Select Date"
											required>
									</div>
								</div>
								<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
									<div class="form-group mb-3">
										<label>Event End Date</label>
										<input type="date" name="edate" class="form-control" placeholder="Select Date"
											required>
									</div>
								</div>
								<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
									<div class="form-group mb-3">
										<label class="form-label">Event Start Time</label>
										<div class="input-group">
											<input type="time" name="stime" class="form-control" required>
										</div>
									</div>
								</div>
								<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
									<div class="form-group mb-3">
										<label class="form-label">Event End Time</label>
										<div class="input-group">
											<input type="time" name="etime" class="form-control" required>
										</div>
									</div>
								</div>
								<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
									<div class="form-group mb-3">
										<label class="form-label">Event Latitude</label>
										<input type="text" class="form-control " name="latitude"
											placeholder="Enter Latitude" required="">
									</div>
								</div>
								<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
									<div class="form-group mb-3">
										<label class="form-label">Event Longtitude</label>
										<input type="text" class="form-control " name="longtitude"
											placeholder="Enter Longtitude" required="">
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="form-group mb-3">
											<label>Event Venue</label>
											<select id="venueDropdown" name="loc_id" class="form-control select2-single">
												<option value="" selected>Select Venue</option>
												<?php
												$venues = $event->query("SELECT * FROM tbl_veneu"); // Ensure correct table name
												while ($venue = $venues->fetch_assoc()) {
													echo '<option value="' . $venue['loc_id'] . '" 
                                               data-address="' . $venue['loc_open_close'] . '" 
                                                 data-place-name="' . $venue['loc_title'] . '">' .
														$venue['loc_title'] . '</option>'; // Make sure to display venue name
												}
												?>
											</select>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group mb-3">
											<label>Select Similer Event</label>
											<div class="dropdown">
												<button class="btn btn-outline-primary dropdown-toggle w-100" type="button"
													data-bs-toggle="dropdown">
													Select Event
												</button>
												<ul class="dropdown-menu p-3" style="max-height: 200px; overflow-y: auto;">
													<?php
													$cat = $event->query("SELECT * FROM `tbl_event` WHERE event_status='Pending'");
													while ($row = $cat->fetch_assoc()) { ?>
														<li>
															<label class="dropdown-item">
																<input type="checkbox" name="smiler_event_id[]"
																	value="<?= $row['id']; ?>">
																<?= htmlspecialchars($row['title']); ?>
															</label>
														</li>
													<?php } ?>
												</ul>
											</div>
										</div>
									</div>
								</div>

								<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">

									<div class="form-group mb-3">
										<label class="form-label">Event Place Name</label>
										<input type="text" class="form-control " name="pname" placeholder="Enter Place Name"
											required="">
									</div>

									<div class="form-group mb-3">
										<label class="form-label">Event Full Address</label>
										<textarea class="form-control" rows="7" name="address" style="resize:none;"
											required></textarea>
									</div>
								</div>

								<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
									<div class="form-group mb-3">
										<label>Event Status</label>
										<select name="status" class="form-control " required>
											<option value="">Select Status</option>
											<option value="1">Publish</option>
											<option value="0">UnPublish</option>
										</select>
									</div>
									
										<div class="form-group mb-3">
											<label>Event Category</label>
											<div class="dropdown">
												<button class="btn btn-outline-primary dropdown-toggle w-100" type="button"
													data-bs-toggle="dropdown">
													Select Category
												</button>
												<ul class="dropdown-menu p-3" style="max-height: 200px; overflow-y: auto;">
													<?php
													$cat = $event->query("SELECT * FROM `tbl_cat`");
													while ($row = $cat->fetch_assoc()) { ?>
														<li>
															<label class="dropdown-item">
																<input type="checkbox" name="cid[]"
																	value="<?= $row['id']; ?>">
																<?= htmlspecialchars($row['title']); ?>
															</label>
														</li>
													<?php } ?>
												</ul>
											</div>
										</div>
									

								
								</div>
								<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
									<div class="form-group mb-3">
										<label>Event Description</label>
										<textarea class="form-control" rows="5" id="cdesc" name="cdesc"
											style="resize: none;" required></textarea>
									</div>
								</div>

								<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
									<div class="form-group mb-3">
										<label>Event Disclaimer</label>
										<textarea class="form-control" rows="5" id="disclaimer" name="disclaimer"
											style="resize: none;" required></textarea>
									</div>
								</div>

								<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
									<div class="form-group mb-3">
										<label>Dress Code</label>
										<div class="input-group">

											<input type="file" name="dress_img">

										</div>
									</div>
								</div>

								<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
									<div class="form-group mb-3">
										<label>Floor Plan</label>
										<div class="input-group">

											<input type="file" name="floor_img">

										</div>
									</div>
								</div>


								<!-- Price Information -->
								<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
									<div class="form-group mb-3">
										<h5>
											<h5>
												<h5>Price Information</h5>
												<label>Status <span style="color: red;">*</span></label>
												<div>
													<input type="radio" id="price_free" name="price_status" checked
														value="F" required>
													<label for="price_free">Free</label>

													<input type="radio" id="price_paid" name="price_status" value="P">
													<label for="price_paid">Paid</label>
												</div>
									</div>
								</div>

								<!-- Non Booking -->
								<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
									<div class="form-group mb-3">
										<h5>
											<h5>
												<h5>Non Booking</h5>
												<label>Status <span style="color: red;">*</span></label>
												<div>
													<input type="radio" id="nonbooking_yes" name="non_booking" value="TRUE"
														onclick="toggleDiv(true)" required>
													<label for="nonbooking_yes">Yes</label>

													<input type="radio" id="nonbooking_no" name="non_booking" checked
														value="FALSE" onclick="toggleDiv(false)">
													<label for="nonbooking_no">No</label>
												</div>
									</div>
								</div>

								<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
									<div class="form-group mb-3">
										<label>Menu Description</label>
										<textarea class="form-control" rows="5" id="menudesc" name="menudesc"
											style="resize: none;" required></textarea>
									</div>
								</div>



								<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
									<div class="form-group mb-3">
										<label>Term And Condition	</label>
										<textarea class="form-control" rows="5" id="terms" name="terms"
											style="resize: none;" required></textarea>
									</div>
								</div>


                               <!----custom headline --->

							   <h5>Custom Headline</h5>

							   <div class="row align-items-end">  <!-- Added align-items-end for vertical alignment -->
  <!-- Title Input -->
								<div class="col-md-5 col-lg-5 col-xs-12 col-sm-12">
									<div class="form-group mb-3">
									<label class="form-label">Headline Title</label>
									<input type="text" class="form-control" name="headline_title" id="headline_title"
										placeholder="Enter Title">
									</div>
								</div>

								<!-- Description Input -->
								<div class="col-md-5 col-lg-5 col-xs-12 col-sm-12">
									<div class="form-group mb-3">
									<label class="form-label">Headline Description</label>
									<input type="text" class="form-control" name="headline_desc" id="headline_desc"
										placeholder="Enter Description" >
									</div>
								</div>

								<!-- Add Button (now smaller) -->
								<div class="col-md-2 col-lg-2 col-xs-12 col-sm-12">
									<div class="form-group mb-3">
									<button type="button" class="btn btn-sm btn-secondary" onclick="addHeadline()">
										Add Headline
									</button>
									</div>
								</div>
								</div>
								

								<h4 id="headlineListHeading" style="display: none;">Headline List</h4>
								<div id="headlineList"></div>

								<!-- Hidden field to store headline list as JSON -->
								<input type="hidden" name="headline_list_json" id="headline_list_json">


							   <!----end headline------>
                                


								<!-- Div to Show/Hide -->
								<div class="form-group mb-3" id="extraFields" style="display: none;">
									<div class="form-group">
										<label>Number</label>
										<input type="text" class="form-control clear-on-hide" name="user_number"
											placeholder="Number">
									</div>
									</br>
									<div class="form-group">
										<label>Link</label>
										<input type="text" class="form-control clear-on-hide" name="user_link"
											placeholder="Link">
									</div>
								</div>


								<div class="form-group mb-3">
									<button type="submit" class="btn btn-rounded btn-primary"><span
											class="btn-icon-start text-primary"><i class="flaticon-381-speaker"></i>
										</span>Add Event</button>
								</div>
							</div>
						</form>
					</div>

				</div>

			<?php } ?>
		</div>
	</div>

</div>
</div>

<script>
	var isEdit = <?php echo isset($_GET['id']) ? 'true' : 'false'; ?>;
	function toggleDiv(show) {
		document.getElementById("extraFields").style.display = show ? "block" : "none";
		if (!show && !isEdit) {
			document.querySelectorAll(".clear-on-hide").forEach(input => input.value = "");
		}
	}

	// Auto-show when editing (if Yes is selected)
	window.onload = function () {
		if (document.getElementById("nonbooking_yes").checked) {
			toggleDiv(true);
		} else if (document.getElementById("nonbooking_no").checked) {
			toggleDiv(false);
		}
	};
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
	$(document).ready(function () {
		$("#venueDropdown").change(function () {
			var selectedOption = $(this).find(":selected");
			var address = selectedOption.data("address");
			var placeName = selectedOption.data("place-name");

			if (selectedOption.val()) {
				// If a valid option is selected, autofill and make fields uneditable
				$("input[name='pname']").val(placeName).prop("readonly", true);
				$("textarea[name='address']").val(address).prop("readonly", true);
			} else {
				// If an empty value is selected, clear fields and make them editable
				$("input[name='pname']").val("").prop("readonly", false);
				$("textarea[name='address']").val("").prop("readonly", false);
			}
		});


	});
</script>


<script>
  let headlines = [];

  function addHeadline() {
    const title = document.getElementById("headline_title").value.trim();
    const desc = document.getElementById("headline_desc").value.trim();

    if (title && desc) {
      headlines.push({ title, description: desc });
      updateHeadlineList();
      document.getElementById("headline_title").value = '';
      document.getElementById("headline_desc").value = '';
    }
  }

  function removeHeadline(index) {
    headlines.splice(index, 1);
    updateHeadlineList();
  }

  function updateHeadlineList() {
    const container = document.getElementById("headlineList");
    const heading = document.getElementById("headlineListHeading");
    container.innerHTML = "";

    headlines.forEach((h, index) => {
      container.innerHTML += `
        <div class="columnDivider p-2 mb-2">
          <h5>${h.title}</h5>
          <p>${h.description}</p>
          <button type="button" class="btn btn-sm btn-danger" onclick="removeHeadline(${index})">Remove</button>
        </div>
      `;
    });

    heading.style.display = headlines.length > 0 ? 'block' : 'none';
    document.getElementById("headline_list_json").value = JSON.stringify(headlines);
  }

  // 👇 This function will load initial headlines from DB string
  function loadHeadlinesFromDB(jsonString) {
    try {
      const parsed = JSON.parse(jsonString);
      if (Array.isArray(parsed)) {
        headlines = parsed;
        updateHeadlineList();
      }
    } catch (e) {
      console.error("Invalid JSON from DB", e);
    }
  }

  // 👇 Load from DB value on page load
  window.addEventListener('DOMContentLoaded', () => {
    const dbValue = document.getElementById("headline_list_json").value;
    if (dbValue) {
      loadHeadlinesFromDB(dbValue);
    }
  });
</script>


<?php include 'include/footer.php'; ?>

<style>
	.columnDivider {
 border-bottom: 1px solid #ccc;
 padding-bottom: 15px !important;
}
	</style>
</body>

</html>