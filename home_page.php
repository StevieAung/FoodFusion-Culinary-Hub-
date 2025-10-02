<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
include './Database/db.php';

// Fetch the latest 4 recipes from the collections table
$featured_sql = "
  SELECT 
    rc.recipe_id,
    rc.title,
    rc.description,
    rc.image
  FROM recipe_collections rc
  ORDER BY rc.created_at DESC
  LIMIT 4
";
$featured_result = $conn->query($featured_sql);

?>

<!doctype html>
<html lang="en">
<head>
    <title>Home - FoodFusion</title>
    <?php include './includes/head_tags.php'; ?>
    <link rel="stylesheet" href="./Assets/css/style.css">
    <!-- Bootstrap Icons for eye toggle -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        /* Carousel Enhancements */
        #eventsCarousel .carousel-item {
            position: relative;
            height: 450px; /* Set a fixed height for the carousel */
        }
        #eventsCarousel .carousel-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0) 50%);
            border-radius: 1rem; /* Match the image's border-radius */
        }
        #eventsCarousel .carousel-item img {
            height: 100%;
            object-fit: cover; /* Ensures image covers the area without distortion */
        }
        #eventsCarousel .carousel-caption {
            bottom: 1.5rem; /* Position caption lower */
            background: none !important; /* Remove the default dark background */
        }
        #eventsCarousel .carousel-caption h6 {
            font-size: 1.5rem;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
        }
        #eventsCarousel .carousel-caption p {
            font-size: 1rem;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.7);
        }
        /* Recipe Card Styles from other pages for consistency */
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important; }
        .card-img-top { height: 180px; object-fit: cover; }

        /* Cookie Consent Banner */
        #cookieConsent a {
            color: #ffc107;
            text-decoration: underline;
        }
        #cookieConsent button {
            min-width: 80px;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100 bg-warning-subtle">

<!-- Cookie Consent Banner -->
<div id="cookieConsent" class="position-fixed bottom-0 start-0 w-100 bg-dark text-white p-3 d-flex justify-content-between align-items-center" style="display: none; z-index: 1050;">
    <span>We use cookies to enhance your experience. By continuing to visit this site you agree to our <a href="privacy.php" class="text-warning text-decoration-underline">Privacy Policy</a>.</span>
    <button id="acceptCookies" class="btn btn-warning btn-sm">Accept</button>
</div>

<header>
    <?php include './includes/navbar.php'; ?>
