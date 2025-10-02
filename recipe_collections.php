<?php
session_start();
include './Database/db.php';

// Fetch recipes with cuisine/category names (left joins so nulls are OK)
$sql = "
  SELECT 
    rc.recipe_id,
    rc.title,
    rc.description,
    rc.image,
    rc.ingredients,
    rc.instructions,
    rc.difficulty_level,
    rc.created_at,
    ct.cuisine_name,
    c.category_name
  FROM recipe_collections rc
  LEFT JOIN cuisine_types ct ON rc.cuisine_id = ct.cuisine_id
  LEFT JOIN categories c ON rc.category_id = c.category_id
  ORDER BY rc.created_at DESC
";
$result = $conn->query($sql);

// Fetch cuisines for the filter dropdown
$cuisines_result = $conn->query("SELECT cuisine_name FROM cuisine_types ORDER BY cuisine_name ASC");

// Fetch categories for the filter dropdown
$categories_result = $conn->query("SELECT category_name FROM categories ORDER BY category_name ASC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Recipe Collections</title>
    <?php include './includes/head_tags.php'; ?>
    <style>
        .recipe-card-wrapper { padding-left: 10px; padding-right: 10px; }
        .recipe-card { background: #fff; padding: 15px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); transition: .3s; display:flex; flex-direction:column; height:100%; }
        .recipe-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.12); }
        .card-img-wrapper { width:100%; padding-top:75%; position:relative; overflow:hidden; border-radius:10px; margin-bottom:12px; }
        .card-img-wrapper img { position:absolute; top:0; left:0; width:100%; height:100%; object-fit:cover; }
        .recipe-card h5 { margin-top:0; color:#222; font-size:1.15rem; }
        .recipe-card p { color:#555; font-size:.9em; line-height:1.4; }
        .recipe-card .badge { margin-right:5px; }
        .details { display:none; margin-top:10px; background:#f9f9f9; padding:10px; border-radius:8px; font-size:.9em; }
        .details.show { display:block; }
        .show-details-btn { background:#ffc107; color:#000; border:none; border-radius:5px; padding:6px 12px; cursor:pointer; transition:.2s; }
        .show-details-btn:hover { background:#e0ac00; }
        .card-footer { font-size:.85em; color:#777; margin-top:auto; padding-top:10px; }

        @media(max-width:768px){
            .recipe-card { padding:15px; }
            .recipe-card h5 { font-size:1.1rem; }
        }
    </style>
</head>
<body class="bg-warning-subtle">
    <?php include './includes/navbar.php'; ?>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="home_page.php" class="btn btn-outline-secondary">← Back to Home</a>
            <h2 class="text-center mb-0 fw-semibold">Recipe Collections</h2>
            <div style="width:150.38px;"></div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <select id="filterCuisine" class="form-select">
                    <option value="">All Cuisines</option>
                    <?php if ($cuisines_result && $cuisines_result->num_rows > 0): ?>
                    <?php while($c = $cuisines_result->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($c['cuisine_name']) ?>"><?= htmlspecialchars($c['cuisine_name']) ?></option>
                    <?php endwhile; ?>
                    <?php endif; ?>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <select id="filterCategory" class="form-select">
                    <option value="">All Categories</option>
                    <option value="Dessert">Dessert</option>
                    <option value="Appetizer">Appetizer</option>
                    <option value="Main Course">Main Course</option>
                    <option value="Beverage">Beverage</option>
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

        <!-- Recipe Grid -->
        <div class="row" id="recipeGrid">
            <?php if ($result === false): ?>
                <div class="alert alert-danger">Database query error: <?= htmlspecialchars($conn->error) ?></div>
            <?php elseif ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): 
                    //Prepare data attributes (fallback to 'Other' for null cuisines)
                    $cardCuisine = $row['cuisine_name'] ?? 'Other';
                    $cardDifficulty = $row['difficulty_level'] ?? '';
                    $cardCategory = $row['category_name'] ?? '';
                    // image path check
                    $imgFile = $row['image'] ?? '';
                    $localImagePath = __DIR__ . '/Assets/images/recipes/' . $imgFile;
                    $imageUrl = (!empty($imgFile) && file_exists($localImagePath)) ? "./Assets/images/recipes/".rawurlencode($imgFile) : "./Assets/images/recipes/default_recipe.jpg";
                ?>
                <div class="col-md-4 mb-4 recipe-card-wrapper"
                    data-cuisine="<?= htmlspecialchars($cardCuisine) ?>"
                    data-category="<?= htmlspecialchars($cardCategory) ?>"
                    data-difficulty="<?= htmlspecialchars($cardDifficulty) ?>">
                    <div class="recipe-card">
                        <div class="card-img-wrapper">
                            <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($row['title'] ?? 'Recipe Image') ?>">
                        </div>

                        <h5><?= htmlspecialchars($row['title']) ?></h5>
                        <p><?= nl2br(htmlspecialchars(mb_substr($row['description'] ?? '', 0, 120))) ?><?= (strlen($row['description'] ?? '') > 120) ? '...' : '' ?></p>

                        <p>
                            <?php if (!empty($cardCuisine) && $cardCuisine !== 'Other'): ?>
                                <span class="badge bg-primary"><?= htmlspecialchars($cardCuisine) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($cardCategory)): ?>
                                <span class="badge bg-success"><?= htmlspecialchars($cardCategory) ?></span>
                            <?php endif; ?>
                            <span class="badge bg-warning text-dark"><?= htmlspecialchars($cardDifficulty) ?></span>
                        </p>
                        <!-- Show Details Button -->
                        <button type="button" class="show-details-btn btn btn-sm" data-target="details-<?= $row['recipe_id'] ?>">Show Details</button>

                        <div class="details" id="details-<?= $row['recipe_id'] ?>" role="region" aria-hidden="true">
                            <h6>Description:</h6>
                            <p><?= nl2br(htmlspecialchars($row['description'] ?? '')) ?></p>

                            <h6>Ingredients:</h6>
                            <p><?= !empty($row['ingredients']) ? nl2br(htmlspecialchars($row['ingredients'])) : 'Ingredients not added yet.' ?></p>

                            <h6>Instructions:</h6>
                            <p><?= !empty($row['instructions']) ? nl2br(htmlspecialchars($row['instructions'])) : 'Instructions not added yet.' ?></p>
                        </div>

                        <div class="card-footer text-muted small mt-2">
                            Added on <?= date("M d, Y", strtotime($row['created_at'])) ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">No curated recipes found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Login/Registration Modals -->
    <?php include 'modals/modals.php'; ?>

    <?php include './includes/footer_tags.php'; ?>
    <?php include './includes/script_tags.php'; ?>
    <script src="./Assets/js/home.js?v=1.3"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
        const filterCuisineEl = document.getElementById('filterCuisine');
        const filterCategoryEl = document.getElementById('filterCategory');
        const filterDifficultyEl = document.getElementById('filterDifficulty');

        function applyFilters() {
            const cuisine = (filterCuisineEl.value || '').toLowerCase().trim();
            const category = (filterCategoryEl.value || '').toLowerCase().trim();
            const difficulty = (filterDifficultyEl.value || '').toLowerCase().trim();

            document.querySelectorAll('.recipe-card-wrapper').forEach(card => {
                const cardCuisine = (card.dataset.cuisine || 'other').toLowerCase().trim();
                const cardCategory = (card.dataset.category || '').toLowerCase().trim();
                const cardDifficulty = (card.dataset.difficulty || '').toLowerCase().trim();

                const matchCuisine = !cuisine || cardCuisine === cuisine;
                const matchCategory = !category || cardCategory === category;
                const matchDifficulty = !difficulty || cardDifficulty === difficulty;

                card.style.display = (matchCuisine && matchCategory && matchDifficulty) ? '' : 'none';
            });
        }

        // Attach listeners
        filterCuisineEl.addEventListener('change', applyFilters);
        filterCategoryEl.addEventListener('change', applyFilters);
        filterDifficultyEl.addEventListener('change', applyFilters);

        // initialize
        applyFilters();

        // details toggle
        document.querySelectorAll('.show-details-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const el = document.getElementById(targetId);
                if (!el) return;
                el.classList.toggle('show');
                const isShown = el.classList.contains('show');
                el.setAttribute('aria-hidden', isShown ? 'false' : 'true');
                this.textContent = isShown ? 'Hide Details' : 'Show Details';
            });
        });
    });
    // Logout functionality is now handled by home.js
    </script>
</body>
</html>
