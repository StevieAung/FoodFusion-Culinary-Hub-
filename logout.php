<?php
session_start();

// Destroy session
session_unset();
session_destroy();

// Redirect to homepage
header("Location: home_page.php");
exit;
?>
