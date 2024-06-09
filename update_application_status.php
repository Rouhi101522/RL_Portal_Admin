<?php
session_start();
include_once("website/config.php");

if ($_SESSION['authorized'] == false) {
    header("location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form data
    if (isset($_POST['applicant_id']) && isset($_POST['app_status'])) {
        $applicantID = $_POST['applicant_id'];
        $appStatus = $_POST['app_status'];

        // Update application status in the database
        $stmt = $conn->prepare("UPDATE acc_inf SET app_stat = :app_stat WHERE applicant_ID = :applicant_ID");
        $stmt->bindParam(":app_stat", $appStatus, PDO::PARAM_STR);
        $stmt->bindParam(":applicant_ID", $applicantID, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            // Redirect to the previous page with a success message
            header("location: {$_SERVER['HTTP_REFERER']}?success=1");
            exit;
        } else {
            // Redirect to the previous page with an error message
            header("location: {$_SERVER['HTTP_REFERER']}?error=1");
            exit;
        }
    } else {
        // Redirect to the previous page with an error message if form data is incomplete
        header("location: {$_SERVER['HTTP_REFERER']}?error=1");
        exit;
    }
} else {
    // Redirect to the previous page if accessed via GET request
    header("location: {$_SERVER['HTTP_REFERER']}");
    exit;
}
?>
