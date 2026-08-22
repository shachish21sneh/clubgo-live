<?php
include 'include/top.php';
include 'include/sidebar.php';
?>
<div class="content-body">
    <!-- row -->
    <div class="container-fluid">
        <div class="form-head mb-4 d-flex flex-wrap align-items-center">
            <div class="me-auto">
                <h2 class="font-w600 mb-0">Venue Management</h2>

            </div>

        </div>
        <div class="row">

            <div class="col-xl-12 col-lg-12">
                <?php
                if (isset($_GET['id'])) {
                    $venue = $event->query("select * from tbl_veneu where loc_id=" . $_GET['id'] . "")->fetch_assoc();
                ?>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Edit Venue</h4>
                        </div>
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data">

                                <!-- Hidden Field for Venue ID -->
                                <input type="hidden" name="venue_id" value="<?= $venue['loc_id']; ?>">

                                <!-- Row 1 -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Title</label>
                                            <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($venue['loc_title']); ?>" required>
                                            <input type="hidden" name="type" value="edit_venue" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Description</label>
                                            <textarea class="form-control" rows="3" name="vdesc" required><?= htmlspecialchars($venue['loc_description']); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Row 2 -->
                                <div class="row">
                                    <!-- <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Location Open Close</label>
                                            <textarea class="form-control" rows="3" name="loc_open_close" required><?= htmlspecialchars($venue['loc_open_close']); ?></textarea>
                                        </div>
                                    </div> -->
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Location Customer Headlines</label>
                                            <textarea class="form-control" rows="3" name="loc_cus_headline" required><?= htmlspecialchars($venue['loc_customer_headlines']); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Row 3: Select City -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Select City</label>
                                            <select name="cityid" class="form-control select2-single" required>
                                                <option value="" disabled>Select City</option>
                                                <?php
                                                $loc_city_id = $venue['loc_city_id'];
                                                $cat = $event->query("SELECT * FROM tbl_city WHERE id = " . intval($loc_city_id));

                                                while ($row = $cat->fetch_assoc()) { ?>
                                                    <option value="<?= $row['id']; ?>" <?= ($row['id'] == $loc_city_id) ? 'selected' : ''; ?>>
                                                        <?= htmlspecialchars($row['name']); ?>
                                                    </option>
                                                <?php } ?>

                                            </select>
                                        </div>
                                    </div>

                                    <!-- Multi-Select Dropdowns for Categories, Cuisines, Facilities, Known For, and Package Items -->
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Select Location categories</label>
                                            <div class="dropdown">
                                                <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">Select Categories</button>
                                                <ul class="dropdown-menu p-3" style="max-height: 200px; overflow-y: auto;">
                                                    <?php
                                                    $selected_cuisines = explode(',', $venue['loc_category_id']);
                                                    $cuisines = $event->query("SELECT * FROM tbl_venue_category WHERE status='A'");
                                                    while ($row = $cuisines->fetch_assoc()) {
                                                        $checked = in_array($row['id'], $selected_cuisines) ? 'checked' : '';
                                                        echo "<li><label class='dropdown-item'><input type='checkbox' name='cid[]' value='{$row['id']}' $checked> " . htmlspecialchars($row['name']) . "</label></li>";
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Row - Cuisines and Facilities -->
                                <div class="row">
                                    <!-- Location Cuisines -->
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Select Location Cuisines</label>
                                            <div class="dropdown">
                                                <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">Select Location Cuisines</button>
                                                <ul class="dropdown-menu p-3" style="max-height: 200px; overflow-y: auto;">
                                                    <?php
                                                    $selected_cuisines = explode(',', $venue['loc_cuisines_id']);
                                                    $cuisines = $event->query("SELECT * FROM tbl_cuisines WHERE status='ACTIVE'");
                                                    while ($row = $cuisines->fetch_assoc()) {
                                                        $checked = in_array($row['id'], $selected_cuisines) ? 'checked' : '';
                                                        echo "<li><label class='dropdown-item'><input type='checkbox' name='cusid[]' value='{$row['id']}' $checked> " . htmlspecialchars($row['name']) . "</label></li>";
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Location Facilities -->
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Select Location Facilities</label>
                                            <div class="dropdown">
                                                <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">Select Facilities</button>
                                                <ul class="dropdown-menu p-3" style="max-height: 200px; overflow-y: auto;">
                                                    <?php
                                                    $selected_facilities = explode(',', $venue['loc_facilities_id']);
                                                    $facilities = $event->query("SELECT * FROM tbl_facilities WHERE status='ACTIVE'");
                                                    while ($row = $facilities->fetch_assoc()) {
                                                        $checked = in_array($row['id'], $selected_facilities) ? 'checked' : '';
                                                        echo "<li><label class='dropdown-item'><input type='checkbox' name='facid[]' value='{$row['id']}' $checked> " . htmlspecialchars($row['name']) . "</label></li>";
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Row - know for and package -->
                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Select Known for</label>
                                            <div class="dropdown">
                                                <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">Select Known for</button>
                                                <ul class="dropdown-menu p-3" style="max-height: 200px; overflow-y: auto;">
                                                    <?php
                                                    $selected_cuisines = explode(',', $venue['loc_known_for']);
                                                    $cuisines = $event->query("SELECT * FROM tbl_known_for WHERE status='ACTIVE'");
                                                    while ($row = $cuisines->fetch_assoc()) {
                                                        $checked = in_array($row['id'], $selected_cuisines) ? 'checked' : '';
                                                        echo "<li><label class='dropdown-item'><input type='checkbox' name='knowforid[]' value='{$row['id']}' $checked> " . htmlspecialchars($row['name']) . "</label></li>";
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Select Location Package</label>
                                            <div class="dropdown">
                                                <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">Select Package</button>
                                                <ul class="dropdown-menu p-3" style="max-height: 200px; overflow-y: auto;">
                                                    <?php
                                                    $selected_facilities = explode(',', $venue['loc_package_id']);
                                                    $facilities = $event->query("SELECT * FROM tbl_package_items WHERE status='ACTIVE'");
                                                    while ($row = $facilities->fetch_assoc()) {
                                                        $checked = in_array($row['id'], $selected_facilities) ? 'checked' : '';
                                                        echo "<li><label class='dropdown-item'><input type='checkbox' name='pkgitemid[]' value='{$row['id']}' $checked> " . htmlspecialchars($row['name']) . "</label></li>";
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Row - days and silier venue -->
                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Select days</label>
                                            <div class="dropdown">
                                                <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Select days
                                                </button>
                                                <ul class="dropdown-menu p-3" style="max-height: 300px; overflow-y: auto; width: 100%;">
                                                    <?php
                                                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                                    $selected_days = [];

                                                    if (!empty($venue['loc_days_json'])) {
                                                        $selected_days = json_decode($venue['loc_days_json'], true);
                                                    }

                                                    foreach ($days as $day):
                                                        $is_checked = isset($selected_days[$day]);
                                                        $start_time = $is_checked ? $selected_days[$day]['start'] : '00:00';
                                                        $end_time = $is_checked ? $selected_days[$day]['end'] : '12:00';
                                                    ?>
                                                        <li>
                                                            <label class="dropdown-item d-flex flex-column align-items-start">
                                                                <div>
                                                                    <input type="checkbox"
                                                                        onchange="toggleTimeInputs(this, '<?= $day ?>')"
                                                                        name="days[<?= $day ?>][open]"
                                                                        <?= $is_checked ? 'checked' : '' ?>>
                                                                    <?= $day ?>
                                                                </div>
                                                                <div id="inputs-<?= $day ?>" style="<?= $is_checked ? '' : 'display:none;' ?> margin-top: 5px;">
                                                                    <div class="mb-2">
                                                                        <label class="form-label">Opening Time</label>
                                                                        <input type="time" name="days[<?= $day ?>][start]" class="form-control" value="<?= $start_time ?>">
                                                                    </div>
                                                                    <div>
                                                                        <label class="form-label">Closing Time</label>
                                                                        <input type="time" name="days[<?= $day ?>][end]" class="form-control" value="<?= $end_time ?>">
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Select Similer venue</label>
                                            <div class="dropdown">
                                                <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">Select Similer venue</button>
                                                <ul class="dropdown-menu p-3" style="max-height: 200px; overflow-y: auto;">
                                                    <?php
                                                    $selected_facilities = explode(',', $venue['loc_similer_venue']);
                                                    $facilities = $event->query("SELECT * FROM tbl_veneu");

                                                    while ($row = $facilities->fetch_assoc()) {
                                                        $checked = in_array($row['loc_id'], $selected_facilities) ? 'checked' : '';
                                                        echo "<li><label class='dropdown-item'><input type='checkbox' name='similar_venue[]' value='{$row['loc_id']}' $checked> " . htmlspecialchars($row['loc_title']) . "</label></li>";
                                                    }
                                                    ?>

                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Row 4: Dates & Time -->
                                <!-- <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Venue Starting Date</label>
                                            <input type="date" name="sdate" class="form-control" value="<?= $venue['loc_from_date']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Venue Ending Date</label>
                                            <input type="date" name="edate" class="form-control" value="<?= $venue['loc_to_date']; ?>" required>
                                        </div>
                                    </div>
                                </div> -->

                                <!-- <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Start Time</label>
                                            <input type="time" name="stime" class="form-control" value="<?= $venue['loc_start_time']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>End Time</label>
                                            <input type="time" name="etime" class="form-control" value="<?= $venue['loc_end_time']; ?>" required>
                                        </div>
                                    </div>
                                </div> -->

                                <!-- Row 5: Status & Featured Order -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Venue Status</label>
                                            <select name="status" class="form-control select2-single" required>
                                                <option value="">Select Status</option>
                                                <option value="A" <?= $venue['loc_status'] == 'A' ? 'selected' : ''; ?>>Publish</option>
                                                <option value="I" <?= $venue['loc_status'] == 'I' ? 'selected' : ''; ?>>Unpublish</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Featured Order</label>
                                            <input type="number" class="form-control" name="featuredorder" value="<?= $venue['is_featured']; ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Row 6: Image Upload -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Image</label>
                                            <input type="file" name="v_img" class="form-control">
                                            <?php if (!empty($venue['loc_image'])) { ?>
                                                <img src="<?= $venue['loc_image']; ?>" alt="Venue Image" class="img-thumbnail mt-2" width="100">
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="form-group text-center mt-4">
                                    <button type="submit" class="btn btn-primary btn-rounded">
                                        <i class="flaticon-381-edit"></i> Update Venue
                                    </button>
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
                    <h4 class="card-title">Add Venue</h4>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">

                        <!-- Row 1 -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Title</label>
                                    <input type="text" class="form-control" name="title" placeholder="Enter Title" required>
                                    <input type="hidden" name="type" value="add_venue" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Description</label>
                                    <textarea class="form-control" rows="3" name="vdesc" required></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Row 2 -->
                        <div class="row">
                            <!-- <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Location Open Close</label>
                                    <textarea class="form-control" rows="3" name="loc_open_close" required></textarea>
                                </div>
                            </div> -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Location Customer Headlines</label>
                                    <textarea class="form-control" rows="3" name="loc_cus_headline" required></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Row 3 -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Select City</label>
                                    <select name="cityid" class="form-control select2-single" required>
                                        <option value="" disabled selected>Select City</option>
                                        <?php
                                        $cat = $event->query("SELECT * FROM tbl_city");
                                        while ($row = $cat->fetch_assoc()) { ?>
                                            <option value="<?= $row['id']; ?>"><?= htmlspecialchars($row['name']); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Select Location Categories</label>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                            Select Categories
                                        </button>
                                        <ul class="dropdown-menu p-3" style="max-height: 200px; overflow-y: auto;">
                                            <?php
                                            $cat = $event->query("SELECT * FROM tbl_venue_category");
                                            while ($row = $cat->fetch_assoc()) { ?>
                                                <li>
                                                    <label class="dropdown-item">
                                                        <input type="checkbox" name="cid[]" value="<?= $row['id']; ?>"> <?= htmlspecialchars($row['name']); ?>
                                                    </label>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- Row cus fac -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>select location cuisines</label>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                            Select location cuisines
                                        </button>
                                        <ul class="dropdown-menu p-3" style="max-height: 200px; overflow-y: auto;">
                                            <?php
                                            $cat = $event->query("SELECT * FROM `tbl_cuisines` WHERE `status`='ACTIVE'");
                                            while ($row = $cat->fetch_assoc()) { ?>
                                                <li>
                                                    <label class="dropdown-item">
                                                        <input type="checkbox" name="cusid[]" value="<?= $row['id']; ?>"> <?= htmlspecialchars($row['name']); ?>
                                                    </label>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Select Location Facilities</label>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                            Select Facilities
                                        </button>
                                        <ul class="dropdown-menu p-3" style="max-height: 200px; overflow-y: auto;">
                                            <?php
                                            $cat = $event->query("SELECT * FROM `tbl_facilities` WHERE `status`='ACTIVE'");
                                            while ($row = $cat->fetch_assoc()) { ?>
                                                <li>
                                                    <label class="dropdown-item">
                                                        <input type="checkbox" name="facid[]" value="<?= $row['id']; ?>"> <?= htmlspecialchars($row['name']); ?>
                                                    </label>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Row pack -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Select Location Known For</label>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                            Select Known For
                                        </button>
                                        <ul class="dropdown-menu p-3" style="max-height: 200px; overflow-y: auto;">
                                            <?php
                                            $cat = $event->query("SELECT * FROM `tbl_known_for` WHERE `status`='ACTIVE'");
                                            while ($row = $cat->fetch_assoc()) { ?>
                                                <li>
                                                    <label class="dropdown-item">
                                                        <input type="checkbox" name="knowforid[]" value="<?= $row['id']; ?>"> <?= htmlspecialchars($row['name']); ?>
                                                    </label>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Select Location Package</label>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                            Select Package
                                        </button>
                                        <ul class="dropdown-menu p-3" style="max-height: 200px; overflow-y: auto;">
                                            <?php
                                            $cat = $event->query("SELECT * FROM `tbl_package_items` WHERE `status`='ACTIVE'");
                                            while ($row = $cat->fetch_assoc()) { ?>
                                                <li>
                                                    <label class="dropdown-item">
                                                        <input type="checkbox" name="pkgitemid[]" value="<?= $row['id']; ?>"> <?= htmlspecialchars($row['name']); ?>
                                                    </label>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Row days -->
                        <div class="row">

                            <!-- <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Select days</label>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                            Select days
                                        </button>
                                        <ul class="dropdown-menu p-3" style="max-height: 200px; overflow-y: auto;">
                                            <?php
                                            $cat = $event->query("SELECT * FROM tbl_days");
                                            while ($row = $cat->fetch_assoc()) { ?>
                                                <li>
                                                    <label class="dropdown-item">
                                                        <input type="checkbox" name="daysid[]" value="<?= $row['id']; ?>"> <?= htmlspecialchars($row['name']); ?>
                                                    </label>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div> -->

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Select days</label>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Select days
                                        </button>
                                        <ul class="dropdown-menu p-3" style="max-height: 300px; overflow-y: auto; width: 100%;">
                                            <?php
                                            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                            foreach ($days as $day): ?>
                                                <li>
                                                    <label class="dropdown-item d-flex flex-column align-items-start">
                                                        <div>
                                                            <input type="checkbox" onchange="toggleTimeInputs(this, '<?= $day ?>')" name="days[<?= $day ?>][open]">
                                                            <?= $day ?>
                                                        </div>
                                                        <div id="inputs-<?= $day ?>" style="display:none; margin-top: 5px;">
                                                            <div class="mb-2">
                                                                <label class="form-label">Opening Time</label>
                                                                <input type="time" name="days[<?= $day ?>][start]" class="form-control" value="00:00">
                                                            </div>
                                                            <div>
                                                                <label class="form-label">Closing Time</label>
                                                                <input type="time" name="days[<?= $day ?>][end]" class="form-control" value="12:00">
                                                            </div>
                                                        </div>
                                                    </label>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>



                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Select Similar Venue</label>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                            Select Similar Venue
                                        </button>
                                        <ul class="dropdown-menu p-3" style="max-height: 200px; overflow-y: auto;">
                                            <?php
                                            $venues = $event->query("SELECT * FROM tbl_veneu");
                                            while ($row = $venues->fetch_assoc()) { ?>
                                                <li>
                                                    <label class="dropdown-item">
                                                        <input type="checkbox" name="similar_venue[]" value="<?= $row['loc_id']; ?>"> <?= htmlspecialchars($row['loc_title']); ?>
                                                    </label>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Row 4 -->
                        <!-- <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Venue Starting Date</label>
                                    <input type="date" name="sdate" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Venue Ending Date</label>
                                    <input type="date" name="edate" class="form-control" required>
                                </div>
                            </div>
                        </div> -->

                        <!-- Row 5 -->
                        <!-- <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Start Time</label>
                                    <input type="time" name="stime" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>End Time</label>
                                    <input type="time" name="etime" class="form-control" required>
                                </div>
                            </div>
                        </div> -->

                        <!-- Row 6 -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Venue Status</label>
                                    <select name="status" class="form-control select2-single" required>
                                        <option value="">Select Status</option>
                                        <option value="A">Publish</option>
                                        <option value="I">Unpublish</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Featured Order</label>
                                    <input type="number" class="form-control" name="featuredorder" placeholder="Enter value" required>
                                </div>
                            </div>
                        </div>

                        <!-- Row 7 -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Image</label>
                                    <input type="file" name="v_img" class="form-control" required>
                                </div>
                            </div>

                        </div>

                        <!-- Submit Button -->
                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-rounded">
                                <i class="flaticon-381-speaker"></i> Add Venue
                            </button>
                        </div>

                    </form>
                </div>
            </div>


        </div>
    </div>
<?php } ?>
</div>




</div>
</div>

</div>

</div>

<?php include 'include/footer.php'; ?>


</body>

<script>
    function toggleTimeInputs(checkbox, day) {
        const el = document.getElementById('inputs-' + day);
        el.style.display = checkbox.checked ? 'block' : 'none';
    }
</script>

</html>