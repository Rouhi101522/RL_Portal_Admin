<?php
include_once("website/config.php");

if (isset($_GET['applicant_id'])) {
    $applicantId = $_GET['applicant_id'];

    $stmt = $conn->prepare("SELECT applicant_profile FROM person_inf WHERE applicant_ID = ?");
    $stmt->execute([$applicantId]);
    $imageData = $stmt->fetchColumn();

    if ($imageData) {
        header("Content-type: image/jpeg");
        echo $imageData;
        exit;
    }
}

// If image data not found or invalid request, return a placeholder image or error message
header("Content-type: image/png"); // Assuming placeholder image is a PNG
echo file_get_contents("path_to_placeholder_image.png");
exit;

?>
