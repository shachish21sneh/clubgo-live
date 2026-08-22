<?php 
include 'include/top.php';
include 'include/sidebar.php';

// Page size handling
$records_per_page = isset($_GET['size']) ? (int)$_GET['size'] : 10;
$records_per_page = in_array($records_per_page, [10, 25, 50, 100]) ? $records_per_page : 10;

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_condition = '';
$search_params = '';

if (!empty($search)) {
    $search_escaped = $event->real_escape_string($search);
    $search_condition = "WHERE (name LIKE '%$search_escaped%' OR email LIKE '%$search_escaped%' OR mobile LIKE '%$search_escaped%' OR code LIKE '%$search_escaped%' OR refercode LIKE '%$search_escaped%')";
    $search_params = '&search=' . urlencode($search);
}

// Pagination settings
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $records_per_page;

// Get total number of records (with search)
$total_query = "SELECT COUNT(*) as total FROM `tbl_user` $search_condition";
$total_stmt = $event->query($total_query);
$total_records = $total_stmt->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);
?>

<div class="content-body">
    <!-- row -->
    <div class="container-fluid">
        <div class="form-head mb-4 d-flex flex-wrap align-items-center">
            <div class="me-auto">
                <h2 class="font-w600 mb-0">User Management</h2>
            </div>	
        </div>
        
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">User List</h4>
                        <!-- Search Box -->
                        <div class="d-flex me-2">
                            <input type="hidden" id="pageSize" value="<?php echo $records_per_page; ?>">
                            <input type="text" id="searchBox" class="form-control me-2" 
                                placeholder="Search by name, email, mobile..." 
                                value="<?php echo htmlspecialchars($search); ?>" 
                                style="width: 280px;">
                            
                            <a href="javascript:void(0);" class="btn btn-primary btn-sm" onclick="doSearch()">
                                <i class="fa fa-search"></i> Search
                            </a>

                            <?php if (!empty($search)) { ?>
                                <a href="?size=<?php echo $records_per_page; ?>" class="btn btn-secondary btn-sm ms-2">
                                    <i class="fa fa-times"></i> Clear
                                </a>
                            <?php } ?>
                        </div>

                        <script>
                        function doSearch() {
                            let search = document.getElementById("searchBox").value.trim();
                            let size   = document.getElementById("pageSize").value;
                            let url    = "?size=" + encodeURIComponent(size);
                            if (search !== "") {
                                url += "&search=" + encodeURIComponent(search);
                            }
                            window.location.href = url;
                        }
                        </script>

                    </div>
                    
                    <div class="card-body">
                        <!-- Records Info -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="dataTables_info">
                                Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $records_per_page, $total_records); ?> 
                                of <?php echo $total_records; ?> entries
                                <?php if (!empty($search)) echo "(filtered from total entries)"; ?>
                            </div>
                            <div class="dataTables_length">
                                <label>Show 
                                    <form method="GET" class="d-inline">
                                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                                        <input type="hidden" name="page" value="1">
                                        <select name="size" onchange="this.form.submit()" class="form-select d-inline" style="width: auto;">
                                            <option value="10" <?php echo $records_per_page == 10 ? 'selected' : ''; ?>>10</option>
                                            <option value="25" <?php echo $records_per_page == 25 ? 'selected' : ''; ?>>25</option>
                                            <option value="50" <?php echo $records_per_page == 50 ? 'selected' : ''; ?>>50</option>
                                            <option value="100" <?php echo $records_per_page == 100 ? 'selected' : ''; ?>>100</option>
                                        </select>
                                    </form> entries
                                </label>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Join Date</th>
                                        <th>Status</th>
                                        <th>Refer Code</th>
                                        <th>Join Code</th>
                                        <th>Wallet</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // Main query with search condition
                                    $main_query = "SELECT * FROM `tbl_user` $search_condition ORDER BY id DESC LIMIT $records_per_page OFFSET $offset";
                                    $stmt = $event->query($main_query);
                                    $i = $offset;
                                    
                                    if ($stmt && $stmt->num_rows > 0) {
                                        while($row = $stmt->fetch_assoc()) {
                                            $i = $i + 1;
                                    ?>
                                    <tr>
                                        <td><img class="rounded-circle" width="35" src="<?php echo $row['pro_pic'];?>" alt=""></td>
                                        <td><?php echo htmlspecialchars($row['name']);?></td>
                                        <td><?php echo htmlspecialchars($row['email']);?></td>
                                        <td><?php echo htmlspecialchars($row['mobile']);?></td>
                                        <td><?php echo $row['rdate'];?></td>
                                        <?php if($row['status'] == '1') { ?>
                                        <td><a class="drop" data-id="<?php echo $row['id'];?>" data-status="0" data-type="update_status" coll-type="user" href="javascript:void(0);"><div class="badge badge-danger">Make Deactive</div></a></td>
                                        <?php } else { ?>
                                        <td><a class="drop" data-id="<?php echo $row['id'];?>" data-status="1" data-type="update_status" coll-type="user" href="javascript:void(0);"><div class="badge badge-success">Make Active</div></a></td>
                                        <?php } ?>
                                        <td><?php echo htmlspecialchars($row['code']);?></td>
                                        <td><?php echo htmlspecialchars($row['refercode']);?></td>
                                        <td><?php echo $row['wallet'].$set['currency'];?></td>
                                    </tr>
                                    <?php 
                                        }
                                    } else {
                                        $no_results_message = !empty($search) ? 'No users found matching your search.' : 'No users found.';
                                        echo '<tr><td colspan="9" class="text-center py-4">' . $no_results_message . '</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($total_pages > 1) { ?>
                        <nav aria-label="User pagination">
                            <ul class="pagination justify-content-center mt-4">
                                <!-- Previous Button -->
                                <?php if ($page > 1) { ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&size=<?php echo $records_per_page; ?><?php echo $search_params; ?>">
                                        Previous
                                    </a>
                                </li>
                                <?php } ?>
                                
                                <!-- Page Numbers -->
                                <?php
                                $start_page = max(1, $page - 2);
                                $end_page = min($total_pages, $page + 2);
                                
                                if ($start_page > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="?page=1&size=' . $records_per_page . $search_params . '">1</a></li>';
                                    if ($start_page > 2) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                }
                                
                                for ($i = $start_page; $i <= $end_page; $i++) {
                                    $active = ($i == $page) ? 'active' : '';
                                    echo '<li class="page-item ' . $active . '">';
                                    echo '<a class="page-link" href="?page=' . $i . '&size=' . $records_per_page . $search_params . '">' . $i . '</a>';
                                    echo '</li>';
                                }
                                
                                if ($end_page < $total_pages) {
                                    if ($end_page < $total_pages - 1) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . '&size=' . $records_per_page . $search_params . '">' . $total_pages . '</a></li>';
                                }
                                ?>
                                
                                <!-- Next Button -->
                                <?php if ($page < $total_pages) { ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&size=<?php echo $records_per_page; ?><?php echo $search_params; ?>">
                                        Next
                                    </a>
                                </li>
                                <?php } ?>
                            </ul>
                        </nav>
                        
                        <!-- Page Info -->
                        <div class="text-center mt-2">
                            <small class="text-muted">
                                Page <?php echo $page; ?> of <?php echo $total_pages; ?>
                            </small>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Remove the unused JavaScript functions since we're using form submission now
console.log('User Management with Pagination loaded');
</script>

<?php include 'include/footer.php';?>

</body>
</html>