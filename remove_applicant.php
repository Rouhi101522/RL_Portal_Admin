<?php
session_start();
include_once("website/config.php");

if (!isset($_SESSION['auth_user'])) {
    header("location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['applicant_id'])) {
    $applicant_ID = $_POST['applicant_id'];

    // Update the is_applicant flag to 0
    $stmt = $conn->prepare("UPDATE acc_inf SET is_applicant = 0 WHERE applicant_ID = ?");
    $stmt->execute([$applicant_ID]);

    // Redirect back to the list of applicants
    header("location: appli.php");
    exit();
} else {
    // Redirect to an error page if the form was not submitted properly
    header("location: error.php");
    exit();
}
?>
