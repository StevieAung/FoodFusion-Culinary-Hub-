<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
include './Database/db.php';

$uploadDir = 'uploads/cuisine/';

// Handle recipe submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_recipe']) && isset($_SESSION['user_id'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $ingredients = $_POST['ingredients'];
    $instructions = $_POST['instructions'];
    $cuisine = $_POST['cuisine'];
    $difficulty = $_POST['difficulty'];
    $user_id = $_SESSION['user_id'];
    $cuisine_image = NULL;

    if(isset($_FILES['cuisine_image']) && $_FILES['cuisine_image']['error'] == 0){
        if(!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $filename = time().'_'.basename($_FILES['cuisine_image']['name']);
        $targetFile = $uploadDir . $filename;

        if(!move_uploaded_file($_FILES['cuisine_image']['tmp_name'], $targetFile)){
            $error_upload = "Failed to upload cuisine image. Check folder permissions.";
        } else {
            $cuisine_image = $targetFile;
        }
    }

    $stmt = $conn->prepare("
        INSERT INTO community_recipes
        (title, description, ingredients, instructions, cuisine, difficulty_level, user_id, cuisine_image)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssssssis", $title, $description, $ingredients, $instructions, $cuisine, $difficulty, $user_id, $cuisine_image);
    $stmt->execute();
}

// Fetch recipes with ratings/comments
$sql = "SELECT r.*, u.first_name, u.last_name, u.profile_pic,
        COALESCE(AVG(rt.rating),0) AS avg_rating,
        COUNT(c.comment_id) AS comment_count
        FROM community_recipes r
        JOIN users u ON r.user_id = u.user_id
        LEFT JOIN ratings rt ON r.recipe_id = rt.recipe_id
        LEFT JOIN comments c ON r.recipe_id = c.recipe_id
        GROUP BY r.recipe_id
        ORDER BY r.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Community Cookbook</title>
<?php include './includes/head_tags.php'; ?>
<style>
/* General */
body { 
  font-family:'Segoe UI',sans-serif; 
  background:#fff3cd; 
  margin:0; /* remove body margin */
  padding:0; 
}
h1 { text-align:center; margin:30px 0; color:#333; }

/* Remove extra space for nav/footer */
.navbar, footer { margin:0 !important; }

/* Back Home Button */
#backHome { 
  display:inline-block; 
  margin:20px; 
  position:relative; 
  z-index:10;
}

/* Recipes Grid */
.recipes { 
  max-width:1200px; 
  margin:20px auto 60px auto; 
  display:grid; 
  grid-template-columns: repeat(auto-fill,minmax(300px,1fr)); 
  gap:20px; 
  padding:0 15px;
}
.recipe-card { 
  background:#fff; 
  padding:20px; 
  border-radius:15px; 
  box-shadow:0 4px 15px rgba(0,0,0,0.1); 
  transition:0.3s; 
}
.recipe-card:hover { 
  transform:translateY(-5px); 
  box-shadow:0 8px 20px rgba(0,0,0,0.15); 
}
.recipe-card img.cuisine-img { 
  width:100%; 
  max-height:200px; 
  object-fit:cover; 
  border-radius:10px; 
  margin-bottom:10px; 
}
.recipe-card h3 { margin-top:0; color:#222; font-size:1.25rem; }
.recipe-card p { color:#555; font-size:0.95em; line-height:1.4; }
.recipe-card .meta { font-size:0.85em; color:#777; margin:8px 0; }

/* Collapsible Details */
.details { 
  display:none; 
  margin-top:10px; 
  background:#f9f9f9; 
  padding:10px; 
  border-radius:8px; 
  font-size:0.9em; 
}

/* Buttons */
.show-details-btn { 
  background:#ffc107; 
  color:#000; 
  border:none; 
  border-radius:5px; 
  padding:6px 12px; 
  cursor:pointer; 
  transition:0.3s; 
}
.show-details-btn:hover { background:#e0ac00; }

#openModal { 
  display:block; 
  margin:0 auto 20px auto; 
}

/* Modal */
.modal { 
  display:none; 
  position:fixed; 
  z-index:1000; 
  left:0; 
  top:0; 
  width:100%; 
  height:100%; 
  overflow:auto; 
  background: rgba(0,0,0,0.5); 
}
.modal-content { 
  background:#fff; 
  margin:50px auto; 
  padding:20px; 
  border-radius:12px; 
  max-width:600px; 
  position:relative; 
  box-shadow:0 4px 15px rgba(0,0,0,0.2);
}
#closeModal { position:absolute; top:10px; right:15px; cursor:pointer; font-size:1.5em; }

/* Recipe Form */
.recipe-form input, 
.recipe-form textarea, 
.recipe-form select, 
.recipe-form button { 
  width:100%; 
  padding:10px; 
  margin-bottom:12px; 
  border-radius:8px; 
  border:1px solid #ccc; 
  font-size:1em; 
}
.recipe-form button { 
  background:#28a745; 
  color:#fff; 
  border:none; 
  cursor:pointer; 
  transition:0.3s; 
}
.recipe-form button:hover { background:#218838; }
#cuisineImagePreview { 
  display:none; 
  max-width:100%; 
  max-height:200px; 
  border-radius:8px; 
  margin-top:8px; 
}

/* Loading overlay */
#loading { 
  display:none; 
  position:fixed; 
  top:0; 
  left:0; 
  width:100%; 
  height:100%; 
  background:rgba(0,0,0,0.5); 
  z-index:9999; 
  text-align:center; 
  color:#fff; 
  font-size:1.5em; 
  padding-top:200px; 
}

/* Star rating */
.star { font-size:1.2em; color:#ccc; cursor:pointer; margin-right:2px; }
.star.selected, .star.hover { color:#ffc107; }

/* Responsive */
@media(max-width:768px){ 
  h1 { font-size:1.75rem; }
  .recipes{ grid-template-columns:1fr; }
  #backHome { margin:15px; font-size:0.9rem; }
}
</style>
</head>
<body>
 <?php include './includes/navbar.php'; ?>

<!-- Back button -->
<div class="container-fluid px-3">
  <a href="home_page.php" class="btn btn-outline-secondary" id="backHome">← Back to Home</a>
</div>

<h1>Community Cookbook</h1>
<div id="loading">Submitting your recipe...</div>

<?php if(isset($_SESSION['user_id'])): ?>
<button id="openModal" class="btn btn-warning">Submit a Recipe</button>
<?php endif; ?>
<!-- Include Modal -->
<?php include 'modals/recipe_submit.php'; ?>
<div class="recipes">
    <?php if($result && $result->num_rows>0): ?>
        <?php while($row=$result->fetch_assoc()): ?>
        <div class="recipe-card">
            <?php if($row['cuisine_image']): ?>
                <img src="<?= htmlspecialchars($row['cuisine_image']) ?>" class="cuisine-img">
            <?php endif; ?>
            <h3><?= htmlspecialchars($row['title']) ?></h3>
            <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
            <p class="meta">
                <strong>Cuisine:</strong> <?= htmlspecialchars($row['cuisine']) ?> |
                <strong>Difficulty:</strong> <?= htmlspecialchars($row['difficulty_level']) ?> |
                <strong>By:</strong> <?= htmlspecialchars($row['first_name'].' '.$row['last_name']) ?>
            </p>

            <button class="show-details-btn" data-target="details-<?= $row['recipe_id'] ?>">Show Details</button>
            <div class="details" id="details-<?= $row['recipe_id'] ?>">
                <h4>Ingredients:</h4>
                <pre><?= htmlspecialchars($row['ingredients']) ?></pre>
                <h4>Instructions:</h4>
                <pre><?= htmlspecialchars($row['instructions']) ?></pre>
            </div>

            <!-- Ratings -->
            <div class="mt-2">
                <strong>Rating:</strong>
                <?php for($i=1;$i<=5;$i++): ?>
                    <span class="star <?= $i <= round($row['avg_rating']) ? 'selected' : '' ?>" data-recipe="<?= $row['recipe_id'] ?>" data-value="<?= $i ?>">&#9733;</span>
                <?php endfor; ?>
                <span>(<?= round($row['avg_rating'],1) ?>)</span>
            </div>

            <!-- Comments -->
            <div class="comments mt-2">
                <div class="comments-list">
                    <?php
                    // Fetch existing comments for this recipe
                    $commentsRes = $conn->query("
                        SELECT c.comment_text, u.first_name, u.last_name
                        FROM comments c
                        JOIN users u ON c.user_id = u.user_id
                        WHERE c.recipe_id = ".$row['recipe_id']."
                        ORDER BY c.created_at ASC
                    ");
                    while($comment = $commentsRes->fetch_assoc()): ?>
                        <p class="text-muted"><strong><?= htmlspecialchars($comment['first_name']) ?>:</strong> <?= htmlspecialchars($comment['comment_text']) ?></p>
                    <?php endwhile; ?>
                </div>
                <?php if(isset($_SESSION['user_id'])): ?>
                <form class="comment-form" data-recipe="<?= $row['recipe_id'] ?>">
                    <input type="hidden" name="recipe_id" value="<?= $row['recipe_id'] ?>">
                    <input type="text" name="comment_text" placeholder="Add a comment..." required>
                    <button type="submit" class="btn btn-primary btn-sm">Comment</button>
                </form>
                <?php endif; ?>
            </div>

        </div>
        <?php endwhile; ?>
    <?php else: ?>
    <p style="text-align:center;">No recipes yet.</p>
    <?php endif; ?>
</div>


<?php include './includes/footer_tags.php'; ?>
<?php include './includes/script_tags.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function(){

    // Modal open/close
    const modal = document.getElementById('recipeModal');
    const openBtn = document.getElementById('openModal');
    const closeBtn = document.getElementById('closeModal');
    if(openBtn) openBtn.onclick = ()=> modal.style.display='block';
    if(closeBtn) closeBtn.onclick = ()=> modal.style.display='none';
    window.onclick = (e)=> { if(e.target==modal) modal.style.display='none'; }

    // Loading overlay
    const modalForm = document.getElementById('modalRecipeForm');
    if(modalForm){
        modalForm.addEventListener('submit', ()=>{
            document.getElementById('loading').style.display='block';
        });
    }

    // Collapsible details
    document.querySelectorAll('.show-details-btn').forEach(btn=>{
        btn.addEventListener('click', function(){
            const targetId = this.getAttribute('data-target');
            const el = document.getElementById(targetId);
            if(el) el.style.display = (el.style.display==='block') ? 'none' : 'block';
        });
    });

    // Cuisine image preview
    const cuisineInput = document.getElementById('cuisineImageInput');
    const cuisinePreview = document.getElementById('cuisineImagePreview');
    if(cuisineInput){
        cuisineInput.addEventListener('change', function(){
            const file = this.files[0];
            if(file){
                const reader = new FileReader();
                reader.onload = function(e){
                    cuisinePreview.src = e.target.result;
                    cuisinePreview.style.display='block';
                }
                reader.readAsDataURL(file);
            } else {
                cuisinePreview.src = '#';
                cuisinePreview.style.display='none';
            }
        });
    }

    // Rating click (submit via AJAX to rating_handler.php)
    document.querySelectorAll('.star').forEach(star=>{
        star.addEventListener('click', function(){
            const recipe_id = this.getAttribute('data-recipe');
            const value = this.getAttribute('data-value');
            fetch('rating_handler.php', {
                method:'POST',
                headers:{'Content-Type':'application/x-www-form-urlencoded'},
                body:`recipe_id=${recipe_id}&rating=${value}`
            }).then(()=> location.reload());
        });
    });

    // 💬 Handle comment submissions without reload
document.querySelectorAll('.comment-form').forEach(form=>{
    form.addEventListener('submit', function(e){
        e.preventDefault();

        const recipe_id = this.querySelector('[name="recipe_id"]').value;
        const comment_text = this.querySelector('[name="comment_text"]').value.trim();
        const commentsList = this.parentElement.querySelector('.comments-list');

        if(!comment_text) return;

        fetch('comment_handler.php', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: `recipe_id=${encodeURIComponent(recipe_id)}&comment_text=${encodeURIComponent(comment_text)}`
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                // Add new comment
                const newComment = document.createElement('p');
                newComment.classList.add('text-muted','mt-1','mb-1');
                newComment.innerHTML = `<strong>${data.first_name}:</strong> ${data.comment_text}`;
                commentsList.appendChild(newComment);

                // Clear input
                this.querySelector('[name="comment_text"]').value = '';
            } else {
                alert('Failed to add comment: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(err => console.error(err));
    });
});
});
</script>

</body>
</html>
