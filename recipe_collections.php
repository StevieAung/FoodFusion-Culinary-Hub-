<?php
session_start();
include './Database/db.php';

// Fetch all admin-curated recipes
$sql = "SELECT recipe_id, title, description, image, cuisine, difficulty_level, created_at 
        FROM recipe_collections 
        ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Recipe Collections</title>
    <?php include './includes/head_tags.php'; ?>
    <style>
        /* Recipe Card Images - responsive 4:3 aspect ratio */
        .card-img-wrapper {
            width: 100%;
            padding-top: 75%;  /* 4:3 aspect ratio */
            position: relative;
            overflow: hidden;
        }

        .card-img-wrapper img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;  /* Fill container, crop if necessary */
        }

        /* Card layout for consistent height */
        .recipe-card .card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .recipe-card .card-body {
            flex-grow: 1;
        }
    </style>
</head>
<body class="bg-warning-subtle">
    <?php include './includes/navbar.php'; ?>

    <div class="container py-5">
        <h2 class="text-center mb-4">Admin-Curated Recipe Collections</h2>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-md-6">
                <select id="filterCuisine" class="form-select">
                    <option value="">All Cuisines</option>
                    <option value="Italian">Italian</option>
                    <option value="Asian">Asian</option>
                    <option value="American">American</option>
                    <option value="Middle Eastern">Middle Eastern</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="col-md-6">
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
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4 recipe-card" 
                         data-cuisine="<?= htmlspecialchars($row['cuisine']) ?>" 
                         data-difficulty="<?= htmlspecialchars($row['difficulty_level']) ?>">
                        <div class="card h-100 shadow-lg">
                            <div class="card-img-wrapper">
                                <?php if (!empty($row['image'])): ?>
                                    <img src="./Assets/images/recipes/<?= htmlspecialchars($row['image']) ?>" 
                                         alt="<?= htmlspecialchars($row['title']) ?>">
                                <?php else: ?>
                                    <img src="./Assets/images/recipes/default_recipe.jpg" 
                                         alt="No Image">
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars(substr($row['description'], 0, 120)) ?>...</p>
                                <p>
                                    <?php if (!empty($row['cuisine'])): ?>
                                        <span class="badge bg-primary"><?= htmlspecialchars($row['cuisine']) ?></span>
                                    <?php endif; ?>
                                    <span class="badge bg-warning text-dark"><?= htmlspecialchars($row['difficulty_level']) ?></span>
                                </p>
                                <a href="recipe_view.php?id=<?= $row['recipe_id'] ?>" 
                                   class="btn btn-sm btn-outline-dark">View Recipe</a>
                            </div>
                            <div class="card-footer text-muted small">
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
    <div class="m-3">
        <a href="home_page.php" class="btn btn-outline-secondary">← Back to Home</a>
    </div>

    <?php include './includes/footer_tags.php'; ?>
    <?php include './includes/script_tags.php'; ?>

    <script>
        // Filtering logic
        document.querySelectorAll('#filterCuisine, #filterDifficulty').forEach(select => {
            select.addEventListener('change', function() {
                const cuisine = document.getElementById('filterCuisine').value;
                const difficulty = document.getElementById('filterDifficulty').value;

                document.querySelectorAll('.recipe-card').forEach(card => {
                    const matchCuisine = !cuisine || card.dataset.cuisine === cuisine;
                    const matchDifficulty = !difficulty || card.dataset.difficulty === difficulty;
                    card.style.display = (matchCuisine && matchDifficulty) ? 'block' : 'none';
                });
            });
        });
    </script>
</body>
</html>
