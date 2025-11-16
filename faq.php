<?php
require_once 'config/config.php';

$page_title = 'Frequently Asked Questions';

// Get FAQs from database
$db = getDB();
$stmt = $db->prepare("SELECT * FROM faqs WHERE status = 'active' ORDER BY order_index ASC, id ASC");
$stmt->execute();
$faqs = $stmt->fetchAll();

// Group FAQs by category
$faq_categories = [];
foreach ($faqs as $faq) {
    $category = $faq['category'] ?: 'General';
    $faq_categories[$category][] = $faq;
}

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="bg-info text-white py-5">
    <div class="container text-center">
        <h2 class="fw-bold mb-3">Frequently Asked Questions</h2>
        <p class="lead mb-0">Find answers to common questions about our parking reservation system</p>
    </div>
</div>

<div class="container py-5">
    <!-- Search FAQ -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-6">
            <div class="search-box">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" 
                           placeholder="Search FAQs..." id="faqSearch">
                </div>
            </div>
        </div>
    </div>
    
    <!-- FAQ Categories -->
    <div class="row">
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">Categories</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="#all" class="list-group-item list-group-item-action active" data-category="all">
                            <i class="fas fa-list me-2"></i>All Questions
                        </a>
                        <?php foreach (array_keys($faq_categories) as $category): ?>
                        <a href="#<?php echo strtolower(str_replace(' ', '-', $category)); ?>" 
                           class="list-group-item list-group-item-action" 
                           data-category="<?php echo strtolower(str_replace(' ', '-', $category)); ?>">
                            <i class="fas fa-folder me-2"></i><?php echo htmlspecialchars($category); ?>
                            <span class="badge bg-primary float-end"><?php echo count($faq_categories[$category]); ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-9">
            <!-- FAQ Content -->
            <div class="faq-content">
                <?php if (empty($faqs)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-question-circle text-muted display-1 mb-4"></i>
                        <h4 class="text-muted mb-3">No FAQs Available</h4>
                        <p class="text-muted mb-4">We're working on adding helpful questions and answers.</p>
                        <a href="contact.php" class="btn btn-primary">
                            <i class="fas fa-envelope me-2"></i>Contact Support
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($faq_categories as $category => $category_faqs): ?>
                    <div class="faq-category mb-5" data-category="<?php echo strtolower(str_replace(' ', '-', $category)); ?>">
                        <h3 class="fw-bold mb-4 text-primary">
                            <i class="fas fa-folder-open me-2"></i><?php echo htmlspecialchars($category); ?>
                        </h3>
                        
                        <div class="accordion" id="accordion<?php echo str_replace(' ', '', $category); ?>">
                            <?php foreach ($category_faqs as $index => $faq): ?>
                            <div class="accordion-item border-0 shadow-sm mb-3 faq-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold" type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#collapse<?php echo $faq['id']; ?>">
                                        <?php echo htmlspecialchars($faq['question']); ?>
                                    </button>
                                </h2>
                                <div id="collapse<?php echo $faq['id']; ?>" 
                                     class="accordion-collapse collapse" 
                                     data-bs-parent="#accordion<?php echo str_replace(' ', '', $category); ?>">
                                    <div class="accordion-body">
                                        <p class="text-muted mb-0">
                                            <?php echo nl2br(htmlspecialchars($faq['answer'])); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Still Need Help -->
            <div class="card border-0 shadow-sm bg-light mt-5">
                <div class="card-body p-4 text-center">
                    <h4 class="fw-bold mb-3">Still Need Help?</h4>
                    <p class="text-muted mb-4">
                        Can't find the answer you're looking for? Our support team is here to help you.
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                        <a href="contact.php" class="btn btn-primary">
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

<?php
$additional_js = "
<script>
// FAQ Search functionality
document.getElementById('faqSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.accordion-button').textContent.toLowerCase();
        const answer = item.querySelector('.accordion-body').textContent.toLowerCase();
        
        if (question.includes(searchTerm) || answer.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
    
    // Show/hide categories based on visible items
    document.querySelectorAll('.faq-category').forEach(category => {
        const visibleItems = category.querySelectorAll('.faq-item[style*=\"block\"], .faq-item:not([style])');
        if (searchTerm === '' || visibleItems.length > 0) {
            category.style.display = 'block';
        } else {
            category.style.display = 'none';
        }
    });
});

// Category filtering
document.querySelectorAll('[data-category]').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Update active state
        document.querySelectorAll('[data-category]').forEach(l => l.classList.remove('active'));
        this.classList.add('active');
        
        const category = this.getAttribute('data-category');
        const categories = document.querySelectorAll('.faq-category');
        
        if (category === 'all') {
            categories.forEach(cat => cat.style.display = 'block');
        } else {
            categories.forEach(cat => {
                if (cat.getAttribute('data-category') === category) {
                    cat.style.display = 'block';
                } else {
                    cat.style.display = 'none';
                }
            });
        }
        
        // Clear search when filtering by category
        document.getElementById('faqSearch').value = '';
        document.querySelectorAll('.faq-item').forEach(item => {
            item.style.display = 'block';
        });
    });
});

// Smooth scroll to sections
document.querySelectorAll('a[href^=\"#\"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
</script>
";

include 'includes/footer.php';
?>
