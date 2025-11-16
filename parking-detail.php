<?php
require_once 'config/config.php';

$parking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$parking_id) {
    redirect(SITE_URL . '/parking.php');
}

// Get parking location details
$db = getDB();
$stmt = $db->prepare("SELECT * FROM parking_locations WHERE id = ? AND status = 'active'");
$stmt->execute([$parking_id]);
$parking = $stmt->fetch();

if (!$parking) {
    redirect(SITE_URL . '/parking.php');
}

$page_title = $parking['title'];

// Get related parking locations
$stmt = $db->prepare("SELECT * FROM parking_locations WHERE id != ? AND status = 'active' ORDER BY RAND() LIMIT 3");
$stmt->execute([$parking_id]);
$related_locations = $stmt->fetchAll();

// Parse features
$features = json_decode($parking['features'], true) ?: [];
$images = json_decode($parking['images'], true) ?: [];

include 'includes/header.php';
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="bg-light py-2 d-none d-md-block">
    <div class="container">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/parking.php">Find Parking</a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($parking['title']); ?></li>
        </ol>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Image Gallery -->
            <div class="parking-gallery mb-4">
                <div class="main-image position-relative">
                    <img src="https://picsum.photos/800/400?random=<?php echo $parking['id']; ?>" 
                         class="img-fluid rounded-3 w-100" 
                         alt="<?php echo htmlspecialchars($parking['title']); ?>"
                         style="height: 400px; object-fit: cover;">
                    
                    <!-- Category Badge -->
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-<?php echo $parking['category'] == 'covered' ? 'success' : 'primary'; ?> fs-6 px-3 py-2">
                            <i class="fas fa-<?php echo $parking['category'] == 'covered' ? 'home' : 'sun'; ?> me-2"></i>
                            <?php echo ucfirst($parking['category']); ?> Parking
                        </span>
                    </div>
                    
                    <!-- Availability Badge -->
                    <div class="position-absolute bottom-0 start-0 m-3">
                        <span class="badge bg-<?php echo $parking['available_slots'] > 10 ? 'success' : ($parking['available_slots'] > 0 ? 'warning' : 'danger'); ?> fs-6 px-3 py-2">
                            <i class="fas fa-car me-2"></i>
                            <?php echo $parking['available_slots']; ?> Slots Available
                        </span>
                    </div>
                </div>
                
                <!-- Thumbnail Images -->
                <?php if (!empty($images) && count($images) > 1): ?>
                <div class="row g-2 mt-2">
                    <?php foreach (array_slice($images, 1, 4) as $index => $image): ?>
                    <div class="col-3">
                        <img src="https://picsum.photos/200/150?random=<?php echo $parking['id'] + $index; ?>" 
                             class="img-fluid rounded-2 w-100" 
                             style="height: 100px; object-fit: cover; cursor: pointer;"
                             onclick="changeMainImage(this.src)">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Parking Details -->
            <div class="parking-details">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h2 class="fw-bold mb-2"><?php echo htmlspecialchars($parking['title']); ?></h2>
                        <p class="text-muted mb-0">
                            <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                            <?php echo htmlspecialchars($parking['address']); ?>
                        </p>
                    </div>
                    <div class="text-end">
                        <div class="price-display">
                            <span class="display-6 fw-bold text-primary">
                                <?php echo formatCurrency($parking['price_per_hour']); ?>
                            </span>
                            <div class="text-muted">per hour</div>
                        </div>
                    </div>
                </div>
                
                <!-- Features -->
                <?php if (!empty($features)): ?>
                <div class="features-section mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-star text-warning me-2"></i>Features & Amenities
                    </h5>
                    <div class="row g-2">
                        <?php foreach ($features as $feature): ?>
                        <div class="col-md-6">
                            <div class="feature-item d-flex align-items-center p-2 bg-light rounded-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span><?php echo htmlspecialchars($feature); ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Description -->
                <?php if ($parking['description']): ?>
                <div class="description-section mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-info-circle text-info me-2"></i>Description
                    </h5>
                    <p class="text-muted lh-lg">
                        <?php echo nl2br(htmlspecialchars($parking['description'])); ?>
                    </p>
                </div>
                <?php endif; ?>
                
                <!-- Location Map -->
                <div class="map-section mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-map text-primary me-2"></i>Location
                    </h5>
                    <div class="map-placeholder bg-light rounded-3 d-flex align-items-center justify-content-center" 
                         style="height: 300px;">
                        <div class="text-center text-muted">
                            <i class="fas fa-map-marker-alt display-4 mb-3"></i>
                            <h6>Interactive Map</h6>
                            <p class="mb-0">Map integration with Google Maps or OpenStreetMap</p>
                            <?php if ($parking['latitude'] && $parking['longitude']): ?>
                            <small class="text-muted">
                                Coordinates: <?php echo $parking['latitude']; ?>, <?php echo $parking['longitude']; ?>
                            </small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Booking Sidebar -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 20px;">
                <!-- Booking Card -->
                <div class="card border-0 shadow-lg mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-calendar-check me-2"></i>Reserve Your Spot
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!isLoggedIn()): ?>
                            <div class="text-center mb-4">
                                <i class="fas fa-user-lock text-muted display-4 mb-3"></i>
                                <h6 class="text-muted">Login Required</h6>
                                <p class="text-muted small">Please login to make a reservation</p>
                                <a href="auth/login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" 
                                   class="btn btn-primary w-100">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login to Book
                                </a>
                            </div>
                        <?php elseif ($parking['available_slots'] <= 0): ?>
                            <div class="text-center mb-4">
                                <i class="fas fa-times-circle text-danger display-4 mb-3"></i>
                                <h6 class="text-danger">No Slots Available</h6>
                                <p class="text-muted small">This parking location is currently full</p>
                                <button class="btn btn-outline-primary w-100" onclick="notifyWhenAvailable()">
                                    <i class="fas fa-bell me-2"></i>Notify When Available
                                </button>
                            </div>
                        <?php else: ?>
                            <form id="bookingForm">
                                <input type="hidden" name="parking_id" value="<?php echo $parking['id']; ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                
                                <!-- Date Selection -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Select Date</label>
                                    <input type="date" class="form-control" id="booking_date" name="date" 
                                           value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                
                                <!-- Time Selection -->
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label fw-bold">Start Time</label>
                                        <input type="time" class="form-control" id="start_time" name="start_time" 
                                               value="<?php echo date('H:i'); ?>" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold">End Time</label>
                                        <input type="time" class="form-control" id="end_time" name="end_time" required>
                                    </div>
                                </div>
                                
                                <!-- Duration Display -->
                                <div class="mb-3">
                                    <div class="bg-light p-3 rounded-2">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Duration:</span>
                                            <span class="fw-bold" id="duration_display">0 hours</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Rate:</span>
                                            <span><?php echo formatCurrency($parking['price_per_hour']); ?>/hour</span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-bold">Total:</span>
                                            <span class="fw-bold text-primary fs-5" id="total_amount">
                                                <?php echo formatCurrency(0); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-primary btn-lg" onclick="addToCartFromDetail()">
                                        <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                    </button>
                                    <button type="button" class="btn btn-success btn-lg" onclick="bookNow()">
                                        <i class="fas fa-bolt me-2"></i>Book Now
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Quick Info -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-3">Quick Information</h6>
                        <div class="info-item d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Slots:</span>
                            <span class="fw-bold"><?php echo $parking['total_slots']; ?></span>
                        </div>
                        <div class="info-item d-flex justify-content-between mb-2">
                            <span class="text-muted">Available:</span>
                            <span class="fw-bold text-success"><?php echo $parking['available_slots']; ?></span>
                        </div>
                        <div class="info-item d-flex justify-content-between mb-2">
                            <span class="text-muted">Category:</span>
                            <span class="fw-bold"><?php echo ucfirst($parking['category']); ?></span>
                        </div>
                        <div class="info-item d-flex justify-content-between">
                            <span class="text-muted">Added:</span>
                            <span class="fw-bold"><?php echo timeAgo($parking['created_at']); ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Support -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 text-center">
                        <h6 class="fw-bold mb-2">Need Help?</h6>
                        <p class="text-muted small mb-3">Our support team is here to assist you</p>
                        <div class="d-grid gap-2">
                            <a href="contact.php" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-envelope me-2"></i>Contact Support
                            </a>
                            <a href="tel:+919876543210" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-phone me-2"></i>Call Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Related Locations -->
