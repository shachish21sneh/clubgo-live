<?php
include 'include/top.php';
include 'include/sidebar.php';
?>
<div class="content-body">
  <!-- row -->
  <div class="container-fluid">
    <div class="form-head mb-4 d-flex flex-wrap align-items-center">
      <div class="me-auto">
        <h2 class="font-w600 mb-0">City Management</h2>

      </div>

    </div>
    <div class="row">

      <div class="col-xl-12 col-lg-12">
        <?php
        if (isset($_GET['id'])) {
          $data = $event->query("select * from tbl_city where id=" . $_GET['id'] . "")->fetch_assoc();
          ?>
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Edit City</h4>
            </div>
            <div class="card-body">

              <form method="post" enctype="multipart/form-data">


                <div class="form-group mb-3">
                  <label>City Name</label>
                  <input type="text" class="form-control input-rounded" value="<?php echo $data['name']; ?>" name="title"
                    placeholder="Enter City Name" required="">
                    <input type="hidden" name="type" value="edit_city"/>
                    <input type="hidden" name="id" value="<?php echo $_GET['id'];?>"/>
                </div>
                
                 <div class="form-group mb-3">
                  <label>City Status</label>
                  <select name="status" name="status" class="form-control input-rounded" required>
                    <option value="">Select Status</option>
                    <option value="A" <?php if ($data['status'] == "A") {
                      echo 'selected';
                    } ?>>ACTIVE</option>
                    <option value="I" <?php if ($data['status'] == "I") {
                      echo 'selected';
                    } ?>>INACTIVE</option>
                  </select>
                </div>



                <div class="form-group">
                  <button type="submit" class="btn btn-rounded btn-primary"><span class="btn-icon-start text-primary"><i
                        class="fa fa-list"></i>
                    </span>Edit City</button>
                </div>
              </form>

            </div>
          </div>
        <?php
        } else {
          ?>
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Add City</h4>
            </div>
            <div class="card-body">

              <form method="post" enctype="multipart/form-data">


                <div class="form-group mb-3">
                  <label>City Name</label>
                  <input type="text" class="form-control input-rounded" name="title" placeholder="Enter City Name" required="">
                    <input type="hidden" name="type" value="add_city"/>
                </div>
                <div class="form-group mb-3">
                  <label>City Status</label>
                  <select name="status" name="status" class="form-control input-rounded" required>
                    <option value="">Select Status</option>
                    <option value="A">ACTIVE</option>
                    <option value="I">INACTIVE</option>
                  </select>
                </div>



                <div class="form-group">
                  <button type="submit" class="btn btn-rounded btn-primary"><span class="btn-icon-start text-primary"><i
                        class="fa fa-list"></i>
                    </span>Add City</button>
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

<?php include 'include/footer.php'; ?>

</body>

</html>