</header>
<main>
    <!-- Hero Section (Compact) -->
    <section class="hero container text-center d-flex flex-column align-items-center justify-content-center py-3">
        <div class="row align-items-center">
            <div class="col-12 col-md-6 text-center text-md-start p-2">
                <h2 class="fw-bold mb-2">Cook Like a Pro with Our <span class="text-warning">Easy</span> & <span class="text-warning">Tasty</span> Recipes</h2>
                <p class="lead small mb-2">Your one-stop destination for delicious recipes and food inspiration.</p>
                <a href="recipe_collections.php" class="btn btn-warning btn-sm mt-1">Go to Recipes</a>
            </div>
            <div class="col-12 col-md-6 text-center">
                <img src="./Assets/images/chef.png" alt="chef_logo" class="img-fluid mt-2" style="max-width: 50%;">
            </div>
        </div>
    </section>

    <!-- Upcoming Cooking Events Carousel (Responsive) -->
    <section class="container my-4">
        <h4 class="fw-bold text-center mb-3">Upcoming Cooking Events</h4>
        <div id="eventsCarousel" class="carousel slide shadow-lg" data-bs-ride="carousel">
            <!-- Carousel Indicators -->
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#eventsCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#eventsCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#eventsCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>
            <div class="carousel-inner rounded-4">

                <!-- Carousel Item 1 -->
                <div class="carousel-item active">
                    <img src="./Assets/images/event1.jpeg" class="d-block w-100" alt="Italian Pasta Workshop">
                    <div class="carousel-caption d-none d-md-block">
                        <h6>Italian Pasta Workshop</h6>
                        <p>Join us for an interactive pasta-making session.</p>
                    </div>
                </div>

                <!-- Carousel Item 2 -->
                <div class="carousel-item">
                    <img src="./Assets/images/event2.jpeg" class="d-block w-100" alt="Vegan Cooking Class">
                    <div class="carousel-caption d-none d-md-block">
                        <h6>Vegan Cooking Class</h6>
                        <p>Discover delicious vegan recipes with our chef.</p>
                    </div>
                </div>

                <!-- Carousel Item 3 -->
                <div class="carousel-item">
                    <img src="./Assets/images/event3.jpeg" class="d-block w-100" alt="Chocolate Dessert Workshop">
                    <div class="carousel-caption d-none d-md-block">
                        <h6>Chocolate Dessert Workshop</h6>
                        <p>Learn how to make exquisite chocolate desserts.</p>
                    </div>
                </div>

            </div>

            <!-- Carousel Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#eventsCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#eventsCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>

    <!-- Featured Recipes (Compact) -->
    <section class="container p-4 mb-4 text-center rounded-5">
          <h4 class="fw-bold mb-2">Popular Recipes You Can't Miss</h4>
        <p class="fst-italic opacity-50 small mb-3">From comfort food classics to exotic flavors</p>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-3">
            <?php if ($featured_result && $featured_result->num_rows > 0): ?>
                <?php while($row = $featured_result->fetch_assoc()): 
                    $imgFile = $row['image'] ?? '';
                    $localImagePath = __DIR__ . '/Assets/images/recipes/' . $imgFile;
                    $imageUrl = (!empty($imgFile) && file_exists($localImagePath)) ? "./Assets/images/recipes/".rawurlencode($imgFile) : "./Assets/images/recipes/default_recipe.jpg";
                ?>
                <div class="col">
                    <div class="card h-100 rounded-4 shadow-sm card-hover">
                        <img src="<?= $imageUrl ?>" class="card-img-top rounded-4 p-2" alt="<?= htmlspecialchars($row['title']) ?>">
                        <div class="card-body p-2">
                            <h6 class="fw-bold mb-1"><?= htmlspecialchars($row['title']) ?></h6>
                            <p class="small mb-2"><?= htmlspecialchars(substr($row['description'], 0, 70)) ?>...</p>
                            <a href="recipe_collections.php?id=<?= $row['recipe_id'] ?>" class="btn btn-warning btn-sm w-100">View Recipe</a>
                        </div>
                  </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No featured recipes available at the moment.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<footer>
    <?php include './includes/footer_tags.php'; ?>
</footer>

<!-- Modals -->
<?php include 'modals/modals.php'; ?>

<?php include './includes/script_tags.php'; ?>

<script src="./Assets/js/home.js?v=1.2"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Show registration modal on a timer for non-logged-in users ---
        <?php if (!isset($_SESSION['user_id'])): ?>
            setTimeout(() => {
                // Ensure the modal element exists before trying to initialize
                const registrationModalEl = document.getElementById('registrationModal');
                if (registrationModalEl) {
                    const registrationModal = new bootstrap.Modal(registrationModalEl);
                    registrationModal.show();
                }
            }, 5000); // Show after 5 seconds
        <?php endif; ?>

        // --- Logout functionality ---
        document.getElementById('logoutBtn')?.addEventListener('click', function() {
            logoutUser();
        });

        // --- Cookie Consent ---
        const cookieBanner = document.getElementById('cookieConsent');
        const acceptButton = document.getElementById('acceptCookies');

        // Check if cookie already exists
        if (cookieBanner && !document.cookie.split('; ').find(row => row.startsWith('foodfusion_cookie='))) {
            cookieBanner.style.display = 'flex';
        }

        // Set cookie when accepted
        acceptButton?.addEventListener('click', function() {
            const expiryDate = new Date();
            expiryDate.setFullYear(expiryDate.getFullYear() + 1); // 1-year expiry
            document.cookie = `foodfusion_cookie=accepted; expires=${expiryDate.toUTCString()}; path=/`;
            if (cookieBanner) {
                cookieBanner.style.display = 'none';
            }
        });
    });
</script>
</body>
</html>
