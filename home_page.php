<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
include './Database/db.php';

// If user is logged in, fetch first name and profile picture if not already in session
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Only fetch if not already set
    if (!isset($_SESSION['user_firstname']) || !isset($_SESSION['profile_pic'])) {
        $stmt = $conn->prepare("SELECT first_name, profile_pic FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($first_name, $profile_pic);
        if ($stmt->fetch()) {
            $_SESSION['user_firstname'] = $first_name;
            $_SESSION['profile_pic'] = $profile_pic ?? './Assets/images/account_icon.png';
        }
        $stmt->close();
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <title>Home - FoodFusion</title>
    <?php include './includes/head_tags.php'; ?>
    <link rel="stylesheet" href="./Assets/css/style.css">
    <!-- Bootstrap Icons for eye toggle -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="d-flex flex-column min-vh-100 bg-warning-subtle">

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
                <a href="#" class="btn btn-warning btn-sm mt-1">Go to Recipes</a>
            </div>
            <div class="col-12 col-md-6 text-center">
                <img src="./Assets/images/chef.png" alt="chef_logo" class="img-fluid mt-2" style="max-width: 50%;">
            </div>
        </div>
    </section>

    <!-- Upcoming Cooking Events Carousel (Responsive) -->
    <section class="container my-4">
        <h4 class="fw-bold text-center mb-3">Upcoming Cooking Events</h4>
        <div id="eventsCarousel" class="carousel slide shadow rounded-4" data-bs-ride="carousel">
            <div class="carousel-inner">

                <!-- Carousel Item 1 -->
                <div class="carousel-item active">
                    <img src="./Assets/images/event1.jpeg" class="d-block w-100 rounded-4" alt="Italian Pasta Workshop">
                    <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded-3 p-2">
                        <h6>Italian Pasta Workshop</h6>
                        <p class="small mb-0">Join us for an interactive pasta-making session.</p>
                    </div>
                </div>

                <!-- Carousel Item 2 -->
                <div class="carousel-item">
                    <img src="./Assets/images/event2.jpeg" class="d-block w-100 rounded-4" alt="Vegan Cooking Class">
                    <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded-3 p-2">
                        <h6>Vegan Cooking Class</h6>
                        <p class="small mb-0">Discover delicious vegan recipes with our chef.</p>
                    </div>
                </div>

                <!-- Carousel Item 3 -->
                <div class="carousel-item">
                    <img src="./Assets/images/event3.jpeg" class="d-block w-100 rounded-4" alt="Chocolate Dessert Workshop">
                    <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded-3 p-2">
                        <h6>Chocolate Dessert Workshop</h6>
                        <p class="small mb-0">Learn how to make exquisite chocolate desserts.</p>
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
            <div class="col">
                <div class="card h-100 rounded-4 shadow-sm card-hover">
                    <img src="./Assets/images/pasta.jpeg" class="card-img-top rounded-4 p-2" alt="pasta">
                    <div class="card-body p-2">
                        <h6 class="fw-bold mb-1">Delicious Pasta</h6>
                        <p class="small mb-2">Try this creamy and flavorful pasta recipe.</p>
                        <a href="#" class="btn btn-warning btn-sm w-100">View Recipe</a>
                    </div>
              </div>
            </div>
            <div class="col">
                <div class="card h-100 rounded-4 shadow-sm card-hover">
                    <img src="./Assets/images/pasta.jpeg" class="card-img-top rounded-4 p-2" alt="pasta">
                    <div class="card-body p-2">
                        <h6 class="fw-bold mb-1">Delicious Pasta</h6>
                        <p class="small mb-2">Try this creamy and flavorful pasta recipe.</p>
                        <a href="#" class="btn btn-warning btn-sm w-100">View Recipe</a>
                    </div>
              </div>
            </div>
            <div class="col">
                <div class="card h-100 rounded-4 shadow-sm card-hover">
                    <img src="./Assets/images/pasta.jpeg" class="card-img-top rounded-4 p-2" alt="pasta">
                    <div class="card-body p-2">
                        <h6 class="fw-bold mb-1">Delicious Pasta</h6>
                        <p class="small mb-2">Try this creamy and flavorful pasta recipe.</p>
                        <a href="#" class="btn btn-warning btn-sm w-100">View Recipe</a>
                    </div>
              </div>
            </div>
            <div class="col">
                <div class="card h-100 rounded-4 shadow-sm card-hover">
                    <img src="./Assets/images/pasta.jpeg" class="card-img-top rounded-4 p-2" alt="pasta">
                    <div class="card-body p-2">
                        <h6 class="fw-bold mb-1">Delicious Pasta</h6>
                        <p class="small mb-2">Try this creamy and flavorful pasta recipe.</p>
                        <a href="#" class="btn btn-warning btn-sm w-100">View Recipe</a>
                    </div>
              </div>
            </div>
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
    // Show registration modal on a timer for non-logged-in users
    <?php if (!isset($_SESSION['user_id'])): ?>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                const registrationModal = new bootstrap.Modal(document.getElementById('registrationModal'));
                registrationModal.show();
            }, 5000); // Show after 5 seconds
        });
    <?php endif; ?>

    // Logout functionality
    document.getElementById('logoutBtn')?.addEventListener('click', function() {
        logoutUser();
    });
</script>
</body>
</html>
