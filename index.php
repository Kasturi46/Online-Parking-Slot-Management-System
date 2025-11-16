<?php
require_once 'config/config.php';

$page_title = 'Find & Reserve Parking Spots';

// Get featured parking locations
$db = getDB();
$stmt = $db->prepare("SELECT * FROM parking_locations WHERE status = 'active' ORDER BY created_at DESC LIMIT 6");
$stmt->execute();
$featured_locations = $stmt->fetchAll();

// Check for welcome or logout messages
$welcome_message = isset($_GET['welcome']) ? 'Welcome to ParkReserve! Start exploring parking options.' : '';
$logout_message = isset($_GET['logout']) ? 'You have been successfully logged out.' : '';

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section position-relative overflow-hidden">
    <div class="container">
        <div class="row align-items-center min-vh-75">
            <div class="col-lg-6">
                <div class="hero-content text-white fade-in">
                    <h1 class="display-4 fw-bold mb-4">
                        Find Perfect <span class="text-warning">Parking</span> 
                        <br>Near You
                    </h1>
                    <p class="lead mb-4">
                        Reserve secure parking spots in advance. Save time, avoid hassle, 
                        and park with confidence at prime locations across the city.
                    </p>
                    
                    <!-- Quick Search Form -->
                    <div class="card bg-white text-dark shadow-lg border-0 mb-4">
                        <div class="card-body p-4">
                            <form action="parking.php" method="GET" class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Location</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt text-primary"></i></span>
                                        <input type="text" class="form-control" name="location" 
                                               placeholder="Enter location or landmark" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Date</label>
                                    <input type="date" class="form-control" name="date" 
                                           value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Time</label>
                                    <input type="time" class="form-control" name="time" 
                                           value="<?php echo date('H:i'); ?>" required>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg px-4 py-2 fw-bold">
                                        <i class="fas fa-search me-2"></i>Find Parking
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Stats -->
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="stat-item">
                                <h3 class="fw-bold mb-1">500+</h3>
                                <small>Parking Spots</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item">
                                <h3 class="fw-bold mb-1">50+</h3>
                                <small>Locations</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item">
                                <h3 class="fw-bold mb-1">24/7</h3>
                                <small>Support</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 d-none d-lg-block">
                <div class="hero-image text-center">
                    <div class="position-relative">
                        <div class="bg-white rounded-circle shadow-lg p-5 d-inline-block">
                            <i class="fas fa-car text-primary" style="font-size: 8rem;"></i>
                        </div>
                        <!-- Floating elements -->
                        <div class="position-absolute top-0 start-0 bg-success text-white rounded-pill px-3 py-2 shadow">
                            <i class="fas fa-check me-1"></i>Secure
                        </div>
                        <div class="position-absolute top-50 end-0 bg-warning text-dark rounded-pill px-3 py-2 shadow">
                            <i class="fas fa-clock me-1"></i>24/7
                        </div>
                        <div class="position-absolute bottom-0 start-50 translate-middle-x bg-info text-white rounded-pill px-3 py-2 shadow">
                            <i class="fas fa-shield-alt me-1"></i>Protected
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Background Pattern -->
    <div class="position-absolute top-0 end-0 opacity-10">
        <i class="fas fa-parking" style="font-size: 20rem; color: white;"></i>
    </div>
</section>

