<?php 
// Optional: enable PHP error display while debugging
// Turn off in production
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'include/top.php';
include 'include/sidebar.php';

// Debug mode: set to true to show SQL errors and queries
$DEBUG = true;

// Helper for running queries with optional debug output
function run_query($conn, $sql, $debug = false) {
    $res = $conn->query($sql);
    if ($res === false) {
        if ($debug) {
            // die with useful debug info
            die("<strong>MySQL error:</strong> " . htmlspecialchars($conn->error) 
                . "<br><strong>SQL:</strong> " . htmlspecialchars($sql));
        } else {
            die("Database error. Please contact admin.");
        }
    }
    return $res;
}

// Detect correct venue table name (tbl_veneu or tbl_veneu)
$venue_table = 'tbl_veneu';
$check1 = $event->query("SHOW TABLES LIKE 'tbl_veneu'");
if (!$check1) {
    if ($DEBUG) die("Error checking tables: " . htmlspecialchars($event->error));
}
if ($check1->num_rows === 0) {
    $check2 = $event->query("SHOW TABLES LIKE 'tbl_veneu'");
    if ($check2 && $check2->num_rows > 0) {
        $venue_table = 'tbl_veneu';
    } else {
        // Neither table exists — keep 'tbl_veneu' but notify (if debug)
        if ($DEBUG) {
            die("Neither 'tbl_veneu' nor 'tbl_veneu' table found. Please verify your DB tables with SHOW TABLES;");
        }
    }
}

// Page size handling
$records_per_page = isset($_GET['size']) ? (int)$_GET['size'] : 10;
$records_per_page = in_array($records_per_page, [10, 25, 50, 100]) ? $records_per_page : 10;

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_condition = '';
$search_params = '';

if ($search !== '') {
    $search_escaped = $event->real_escape_string($search);
    // use fully qualified names to avoid ambiguity
    $search_condition = "WHERE (tbl_event.title LIKE '%$search_escaped%' 
                            OR tbl_event.id LIKE '%$search_escaped%' 
                            OR tbl_event.event_status LIKE '%$search_escaped%' 
                            OR tbl_event.sdate LIKE '%$search_escaped%'
                            OR {$venue_table}.loc_title LIKE '%$search_escaped%')";
    $search_params = '&search=' . urlencode($search);
}

// Pagination settings
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $records_per_page;

// Get total number of records (with search)
$total_query = "SELECT COUNT(*) as total 
                FROM tbl_event 
                LEFT JOIN {$venue_table} ON tbl_event.loc_id = {$venue_table}.loc_id 
                $search_condition";
$total_stmt = run_query($event, $total_query, $DEBUG);
$total_row = $total_stmt->fetch_assoc();
$total_records = isset($total_row['total']) ? (int)$total_row['total'] : 0;
$total_pages = ($total_records > 0) ? ceil($total_records / $records_per_page) : 0;

// Main query with JOIN (direct table names)
$main_query = "SELECT tbl_event.*, {$venue_table}.loc_title as venue_name 
               FROM tbl_event 
               LEFT JOIN {$venue_table} ON tbl_event.loc_id = {$venue_table}.loc_id 
               $search_condition 
               ORDER BY tbl_event.id DESC 
               LIMIT " . (int)$records_per_page . " OFFSET " . (int)$offset;

