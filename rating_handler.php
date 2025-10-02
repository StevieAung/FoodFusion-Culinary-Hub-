<?php
session_start();
include './Database/db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recipe_id'], $_POST['rating'], $_POST['recipe_type'], $_SESSION['user_id'])){
    $recipe_id = intval($_POST['recipe_id']);
    $rating = intval($_POST['rating']);
    $recipe_type = $_POST['recipe_type'];
    $user_id = $_SESSION['user_id'];

    // Check if user already rated
    $stmt = $conn->prepare("SELECT rating_id FROM ratings WHERE recipe_fk_id=? AND user_id=? AND recipe_type=?");
    $stmt->bind_param("iis", $recipe_id, $user_id, $recipe_type);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0){
        // Update existing rating
        $stmt = $conn->prepare("UPDATE ratings SET rating=? WHERE recipe_fk_id=? AND user_id=? AND recipe_type=?");
        $stmt->bind_param("iiis", $rating, $recipe_id, $user_id, $recipe_type);
    } else {
        // Insert new rating
        $stmt = $conn->prepare("INSERT INTO ratings (recipe_fk_id, user_id, rating, recipe_type) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $recipe_id, $user_id, $rating, $recipe_type);
    }
    $stmt->execute();

    // Fetch new average rating
    $avgStmt = $conn->prepare("SELECT AVG(rating) as avg_rating FROM ratings WHERE recipe_fk_id=? AND recipe_type=?");
    $avgStmt->bind_param("is", $recipe_id, $recipe_type);
    $avgStmt->execute();
    $avgResult = $avgStmt->get_result()->fetch_assoc();
    $new_avg = floatval($avgResult['avg_rating']);

    echo json_encode(['success'=>true, 'new_avg'=>$new_avg]);
    exit;
}

echo json_encode(['success'=>false, 'error'=>'Invalid request']);
