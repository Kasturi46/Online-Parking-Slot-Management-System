<?php
require_once 'config/config.php';

$page_title = 'Find Parking';

// Get search parameters
$location = isset($_GET['location']) ? sanitizeInput($_GET['location']) : '';
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$time = isset($_GET['time']) ? $_GET['time'] : date('H:i');
$category = isset($_GET['category']) ? $_GET['category'] : '';
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 1000;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'price_asc';

// Build query
$db = getDB();
$where_conditions = ["status = 'active'"];
$params = [];

if ($location) {
    $where_conditions[] = "(title LIKE ? OR address LIKE ?)";
    $params[] = "%$location%";
    $params[] = "%$location%";
}

if ($category) {
    $where_conditions[] = "category = ?";
    $params[] = $category;
}

if ($min_price > 0) {
    $where_conditions[] = "price_per_hour >= ?";
    $params[] = $min_price;
}

if ($max_price < 1000) {
    $where_conditions[] = "price_per_hour <= ?";
    $params[] = $max_price;
}

// Sort options
$order_by = "created_at DESC";
switch ($sort) {
    case 'price_asc':
        $order_by = "price_per_hour ASC";
        break;
    case 'price_desc':
        $order_by = "price_per_hour DESC";
        break;
    case 'name_asc':
        $order_by = "title ASC";
        break;
    case 'availability':
        $order_by = "available_slots DESC";
        break;
}

$where_clause = implode(' AND ', $where_conditions);
$query = "SELECT * FROM parking_locations WHERE $where_clause ORDER BY $order_by";

$stmt = $db->prepare($query);
$stmt->execute($params);
$parking_locations = $stmt->fetchAll();

include 'includes/header.php';
?>

