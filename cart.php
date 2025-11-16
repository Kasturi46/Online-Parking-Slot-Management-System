<?php
require_once 'config/config.php';

$page_title = 'Shopping Cart';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect(SITE_URL . '/auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart_items = $_SESSION['cart'];
$total_amount = 0;

// Calculate total
foreach ($cart_items as $item) {
    $total_amount += $item['total_amount'];
}

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'remove':
                $item_id = $_POST['item_id'];
                $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($item_id) {
                    return $item['id'] !== $item_id;
                });
                $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
                redirect(SITE_URL . '/cart.php?removed=1');
                break;
                
            case 'update':
                $item_id = $_POST['item_id'];
                $new_hours = (int)$_POST['hours'];
                
                if ($new_hours > 0) {
                    foreach ($_SESSION['cart'] as &$item) {
                        if ($item['id'] === $item_id) {
                            $item['hours'] = $new_hours;
                            $item['total_amount'] = $item['price_per_hour'] * $new_hours;
                            
                            // Update end time
                            $start = new DateTime($item['start_time']);
                            $end = clone $start;
                            $end->add(new DateInterval('PT' . $new_hours . 'H'));
                            $item['end_time'] = $end->format('Y-m-d\TH:i');
                            break;
                        }
                    }
                }
                redirect(SITE_URL . '/cart.php?updated=1');
                break;
                
            case 'clear':
                $_SESSION['cart'] = [];
                redirect(SITE_URL . '/cart.php?cleared=1');
                break;
        }
    }
}

// Recalculate total after updates
$cart_items = $_SESSION['cart'];
$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['total_amount'];
}

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="bg-primary text-white py-4 d-none d-md-block">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-0 fw-bold">
                    <i class="fas fa-shopping-cart me-2"></i>Shopping Cart
                </h4>
                <p class="mb-0 opacity-75">
                    <?php echo count($cart_items); ?> item<?php echo count($cart_items) !== 1 ? 's' : ''; ?> in your cart
                </p>
            </div>
            <div class="col-md-4 text-end">
                <?php if (!empty($cart_items)): ?>
                    <a href="checkout.php" class="btn btn-light">
                        <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Header -->
<div class="bg-white border-bottom p-3 d-md-none">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h6 class="mb-0 fw-bold">Shopping Cart</h6>
            <small class="text-muted"><?php echo count($cart_items); ?> items</small>
        </div>
        <?php if (!empty($cart_items)): ?>
            <a href="checkout.php" class="btn btn-primary btn-sm">
                <i class="fas fa-credit-card me-1"></i>Checkout
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="container py-4">
    <!-- Success Messages -->
    <?php if (isset($_GET['removed'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            Item removed from cart successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            Cart updated successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['cleared'])): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Cart cleared successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($cart_items)): ?>
        <!-- Empty Cart -->
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart text-muted display-1 mb-4"></i>
            <h3 class="text-muted mb-3">Your cart is empty</h3>
            <p class="text-muted mb-4">Add some parking reservations to get started!</p>
            <a href="parking.php" class="btn btn-primary btn-lg">
                <i class="fas fa-search me-2"></i>Find Parking
            </a>
        </div>
    <?php else: ?>
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">Cart Items</h5>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to clear your cart?')">
                            <input type="hidden" name="action" value="clear">
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-trash me-1"></i>Clear Cart
                            </button>
                        </form>
                    </div>
                    <div class="card-body p-0">
                        <?php foreach ($cart_items as $index => $item): ?>
                            <div class="cart-item border-bottom p-4 <?php echo $index === count($cart_items) - 1 ? 'border-0' : ''; ?>">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="d-flex">
                                            <div class="cart-item-image me-3">
                                                <img src="https://picsum.photos/80/60?random=<?php echo $item['parking_id']; ?>" 
                                                     class="rounded" alt="Parking" style="width: 80px; height: 60px; object-fit: cover;">
                                            </div>
                                            <div class="cart-item-details">
                                                <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($item['parking_title']); ?></h6>
                                                <p class="text-muted small mb-1">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    <?php echo htmlspecialchars(substr($item['parking_address'], 0, 50)) . '...'; ?>
                                                </p>
                                                <div class="text-muted small">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    <?php echo date('M j, Y', strtotime($item['start_time'])); ?>
                                                    <span class="mx-2">•</span>
                                                    <i class="fas fa-clock me-1"></i>
                                                    <?php echo date('g:i A', strtotime($item['start_time'])); ?> - 
                                                    <?php echo date('g:i A', strtotime($item['end_time'])); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2 text-center">
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                            <div class="input-group input-group-sm">
                                                <input type="number" class="form-control text-center" name="hours" 
                                                       value="<?php echo $item['hours']; ?>" min="1" max="24"
                                                       onchange="this.form.submit()">
                                                <span class="input-group-text">hrs</span>
                                            </div>
                                        </form>
                                    </div>
                                    
                                    <div class="col-md-2 text-center">
                                        <div class="price">
                                            <span class="fw-bold text-primary">
                                                <?php echo formatCurrency($item['total_amount']); ?>
                                            </span>
                                            <div class="text-muted small">
                                                <?php echo formatCurrency($item['price_per_hour']); ?>/hr
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2 text-center">
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Remove this item from cart?')">
                                            <input type="hidden" name="action" value="remove">
                                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 20px;">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-calculator me-2"></i>Order Summary
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="summary-item d-flex justify-content-between mb-2">
                                <span>Subtotal (<?php echo count($cart_items); ?> items):</span>
                                <span class="fw-bold"><?php echo formatCurrency($total_amount); ?></span>
                            </div>
                            
                            <div class="summary-item d-flex justify-content-between mb-2">
                                <span>Service Fee:</span>
                                <span class="fw-bold"><?php echo formatCurrency($total_amount * 0.05); ?></span>
                            </div>
                            
                            <?php 
                            $tax_rate = (float)getSetting('tax_rate', 18) / 100;
                            $tax_amount = $total_amount * $tax_rate;
                            ?>
                            <div class="summary-item d-flex justify-content-between mb-3">
                                <span>Tax (<?php echo getSetting('tax_rate', 18); ?>%):</span>
                                <span class="fw-bold"><?php echo formatCurrency($tax_amount); ?></span>
                            </div>
                            
                            <hr>
                            
                            <?php $final_total = $total_amount + ($total_amount * 0.05) + $tax_amount; ?>
                            <div class="summary-total d-flex justify-content-between mb-4">
                                <span class="fs-5 fw-bold">Total:</span>
                                <span class="fs-4 fw-bold text-primary"><?php echo formatCurrency($final_total); ?></span>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="checkout.php" class="btn btn-primary btn-lg">
                                    <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                                </a>
                                <a href="parking.php" class="btn btn-outline-primary">
                                    <i class="fas fa-plus me-2"></i>Add More Items
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Promo Code -->
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Have a Promo Code?</h6>
                            <form id="promoForm">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Enter promo code" id="promoCode">
                                    <button class="btn btn-outline-primary" type="submit">Apply</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$additional_js = "
<script>
document.getElementById('promoForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const promoCode = document.getElementById('promoCode').value;
    
    if (!promoCode.trim()) {
        showNotification('Please enter a promo code', 'error');
        return;
    }
    
    // Here you would typically send an AJAX request to validate the promo code
    showNotification('Promo code validation feature coming soon!', 'info');
});

// Auto-dismiss alerts
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (alert.querySelector('.btn-close')) {
                alert.querySelector('.btn-close').click();
            }
        });
    }, 3000);
});
</script>
";

include 'includes/footer.php';
?>
