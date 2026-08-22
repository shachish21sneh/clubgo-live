<?php 
include 'include/top.php';
include 'include/sidebar.php';

// Page size handling
$records_per_page = isset($_GET['size']) ? (int)$_GET['size'] : 10;
$records_per_page = in_array($records_per_page, [10, 25, 50, 100]) ? $records_per_page : 10;

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search_condition = '';
$search_params = '';

// Base WHERE condition
$where_conditions = [];

// Search condition
if (!empty($search)) {
    $search_escaped = $event->real_escape_string($search);
    $where_conditions[] = "(u.name LIKE '%$search_escaped%' OR u.email LIKE '%$search_escaped%' OR e.title LIKE '%$search_escaped%' OR tp.type LIKE '%$search_escaped%' OR t.id LIKE '%$search_escaped%')";
    $search_params .= '&search=' . urlencode($search);
}

// Filter condition
$currentDate = date('Y-m-d H:i:s');
if ($filter !== 'all') {
    if ($filter === 'upcoming') {
        $where_conditions[] = "CONCAT(e.sdate, ' ', e.stime) >= '$currentDate'";
    } elseif ($filter === 'past') {
        $where_conditions[] = "CONCAT(e.sdate, ' ', e.stime) < '$currentDate'";
    } elseif ($filter === 'cancelled') {
        $where_conditions[] = "tp.type = 'Cancelled'";
    }
    $search_params .= '&filter=' . urlencode($filter);
}

