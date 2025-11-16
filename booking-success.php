<?php
require_once 'config/config.php';

$page_title = 'Booking Confirmed';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect(SITE_URL . '/auth/login.php');
}

// For demo purposes, we'll show a success page
$booking_id = 'PK' . date('Ymd') . rand(1000, 9999);
$demo_mode = isset($_GET['demo']);

// Clear cart after successful booking
if ($demo_mode) {
    $_SESSION['cart'] = [];
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Success Message -->
            <div class="text-center mb-5">
                <div class="success-icon mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                </div>
                <h2 class="fw-bold text-success mb-3">Booking Confirmed!</h2>
                <p class="lead text-muted">
                    Your parking reservation has been successfully processed.
                </p>
            </div>
            
            <!-- Booking Details Card -->
            <div class="card border-0 shadow-lg mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-ticket-alt me-2"></i>Booking Details
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="booking-info">
                                <h6 class="fw-bold text-muted mb-3">BOOKING INFORMATION</h6>
                                <div class="info-item mb-3">
                                    <label class="text-muted small">Booking ID</label>
                                    <div class="fw-bold fs-5 text-primary"><?php echo $booking_id; ?></div>
                                </div>
                                <div class="info-item mb-3">
                                    <label class="text-muted small">Status</label>
                                    <div><span class="badge bg-success fs-6">Confirmed</span></div>
                                </div>
                                <div class="info-item mb-3">
                                    <label class="text-muted small">Booking Date</label>
                                    <div class="fw-bold"><?php echo date('M j, Y g:i A'); ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="qr-code text-center">
                                <h6 class="fw-bold text-muted mb-3">QR CODE</h6>
                                <div class="qr-placeholder bg-light rounded p-4 mb-3">
                                    <i class="fas fa-qrcode text-muted" style="font-size: 4rem;"></i>
                                    <div class="mt-2 small text-muted">Scan at parking entrance</div>
                                </div>
                                <button class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-download me-1"></i>Download QR
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Demo Notice -->
            <?php if ($demo_mode): ?>
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Demo Mode:</strong> This is a demonstration booking. No actual payment was processed.
            </div>
            <?php endif; ?>
            
            <!-- Next Steps -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-list-check text-primary me-2"></i>What's Next?
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="step-item text-center p-3">
                                <i class="fas fa-mobile-alt text-primary mb-2" style="font-size: 2rem;"></i>
                                <h6 class="fw-bold">Save QR Code</h6>
                                <p class="text-muted small mb-0">Download or screenshot your QR code for easy access</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="step-item text-center p-3">
                                <i class="fas fa-car text-primary mb-2" style="font-size: 2rem;"></i>
                                <h6 class="fw-bold">Arrive on Time</h6>
                                <p class="text-muted small mb-0">Reach the parking location at your reserved time</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="step-item text-center p-3">
                                <i class="fas fa-qrcode text-primary mb-2" style="font-size: 2rem;"></i>
                                <h6 class="fw-bold">Scan & Park</h6>
                                <p class="text-muted small mb-0">Show your QR code at the entrance and park</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="text-center">
                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                    <a href="bookings.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-calendar me-2"></i>View All Bookings
                    </a>
                    <a href="parking.php" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-search me-2"></i>Book More Parking
                    </a>
                    <a href="<?php echo SITE_URL; ?>" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-home me-2"></i>Go to Home
                    </a>
                </div>
            </div>
            
            <!-- Support Info -->
            <div class="text-center mt-5">
                <div class="card border-0 bg-light">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-2">Need Help?</h6>
                        <p class="text-muted mb-3">Our support team is available 24/7 to assist you</p>
                        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                            <a href="contact.php" class="btn btn-outline-primary">
                                <i class="fas fa-envelope me-2"></i>Contact Support
                            </a>
                            <a href="tel:+919876543210" class="btn btn-outline-success">
                                <i class="fas fa-phone me-2"></i>Call +91 98765 43210
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$additional_css = "
<style>
.success-icon {
    animation: bounceIn 0.8s ease-out;
}

@keyframes bounceIn {
    0% { transform: scale(0.3); opacity: 0; }
    50% { transform: scale(1.05); }
    70% { transform: scale(0.9); }
    100% { transform: scale(1); opacity: 1; }
}

.step-item {
    transition: transform 0.3s ease;
}

.step-item:hover {
    transform: translateY(-5px);
}

.qr-placeholder {
    border: 2px dashed #dee2e6;
}
</style>
";

include 'includes/footer.php';
?>
