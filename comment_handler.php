<?php
session_start();
include './Database/db.php';
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) die(json_encode(['success'=>false]));

if($_SERVER['REQUEST_METHOD']=='POST'){
    $recipe_id = $_POST['recipe_id'];
    $comment_text = $_POST['comment'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO comments (recipe_id, user_id, comment_text) VALUES (?,?,?)");
    $stmt->bind_param("iis", $recipe_id, $user_id, $comment_text);
    echo $stmt->execute() ? json_encode(['success'=>true]) : json_encode(['success'=>false]);
}
