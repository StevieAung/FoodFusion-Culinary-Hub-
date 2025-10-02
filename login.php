<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include './Database/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, first_name, password_hash, profile_pic, failed_attempts, lockout_until 
                            FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $first_name, $password_hash, $profile_pic, $failed_attempts, $lockout_until);
        $stmt->fetch();

        // check lockout
        if ($lockout_until && strtotime($lockout_until) > time()) {
            echo json_encode(['status' => 'error', 'message' => 'Account locked. Try again later.']);
            exit;
        }

        if (password_verify($password, $password_hash)) {
            // reset failed attempts
            $update = $conn->prepare("UPDATE users SET failed_attempts = 0, lockout_until = NULL WHERE user_id = ?");
            $update->bind_param("i", $user_id);
            $update->execute();
            $update->close();

            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_firstname'] = $first_name;
            $_SESSION['profile_pic'] = $profile_pic ?? './Assets/images/account_icon.png'; // Set profile pic in session

            echo json_encode(['status' => 'success', 'message' => 'Login successful!']);
        } else {
            $failed_attempts++;
            $lockout = null;

            if ($failed_attempts >= 3) {
                $lockout = date("Y-m-d H:i:s", strtotime("+3 minutes"));
            }

            $update = $conn->prepare("UPDATE users SET failed_attempts = ?, lockout_until = ? WHERE email = ?");
            $update->bind_param("iss", $failed_attempts, $lockout, $email);
            $update->execute();
            $update->close();

            echo json_encode(['status' => 'error', 'message' => 'Invalid password.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found.']);
    }
    $stmt->close();
}
?>
