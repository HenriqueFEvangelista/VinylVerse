<?php
include '../components/auth.php';
include '../config/db.php';

$user_id = $_SESSION['user_id'];

$username = trim($_POST['username']);
$email = trim($_POST['email']);

$stmt = $conn->prepare("UPDATE users SET username=?, email=? WHERE id=?");
$stmt->bind_param("ssi", $username, $email, $user_id);

$stmt->execute();

header("Location: userAccount.php?updated=1");
exit;
