<?php
session_start();
include './Database/db.php';

// (Optional) Check if user is an admin
// if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
//     header("Location: admin_login.php");
//     exit();
// }

$message = "";

// Fetch categories and cuisines for dropdowns
$categories_result = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name ASC");
$cuisines_result   = $conn->query("SELECT cuisine_id, cuisine_name FROM cuisine_types ORDER BY cuisine_name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $ingredients = trim($_POST['ingredients']);
    $instructions = trim($_POST['instructions']);
    $category_id = intval($_POST['category_id']);
    $cuisine_id = intval($_POST['cuisine_id']);
    $difficulty = $_POST['difficulty_level'];

    $imageName = null;

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $targetDir = __DIR__ . "/Assets/images/recipes/";
        $imageName = time() . "_" . basename($_FILES['image']['name']); // unique filename
        $targetFile = $targetDir . $imageName;

        // Validate file type
        $allowedTypes = ['image/jpeg','image/png','image/gif'];
        if (in_array($_FILES['image']['type'], $allowedTypes)) {
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                // File uploaded successfully
            } else {
                $message = "<div class='alert alert-danger'>Error uploading image.</div>";
                $imageName = null;
            }
        } else {
            $message = "<div class='alert alert-danger'>Invalid file type. Only JPG, PNG, GIF allowed.</div>";
            $imageName = null;
        }
    }

    if (empty($message)) {
        $stmt = $conn->prepare("
            INSERT INTO recipe_collections 
            (title, description, ingredients, instructions, image, difficulty_level, category_id, cuisine_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssssii", 
            $title, $description, $ingredients, $instructions, 
            $imageName, $difficulty, $category_id, $cuisine_id
        );

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Recipe added successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Database error: " . htmlspecialchars($stmt->error) . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Recipe</title>
    <?php include './includes/head_tags.php'; ?>
</head>
<body class="bg-light">
<?php include './includes/navbar.php'; ?>

<div class="container py-5">
    <div class="card shadow p-4">
        <h2 class="mb-4">Add New Recipe</h2>
        <?= $message ?>

        <form action="add_recipe.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Recipe Title</label>
                <input type="text" name="title" id="title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Short Description</label>
                <textarea name="description" id="description" class="form-control" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label for="ingredients" class="form-label">Ingredients</label>
                <textarea name="ingredients" id="ingredients" class="form-control" rows="4" required></textarea>
            </div>

            <div class="mb-3">
                <label for="instructions" class="form-label">Instructions</label>
                <textarea name="instructions" id="instructions" class="form-control" rows="5" required></textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="category_id" class="form-label">Category</label>
                    <select name="category_id" id="category_id" class="form-select" required>
                        <option value="">-- Select Category --</option>
                        <?php if ($categories_result && $categories_result->num_rows > 0): ?>
                            <?php while($cat = $categories_result->fetch_assoc()): ?>
                                <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="cuisine_id" class="form-label">Cuisine</label>
                    <select name="cuisine_id" id="cuisine_id" class="form-select" required>
                        <option value="">-- Select Cuisine --</option>
                        <?php if ($cuisines_result && $cuisines_result->num_rows > 0): ?>
                            <?php while($c = $cuisines_result->fetch_assoc()): ?>
                                <option value="<?= $c['cuisine_id'] ?>"><?= htmlspecialchars($c['cuisine_name']) ?></option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="difficulty_level" class="form-label">Difficulty</label>
                    <select name="difficulty_level" id="difficulty_level" class="form-select" required>
                        <option value="Easy">Easy</option>
                        <option value="Medium">Medium</option>
                        <option value="Hard">Hard</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Recipe Image</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*">
                <small class="text-muted">Allowed: JPG, PNG, GIF. Max size ~2MB.</small>
            </div>

            <button type="submit" name="submit" class="btn btn-success">Save Recipe</button>
            <a href="recipe_collections.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include './includes/footer_tags.php'; ?>
<?php include './includes/script_tags.php'; ?>
</body>
</html>
