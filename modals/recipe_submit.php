<?php
if(!isset($_SESSION)) session_start();
if(!isset($_SESSION['user_id'])){
    echo "<p>Please log in to submit a recipe.</p>";
    return;
}
?>
<div id="recipeModal" class="modal">
    <div class="modal-content">
        <span id="closeModal">&times;</span>
        <h2>Submit a Recipe</h2>
        <form method="POST" enctype="multipart/form-data" class="recipe-form" id="modalRecipeForm" action="./community_cookbook.php">
            <input type="text" name="title" placeholder="Recipe Title" required>
            <textarea name="description" placeholder="Short Description" required></textarea>
            <textarea name="ingredients" placeholder="Ingredients (one per line)" required></textarea>
            <textarea name="instructions" placeholder="Instructions (step by step)" required></textarea>
            <input type="text" name="cuisine" placeholder="Cuisine">
            <select name="difficulty">
                <option value="Easy">Easy</option>
                <option value="Medium">Medium</option>
                <option value="Hard">Hard</option>
            </select>
            <input type="file" name="cuisine_image" id="cuisineImageInput" accept="image/*">
            <img id="cuisineImagePreview" src="#" alt="Cuisine Preview">
            <button type="submit" name="submit_recipe">Submit Recipe</button>
        </form>
    </div>
</div>

<script>
// Modal open/close
const modal = document.getElementById('recipeModal');
const closeBtn = document.getElementById('closeModal');
if(closeBtn){ closeBtn.onclick = ()=> modal.style.display='none'; }
window.onclick = (e)=> { if(e.target==modal) modal.style.display='none'; }

// Live preview
const cuisineInput = document.getElementById('cuisineImageInput');
const cuisinePreview = document.getElementById('cuisineImagePreview');
if(cuisineInput){
    cuisineInput.addEventListener('change', function(){
        const file = this.files[0];
        if(file){
            const reader = new FileReader();
            reader.onload = function(e){
                cuisinePreview.src = e.target.result;
                cuisinePreview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            cuisinePreview.src = '#';
            cuisinePreview.style.display = 'none';
        }
    });
}
</script>
