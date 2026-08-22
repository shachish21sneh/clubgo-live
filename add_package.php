<?php
include 'include/top.php';
include 'include/sidebar.php';
?>
<div class="content-body">
  <!-- row -->
  <div class="container-fluid">
    <div class="form-head mb-4 d-flex flex-wrap align-items-center">
      <div class="me-auto">
        <h2 class="font-w600 mb-0">Package Management</h2>

      </div>

    </div>
    <div class="row">

      <div class="col-xl-12 col-lg-12">
        <?php
        if (isset($_GET['id'])) {
          $data = $event->query("select * from tbl_package where id=" . $_GET['id'] . "")->fetch_assoc();
          ?>
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Edit Package</h4>
            </div>
            <div class="card-body">

              <form method="post" enctype="multipart/form-data">


                <div class="form-group mb-3">
                  <label>Package Name</label>
                  <input type="text" class="form-control input-rounded" value="<?php echo $data['title']; ?>" name="title"
                    placeholder="Enter Package Name" required="">
                  <input type="hidden" name="type" value="edit_Package" />
                  <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
                </div>

                <div class="form-group mb-3">
                  <label>Price</label>
                  <input type="text" class="form-control  numberonly" value="<?php echo $data['price']; ?>" name="price"
                    placeholder="Enter Price" required="">
                </div>

                <div class="form-group mb-3">
                  <label>Package Status</label>
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
                    </span>Edit Package</button>
                </div>
              </form>

            </div>
          </div>
          <?php
        } else {
          ?>
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Add Package</h4>
            </div>
            <div class="card-body">

              <form method="post" enctype="multipart/form-data">


                <div class="form-group mb-3">
                  <label>Package Name</label>
                  <input type="text" class="form-control input-rounded" name="title" placeholder="Enter Package Name"
                    required="">
                  <input type="hidden" name="type" value="add_Package" />
                </div>
                <div class="form-group mb-3">
                  <label>Price</label>
                  <input type="text" class="form-control  numberonly" name="price" placeholder="Enter Price" required="">
                </div>

                <div class="form-group mb-3">
                  <label>Package Status</label>
                  <select name="status" name="status" class="form-control input-rounded" required>
                    <option value="">Select Status</option>
                    <option value="A">ACTIVE</option>
                    <option value="I">INACTIVE</option>
                  </select>
                </div>



                <div class="form-group">
                  <button type="submit" class="btn btn-rounded btn-primary"><span class="btn-icon-start text-primary"><i
                        class="fa fa-list"></i>
                    </span>Add Package</button>
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