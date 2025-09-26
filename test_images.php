<?php
session_start();
include './Database/db.php';

// Fetch all recipes with images
$sql = "SELECT recipe_id, title, image FROM recipe_collections ORDER BY recipe_id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test Recipe Images</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .recipe {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 20px;
            width: 300px;
        }
        .recipe img {
            max-width: 100%;
            height: auto;
            border: 1px solid #ccc;
            margin-top: 10px;
        }
        .debug {
            font-size: 14px;
            color: #333;
            background: #f9f9f9;
            padding: 5px;
        }
    </style>
</head>
<body>
    <h2>Recipe Image Debug</h2>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="recipe">
                <strong><?= htmlspecialchars($row['title']) ?></strong><br>
                <div class="debug">
                    Path: ./Assets/images/recipes/<?= htmlspecialchars($row['image']) ?>
                </div>
                <img src="./Assets/images/recipes/<?= htmlspecialchars($row['image']) ?>" 
                     alt="<?= htmlspecialchars($row['title']) ?>"
                     onerror="this.style.border='3px solid red'; this.alt='Image not found';">
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No recipes found in database.</p>
    <?php endif; ?>
</body>
</html>
