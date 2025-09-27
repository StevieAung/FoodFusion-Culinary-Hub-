<?php
session_start();
include './Database/db.php';

if(!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST'){
    echo json_encode(['success'=>false,'error'=>'Not allowed']);
    exit;
}

$recipe_id = $_POST['recipe_id'] ?? '';
$comment_text = $_POST['comment_text'] ?? '';
$user_id = $_SESSION['user_id'];

if($recipe_id && $comment_text){
    $stmt = $conn->prepare("INSERT INTO comments (recipe_id,user_id,comment_text) VALUES (?,?,?)");
    $stmt->bind_param("iis",$recipe_id,$user_id,$comment_text);
    $stmt->execute();

    echo json_encode(['success'=>true,'comment_text'=>$comment_text,'first_name'=>$_SESSION['user_firstname'] ?? 'You']);
    exit;
}

echo json_encode(['success'=>false,'error'=>'Invalid data']);
