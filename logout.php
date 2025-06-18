<?php
// logout.php

session_start();
session_destroy(); // Destroy session data

header('Location: login.php'); // Redirect to login page
exit;
?>