<?php if (!empty($related_locations)): ?>
<section class="py-5 bg-light">
    <div class="container">
        <h4 class="fw-bold mb-4">
            <i class="fas fa-map-marked-alt text-primary me-2"></i>
            Similar Parking Locations
        </h4>
        
        <div class="row g-4">
            <?php foreach ($related_locations as $location): ?>
            <div class="col-md-4">
                <div class="parking-card bg-white shadow-sm h-100">
                    <div class="parking-card-image" 
                         style="background-image: url('https://picsum.photos/300/200?random=<?php echo $location['id']; ?>');">
                        <div class="parking-card-badge">
                            <span class="text-<?php echo $location['category'] == 'covered' ? 'success' : 'primary'; ?>">
                                <?php echo ucfirst($location['category']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="card-body p-3">
                        <h6 class="card-title fw-bold mb-2"><?php echo htmlspecialchars($location['title']); ?></h6>
                        <p class="text-muted small mb-2">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            <?php echo htmlspecialchars(substr($location['address'], 0, 40)) . '...'; ?>
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="price">
                                <span class="fw-bold text-primary"><?php echo formatCurrency($location['price_per_hour']); ?></span>
                                <small class="text-muted">/hour</small>
                            </div>
                            <span class="badge bg-success"><?php echo $location['available_slots']; ?> available</span>
                        </div>
                        
                        <a href="parking-detail.php?id=<?php echo $location['id']; ?>" 
                           class="btn btn-outline-primary w-100 btn-sm">
                            <i class="fas fa-eye me-1"></i>View Details
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
const pricePerHour = <?php echo $parking['price_per_hour']; ?>;
const parkingId = <?php echo $parking['id']; ?>;

function changeMainImage(src) {
    document.querySelector('.main-image img').src = src;
}

function calculateTotal() {
    const startTime = document.getElementById('start_time').value;
    const endTime = document.getElementById('end_time').value;
    
    if (startTime && endTime) {
        const start = new Date('2000-01-01 ' + startTime);
        const end = new Date('2000-01-01 ' + endTime);
        
        if (end <= start) {
            end.setDate(end.getDate() + 1); // Next day
        }
        
        const diffMs = end - start;
        const hours = Math.ceil(diffMs / (1000 * 60 * 60));
        
        document.getElementById('duration_display').textContent = hours + ' hour' + (hours !== 1 ? 's' : '');
        document.getElementById('total_amount').textContent = '₹' + (pricePerHour * hours).toFixed(2);
        
        return hours;
    }
    
    return 0;
}

function addToCartFromDetail() {
    const hours = calculateTotal();
    if (hours <= 0) {
        showNotification('Please select valid start and end times', 'error');
        return;
    }
    
    const date = document.getElementById('booking_date').value;
    const startTime = document.getElementById('start_time').value;
    const endTime = document.getElementById('end_time').value;
    
    const startDateTime = date + 'T' + startTime;
    const endDateTime = date + 'T' + endTime;
    
    addToCart(parkingId, hours, startDateTime, endDateTime);
}

function bookNow() {
    const hours = calculateTotal();
    if (hours <= 0) {
        showNotification('Please select valid start and end times', 'error');
        return;
    }
    
    addToCartFromDetail();
    
    // Redirect to checkout after a short delay
    setTimeout(() => {
        window.location.href = '<?php echo SITE_URL; ?>/checkout.php';
    }, 1000);
}

function notifyWhenAvailable() {
    showNotification('You will be notified when slots become available', 'info');
}

// Auto-calculate total when times change
document.getElementById('start_time').addEventListener('change', calculateTotal);
document.getElementById('end_time').addEventListener('change', calculateTotal);

// Set default end time (2 hours after start time)
document.getElementById('start_time').addEventListener('change', function() {
    const startTime = this.value;
    if (startTime) {
        const start = new Date('2000-01-01 ' + startTime);
        start.setHours(start.getHours() + 2);
        const endTime = start.toTimeString().slice(0, 5);
        document.getElementById('end_time').value = endTime;
        calculateTotal();
    }
});

// Initial calculation
document.addEventListener('DOMContentLoaded', function() {
    // Set default end time
    const startInput = document.getElementById('start_time');
    if (startInput && startInput.value) {
        const start = new Date('2000-01-01 ' + startInput.value);
        start.setHours(start.getHours() + 2);
        const endTime = start.toTimeString().slice(0, 5);
        document.getElementById('end_time').value = endTime;
    }
    calculateTotal();
});
</script>

<?php include 'includes/footer.php'; ?>
