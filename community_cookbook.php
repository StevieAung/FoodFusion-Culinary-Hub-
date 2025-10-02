<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include './Database/db.php';

// Define a relative path from the web root for consistency
$uploadDir = 'uploads/community_recipes_img/';

// Handle recipe submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_recipe']) && isset($_SESSION['user_id'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $ingredients = trim($_POST['ingredients']);
    $instructions = trim($_POST['instructions']);
    $difficulty = $_POST['difficulty'];
    $category_id = intval($_POST['category_id']);
    $cuisine_id = intval($_POST['cuisine_id']);
    $user_id = $_SESSION['user_id'];
    $cuisine_image = null;

    if (isset($_FILES['cuisine_image']) && $_FILES['cuisine_image']['error'] === 0) {
        // Create the directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $filename = time() . '_' . preg_replace('/[^A-Za-z0-9.\-_]/', '_', basename($_FILES['cuisine_image']['name']));
        $targetFile = $uploadDir . $filename; // This is the relative path to store and use

        // Move the file to the correct destination
        if (move_uploaded_file($_FILES['cuisine_image']['tmp_name'], $targetFile)) {
            $cuisine_image = $targetFile; // Store the correct relative path
        }
    }

    $stmt = $conn->prepare("
        INSERT INTO community_recipes
        (title, description, ingredients, instructions, difficulty_level, user_id, cuisine_image, category_id, cuisine_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sssssisii", $title, $description, $ingredients, $instructions, $difficulty, $user_id, $cuisine_image, $category_id, $cuisine_id);
    
    // Execute and then redirect to prevent re-submission on refresh
    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Fetch recipes
$sql = "
        SELECT r.recipe_id, r.title, r.description, r.ingredients, r.instructions, r.difficulty_level,
        r.cuisine_image, r.created_at,
        u.first_name, u.last_name, u.profile_pic,
        cat.category_name,
        cui.cuisine_name,
        COALESCE(AVG(rt.rating), 0) AS avg_rating,
        COUNT(DISTINCT c.comment_id) AS comment_count
    FROM community_recipes r
    JOIN users u ON r.user_id = u.user_id
    LEFT JOIN categories cat ON r.category_id = cat.category_id
    LEFT JOIN cuisine_types cui ON r.cuisine_id = cui.cuisine_id
    LEFT JOIN ratings rt ON r.recipe_id = rt.recipe_fk_id AND rt.recipe_type = 'community'
    LEFT JOIN comments c ON r.recipe_id = c.recipe_fk_id AND c.recipe_type = 'community'
    GROUP BY r.recipe_id
    ORDER BY r.created_at DESC;
";
$result = $conn->query($sql);

$catRes = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name");
$cuiRes = $conn->query("SELECT cuisine_id, cuisine_name FROM cuisine_types ORDER BY cuisine_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Community Cookbook</title>
<?php include './includes/head_tags.php'; ?>
<style>
body {
    font-family:'Segoe UI',sans-serif;
    background:#fff3cd;
    margin:0;
    padding:0;
}
.page-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}
/* Flexbox header for proper alignment */
.header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.header-container h1 {
    flex-grow: 1;
    text-align: center;
    margin: 0;
    font-size: 2rem;
}

/* Themed Recipe Card */
.recipe-card {
  background: #fff;
  border-radius: 15px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: all 0.3s ease;
  height: 100%;
}
.recipe-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}
.recipe-card img {
  width: 100%;
  height: 220px;
  object-fit: cover;
}
.recipe-card h5 {
  margin-top: 10px;
  color: #222;
  font-size: 1.15rem;
  font-weight: 600;
}
.recipe-card p {
  color: #555;
  font-size: .9rem;
  line-height: 1.4;
}
.recipe-card .badge {
  margin-right: 5px;
}
.card-body {
  padding: 15px;
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}
.rating-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  background: #ffc107;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: bold;
  color: #000;
}
.details {
  background: #f9f9f9;
  border-radius: 8px;
  margin-top: 10px;
  padding: 10px;
  font-size: 0.9em;
  display: none;
}
.show-details-btn {
  background:#ffc107;
  color:#000;
  border:none;
  border-radius:5px;
  padding:6px 12px;
  cursor:pointer;
  font-size: 0.85rem;
  transition:.2s;
}
.show-details-btn:hover {
  background:#e0ac00;
}
.star {
  font-size: 1.2rem;
  color: #ccc;
  cursor: pointer;
  margin-right: 2px;
}
.star.selected {
  color: #ffc107;
}
.comment-box {
  margin-top: 12px;
  font-size: 0.85rem;
}
.author {
  display: flex;
  align-items: center;
  margin-top: auto;
  padding-top: 10px; /* Added padding for spacing */
  font-size: 0.9rem;
  color: #444;
}
.author img {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  margin-right: 8px;
  object-fit: cover;
}
</style>
</head>
<body>
<?php include './includes/navbar.php'; ?>

