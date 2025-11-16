<?php
require_once 'config/config.php';

$page_title = 'My Bookings';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect(SITE_URL . '/auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$user_id = $_SESSION['user_id'];

// Get user bookings (for demo, we'll show sample data)
$db = getDB();
$stmt = $db->prepare("
    SELECT b.*, pl.title as parking_title, pl.address as parking_address 
    FROM bookings b 
    JOIN parking_locations pl ON b.parking_location_id = pl.id 
    WHERE b.user_id = ? 
    ORDER BY b.created_at DESC
");
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll();

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="bg-primary text-white py-4 d-none d-md-block">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-0 fw-bold">
                    <i class="fas fa-calendar-check me-2"></i>My Bookings
                </h4>
                <p class="mb-0 opacity-75">
                    Manage your parking reservations
                </p>
            </div>
            <div class="col-md-4 text-end">
                <a href="parking.php" class="btn btn-light">
                    <i class="fas fa-plus me-2"></i>New Booking
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Header -->
<div class="bg-white border-bottom p-3 d-md-none">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h6 class="mb-0 fw-bold">My Bookings</h6>
            <small class="text-muted"><?php echo count($bookings); ?> bookings</small>
        </div>
        <a href="parking.php" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>New
        </a>
    </div>
</div>

<div class="container py-4">
    <?php if (empty($bookings)): ?>
        <!-- No Bookings -->
        <div class="text-center py-5">
            <i class="fas fa-calendar-times text-muted display-1 mb-4"></i>
            <h3 class="text-muted mb-3">No bookings yet</h3>
            <p class="text-muted mb-4">Start by finding and booking your first parking spot!</p>
            <a href="parking.php" class="btn btn-primary btn-lg">
                <i class="fas fa-search me-2"></i>Find Parking
            </a>
        </div>
    <?php else: ?>
        <!-- Bookings List -->
        <div class="row g-4">
            <?php foreach ($bookings as $booking): ?>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 fw-bold">Booking #<?php echo $booking['booking_id']; ?></h6>
                                <small class="text-muted"><?php echo timeAgo($booking['created_at']); ?></small>
                            </div>
                            <span class="badge bg-<?php 
                                echo $booking['status'] === 'active' ? 'success' : 
                                    ($booking['status'] === 'completed' ? 'primary' : 'secondary'); 
                            ?>">
                                <?php echo ucfirst($booking['status']); ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <h6 class="fw-bold mb-2"><?php echo htmlspecialchars($booking['parking_title']); ?></h6>
                            <p class="text-muted small mb-3">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                <?php echo htmlspecialchars($booking['parking_address']); ?>
                            </p>
                            
                            <div class="booking-details mb-3">
                                <div class="row g-2 text-sm">
                                    <div class="col-6">
                                        <div class="detail-item">
                                            <i class="fas fa-calendar text-primary me-2"></i>
                                            <span class="text-muted">Date:</span>
                                            <div class="fw-bold"><?php echo date('M j, Y', strtotime($booking['start_time'])); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="detail-item">
                                            <i class="fas fa-clock text-primary me-2"></i>
                                            <span class="text-muted">Time:</span>
                                            <div class="fw-bold">
                                                <?php echo date('g:i A', strtotime($booking['start_time'])); ?> - 
                                                <?php echo date('g:i A', strtotime($booking['end_time'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="detail-item">
                                            <i class="fas fa-hourglass-half text-primary me-2"></i>
                                            <span class="text-muted">Duration:</span>
                                            <div class="fw-bold"><?php echo $booking['hours']; ?> hours</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="detail-item">
                                            <i class="fas fa-rupee-sign text-primary me-2"></i>
                                            <span class="text-muted">Amount:</span>
                                            <div class="fw-bold text-success"><?php echo formatCurrency($booking['final_amount']); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm flex-fill" 
                                        onclick="showBookingDetails('<?php echo $booking['booking_id']; ?>')">
                                    <i class="fas fa-eye me-1"></i>Details
                                </button>
                                <?php if ($booking['status'] === 'active'): ?>
                                    <button class="btn btn-primary btn-sm" onclick="showQRCode('<?php echo $booking['booking_id']; ?>')">
                                        <i class="fas fa-qrcode me-1"></i>QR Code
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Demo Notice -->
        <div class="alert alert-info mt-4" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Demo Mode:</strong> This page shows sample booking data. In the full version, your actual bookings would be displayed here.
        </div>
    <?php endif; ?>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-qrcode me-2"></i>Booking QR Code
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="qr-code-display mb-3">
                    <div class="qr-placeholder bg-light rounded p-5">
                        <i class="fas fa-qrcode text-muted" style="font-size: 6rem;"></i>
                        <div class="mt-3">
                            <h6 class="fw-bold" id="qrBookingId">Booking #PK20241116001</h6>
                            <p class="text-muted small">Show this QR code at the parking entrance</p>
                        </div>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary">
                        <i class="fas fa-download me-2"></i>Download QR Code
                    </button>
                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-share me-2"></i>Share QR Code
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-receipt me-2"></i>Booking Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="booking-receipt">
                    <div class="text-center mb-4">
                        <h4 class="fw-bold text-primary">ParkReserve</h4>
                        <p class="text-muted">Parking Reservation Receipt</p>
                    </div>
                    
                    <div class="receipt-details">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted small">Booking ID</label>
                                <div class="fw-bold" id="detailBookingId">PK20241116001</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Status</label>
                                <div><span class="badge bg-success">Active</span></div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Parking Location</label>
                                <div class="fw-bold">City Center Mall Parking</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Payment Status</label>
                                <div><span class="badge bg-success">Completed</span></div>
                            </div>
                        </div>
                        
                        <hr class="my-3">
                        
                        <div class="price-breakdown">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Parking Fee (2 hours):</span>
                                <span>₹100.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Service Fee:</span>
                                <span>₹5.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax (18%):</span>
                                <span>₹18.00</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total Paid:</span>
                                <span class="text-success">₹123.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary">
                    <i class="fas fa-download me-2"></i>Download Receipt
                </button>
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php
$additional_js = "
<script>
function showQRCode(bookingId) {
    document.getElementById('qrBookingId').textContent = 'Booking #' + bookingId;
    new bootstrap.Modal(document.getElementById('qrModal')).show();
}

function showBookingDetails(bookingId) {
    document.getElementById('detailBookingId').textContent = bookingId;
    new bootstrap.Modal(document.getElementById('detailsModal')).show();
}
</script>
";

include 'includes/footer.php';
?>
