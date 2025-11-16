<?php
require_once 'config/config.php';

$page_title = 'Checkout';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect(SITE_URL . '/auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    redirect(SITE_URL . '/cart.php');
}

$cart_items = $_SESSION['cart'];
$subtotal = 0;

// Calculate totals
foreach ($cart_items as $item) {
    $subtotal += $item['total_amount'];
}

$service_fee = $subtotal * 0.05;
$tax_rate = (float)getSetting('tax_rate', 18) / 100;
$tax_amount = $subtotal * $tax_rate;
$total = $subtotal + $service_fee + $tax_amount;

$user = getCurrentUser();

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="bg-success text-white py-4 d-none d-md-block">
    <div class="container">
        <h4 class="mb-0 fw-bold">
            <i class="fas fa-credit-card me-2"></i>Secure Checkout
        </h4>
        <p class="mb-0 opacity-75">Complete your parking reservation</p>
    </div>
</div>

<div class="container py-4">
    <div class="row">
        <!-- Checkout Form -->
        <div class="col-lg-8">
            <form id="checkoutForm" method="POST" action="process-booking.php">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <!-- Contact Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-user me-2 text-primary"></i>Contact Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Full Name *</label>
                                <input type="text" class="form-control" name="name" 
                                       value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email Address *</label>
                                <input type="email" class="form-control" name="email" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Phone Number *</label>
                                <input type="tel" class="form-control" name="phone" 
                                       value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" 
                                       placeholder="+91 98765 43210" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Vehicle Number</label>
                                <input type="text" class="form-control" name="vehicle_number" 
                                       placeholder="DL 01 AB 1234">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Method -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-credit-card me-2 text-primary"></i>Payment Method
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="payment-methods">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" 
                                       id="razorpay" value="razorpay" checked>
                                <label class="form-check-label d-flex align-items-center" for="razorpay">
                                    <div class="payment-option p-3 border rounded w-100 ms-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-credit-card text-primary me-3 fs-4"></i>
                                            <div>
                                                <h6 class="mb-1 fw-bold">Credit/Debit Card & UPI</h6>
                                                <small class="text-muted">Secure payment via Razorpay</small>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" 
                                       id="wallet" value="wallet">
                                <label class="form-check-label d-flex align-items-center" for="wallet">
                                    <div class="payment-option p-3 border rounded w-100 ms-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-wallet text-success me-3 fs-4"></i>
                                            <div>
                                                <h6 class="mb-1 fw-bold">Digital Wallets</h6>
                                                <small class="text-muted">PayTM, PhonePe, Google Pay</small>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" 
                                       id="netbanking" value="netbanking">
                                <label class="form-check-label d-flex align-items-center" for="netbanking">
                                    <div class="payment-option p-3 border rounded w-100 ms-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-university text-info me-3 fs-4"></i>
                                            <div>
                                                <h6 class="mb-1 fw-bold">Net Banking</h6>
                                                <small class="text-muted">All major banks supported</small>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Terms and Conditions -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="terms.php" target="_blank">Terms of Service</a> 
                                and <a href="privacy.php" target="_blank">Privacy Policy</a>
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="notifications" name="notifications">
                            <label class="form-check-label" for="notifications">
                                Send me booking confirmations and updates via SMS/Email
                            </label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 20px;">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-receipt me-2"></i>Order Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Cart Items -->
                        <div class="order-items mb-3">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="order-item border-bottom pb-3 mb-3">
                                    <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($item['parking_title']); ?></h6>
                                    <p class="text-muted small mb-1">
                                        <?php echo htmlspecialchars(substr($item['parking_address'], 0, 40)) . '...'; ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small">
                                            <?php echo date('M j, Y g:i A', strtotime($item['start_time'])); ?>
                                            <br>
                                            <?php echo $item['hours']; ?> hour<?php echo $item['hours'] > 1 ? 's' : ''; ?>
                                        </span>
                                        <span class="fw-bold text-primary">
                                            <?php echo formatCurrency($item['total_amount']); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Price Breakdown -->
                        <div class="price-breakdown">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span><?php echo formatCurrency($subtotal); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Service Fee:</span>
                                <span><?php echo formatCurrency($service_fee); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Tax (<?php echo getSetting('tax_rate', 18); ?>%):</span>
                                <span><?php echo formatCurrency($tax_amount); ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="fs-5 fw-bold">Total:</span>
                                <span class="fs-4 fw-bold text-success"><?php echo formatCurrency($total); ?></span>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            <button type="submit" form="checkoutForm" class="btn btn-success btn-lg" id="payBtn">
                                <i class="fas fa-lock me-2"></i>Pay Securely
                            </button>
                            <a href="cart.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Cart
                            </a>
                        </div>
                        
                        <!-- Security Info -->
                        <div class="security-info mt-3 p-3 bg-light rounded">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-shield-alt text-success me-2"></i>
                                <small class="fw-bold">Secure Payment</small>
                            </div>
                            <small class="text-muted">
                                Your payment information is encrypted and secure. 
                                We never store your card details.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$additional_js = "
<script>
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const terms = document.getElementById('terms').checked;
    if (!terms) {
        showNotification('Please accept the terms and conditions', 'error');
        return;
    }
    
    const payBtn = document.getElementById('payBtn');
    const originalText = payBtn.innerHTML;
    
    showLoading('payBtn');
    
    // Simulate payment processing
    setTimeout(() => {
        // In a real application, this would integrate with the payment gateway
        showNotification('Payment processing... This is a demo version.', 'info');
        
        setTimeout(() => {
            hideLoading('payBtn', originalText);
            // Redirect to success page
            window.location.href = 'booking-success.php?demo=1';
        }, 2000);
    }, 1000);
});

// Payment method selection styling
document.querySelectorAll('input[name=\"payment_method\"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.payment-option').forEach(option => {
            option.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
        });
        
        if (this.checked) {
            const option = this.parentNode.querySelector('.payment-option');
            option.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
        }
    });
});

// Initialize first payment method as selected
document.querySelector('input[name=\"payment_method\"]:checked').dispatchEvent(new Event('change'));
</script>
";

include 'includes/footer.php';
?>
