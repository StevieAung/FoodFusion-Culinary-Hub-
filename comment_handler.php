<?php
session_start();
include './Database/db.php';
header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST'){
    echo json_encode(['success'=>false,'error'=>'Not allowed']);
    exit;
}

$recipe_fk_id = $_POST['recipe_id'] ?? 0;
$recipe_type = $_POST['recipe_type'] ?? '';
$comment_text = trim($_POST['comment_text'] ?? '');
$user_id = $_SESSION['user_id'];

if($recipe_fk_id && $comment_text && $recipe_type){
    $stmt = $conn->prepare("INSERT INTO comments (recipe_fk_id, user_id, recipe_type, comment_text) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $recipe_fk_id, $user_id, $recipe_type, $comment_text);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success'=>true,
            'comment_text'=>$comment_text,
            'first_name'=>$_SESSION['user_firstname'] ?? 'You'
        ]);
    } else {
        echo json_encode(['success'=>false, 'error'=>$stmt->error]);
    }
    $stmt->close();
    exit;
}

echo json_encode(['success'=>false,'error'=>'Invalid data']);
