<?php
session_start();
include_once("website/config.php");

if (isset($_POST['sender']) && isset($_POST['receiver']) && isset($_POST['msg']) && isset($_SESSION['auth_user'])) {
    $sender = $_POST['sender'];
    $receiver = $_POST['receiver'];
    $message = $_POST['msg'];
    $applicant_ID = $_SESSION['auth_user'];

    $sql = $conn->prepare("INSERT INTO messages (user_id, sender, receiver, message) VALUES (?, ?, ?, ?)");
    $sql->execute([$applicant_ID, $sender, $receiver, $message]);

    echo "Message sent successfully";
} else {
    echo "Failed to send message";
}
?>
