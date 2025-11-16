<?php
require_once 'config/config.php';

$page_title = 'My Profile';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect(SITE_URL . '/auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$user = getCurrentUser();
$success = '';
$error = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'update_profile') {
        $name = sanitizeInput($_POST['name']);
        $phone = sanitizeInput($_POST['phone']);
        
        if (empty($name)) {
            $error = 'Name is required';
        } else {
            $db = getDB();
            $stmt = $db->prepare("UPDATE users SET name = ?, phone = ? WHERE id = ?");
            
            if ($stmt->execute([$name, $phone, $user['id']])) {
                $_SESSION['user_name'] = $name;
                $success = 'Profile updated successfully';
                $user['name'] = $name;
                $user['phone'] = $phone;
            } else {
                $error = 'Failed to update profile';
            }
        }
    }
    
    if (isset($_POST['action']) && $_POST['action'] == 'change_password') {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error = 'All password fields are required';
        } elseif ($new_password !== $confirm_password) {
            $error = 'New passwords do not match';
        } elseif (strlen($new_password) < 6) {
            $error = 'New password must be at least 6 characters long';
        } elseif (!password_verify($current_password, $user['password'])) {
            $error = 'Current password is incorrect';
        } else {
            $db = getDB();
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
            
            if ($stmt->execute([$hashed_password, $user['id']])) {
                $success = 'Password changed successfully';
            } else {
                $error = 'Failed to change password';
            }
        }
    }
}

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="bg-primary text-white py-4 d-none d-md-block">
    <div class="container">
        <h4 class="mb-0 fw-bold">
            <i class="fas fa-user-circle me-2"></i>My Profile
        </h4>
        <p class="mb-0 opacity-75">Manage your account settings</p>
    </div>
</div>

<!-- Mobile Header -->
<div class="bg-white border-bottom p-3 d-md-none">
    <h6 class="mb-0 fw-bold">My Profile</h6>
</div>

<div class="container py-4">
    <!-- Success/Error Messages -->
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

    <div class="row">
        <!-- Profile Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center p-4">
                    <div class="profile-avatar mb-3">
                        <div class="avatar bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px; font-size: 2rem;">
                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($user['name']); ?></h5>
                    <p class="text-muted mb-3"><?php echo htmlspecialchars($user['email']); ?></p>
                    <div class="d-grid gap-2">
                        <span class="badge bg-success">Active Member</span>
                        <small class="text-muted">Member since <?php echo date('M Y', strtotime($user['created_at'])); ?></small>
                    </div>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold">Quick Stats</h6>
                </div>
                <div class="card-body">
                    <div class="stat-item d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Bookings:</span>
                        <span class="fw-bold">0</span>
                    </div>
                    <div class="stat-item d-flex justify-content-between mb-2">
                        <span class="text-muted">Active Bookings:</span>
                        <span class="fw-bold text-success">0</span>
                    </div>
                    <div class="stat-item d-flex justify-content-between">
                        <span class="text-muted">Total Spent:</span>
                        <span class="fw-bold">₹0.00</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Profile Content -->
        <div class="col-lg-8">
            <!-- Profile Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-user me-2 text-primary"></i>Profile Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Full Name *</label>
                                <input type="text" class="form-control" name="name" 
                                       value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email Address</label>
                                <input type="email" class="form-control" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                <div class="form-text">Email cannot be changed</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Phone Number</label>
                                <input type="tel" class="form-control" name="phone" 
                                       value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" 
                                       placeholder="+91 98765 43210">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Member Since</label>
                                <input type="text" class="form-control" 
                                       value="<?php echo date('M j, Y', strtotime($user['created_at'])); ?>" disabled>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Change Password -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-lock me-2 text-primary"></i>Change Password
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" id="passwordForm">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Current Password *</label>
                                <input type="password" class="form-control" name="current_password" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">New Password *</label>
                                <input type="password" class="form-control" name="new_password" 
                                       minlength="6" required>
                                <div class="form-text">Minimum 6 characters</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Confirm New Password *</label>
                                <input type="password" class="form-control" name="confirm_password" 
                                       minlength="6" required>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key me-2"></i>Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Account Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-cog me-2 text-primary"></i>Account Actions
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">Download My Data</h6>
                                <p class="text-muted small mb-0">Get a copy of your account data and booking history</p>
                            </div>
                            <button class="btn btn-outline-primary">
                                <i class="fas fa-download me-1"></i>Download
                            </button>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold text-danger">Delete Account</h6>
                                <p class="text-muted small mb-0">Permanently delete your account and all data</p>
                            </div>
                            <button class="btn btn-outline-danger" onclick="confirmDeleteAccount()">
                                <i class="fas fa-trash me-1"></i>Delete
                            </button>
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
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    const newPassword = document.querySelector('input[name=\"new_password\"]').value;
    const confirmPassword = document.querySelector('input[name=\"confirm_password\"]').value;
    
    if (newPassword !== confirmPassword) {
        e.preventDefault();
        showNotification('New passwords do not match', 'error');
        return;
    }
    
    if (newPassword.length < 6) {
        e.preventDefault();
        showNotification('Password must be at least 6 characters long', 'error');
        return;
    }
});

function confirmDeleteAccount() {
    if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
        if (confirm('This will permanently delete all your data. Are you absolutely sure?')) {
            showNotification('Account deletion feature is not implemented in this demo', 'info');
        }
    }
}

// Auto-dismiss alerts
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
