<?php
session_start();
include './Database/db.php';

// Get recipe ID from query string
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: recipe_collections.php");
    exit;
}

$recipe_id = intval($_GET['id']);

// Fetch recipe details
$sql = "SELECT recipe_id, title, description, image, ingredients, instructions, cuisine, difficulty_level, created_at 
        FROM recipe_collections 
        WHERE recipe_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$result = $stmt->get_result();
$recipe = $result->fetch_assoc();
$stmt->close();

// If not found
if (!$recipe) {
    header("Location: recipe_collections.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($recipe['title']) ?> - FoodFusion</title>
    <?php include './includes/head_tags.php'; ?>
    <style>
        body { background-color: #fdfaf3; }
        .recipe-view-container {
            max-width: 800px; /* Reduced max-width */
            margin: 30px auto;
            background: #fff;
            padding: 30px; /* Reduced padding */
            border-radius: 15px; /* Slightly smaller radius */
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }
        .recipe-header img {
            width: 100%;
            height: auto;
            max-height: 400px; /* Reduced max-height */
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .recipe-title {
            font-size: 2rem; /* Reduced font size */
            font-weight: bold;
            color: #333;
        }
        .recipe-meta span {
            margin-right: 10px;
            font-size: 0.95rem; /* Reduced font size */
        }
        .section-title {
            font-size: 1.3rem; /* Reduced font size */
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 15px;
            border-bottom: 2px solid #ffc107;
            padding-bottom: 5px;
        }
        .recipe-content p, .recipe-content ul, .recipe-content ol {
            font-size: 1rem; /* Reduced font size */
            line-height: 1.6;
            color: #555;
        }
        .back-btn {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include './includes/navbar.php'; ?>

    <div class="container recipe-view-container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg">
                    <?php if (!empty($recipe['image'])): ?>
                        <img src="Assets/images/recipes/<?= htmlspecialchars($recipe['image']) ?>" 
                             class="card-img-top" 
                             alt="<?= htmlspecialchars($recipe['title']) ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <h2 class="card-title mb-3"><?= htmlspecialchars($recipe['title']) ?></h2>
                        <p>
                            <?php if (!empty($recipe['cuisine'])): ?>
                                <span class="badge bg-primary"><?= htmlspecialchars($recipe['cuisine']) ?></span>
                            <?php endif; ?>
                            <span class="badge bg-warning text-dark"><?= htmlspecialchars($recipe['difficulty_level']) ?></span>
                        </p>
                        <p class="text-muted small">Added on <?= date("M d, Y", strtotime($recipe['created_at'])) ?></p>
                        <hr>
                        <h5 class="section-title">Description</h5>
                        <p class="fs-5"><?= nl2br(htmlspecialchars($recipe['description'])) ?></p>

                        <?php if (!empty($recipe['ingredients'])): ?>
                            <h5 class="section-title">Ingredients</h5>
                            <p><?= nl2br(htmlspecialchars($recipe['ingredients'])) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($recipe['instructions'])): ?>
                            <h5 class="section-title">Instructions</h5>
                            <p><?= nl2br(htmlspecialchars($recipe['instructions'])) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="m-3">
                    <a href="recipe_collections.php" class="btn btn-outline-secondary">← Back to Recipes</a>
                </div>
            </div>
        </div>
    </div>

    <?php include './includes/footer_tags.php'; ?>
    <?php include './includes/script_tags.php'; ?>
</body>
</html>
