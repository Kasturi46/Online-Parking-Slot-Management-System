<?php
require_once 'config/config.php';

$page_title = 'Contact Us';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $subject = sanitizeInput($_POST['subject']);
    $message = sanitizeInput($_POST['message']);
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Please fill in all fields';
    } elseif (!validateEmail($email)) {
        $error = 'Please enter a valid email address';
    } else {
        $db = getDB();
        $user_id = isLoggedIn() ? $_SESSION['user_id'] : null;
        
        $stmt = $db->prepare("INSERT INTO support_tickets (user_id, name, email, subject, message) VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$user_id, $name, $email, $subject, $message])) {
            $success = 'Your message has been sent successfully. We will get back to you soon!';
            // Clear form data
            $_POST = [];
        } else {
            $error = 'Failed to send message. Please try again.';
        }
    }
}

$user = isLoggedIn() ? getCurrentUser() : null;

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="bg-primary text-white py-5">
    <div class="container text-center">
        <h2 class="fw-bold mb-3">Contact Us</h2>
        <p class="lead mb-0">We're here to help! Get in touch with our support team.</p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5">
        <!-- Contact Form -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="mb-0 fw-bold">
                        <i class="fas fa-envelope me-2 text-primary"></i>Send us a Message
                    </h4>
                </div>
                <div class="card-body p-4">
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" id="contactForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Full Name *</label>
                                <input type="text" class="form-control" name="name" 
                                       value="<?php echo $user ? htmlspecialchars($user['name']) : (isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''); ?>" 
                                       required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email Address *</label>
                                <input type="email" class="form-control" name="email" 
                                       value="<?php echo $user ? htmlspecialchars($user['email']) : (isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''); ?>" 
                                       required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Subject *</label>
                                <select class="form-select" name="subject" required>
                                    <option value="">Select a subject</option>
                                    <option value="General Inquiry" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'General Inquiry') ? 'selected' : ''; ?>>General Inquiry</option>
                                    <option value="Booking Issue" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'Booking Issue') ? 'selected' : ''; ?>>Booking Issue</option>
                                    <option value="Payment Problem" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'Payment Problem') ? 'selected' : ''; ?>>Payment Problem</option>
                                    <option value="Technical Support" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'Technical Support') ? 'selected' : ''; ?>>Technical Support</option>
                                    <option value="Feature Request" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'Feature Request') ? 'selected' : ''; ?>>Feature Request</option>
                                    <option value="Partnership" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'Partnership') ? 'selected' : ''; ?>>Partnership</option>
                                    <option value="Other" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Message *</label>
                                <textarea class="form-control" name="message" rows="6" 
                                          placeholder="Please describe your inquiry in detail..." required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Contact Information -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-info-circle me-2"></i>Contact Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="contact-item mb-4">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-envelope text-primary me-3 mt-1"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Email</h6>
                                <p class="text-muted mb-0">support@parkreserve.com</p>
                                <p class="text-muted mb-0">info@parkreserve.com</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="contact-item mb-4">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-phone text-primary me-3 mt-1"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Phone</h6>
                                <p class="text-muted mb-0">+91 98765 43210</p>
                                <p class="text-muted mb-0">+91 87654 32109</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="contact-item mb-4">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-map-marker-alt text-primary me-3 mt-1"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Address</h6>
                                <p class="text-muted mb-0">
                                    123 Business Street<br>
                                    Tech City, India 110001
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-clock text-primary me-3 mt-1"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Business Hours</h6>
                                <p class="text-muted mb-1">Monday - Friday: 9:00 AM - 6:00 PM</p>
                                <p class="text-muted mb-1">Saturday: 10:00 AM - 4:00 PM</p>
                                <p class="text-muted mb-0">Sunday: Closed</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">Quick Help</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        <a href="faq.php" class="btn btn-outline-primary">
                            <i class="fas fa-question-circle me-2"></i>FAQ
                        </a>
                        <a href="help.php" class="btn btn-outline-info">
                            <i class="fas fa-life-ring me-2"></i>Help Center
                        </a>
                        <a href="<?php echo SITE_URL; ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-2"></i>Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- FAQ Section -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="text-center mb-4">
                <h3 class="fw-bold">Frequently Asked Questions</h3>
                <p class="text-muted">Quick answers to common questions</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="faq-item p-4 bg-light rounded">
                        <h6 class="fw-bold mb-2">How do I make a reservation?</h6>
                        <p class="text-muted mb-0">Simply search for parking locations, select your preferred spot, choose date and time, and complete the payment process.</p>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="faq-item p-4 bg-light rounded">
                        <h6 class="fw-bold mb-2">Can I cancel my booking?</h6>
                        <p class="text-muted mb-0">Yes, you can cancel your reservation up to 2 hours before the start time for a full refund.</p>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="faq-item p-4 bg-light rounded">
                        <h6 class="fw-bold mb-2">What payment methods do you accept?</h6>
                        <p class="text-muted mb-0">We accept all major credit cards, debit cards, and digital wallets through our secure payment gateway.</p>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="faq-item p-4 bg-light rounded">
                        <h6 class="fw-bold mb-2">How do I contact support?</h6>
                        <p class="text-muted mb-0">You can reach us via email, phone, or by filling out the contact form above. We're available 24/7 to help you.</p>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="faq.php" class="btn btn-primary">
                    <i class="fas fa-arrow-right me-2"></i>View All FAQs
                </a>
            </div>
        </div>
    </div>
</div>

<?php
$additional_js = "
<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    const name = document.querySelector('input[name=\"name\"]').value;
    const email = document.querySelector('input[name=\"email\"]').value;
    const subject = document.querySelector('select[name=\"subject\"]').value;
    const message = document.querySelector('textarea[name=\"message\"]').value;
    
    if (!name || !email || !subject || !message) {
        e.preventDefault();
        showNotification('Please fill in all required fields', 'error');
        return;
    }
    
    if (!validateEmail(email)) {
        e.preventDefault();
        showNotification('Please enter a valid email address', 'error');
        return;
    }
});

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}
</script>
";

include 'includes/footer.php';
?>
