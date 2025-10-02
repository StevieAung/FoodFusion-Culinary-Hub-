
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-white bg-white shadow-sm py-2">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <!-- Brand -->
            <a class="navbar-brand ps-3 fw-bolder" href="home_page.php">
                <h3 class="text-warning fw-bold m-0">FoodFusion</h3>
            </a>

            <!-- Toggler button for mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Nav Links -->
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link active text-warning px-2 px-md-3 fw-bold" href="home_page.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link px-2 px-md-3 fw-bold" href="recipe_collections.php">Recipes</a></li>
                    <li class="nav-item"><a class="nav-link px-2 px-md-3 fw-bold" href="community_cookbook.php">Community Cookbook</a></li>
                    <li class="nav-item"><a class="nav-link px-2 px-md-3 fw-bold" href="resources.php">Resources</a></li>
                    <li class="nav-item"><a class="nav-link px-2 px-md-3 fw-bold" href="about_us.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link px-2 px-md-3 fw-bold" href="contact_us.php">Contact Us</a></li>
                </ul>
            </div>

            <!-- Right: Profile / Login -->
            <div class="d-flex align-items-center mx-3">
                <?php
                $defaultIcon = './Assets/images/account_icon.png'; // general account icon
                if (isset($_SESSION['user_id'])):
                    // Fetch user's profile picture from session or database
                    $profilePic = $_SESSION['profile_pic'] ?? $defaultIcon;
                ?>
                    <div class="dropdown">
                        <a class="d-flex align-items-center text-decoration-none dropdown-toggle"
                        href="#" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?= htmlspecialchars($profilePic); ?>" alt="Profile" class="rounded-circle me-2" width="35" height="35">
                            <span class="fw-semibold text-dark">
                                <?= htmlspecialchars($_SESSION['user_firstname']); ?>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <button type="button" class="dropdown-item text-danger" id="logoutBtn">Logout</button>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <button type="button" class="btn btn-warning fw-bold ms-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                        Login
                    </button>
                    <button type="button" class="btn btn-outline-warning fw-bold ms-2 d-none d-md-inline" data-bs-toggle="modal" data-bs-target="#registerModal">
                        Join Us
                    </button>
                <?php endif; ?>
            </div>

        </div>
    </nav>

