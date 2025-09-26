<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
include './Database/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: home_page.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle profile picture upload
if (isset($_POST['upload_pic']) && isset($_FILES['profile_pic'])) {
    $uploadDir = './uploads/profile_pics/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = basename($_FILES['profile_pic']['name']);
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileType, $allowedTypes)) {
        $tempPath = $_FILES['profile_pic']['tmp_name'];
        $newFileName = time() . '_' . $fileName;
        $targetFile = $uploadDir . $newFileName;

        // Resize image to 100x100 px
        list($width, $height) = getimagesize($tempPath);
        $src = null;
        switch ($fileType) {
            case 'jpg':
            case 'jpeg':
                $src = imagecreatefromjpeg($tempPath);
                break;
            case 'png':
                $src = imagecreatefrompng($tempPath);
                break;
            case 'gif':
                $src = imagecreatefromgif($tempPath);
                break;
        }

        if ($src) {
            $dst = imagecreatetruecolor(100, 100);
            // Preserve transparency for PNG and GIF
            if ($fileType == 'png' || $fileType == 'gif') {
                imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
            }
            imagecopyresampled($dst, $src, 0, 0, 0, 0, 100, 100, $width, $height);

            switch ($fileType) {
                case 'jpg':
                case 'jpeg':
                    imagejpeg($dst, $targetFile);
                    break;
                case 'png':
                    imagepng($dst, $targetFile);
                    break;
                case 'gif':
                    imagegif($dst, $targetFile);
                    break;
            }

            imagedestroy($src);
            imagedestroy($dst);

            // Update database
            $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE user_id = ?");
            $stmt->bind_param("si", $targetFile, $user_id);
            $stmt->execute();
            $stmt->close();

            $success_msg = "Profile picture updated successfully.";
        } else {
            $error_msg = "Failed to process image.";
        }
    } else {
        $error_msg = "Invalid file type. Only JPG, JPEG, PNG, GIF allowed.";
    }
}

// Fetch user details
$stmt = $conn->prepare("SELECT first_name, last_name, email, created_at, profile_pic FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $email, $created_at, $profile_pic);
$stmt->fetch();
$stmt->close();

// Default account icon if no profile picture
$profile_pic = $profile_pic ?? './Assets/images/account_icon.png';
?>
<!doctype html>
<html lang="en">
<head>
    <title>My Profile - FoodFusion</title>
    <?php include './includes/head_tags.php'; ?>
</head>
<body class="bg-warning-subtle">
    <div class="container py-5">
        <div class="card shadow-lg rounded-4 mx-auto" style="max-width: 600px;">
            <div class="card-body text-center">
                <img src="<?= htmlspecialchars($profile_pic); ?>" alt="Profile" 
                     class="rounded-circle mb-3" width="100" height="100">
                <h3 class="fw-bold text-warning"><?= htmlspecialchars($first_name . " " . $last_name); ?></h3>
                <p class="text-muted"><?= htmlspecialchars($email); ?></p>
                <hr>
                <p><strong>Member since:</strong> <?= date("F j, Y", strtotime($created_at)); ?></p>

                <!-- Profile picture upload form -->
                <form method="POST" enctype="multipart/form-data">
                    <input type="file" name="profile_pic" accept="image/*" class="form-control mb-2" required>
                    <button type="submit" name="upload_pic" class="btn btn-warning fw-bold">Upload Picture</button>
                </form>

                <?php if (!empty($success_msg)): ?>
                    <div class="alert alert-success mt-2"><?= htmlspecialchars($success_msg); ?></div>
                <?php elseif (!empty($error_msg)): ?>
                    <div class="alert alert-danger mt-2"><?= htmlspecialchars($error_msg); ?></div>
                <?php endif; ?>

                <a href="home_page.php" class="btn btn-outline-warning fw-bold mt-3">Back to Home</a>
            </div>
        </div>
    </div>
    <?php include './includes/script_tags.php'; ?>
</body>
</html>