<!-- Welcome/Logout Messages -->
<?php if ($welcome_message): ?>
    <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo $welcome_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($logout_message): ?>
    <div class="alert alert-info alert-dismissible fade show m-3" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <?php echo $logout_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">Why Choose ParkReserve?</h2>
            <p class="lead text-muted">Experience the future of parking with our innovative features</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card text-center p-4 h-100 bg-white rounded-3 shadow-sm">
                    <div class="feature-icon bg-primary bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-search text-primary fs-2"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Easy Search</h5>
                    <p class="text-muted">Find parking spots near your destination with our smart search and filtering system.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card text-center p-4 h-100 bg-white rounded-3 shadow-sm">
                    <div class="feature-icon bg-success bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-shield-alt text-success fs-2"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Secure Booking</h5>
                    <p class="text-muted">Your parking spot is guaranteed with our secure booking system and payment protection.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card text-center p-4 h-100 bg-white rounded-3 shadow-sm">
                    <div class="feature-icon bg-warning bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-mobile-alt text-warning fs-2"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Mobile Friendly</h5>
                    <p class="text-muted">Access your bookings anywhere with our responsive design and mobile app experience.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card text-center p-4 h-100 bg-white rounded-3 shadow-sm">
                    <div class="feature-icon bg-info bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-clock text-info fs-2"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Real-time Updates</h5>
                    <p class="text-muted">Get instant notifications about your booking status and parking availability.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card text-center p-4 h-100 bg-white rounded-3 shadow-sm">
                    <div class="feature-icon bg-danger bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-credit-card text-danger fs-2"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Flexible Payment</h5>
                    <p class="text-muted">Multiple payment options including cards, wallets, and UPI for your convenience.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card text-center p-4 h-100 bg-white rounded-3 shadow-sm">
                    <div class="feature-icon bg-secondary bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-headset text-secondary fs-2"></i>
                    </div>
                    <h5 class="fw-bold mb-3">24/7 Support</h5>
                    <p class="text-muted">Round-the-clock customer support to help you with any parking-related queries.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Parking Locations -->