$city = run_query($event, $main_query, $DEBUG);
$i = $offset;
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
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Event List</h4>
                        <!-- Search Box -->
                        <div class="d-flex me-2">
                            <input type="hidden" id="pageSize" value="<?php echo (int)$records_per_page; ?>">
                            <input type="text" id="searchBox" class="form-control me-2" 
                                placeholder="Search by event name, ID, status, venue..." 
                                value="<?php echo htmlspecialchars($search); ?>" 
                                style="width: 300px;">

                            <!-- Search A Tag -->
                            <a href="javascript:void(0);" class="btn btn-primary btn-sm" onclick="doSearch()">
                                <i class="fa fa-search"></i> Search
                            </a>

                            <!-- Clear A Tag -->
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

                            // हमेशा page 1 से शुरू करें
                            let url = "?page=1&size=" + encodeURIComponent(size);
                            if (search !== "") {
                                url += "&search=" + encodeURIComponent(search);
                            }
                            window.location.href = url;
                        }

                        // Enter दबाने पर भी redirect हो
                        document.getElementById("searchBox").addEventListener("keypress", function(e) {
                            if (e.key === "Enter") {
                                e.preventDefault();
                                doSearch();
                            }
                        });
                        </script>

                    </div>
                    
                    <div class="card-body">
                        <!-- Records Info -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="dataTables_info">
                                <?php
                                if ($total_records > 0) {
                                    echo "Showing " . ($offset + 1) . " to " . min($offset + $records_per_page, $total_records) . " of " . $total_records . " entries";
                                } else {
                                    echo "Showing 0 to 0 of 0 entries";
                                }
                                if (!empty($search)) echo " (filtered from total entries)";
                                ?>
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
                                        <th>Sr No.</th>
                                        <th>Event <br>Id.</th>
                                        <th>Event <br>Name</th>
                                        <th>Venue <br>Name</th>
                                        <th>Event<br> Start <br>Date</th>
                                        <th>Event <br>Time</th>
                                        <!-- <th>Total <br>Tickets</th> -->
                                        <th>Publish <br>Status</th>
                                        <th>Event <br>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if ($city && $city->num_rows > 0) {
                                        while($row = $city->fetch_assoc()) {
                                            $i++;
                                    ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                                        <td><?php echo htmlspecialchars($row['venue_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['sdate']); ?></td>
                                        <td><?php echo date("g:i A", strtotime($row['stime'])) . '<br> TO <br>' . date("g:i A", strtotime($row['etime'])); ?></td>
                                        <!-- <td>
                                            <?php 
                                            $tdata_q = "SELECT SUM(tlimit) as total_ticket FROM tbl_type_price WHERE eid=" . (int)$row['id'];
                                            $tdata_res = $event->query($tdata_q);
                                            $tdata = $tdata_res ? $tdata_res->fetch_assoc() : null;
                                            echo empty($tdata['total_ticket']) ? '0 Tickets' : htmlspecialchars($tdata['total_ticket']) . ' Tickets';
                                            ?>
                                        </td> -->
                                        <?php if($row['status'] == 1) { ?>
                                            <td><span class="badge badge-success">Publish</span></td>
                                        <?php } else { ?>
                                            <td><span class="badge badge-danger">Unpublish</span></td>
                                        <?php } ?>
                                        <td><span class="badge badge-success"><?php echo htmlspecialchars($row['event_status']); ?></span></td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="add_events.php?id=<?php echo $row['id'];?>" class="btn btn-primary shadow btn-xs sharp me-1" title="Edit Event">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <a href="list_tickets.php?id=<?php echo $row['id'];?>" class="btn btn-info shadow btn-xs sharp me-1" title="Show Tickets">
                                                    <i class="fa fa-ticket"></i>
                                                </a>
                                            </div>												
                                        </td>												
                                    </tr>
                                    <?php 
                                        }
                                    } else {
                                        $no_results_message = !empty($search) ? 'No events found matching your search.' : 'No events found.';
                                        echo '<tr><td colspan="10" class="text-center py-4">' . htmlspecialchars($no_results_message) . '</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($total_pages > 1) { ?>
                        <nav aria-label="Event pagination">
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
                                
                                for ($p = $start_page; $p <= $end_page; $p++) {
                                    $active = ($p == $page) ? 'active' : '';
                                    echo '<li class="page-item ' . $active . '">';
                                    echo '<a class="page-link" href="?page=' . $p . '&size=' . $records_per_page . $search_params . '">' . $p . '</a>';
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
console.log('Event Management with Pagination + Venue Join loaded');
</script>

<?php include 'include/footer.php';?>

</body>
</html>