<div class="page-container">
    <div class="header-container">
        <a href="home_page.php" class="btn btn-outline-secondary">← Back to Home</a>
        <h1 class="fw-bold">Community Cookbook</h1>
        <div style="width: 150.38px;"></div> <!-- Spacer to keep title centered -->
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <select id="filterCuisine" class="form-select">
                <option value="">All Cuisines</option>
                <?php if ($cuiRes && $cuiRes->num_rows > 0): ?>
                <?php while($c = $cuiRes->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($c['cuisine_name']) ?>"><?= htmlspecialchars($c['cuisine_name']) ?></option>
                <?php endwhile; ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <select id="filterCategory" class="form-select">
                <option value="">All Categories</option>
                <?php if ($catRes && $catRes->num_rows > 0): ?>
                <?php mysqli_data_seek($catRes, 0); // Reset pointer before re-using ?>
                <?php while($c = $catRes->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($c['category_name']) ?>"><?= htmlspecialchars($c['category_name']) ?></option>
                <?php endwhile; ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <select id="filterDifficulty" class="form-select">
                <option value="">All Difficulty Levels</option>
                <option value="Easy">Easy</option>
                <option value="Medium">Medium</option>
                <option value="Hard">Hard</option>
            </select>
        </div>
    </div>

    <?php if(isset($_SESSION['user_id'])): ?>
        <div class="text-center mb-3">
            <button id="openModal" class="btn btn-warning">Submit a Recipe</button>
        </div>
    <?php endif; ?>

    <!-- Recipes Grid -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php if($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
               <div class="col recipe-card-wrapper"
                    data-cuisine="<?= htmlspecialchars($row['cuisine_name'] ?? '') ?>"
                    data-category="<?= htmlspecialchars($row['category_name'] ?? '') ?>"
                    data-difficulty="<?= htmlspecialchars($row['difficulty_level'] ?? '') ?>">
                <div class="recipe-card">
                    <div style="position:relative;">
                    <img src="<?= htmlspecialchars($row['cuisine_image'] ?: './Assets/images/recipes/default_recipe.jpg') ?>" 
                        alt="<?= htmlspecialchars($row['title']) ?>">
                    <span class="rating-badge">★ <?= round($row['avg_rating'],1) ?></span>
                    </div>
                    <div class="card-body">
                    <h5><?= htmlspecialchars($row['title']) ?></h5>
                    <p><?= nl2br(htmlspecialchars(mb_substr($row['description'], 0, 120))) ?><?= (strlen($row['description']) > 120) ? '...' : '' ?></p>
                    
                    <p>
                        <?php if (!empty($row['cuisine_name'])): ?>
                        <span class="badge bg-primary"><?= htmlspecialchars($row['cuisine_name']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($row['category_name'])): ?>
                        <span class="badge bg-success"><?= htmlspecialchars($row['category_name']) ?></span>
                        <?php endif; ?>
                        <span class="badge bg-warning text-dark"><?= htmlspecialchars($row['difficulty_level']) ?></span>
                    </p>
                    
                    <!-- Ratings -->
                    <div class="d-flex align-items-center mt-2">
                        <?php for($i=1; $i<=5; $i++): ?>
                        <span class="star <?= $i <= round($row['avg_rating']) ? 'selected' : '' ?>" 
                                data-recipe="<?= $row['recipe_id'] ?>" data-value="<?= $i ?>">&#9733;</span>
                        <?php endfor; ?>
                        <small class="ms-2">(<?= round($row['avg_rating'],1) ?>)</small>
                    </div>

                    <button class="show-details-btn mt-3" data-target="details-<?= $row['recipe_id'] ?>">Show Details</button>
                    <div class="details" id="details-<?= $row['recipe_id'] ?>">
                        <h6>Ingredients:</h6>
                        <pre><?= htmlspecialchars($row['ingredients']) ?></pre>
                        <h6>Instructions:</h6>
                        <pre><?= htmlspecialchars($row['instructions']) ?></pre>
                    </div>

                    <!-- Comments -->
                    <div class="comment-box">
                        <?php
                        // Prepare and execute the comments query for this recipe
                        $commentsStmt = $conn->prepare("
                            SELECT c.comment_text, u.first_name, u.last_name 
                            FROM comments c 
                            JOIN users u ON c.user_id = u.user_id 
                            WHERE c.recipe_fk_id = ? AND c.recipe_type = 'community' 
                            ORDER BY c.created_at ASC
                        ");
                        $commentsStmt->bind_param("i", $row['recipe_id']);
                        $commentsStmt->execute();
                        $commentsRes = $commentsStmt->get_result();
                        ?>
                        <div class="comments-list">
                            <?php while($comment = $commentsRes->fetch_assoc()): ?>
                                <p class="text-muted mb-1"><strong><?= htmlspecialchars($comment['first_name']) ?>:</strong> <?= htmlspecialchars($comment['comment_text']) ?></p>
                            <?php endwhile; ?>
                        </div>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <form class="comment-form mt-2" data-recipe="<?= $row['recipe_id'] ?>">
                                <input type="hidden" name="recipe_id" value="<?= $row['recipe_id'] ?>">
                                <input type="text" class="form-control mb-1" name="comment_text" placeholder="Add a comment..." required>
                                <button type="submit" class="btn btn-primary btn-sm w-100">Comment</button>
                            </form>
                        <?php endif; ?>
                        <?php $commentsStmt->close(); ?>
                    </div>

                    <div class="author">
                        <?php if($row['profile_pic']): ?>
                        <img src="<?= htmlspecialchars($row['profile_pic']) ?>" alt="author">
                        <?php endif; ?>
                        <span><?= htmlspecialchars($row['first_name'].' '.$row['last_name']) ?></span>
                    </div>
                    </div>
                </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-center">No recipes yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal for Submitting a Recipe -->
<div id="recipeModal" class="modal" style="display:none; position:fixed; z-index:1050; left:0; top:0; width:100%; height:100%; overflow:auto; background-color:rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Submit Your Recipe</h5>
                <button type="button" id="closeModal" class="btn-close"></button>
            </div>
            <div class="modal-body">
                <form action="community_cookbook.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="ingredients" class="form-label">Ingredients</label>
                        <textarea class="form-control" id="ingredients" name="ingredients" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="instructions" class="form-label">Instructions</label>
                        <textarea class="form-control" id="instructions" name="instructions" rows="5" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="difficulty" class="form-label">Difficulty</label>
                            <select class="form-select" id="difficulty" name="difficulty" required>
                                <option value="Easy">Easy</option>
                                <option value="Medium">Medium</option>
                                <option value="Hard">Hard</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <?php mysqli_data_seek($catRes, 0); // Reset pointer for second loop ?>
                                <?php while($cat = $catRes->fetch_assoc()): ?>
                                    <option value="<?= $cat['category_id'] ?>"><?= $cat['category_name'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="cuisine_id" class="form-label">Cuisine</label>
                        <select class="form-select" id="cuisine_id" name="cuisine_id" required>
                            <?php mysqli_data_seek($cuiRes, 0); // Reset pointer for second loop ?>
                            <?php while($cui = $cuiRes->fetch_assoc()): ?>
                                <option value="<?= $cui['cuisine_id'] ?>"><?= $cui['cuisine_name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="cuisine_image" class="form-label">Recipe Image</label>
                        <input type="file" class="form-control" id="cuisine_image" name="cuisine_image" accept="image/*">
                        <img id="imagePreview" src="#" alt="Image Preview" class="mt-2 rounded" style="display:none; max-width: 100%; height: auto;">
                    </div>
                    <button type="submit" name="submit_recipe" class="btn btn-primary w-100">Submit Recipe</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Login/Registration Modals -->
<?php include 'modals/modals.php'; ?>

<?php include './includes/footer_tags.php'; ?>
<?php include './includes/script_tags.php'; ?>
<script src="./Assets/js/home.js?v=1.3"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Filter Functionality ---
    const filterCuisineEl = document.getElementById('filterCuisine');
    const filterCategoryEl = document.getElementById('filterCategory');
    const filterDifficultyEl = document.getElementById('filterDifficulty');

    function applyFilters() {
        const cuisine = (filterCuisineEl.value || '').toLowerCase().trim();
        const category = (filterCategoryEl.value || '').toLowerCase().trim();
        const difficulty = (filterDifficultyEl.value || '').toLowerCase().trim();

        document.querySelectorAll('.recipe-card-wrapper').forEach(card => {
            const cardCuisine = (card.dataset.cuisine || '').toLowerCase().trim();
            const cardCategory = (card.dataset.category || '').toLowerCase().trim();
            const cardDifficulty = (card.dataset.difficulty || '').toLowerCase().trim();

            const matchCuisine = !cuisine || cardCuisine === cuisine;
            const matchCategory = !category || cardCategory === category;
            const matchDifficulty = !difficulty || cardDifficulty === difficulty;

            card.style.display = (matchCuisine && matchCategory && matchDifficulty) ? '' : 'none';
        });
    }

    // Attach listeners to filters
    if (filterCuisineEl) filterCuisineEl.addEventListener('change', applyFilters);
    if (filterCategoryEl) filterCategoryEl.addEventListener('change', applyFilters);
    if (filterDifficultyEl) filterDifficultyEl.addEventListener('change', applyFilters);

    // Initial filter application
    applyFilters();

    // --- Modal Functionality ---
    const modal = document.getElementById('recipeModal');
    const openBtn = document.getElementById('openModal');
    const closeBtn = document.getElementById('closeModal');
    if (openBtn) {
        openBtn.onclick = () => modal.style.display = "block";
    }
    if (closeBtn) {
        closeBtn.onclick = () => modal.style.display = "none";
    }
    window.onclick = (event) => {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // --- Image Preview for Submission Modal ---
    const imageInput = document.getElementById('cuisine_image');
    const imagePreview = document.getElementById('imagePreview');
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // Toggle details
    document.querySelectorAll('.show-details-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const detailsEl = document.getElementById(targetId);
            if (detailsEl) {
                detailsEl.style.display = (detailsEl.style.display === 'none' || detailsEl.style.display === '') ? 'block' : 'none';
            }
        });
    });

    // Ratings AJAX
    document.querySelectorAll('.star').forEach(star => {
        star.addEventListener('click', function() {
            const recipe_id = this.dataset.recipe;
            const value = this.dataset.value;
            fetch('rating_handler.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `recipe_id=${recipe_id}&rating=${value}&recipe_type=community`
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) location.reload();
            });
        });
    });

    // Comments AJAX
    document.querySelectorAll('.comment-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new URLSearchParams(new FormData(this));
            formData.append('recipe_type', 'community');
            const commentsList = this.closest('.card-body').querySelector('.comments-list');
            
            fetch('comment_handler.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const newComment = document.createElement('p');
                    newComment.className = 'text-muted';
                    newComment.innerHTML = `<strong>${data.first_name}:</strong> ${data.comment_text}`;
                    commentsList.appendChild(newComment);
                    this.reset();
                }
            });
        });
    });
});
</script>
</body>
</html>