<!-- Search Header -->
<div class="bg-primary text-white py-4 d-none d-md-block">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-0 fw-bold">
                    <i class="fas fa-search me-2"></i>Find Parking
                </h4>
                <p class="mb-0 opacity-75">
                    <?php echo count($parking_locations); ?> parking locations found
                    <?php if ($location): ?>
                        for "<?php echo htmlspecialchars($location); ?>"
                    <?php endif; ?>
                </p>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#filterModal">
                    <i class="fas fa-filter me-2"></i>Filters
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Search Header -->
<div class="bg-white border-bottom p-3 d-md-none">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h6 class="mb-0 fw-bold">Find Parking</h6>
            <small class="text-muted"><?php echo count($parking_locations); ?> locations</small>
        </div>
        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
            <i class="fas fa-filter"></i>
        </button>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <!-- Desktop Sidebar Filters -->
        <div class="col-lg-3 d-none d-lg-block">
            <div class="sticky-top" style="top: 20px;">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-filter me-2"></i>Filters
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" id="filterForm">
                            <!-- Location Search -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Location</label>
                                <input type="text" class="form-control" name="location" 
                                       value="<?php echo htmlspecialchars($location); ?>" 
                                       placeholder="Enter location">
                            </div>
                            
                            <!-- Date & Time -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Date & Time</label>
                                <div class="row g-2">
                                    <div class="col-12">
                                        <input type="date" class="form-control" name="date" 
                                               value="<?php echo $date; ?>">
                                    </div>
                                    <div class="col-12">
                                        <input type="time" class="form-control" name="time" 
                                               value="<?php echo $time; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Category -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Category</label>
                                <select class="form-select" name="category">
                                    <option value="">All Categories</option>
                                    <option value="covered" <?php echo $category === 'covered' ? 'selected' : ''; ?>>
                                        Covered Parking
                                    </option>
                                    <option value="open" <?php echo $category === 'open' ? 'selected' : ''; ?>>
                                        Open Parking
                                    </option>
                                </select>
                            </div>
                            
                            <!-- Price Range -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Price Range (per hour)</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="number" class="form-control" name="min_price" 
                                               value="<?php echo $min_price; ?>" placeholder="Min" min="0">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" class="form-control" name="max_price" 
                                               value="<?php echo $max_price; ?>" placeholder="Max" min="0">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sort -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Sort By</label>
                                <select class="form-select" name="sort">
                                    <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>
                                        Price: Low to High
                                    </option>
                                    <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>
                                        Price: High to Low
                                    </option>
                                    <option value="name_asc" <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>>
                                        Name: A to Z
                                    </option>
                                    <option value="availability" <?php echo $sort === 'availability' ? 'selected' : ''; ?>>
                                        Most Available
                                    </option>
                                </select>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>Apply Filters
                                </button>
                                <a href="parking.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Clear All
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="p-3 p-lg-4">
                <!-- Results Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="mb-1 fw-bold">Available Parking Locations</h5>
                        <p class="text-muted mb-0">
                            Showing <?php echo count($parking_locations); ?> results
                            <?php if ($date && $time): ?>
                                for <?php echo date('M j, Y', strtotime($date)); ?> at <?php echo date('g:i A', strtotime($time)); ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <!-- Mobile Sort -->
                    <div class="d-lg-none">
                        <select class="form-select form-select-sm" onchange="updateSort(this.value)">
                            <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Price ↑</option>
                            <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Price ↓</option>
                            <option value="name_asc" <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>>Name ↑</option>
                            <option value="availability" <?php echo $sort === 'availability' ? 'selected' : ''; ?>>Available</option>
                        </select>
                    </div>
                </div>
                
                <!-- Parking Locations Grid -->
                <?php if (empty($parking_locations)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-search text-muted display-1 mb-3"></i>
                        <h4 class="text-muted mb-3">No parking locations found</h4>
                        <p class="text-muted mb-4">Try adjusting your search criteria or browse all available locations.</p>
                        <a href="parking.php" class="btn btn-primary">
                            <i class="fas fa-refresh me-2"></i>View All Locations
                        </a>
                    </div>
                <?php else: ?>
                    <div class="parking-card-grid">
                        <?php foreach ($parking_locations as $location): ?>
                            <div class="parking-card bg-white shadow-sm fade-in">
                                <div class="parking-card-image" 
                                     style="background-image: url('<?php echo SITE_URL; ?>/assets/images/parking-default.jpg');">
                                    <div class="parking-card-badge">
                                        <span class="text-<?php echo $location['category'] == 'covered' ? 'success' : 'primary'; ?>">
                                            <i class="fas fa-<?php echo $location['category'] == 'covered' ? 'home' : 'sun'; ?> me-1"></i>
                                            <?php echo ucfirst($location['category']); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="card-body p-3">
                                    <h6 class="card-title fw-bold mb-2">
                                        <?php echo htmlspecialchars($location['title']); ?>
                                    </h6>
                                    
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-map-marker-alt me-1 text-primary"></i>
                                        <?php echo htmlspecialchars(substr($location['address'], 0, 60)) . (strlen($location['address']) > 60 ? '...' : ''); ?>
                                    </p>
                                    
                                    <!-- Features -->
                                    <?php if ($location['features']): ?>
                                        <div class="mb-2">
                                            <?php 
                                            $features = json_decode($location['features'], true);
                                            if ($features && is_array($features)):
                                                foreach (array_slice($features, 0, 3) as $feature):
                                            ?>
                                                <span class="badge bg-light text-dark me-1 mb-1">
                                                    <?php echo htmlspecialchars($feature); ?>
                                                </span>
                                            <?php 
                                                endforeach;
                                                if (count($features) > 3):
                                            ?>
                                                <span class="badge bg-secondary">+<?php echo count($features) - 3; ?> more</span>
                                            <?php endif; endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="price">
                                            <span class="fw-bold text-primary fs-5">
                                                <?php echo formatCurrency($location['price_per_hour']); ?>
                                            </span>
                                            <small class="text-muted">/hour</small>
                                        </div>
                                        <div class="availability">
                                            <span class="badge bg-<?php echo $location['available_slots'] > 10 ? 'success' : ($location['available_slots'] > 0 ? 'warning' : 'danger'); ?>">
                                                <?php echo $location['available_slots']; ?> available
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <a href="parking-detail.php?id=<?php echo $location['id']; ?>" 
                                           class="btn btn-outline-primary flex-fill btn-sm">
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </a>
                                        
                                        <?php if (isLoggedIn() && $location['available_slots'] > 0): ?>
                                            <button class="btn btn-primary btn-sm px-3" 
                                                    onclick="quickBook(<?php echo $location['id']; ?>)"
                                                    title="Quick Book">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        <?php elseif (!isLoggedIn()): ?>
                                            <a href="auth/login.php" class="btn btn-primary btn-sm px-3" title="Login to Book">
                                                <i class="fas fa-sign-in-alt"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-filter me-2"></i>Filter Results
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="GET" id="mobileFilterForm">
                    <!-- Same form fields as desktop sidebar -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Location</label>
                        <input type="text" class="form-control" name="location" 
                               value="<?php echo htmlspecialchars($location); ?>" 
                               placeholder="Enter location">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Date</label>
                        <input type="date" class="form-control" name="date" value="<?php echo $date; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Time</label>
                        <input type="time" class="form-control" name="time" value="<?php echo $time; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Category</label>
                        <select class="form-select" name="category">
                            <option value="">All Categories</option>
                            <option value="covered" <?php echo $category === 'covered' ? 'selected' : ''; ?>>Covered</option>
                            <option value="open" <?php echo $category === 'open' ? 'selected' : ''; ?>>Open</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Price Range</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" class="form-control" name="min_price" 
                                       value="<?php echo $min_price; ?>" placeholder="Min">
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control" name="max_price" 
                                       value="<?php echo $max_price; ?>" placeholder="Max">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                    Clear All
                </button>
                <button type="button" class="btn btn-primary" onclick="applyMobileFilters()">
                    Apply Filters
                </button>
            </div>
        </div>
    </div>
</div>

<?php
$additional_js = "
<script>
function updateSort(sortValue) {
    const url = new URL(window.location);
    url.searchParams.set('sort', sortValue);
    window.location.href = url.toString();
}

function clearFilters() {
    window.location.href = 'parking.php';
}

function applyMobileFilters() {
    document.getElementById('mobileFilterForm').submit();
}

function quickBook(parkingId) {
    if (!confirm('Add this parking location to your cart for quick booking?')) {
        return;
    }
    
    const now = new Date();
    const startTime = new Date(now.getTime() + 30 * 60000);
    const endTime = new Date(startTime.getTime() + 2 * 60 * 60000);
    
    addToCart(
        parkingId, 
        2, 
        startTime.toISOString().slice(0, 16), 
        endTime.toISOString().slice(0, 16)
    );
}

// Auto-submit form on desktop when filters change
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        const inputs = filterForm.querySelectorAll('input, select');
        inputs.forEach(input => {
            if (input.type !== 'submit') {
                input.addEventListener('change', function() {
                    // Debounce for text inputs
                    if (this.type === 'text') {
                        clearTimeout(this.timeout);
                        this.timeout = setTimeout(() => {
                            filterForm.submit();
                        }, 500);
                    } else {
                        filterForm.submit();
                    }
                });
            }
        });
    }
});
</script>
";

include 'includes/footer.php';
?>
