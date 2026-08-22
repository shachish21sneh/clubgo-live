<?php
include 'include/top.php';
include 'include/sidebar.php';
?>
<div class="content-body">
    <!-- row -->
    <div class="container-fluid">
        <div class="form-head mb-4 d-flex flex-wrap align-items-center">
            <div class="me-auto">
                <h2 class="font-w600 mb-0">Type & Price Management</h2>

            </div>

        </div>
        <div class="row">

            <div class="col-xl-12 col-lg-12">
                <?php
                if (isset($_GET['id'])) {
                    $data = $event->query("select * from  tbl_type_price where id=" . $_GET['id'] . "")->fetch_assoc();
                    ?>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Edit Type & Price</h4>
                        </div>
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data">


                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Name</label>
                                            <input type="text" class="form-control" name="name"
                                                value="<?php echo $data['name']; ?>" placeholder="Enter ticket name Type"
                                                required="">
                                                <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
                                                <input type="hidden" name="type" value="edit_type" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Detail</label>
                                            <input type="text" class="form-control" name="detail"
                                                value="<?php echo $data['details']; ?>" placeholder="Enter Ticket Detail"
                                                required="">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label>Select Event</label>
                                    <select name="eid" class="form-control select2-single" required>
                                        <option value="" disabled selected>Select Event</option>
                                        <?php
                                        $cat = $event->query("select * from tbl_event");
                                        while ($row = $cat->fetch_assoc()) {
                                            ?>
                                            <option value="<?php echo $row['id']; ?>" <?php if ($data['eid'] == $row['id']) {
                                                   echo 'selected';
                                               } ?>>
                                                <?php echo $row['title']; ?>
                                            </option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Select Entry Type</label>
                                    <select id="entryType" name="entrytype" class="form-control select2-single" required>
                                        <option value="single" <?php echo ($data['entry_type'] ?? '') == 'single' ? 'selected' : ''; ?>>Single</option>
                                        <option value="couple" <?php echo ($data['entry_type'] ?? '') == 'couple' ? 'selected' : ''; ?>>Couple</option>
                                    </select>
                                </div>

                                <div id="ticketDetailsSingle">
                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label>Ticket Price</label>
                                                <input type="text" class="form-control numberonly"
                                                    value="<?php echo $data['price']; ?>" name="price"
                                                    placeholder="Enter Ticket Price" required="">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label>Discount Price</label>
                                                <input type="text" class="form-control numberonly" name="discount_price"
                                                    value="<?php echo $data['discount_price']; ?>"
                                                    placeholder="Enter Discount Price">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label>Ticket Description</label>
                                                <input type="text" class="form-control" name="ticket_description"
                                                value="<?php echo $data['description']; ?>"
                                                    placeholder="Enter Ticket Description">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label>Single Name</label>
                                                <input type="text" class="form-control" name="single_name"
                                                value="<?php echo $data['single_name']; ?>"
                                                    placeholder="Single Name">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="ticketDetailsCouple" style="display: none;">

                                    <div id="couplerow">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label>Ticket Price for Couple</label>
                                                    <input type="text" class="form-control numberonly" name="t_price_c"
                                                    value="<?php echo $data['couple_price']; ?>"
                                                        placeholder="Enter Ticket Price for Couple">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label>Discount Price for Couple</label>
                                                    <input type="text" class="form-control numberonly"
                                                        name="discount_price_c"
                                                        value="<?php echo $data['discount_couple_price']; ?>"
                                                        placeholder="Enter Discount Price for Couple">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group mb-3">
                                                <label>Ticket Description for Couple</label>
                                                <input type="text" class="form-control" name="ticket_description_c"
                                                value="<?php echo $data['description_couple']; ?>"
                                                    placeholder="Enter Ticket Description for Couple">
                                            </div>
                                        </div>

                                    </div>

                                    <div id="femalerow">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label>Ticket Price for Female</label>
                                                    <input type="text" class="form-control numberonly" name="t_price_f"
                                                    value="<?php echo $data['female_price']; ?>"
                                                        placeholder="Enter Ticket Price">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label>Discount Price for Female</label>
                                                    <input type="text" class="form-control numberonly"
                                                        name="discount_price_f"
                                                        value="<?php echo $data['discount_female_price']; ?>"
                                                        placeholder="Enter Discount Price for Female">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group mb-3">
                                                <label>Ticket Description for Female</label>
                                                <input type="text" class="form-control" name="ticket_description_f"
                                                value="<?php echo $data['description_female']; ?>"
                                                    placeholder="Enter Ticket Description for Female">
                                            </div>
                                        </div>

                                    </div>


                                    <div id="malerow">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label>Ticket Price for Male</label>
                                                    <input type="text" class="form-control numberonly" name="t_price_m"
                                                    value="<?php echo $data['male_price']; ?>"
                                                        placeholder="Enter Ticket Price">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label>Discount Price for Male</label>
                                                    <input type="text" class="form-control numberonly"
                                                    value="<?php echo $data['discount_male_price']; ?>"
                                                        name="discount_price_m" placeholder="Enter Ticket Price">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group mb-3">
                                                <label>Ticket Description for Male</label>
                                                <input type="text" class="form-control" name="ticket_description_m"
                                                value="<?php echo $data['description_male']; ?>"
                                                    placeholder="Enter Ticket Description for Male">
                                            </div>
                                        </div>

                                    </div>

                                </div>
                                </br></br>

                                <div class="row">
                                    <!-- <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Event Type</label>
                                            <input type="text" class="form-control" name="etype"
                                                value="<?php echo $data['type']; ?>" placeholder="Enter Event Type"
                                                required="">
                                            <input type="hidden" name="type" value="edit_type" />
                                           
                                        </div>
                                    </div> -->
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Event Ticket Limit</label>
                                            <input type="text" class="form-control numberonly"
                                                value="<?php echo $data['tlimit']; ?>" name="tlimit"
                                                placeholder="Enter Ticket Limit" required="">
                                        </div>
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Ticket Start Time</label>
                                            <input type="text" class="form-control" name="t_start_time" 
                                            value="<?php echo $data['start_time']; ?>"
                                            placeholder="12:10"
                                                required="">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Ticket End Time</label>
                                            <input type="text" class="form-control" name="t_end_time"
                                            value="<?php echo $data['end_time']; ?>"
                                             placeholder="12:10"
                                                required="">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Couple Ratio</label>
                                            <input type="text" class="form-control"
                                                value="<?php echo $data['couple_ratio']; ?>" name="couple_ratio"
                                                placeholder="2:1" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Ticket Status</label>
                                            <select name="status" class="form-control" required>
                                                <option value="">Select Status</option>
                                                <option value="1" <?php if ($data['status'] == 1) {
                                                    echo 'selected';
                                                } ?>>
                                                    Publish</option>
                                                <option value="0" <?php if ($data['status'] == 0) {
                                                    echo 'selected';
                                                } ?>>
                                                    UnPublish</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-rounded btn-primary">
                                        <span class="btn-icon-start text-primary"><i class="fa fa-list"></i></span>
                                        Edit Type & Price
                                    </button>
                                </div>

                            </form>
                        </div>

                    </div>
                    <?php
                } else {
                    ?>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Add Type & Price</h4>
                        </div>
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Name</label>
                                            <input type="text" class="form-control" name="name"
                                                placeholder="Enter ticket name Type" required="">
                                                <input type="hidden" name="type" value="add_type" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Detail</label>
                                            <input type="text" class="form-control" name="detail"
                                                placeholder="Enter Ticket Detail" required="">
                                        </div>
                                    </div>
                                </div>


                            

<!--- --!---->
                                            <div class="form-group mb-3">
											<label>Select Event</label>
											<div class="dropdown">
												<button class="btn btn-outline-primary dropdown-toggle w-100" type="button"
													data-bs-toggle="dropdown">
													Select Event
												</button>
												<ul class="dropdown-menu p-3" style="max-height: 200px; overflow-y: auto;">
													<?php
													$cat = $event->query("SELECT * FROM `tbl_event`");
													while ($row = $cat->fetch_assoc()) { ?>
														<li>
															<label class="dropdown-item">
																<input type="checkbox" name="eid[]"
																	value="<?= $row['id']; ?>">
																<?= htmlspecialchars($row['title']); ?>
															</label>
														</li>
													<?php } ?>
												</ul>
											</div>
										</div>
<!---- ---->




                                <div class="form-group mb-3">
                                    <label>Select Entry type</label>
                                    <select id="entryType" name="entrytype" class="form-control select2-single" required>
                                        <option value="single" selected>Single</option>
                                        <option value="couple">Couple</option>
                                    </select>
                                </div>

                                <div id="ticketDetailsSingle">
                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label>Ticket Price</label>
                                                <input type="text" class="form-control numberonly" name="price"
                                                    placeholder="Enter Ticket Price">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label>Discount Price</label>
                                                <input type="text" class="form-control numberonly" name="discount_price"
                                                    placeholder="Enter Discount Price">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label>Ticket Description</label>
                                                <input type="text" class="form-control" name="ticket_description"
                                                    placeholder="Enter Ticket Description">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label>Single Name</label>
                                                <input type="text" class="form-control" name="single_name"
                                                    placeholder="Single Name">
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div id="ticketDetailsCouple" style="display: none;">

                                    <div id="couplerow">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label>Ticket Price for Couple</label>
                                                    <input type="text" class="form-control numberonly" name="t_price_c"
                                                        placeholder="Enter Ticket Price for Couple">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label>Discount Price for Couple</label>
                                                    <input type="text" class="form-control numberonly"
                                                        name="discount_price_c"
                                                        placeholder="Enter Discount Price for Couple">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group mb-3">
                                                <label>Ticket Description for Couple</label>
                                                <input type="text" class="form-control" name="ticket_description_c"
                                                    placeholder="Enter Ticket Description for Couple">
                                            </div>
                                        </div>

                                    </div>

                                    <div id="femalerow">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label>Ticket Price for Female</label>
                                                    <input type="text" class="form-control numberonly" name="t_price_f"
                                                        placeholder="Enter Ticket Price">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label>Discount Price for Female</label>
                                                    <input type="text" class="form-control numberonly"
                                                        name="discount_price_f"
                                                        placeholder="Enter Discount Price for Female">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group mb-3">
                                                <label>Ticket Description for Female</label>
                                                <input type="text" class="form-control" name="ticket_description_f"
                                                    placeholder="Enter Ticket Description for Female">
                                            </div>
                                        </div>

                                    </div>


                                    <div id="malerow">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label>Ticket Price for Male</label>
                                                    <input type="text" class="form-control numberonly" name="t_price_m"
                                                        placeholder="Enter Ticket Price">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label>Discount Price for Male</label>
                                                    <input type="text" class="form-control numberonly"
                                                        name="discount_price_m" placeholder="Enter Ticket Price">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group mb-3">
                                                <label>Ticket Description for Male</label>
                                                <input type="text" class="form-control" name="ticket_description_m"
                                                    placeholder="Enter Ticket Description for Male">
                                            </div>
                                        </div>

                                    </div>

                                </div>
                                </br></br>

                                <div class="row">
                                    <!-- <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Event Type</label>
                                            <input type="text" class="form-control" name="etype"
                                                placeholder="Enter Event Type" required="">
                                            <input type="hidden" name="type" value="add_type" />
                                        </div>
                                    </div> -->
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Event Ticket Limit</label>
                                            <input type="text" class="form-control numberonly" name="tlimit"
                                                placeholder="Enter Ticket Limit" required="">
                                        </div>
                                    </div>

                                </div>


                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Ticket Start Time</label>
                                            <input type="text" class="form-control" name="t_start_time" placeholder="12:10"
                                                required="">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Ticket End Time</label>
                                            <input type="text" class="form-control" name="t_end_time" placeholder="12:10"
                                                required="">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Couple Ratio</label>
                                            <input type="text" class="form-control" name="couple_ratio" placeholder="2:1"
                                                required="">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Ticket Status</label>
                                            <select name="status" class="form-control" required>
                                                <option value="">Select Status</option>
                                                <option value="1">Publish</option>
                                                <option value="0">UnPublish</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-rounded btn-primary">
                                        <span class="btn-icon-start text-primary"><i class="fa fa-list"></i></span>
                                        Add Type & Price
                                    </button>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // Initially hide the Couple Ticket section
        $("#ticketDetailsCouple").hide();

        // Listen for changes in the select dropdown
        $("select[name='entrytype']").change(function () {
            var selectedValue = $(this).val();
            if (selectedValue === "couple") {
                $("#ticketDetailsSingle").hide();
                $("#ticketDetailsCouple").show();
            } else {
                $("#ticketDetailsSingle").show();
                $("#ticketDetailsCouple").hide();
            }
        }).trigger("change"); // Trigger change to apply default behavior
    });
</script>
<?php include 'include/footer.php'; ?>

</body>

</html>