// Combine conditions
if (!empty($where_conditions)) {
    $search_condition = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Pagination settings
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $records_per_page;

// Get total number of records (with search and filter)
$total_query = "SELECT COUNT(*) as total 
                FROM tbl_ticket t
                JOIN tbl_user u ON t.uid = u.id
                JOIN tbl_event e ON t.eid = e.id
                JOIN tbl_type_price tp ON t.typeid = tp.id
                $search_condition";
$total_stmt = $event->query($total_query);
$total_records = $total_stmt->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);
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
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h4 class="card-title">Ticket List</h4>
                        
                        <!-- Search and Filter Section -->
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <!-- Filter Dropdown -->
                            <form method="GET" class="d-flex align-items-center me-2">
                                <input type="hidden" name="size" value="<?php echo $records_per_page; ?>">
                                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                                <input type="hidden" name="page" value="1">
                                <select class="form-select" name="filter" onchange="this.form.submit()" style="width: 150px;">
                                    <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Tickets</option>
                                    <option value="upcoming" <?php echo $filter === 'upcoming' ? 'selected' : ''; ?>>Upcoming Events</option>
                                    <option value="past" <?php echo $filter === 'past' ? 'selected' : ''; ?>>Past Events</option>
                                    <option value="cancelled" <?php echo $filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled Tickets</option>
                                </select>
                            </form>
                            
                            <!-- Search Box -->
                            <div class="d-flex">
                                <input type="hidden" id="pageSize" value="<?php echo $records_per_page; ?>">
                                <input type="hidden" id="filterValue" value="<?php echo $filter; ?>">
                                
                                <input type="text" id="searchBox" class="form-control me-2" 
                                    placeholder="Search tickets, users, events..." 
                                    value="<?php echo htmlspecialchars($search); ?>" 
                                    style="width: 280px;">

                                <!-- Search as A Tag -->
                                <a href="javascript:void(0);" class="btn btn-primary btn-sm" onclick="doSearch()">
                                    <i class="fa fa-search"></i> Search
                                </a>

                                <!-- Clear -->
                                <?php if (!empty($search) || $filter !== 'all') { ?>
                                    <a href="?size=<?php echo $records_per_page; ?>" class="btn btn-secondary btn-sm ms-2">
                                        <i class="fa fa-times"></i> Clear
                                    </a>
                                <?php } ?>
                            </div>

                            <script>
                            function doSearch() {
                                let search = document.getElementById("searchBox").value.trim();
                                let size   = document.getElementById("pageSize").value;
                                let filter = document.getElementById("filterValue").value;

                                // हमेशा page=1 से start करो
                                let url = "?page=1&size=" + encodeURIComponent(size) + "&filter=" + encodeURIComponent(filter);

                                if (search !== "") {
                                    url += "&search=" + encodeURIComponent(search);
                                }

                                window.location.href = url;
                            }

                            // Enter दबाने पर भी search trigger हो
                            document.getElementById("searchBox").addEventListener("keypress", function(e) {
                                if (e.key === "Enter") {
                                    e.preventDefault();
                                    doSearch();
                                }
                            });
                            </script>

                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!-- Records Info -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="dataTables_info">
                                Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $records_per_page, $total_records); ?> 
                                of <?php echo $total_records; ?> entries
                                <?php if (!empty($search) || $filter !== 'all') echo "(filtered)"; ?>
                            </div>
                            <div class="dataTables_length">
                                <label>Show 
                                    <form method="GET" class="d-inline">
                                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                                        <input type="hidden" name="filter" value="<?php echo $filter; ?>">
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
                                        <th>Ticket ID</th>
                                        <th>User</th>
                                        <th>Event</th>
                                        <th>Ticket Type</th>
                                        <th>Quantity</th>
                                        <th>Total Amount</th>
                                        <th>Event Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // Main query with search condition and pagination
                                    $main_query = "SELECT t.*, u.name as user_name, u.email as user_email, u.mobile as user_mobile, 
                                                  e.title as event_title, e.sdate as event_date, e.stime as event_time,
                                                  tp.type as ticket_type, tp.price as ticket_price
                                                  FROM tbl_ticket t
                                                  JOIN tbl_user u ON t.uid = u.id
                                                  JOIN tbl_event e ON t.eid = e.id
                                                  JOIN tbl_type_price tp ON t.typeid = tp.id
                                                  $search_condition
                                                  ORDER BY t.id DESC 
                                                  LIMIT $records_per_page OFFSET $offset";
                                    
                                    $stmt = $event->query($main_query);
                                    $i = $offset;
                                    
                                    if ($stmt && $stmt->num_rows > 0) {
                                        while($row = $stmt->fetch_assoc()) {
                                            $i++;
                                            $eventDate = $row['event_date'];
                                            $eventDateTime = $row['event_date'] . ' ' . $row['event_time'];
                                            $isPastEvent = (strtotime($eventDateTime) < time());
                                            $statusClass = '';
                                            $statusText = '';
                                            
                                            // Determine status
                                            if ($row['ticket_type'] == 'Cancelled') {
                                                $statusClass = 'badge-danger';
                                                $statusText = 'Cancelled';
                                            } elseif ($isPastEvent) {
                                                $statusClass = 'badge-secondary';
                                                $statusText = 'Completed';
                                            } else {
                                                $statusClass = 'badge-success';
                                                $statusText = 'Upcoming';
                                            }
                                    ?>
                                    <tr>
                                        <td><strong>#<?php echo $row['id']; ?></strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <span class="bg-primary rounded-circle p-2 text-white d-inline-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                        <?php echo substr($row['user_name'], 0, 1); ?>
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($row['user_name']); ?></h6>
                                                    <small class="text-muted"><?php echo htmlspecialchars($row['user_email']); ?></small><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($row['user_mobile']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($row['event_title']); ?></h6>
                                                <small class="text-muted">
                                                    <?php echo date('d M, Y', strtotime($row['event_date'])); ?> at 
                                                    <?php echo date('g:i A', strtotime($row['event_time'])); ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <?php echo htmlspecialchars($row['ticket_type']); ?>
                                                <br><small class="text-muted"><?php echo $set['currency'].$row['ticket_price']; ?> each</small>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-info"><?php echo $row['total_ticket']; ?></span></td>
                                        <td><strong><?php echo $set['currency'].$row['total_amt']; ?></strong></td>
                                        <td><?php echo date('d M, Y', strtotime($row['event_date'])); ?></td>
                                        <td><span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span></td>
                                    </tr>
                                    <?php 
                                        }
                                    } else {
                                        $no_results_message = 'No tickets found';
                                        if (!empty($search)) {
                                            $no_results_message .= ' matching your search';
                                        }
                                        if ($filter !== 'all') {
                                            $no_results_message .= ' for the selected filter';
                                        }
                                        $no_results_message .= '.';
                                        echo '<tr><td colspan="8" class="text-center py-4">' . $no_results_message . '</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($total_pages > 1) { ?>
                        <nav aria-label="Ticket pagination">
                            <ul class="pagination justify-content-center mt-4">
                                <!-- Previous Button -->
                                <?php if ($page > 1) { ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&size=<?php echo $records_per_page; ?><?php echo $search_params; ?>">
                                    <i class="fa fa-angle-double-left" aria-hidden="true"></i>
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
                                        <i class="fa fa-angle-double-right" aria-hidden="true"></i>
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
console.log('Ticket Management with Pagination loaded');
</script>

<?php include 'include/footer.php'; ?>
</body>
</html>