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
    <title><?= htmlspecialchars($recipe['title']) ?> | FoodFusion</title>
    <?php include './includes/head_tags.php'; ?>
</head>
<body class="bg-warning-subtle">
    <?php include './includes/navbar.php'; ?>

    <div class="container py-5">
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
                        <h5>Description</h5>
                        <p class="fs-5"><?= nl2br(htmlspecialchars($recipe['description'])) ?></p>

                        <?php if (!empty($recipe['ingredients'])): ?>
                            <h5>Ingredients</h5>
                            <p><?= nl2br(htmlspecialchars($recipe['ingredients'])) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($recipe['instructions'])): ?>
                            <h5>Instructions</h5>
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
