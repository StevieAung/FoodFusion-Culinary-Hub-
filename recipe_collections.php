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
        .recipe-card-wrapper {
            padding-left: 10px;
            padding-right: 10px;
        }
        .recipe-card {
            background: #fff;
            padding: 15px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: 0.3s;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .recipe-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }

        .card-img-wrapper {
            width: 100%;
            padding-top: 75%;
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            margin-bottom: 12px;
        }
        .card-img-wrapper img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .recipe-card h5 { margin-top: 0; color: #222; font-size: 1.15rem; }
        .recipe-card p { color: #555; font-size: 0.9em; line-height: 1.4; }
        .recipe-card .badge { margin-right: 5px; }

        .details { display: none; margin-top: 10px; background: #f9f9f9; padding: 10px; border-radius: 8px; font-size: 0.9em; }

        .show-details-btn { background: #ffc107; color: #000; border: none; border-radius: 5px; padding: 6px 12px; cursor: pointer; transition: 0.3s; }
        .show-details-btn:hover { background: #e0ac00; }

        .card-footer { font-size: 0.85em; color: #777; margin-top: auto; padding-top: 10px; }

        @media(max-width:768px){
            .recipe-card { padding: 15px; }
            .recipe-card h5 { font-size: 1.1rem; }
        }
    </style>

</head>
<body class="bg-warning-subtle">
    <?php include './includes/navbar.php'; ?>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="home_page.php" class="btn btn-outline-secondary">← Back to Home</a>
            <h2 class="text-center mb-0">Admin-Curated Recipe Collections</h2>
            <div style="width: 150.38px;"></div> <!-- Spacer to keep title centered -->
        </div>

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
                <div class="col-md-4 mb-4 recipe-card-wrapper" 
                     data-cuisine="<?= htmlspecialchars($row['cuisine']) ?>" 
                     data-difficulty="<?= htmlspecialchars($row['difficulty_level']) ?>">
                    <div class="recipe-card">
                        <div class="card-img-wrapper">
                            <?php if (!empty($row['image'])): ?>
                                <img src="./Assets/images/recipes/<?= htmlspecialchars($row['image']) ?>" 
                                     alt="<?= htmlspecialchars($row['title']) ?>">
                            <?php else: ?>
                                <img src="./Assets/images/recipes/default_recipe.jpg" alt="No Image">
                            <?php endif; ?>
                        </div>

                        <h5><?= htmlspecialchars($row['title']) ?></h5>
                        <p><?= nl2br(htmlspecialchars(substr($row['description'],0,120))) ?>...</p>

                        <p>
                            <?php if (!empty($row['cuisine'])): ?>
                                <span class="badge bg-primary"><?= htmlspecialchars($row['cuisine']) ?></span>
                            <?php endif; ?>
                            <span class="badge bg-warning text-dark"><?= htmlspecialchars($row['difficulty_level']) ?></span>
                        </p>

                        <!-- Show Details Button -->
                        <button class="show-details-btn btn btn-sm btn-warning" data-target="details-<?= $row['recipe_id'] ?>">Show Details</button>

                        <div class="details" id="details-<?= $row['recipe_id'] ?>">
                            <h6>Description:</h6>
                            <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                            <h6>Ingredients:</h6>
                            <p>Ingredients not added yet.</p>
                            <h6>Instructions:</h6>
                            <p>Instructions not added yet.</p>
                        </div>

                        <a href="recipe_view.php?id=<?= $row['recipe_id'] ?>" 
                           class="btn btn-sm btn-outline-dark mt-2">View Full Recipe</a>

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
    <div class="m-3">
        
    </div>

    <?php include './includes/footer_tags.php'; ?>
    <?php include './includes/script_tags.php'; ?>

    <script>
        // Filtering logic
        document.querySelectorAll('#filterCuisine, #filterDifficulty').forEach(select => {
            select.addEventListener('change', function() {
                const cuisine = document.getElementById('filterCuisine').value;
                const difficulty = document.getElementById('filterDifficulty').value;

                document.querySelectorAll('.recipe-card-wrapper').forEach(card => {
                    const matchCuisine = !cuisine || card.dataset.cuisine === cuisine;
                    const matchDifficulty = !difficulty || card.dataset.difficulty === difficulty;
                    card.style.display = (matchCuisine && matchDifficulty) ? 'block' : 'none';
                });
            });
        });

        // Collapsible details
        document.querySelectorAll('.show-details-btn').forEach(btn=>{
            btn.addEventListener('click', function(){
                const targetId = this.getAttribute('data-target');
                const el = document.getElementById(targetId);
                if(el) el.style.display = (el.style.display==='block') ? 'none' : 'block';
            });
        });
    </script>
</body>
</html>
