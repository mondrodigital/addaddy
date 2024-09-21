<?php
require_once 'airtable_connector.php';

$templates = getTemplatesFromAirtable();

if (isset($templates['error'])) {
    echo "<p>Error: " . htmlspecialchars($templates['error']) . "</p>";
    exit;
}

$industries = array_unique(array_filter(array_column($templates, 'industry')));
$types = array_unique(array_filter(array_column($templates, 'type')));
$purposes = array_unique(array_filter(array_column($templates, 'purpose')));

$selectedIndustries = isset($_GET['industries']) ? explode(',', $_GET['industries']) : [];
$selectedTypes = isset($_GET['types']) ? explode(',', $_GET['types']) : [];
$selectedPurposes = isset($_GET['purposes']) ? explode(',', $_GET['purposes']) : [];

function getEmoji($category) {
    $emojis = [
        'Finance' => '💼', 'Healthcare' => '🏥', 'Technology' => '💻', 'Environment' => '🌿',
        'Fashion' => '👗', 'Food' => '🍽️', 'Education' => '🎓', 'Real Estate' => '🏠',
        'Infographic' => '📊', 'Social Media' => '📱', 'Drive Action - Purchase' => '🎯', 'Poster' => '🖼️',
        'Catalog' => '📚', 'Print' => '🖨️', 'Flyer' => '📄',
        'Inform' => 'ℹ', 'Drive Action - Sign Up/ Consultation' => '🔄', 'Brand Awareness' => '🌟'
    ];
    return $emojis[$category] ?? '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Template Gallery</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <nav class="main-nav">
            <div class="nav-logo">
                <img src="path/to/your/logo.png" alt="Logo">
            </div>
            <div class="nav-links">
                <a href="#" class="nav-link">Home</a>
                <a href="#" class="nav-link">Templates</a>
                <a href="#" class="nav-link">Pricing</a>
                <a href="#" class="nav-link">About</a>
            </div>
            <div class="auth-buttons">
                <a href="#" class="auth-button login-button">Log In</a>
                <a href="#" class="auth-button signup-button">Sign Up</a>
            </div>
        </nav>
    </header>

    <div class="hero-section">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Create Ads That Convert Without the Stress</h1>
                <h2>Get ready-to-use Canva templates infused with expert copywriting—so you can launch high-performing ads in minutes, no design or writing skills required.</h2>
                <div class="value-propositions">
                    <p>⏱️ Save Time: Spend less time designing and more time growing your business.</p>
                    <p>💰 Copy That Sells: Professionally written copy that drives action.</p>
                    <p>🎨 No Experience Needed: Create eye-catching ads with a few clicks.</p>
                </div>
                <button class="cta-button">Get Started</button>
            </div>
            <div class="mockup-container">
                <div class="mockup-ad">
                    <div class="ad-content">
                        <h3 class="ad-title">Collaborate Smarter</h3>
                        <p class="ad-description">Streamline your team communication and boost productivity with our innovative platform.</p>
                        <div class="ad-image-placeholder">Image Placeholder</div>
                        <button class="ad-cta">Try for Free</button>
                    </div>
                </div>
                <button class="change-industry-button">Change Industry</button>
            </div>
        </div>
    </div>

    <div class="container">
        <h1>Canva Templates</h1>
        
        <div class="filter-section">
            <div class="filter-buttons">
                <?php foreach ($industries as $industry): ?>
                    <button class="filter-button <?php echo in_array($industry, $selectedIndustries) ? 'active' : ''; ?>" data-filter="industry" data-value="<?php echo $industry; ?>">
                        <?php echo getEmoji($industry) . ' ' . $industry; ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        
        
        <div class="filter-section">
            <h2>Purpose</h2>
            <div class="filter-buttons">
                <?php foreach ($purposes as $purpose): ?>
                    <button class="filter-button <?php echo in_array($purpose, $selectedPurposes) ? 'active' : ''; ?>" data-filter="purpose" data-value="<?php echo $purpose; ?>">
                        <?php echo getEmoji($purpose) . ' ' . $purpose; ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="template-grid">
            <?php foreach ($templates as $template): ?>
                <div class="template-card" 
                     data-industry="<?php echo htmlspecialchars($template['industry'] ?? ''); ?>" 
                     data-type="<?php echo htmlspecialchars($template['type'] ?? ''); ?>" 
                     data-purpose="<?php echo htmlspecialchars($template['purpose'] ?? ''); ?>">
                    <?php if (!empty($template['image'])): ?>
                        <img src="<?php echo htmlspecialchars($template['image']); ?>" alt="<?php echo htmlspecialchars($template['name']); ?>" />
                    <?php else: ?>
                        <div class="image-placeholder">Image not found</div>
                    <?php endif; ?>
                    <div class="template-overlay">
                        <h3 class="template-title"><?php echo htmlspecialchars($template['name']); ?></h3>
                        <?php if (!empty($template['description'])): ?>
                            <p class="template-description"><?php echo htmlspecialchars($template['description']); ?></p>
                        <?php endif; ?>
                        <div class="template-filters">
                            <?php if (!empty($template['purpose'])): ?>
                                <span><?php echo getEmoji($template['purpose']) . ' ' . htmlspecialchars($template['purpose']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="template-buttons">
                            <?php if (!empty($template['designLink'])): ?>
                                <button class="open-canva-button">
                                    <a href="<?php echo htmlspecialchars($template['designLink']); ?>" target="_blank">
                                        <i class="fas fa-edit"></i> Edit in Canva
                                    </a>
                                </button>
                            <?php endif; ?>
                            <?php
                            $promptText = !empty($template['long_text']) ? $template['long_text'] : $template['description'];
                            if (!empty($promptText)):
                            ?>
                                <button class="copy-prompt-button" data-prompt="<?php echo htmlspecialchars($promptText); ?>">
                                    <i class="fas fa-copy"></i> Copy prompt
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="pro-banner">
            <div class="banner-content">
                <p>Get notified when new templates drop weekly!</p>
                <form class="email-form">
                    <input type="email" class="email-input" placeholder="Enter your email" required>
                    <button type="submit" class="subscribe-button">Subscribe</button>
                </form>
                <div class="close-button-container">
                    <button class="close-banner">&times;</button>
                </div>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>