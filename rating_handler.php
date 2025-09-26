<?php
session_start();
include './Database/db.php';
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) die(json_encode(['success'=>false]));

if($_SERVER['REQUEST_METHOD']=='POST'){
    $recipe_id = $_POST['recipe_id'];
    $rating = $_POST['rating'];
    $user_id = $_SESSION['user_id'];

    // Insert or update rating
    $stmt = $conn->prepare("INSERT INTO ratings (recipe_id,user_id,rating) VALUES (?,?,?) 
        ON DUPLICATE KEY UPDATE rating=?");
    $stmt->bind_param("iiii",$recipe_id,$user_id,$rating,$rating);
    if($stmt->execute()){
        // Calculate new average
        $avg_res = $conn->query("SELECT AVG(rating) as new_avg FROM ratings WHERE recipe_id=$recipe_id");
        $avg = $avg_res->fetch_assoc()['new_avg'];
        echo json_encode(['success'=>true,'new_avg'=>round($avg,1)]);
    } else {
        echo json_encode(['success'=>false]);
    }
}
