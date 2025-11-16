<?php
require_once 'config/config.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect(SITE_URL . '/auth/login.php');
}

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    redirect(SITE_URL . '/cart.php?error=empty_cart');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        redirect(SITE_URL . '/checkout.php?error=invalid_token');
    }
    
    $user_id = $_SESSION['user_id'];
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $vehicle_number = sanitizeInput($_POST['vehicle_number']);
    $payment_method = sanitizeInput($_POST['payment_method']);
    
    // Validate required fields
    if (empty($name) || empty($email) || empty($phone) || empty($payment_method)) {
        redirect(SITE_URL . '/checkout.php?error=missing_fields');
    }
    
    if (!isset($_POST['terms'])) {
        redirect(SITE_URL . '/checkout.php?error=terms_required');
    }
    
    $db = getDB();
    
    try {
        $db->beginTransaction();
        
        // Process each cart item
        foreach ($_SESSION['cart'] as $item) {
            $booking_id = generateBookingId();
            
            // Calculate amounts
            $subtotal = $item['total_amount'];
            $service_fee = getSetting('service_fee', 5);
            $tax_rate = getSetting('tax_rate', 18);
            $tax_amount = ($subtotal + $service_fee) * ($tax_rate / 100);
            $final_amount = $subtotal + $service_fee + $tax_amount;
            
            // Insert booking
            $stmt = $db->prepare("
                INSERT INTO bookings (
                    booking_id, user_id, parking_location_id, start_time, end_time, 
                    hours, subtotal, service_fee, tax_amount, final_amount, 
                    payment_method, payment_status, status, vehicle_number
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'completed', 'active', ?)
            ");
            
            $stmt->execute([
                $booking_id,
                $user_id,
                $item['parking_id'],
                $item['start_time'],
                $item['end_time'],
                $item['hours'],
                $subtotal,
                $service_fee,
                $tax_amount,
                $final_amount,
                $payment_method,
                $vehicle_number
            ]);
            
            // Update parking slot availability
            $stmt = $db->prepare("
                UPDATE parking_locations 
                SET available_slots = available_slots - 1 
                WHERE id = ? AND available_slots > 0
            ");
            $stmt->execute([$item['parking_id']]);
        }
        
        $db->commit();
        
        // Clear cart
        $_SESSION['cart'] = [];
        
        // Redirect to success page
        redirect(SITE_URL . '/booking-success.php?demo=1');
        
    } catch (Exception $e) {
        $db->rollback();
        redirect(SITE_URL . '/checkout.php?error=booking_failed');
    }
} else {
    redirect(SITE_URL . '/checkout.php');
}
?>