<?php if (!empty($featured_locations)): ?>
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-2">Featured Parking Locations</h2>
                <p class="text-muted mb-0">Popular parking spots in prime locations</p>
            </div>
            <a href="parking.php" class="btn btn-outline-primary">
                View All <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
        
        <div class="parking-card-grid">
            <?php foreach ($featured_locations as $location): ?>
                <div class="parking-card bg-white shadow-sm">
                    <div class="parking-card-image" 
                         style="background-image: url('<?php echo SITE_URL; ?>/assets/images/parking-default.jpg');">
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
                            <?php echo htmlspecialchars(substr($location['address'], 0, 50)) . '...'; ?>
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="price">
                                <span class="fw-bold text-primary fs-5"><?php echo formatCurrency($location['price_per_hour']); ?></span>
                                <small class="text-muted">/hour</small>
                            </div>
                            <div class="availability">
                                <span class="badge bg-success">
                                    <?php echo $location['available_slots']; ?> available
                                </span>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <a href="parking-detail.php?id=<?php echo $location['id']; ?>" 
                               class="btn btn-primary flex-fill btn-sm">
                                <i class="fas fa-eye me-1"></i>View Details
                            </a>
                            <?php if (isLoggedIn()): ?>
                                <button class="btn btn-outline-primary btn-sm" 
                                        onclick="quickBook(<?php echo $location['id']; ?>)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- How It Works -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">How It Works</h2>
            <p class="lead text-muted">Simple steps to secure your parking spot</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-3 text-center">
                <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 60px; height: 60px;">
                    <span class="fw-bold fs-4">1</span>
                </div>
                <h5 class="fw-bold mb-2">Search Location</h5>
                <p class="text-muted">Enter your destination and find nearby parking spots</p>
            </div>
            
            <div class="col-md-3 text-center">
                <div class="step-number bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 60px; height: 60px;">
                    <span class="fw-bold fs-4">2</span>
                </div>
                <h5 class="fw-bold mb-2">Choose & Book</h5>
                <p class="text-muted">Select your preferred spot and book for your desired time</p>
            </div>
            
            <div class="col-md-3 text-center">
                <div class="step-number bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 60px; height: 60px;">
                    <span class="fw-bold fs-4">3</span>
                </div>
                <h5 class="fw-bold mb-2">Pay Securely</h5>
                <p class="text-muted">Complete payment using your preferred method</p>
            </div>
            
            <div class="col-md-3 text-center">
                <div class="step-number bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 60px; height: 60px;">
                    <span class="fw-bold fs-4">4</span>
                </div>
                <h5 class="fw-bold mb-2">Park & Go</h5>
                <p class="text-muted">Show your QR code and enjoy hassle-free parking</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">What Our Users Say</h2>
            <p class="lead text-muted">Trusted by thousands of satisfied customers</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="testimonial-card bg-white p-4 rounded-3 shadow-sm h-100">
                    <div class="stars text-warning mb-3">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="mb-3">"ParkReserve saved me so much time! No more circling around looking for parking. The app is super easy to use."</p>
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 40px; height: 40px;">
                            <span class="fw-bold">RS</span>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Rahul Sharma</h6>
                            <small class="text-muted">Business Executive</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="testimonial-card bg-white p-4 rounded-3 shadow-sm h-100">
                    <div class="stars text-warning mb-3">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="mb-3">"Great service! The parking spots are exactly as described and the booking process is seamless. Highly recommended!"</p>
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 40px; height: 40px;">
                            <span class="fw-bold">PK</span>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Priya Kumari</h6>
                            <small class="text-muted">Software Developer</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="testimonial-card bg-white p-4 rounded-3 shadow-sm h-100">
                    <div class="stars text-warning mb-3">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="mb-3">"The customer support is excellent. They helped me when I had an issue with my booking. Very professional service."</p>
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 40px; height: 40px;">
                            <span class="fw-bold">AK</span>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Amit Kumar</h6>
                            <small class="text-muted">Marketing Manager</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h2 class="fw-bold mb-3">Ready to Park Smarter?</h2>
        <p class="lead mb-4">Join thousands of users who have made parking hassle-free with ParkReserve</p>
        
        <?php if (!isLoggedIn()): ?>
            <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                <a href="auth/signup.php" class="btn btn-light btn-lg px-4 py-2 fw-bold">
                    <i class="fas fa-user-plus me-2"></i>Get Started Free
                </a>
                <a href="parking.php" class="btn btn-outline-light btn-lg px-4 py-2 fw-bold">
                    <i class="fas fa-search me-2"></i>Browse Parking
                </a>
            </div>
        <?php else: ?>
            <a href="parking.php" class="btn btn-light btn-lg px-4 py-2 fw-bold">
                <i class="fas fa-search me-2"></i>Find Parking Now
            </a>
        <?php endif; ?>
    </div>
</section>

<?php
$additional_css = "
<style>
.min-vh-75 {
    min-height: 75vh;
}

.hero-section {
    background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
    position: relative;
}

.stat-item h3 {
    font-size: 2rem;
}

.feature-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
}

.step-number {
    font-size: 1.5rem;
}

.testimonial-card {
    transition: transform 0.3s ease;
}

.testimonial-card:hover {
    transform: translateY(-3px);
}

@media (max-width: 768px) {
    .hero-section {
        padding: 40px 0;
    }
    
    .display-4 {
        font-size: 2rem;
    }
    
    .lead {
        font-size: 1rem;
    }
}
</style>
";

$additional_js = "
<script>
function quickBook(parkingId) {
    if (!confirm('Add this parking location to your cart for quick booking?')) {
        return;
    }
    
    // Default to 2 hours from now
    const now = new Date();
    const startTime = new Date(now.getTime() + 30 * 60000); // 30 minutes from now
    const endTime = new Date(startTime.getTime() + 2 * 60 * 60000); // 2 hours later
    
    addToCart(
        parkingId, 
        2, 
        startTime.toISOString().slice(0, 16), 
        endTime.toISOString().slice(0, 16)
    );
}

// Auto-dismiss alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (alert.querySelector('.btn-close')) {
                alert.querySelector('.btn-close').click();
            }
        });
    }, 5000);
});
</script>
";

include 'includes/footer.php';
?>
