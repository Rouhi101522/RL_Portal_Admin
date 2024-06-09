<?php
session_start();
include_once("website/config.php");

if ($_SESSION['authorized'] == false) {
    header("location: index.php");
    exit;
}

if (!isset($_POST['document_id']) || !isset($_POST['status']) || !isset($_POST['applicant_id'])) {
    echo "Invalid request.";
    exit;
}

$document_id = $_POST['document_id'];
$status = $_POST['status'];
$applicant_id = $_POST['applicant_id'];
$admin_notes = $_POST['admin_notes'];

// Update document status
if(isset($status)){
    $stmt = $conn->prepare("UPDATE documents SET document_status = :status WHERE document_id = :document_id");
    $stmt->bindParam(":status", $status, PDO::PARAM_STR);
    $stmt->bindParam(":document_id", $document_id, PDO::PARAM_INT);
    $stmt->execute();
}

if(isset($admin_notes)){
// Perform the update
$stmt = $conn->prepare("UPDATE documents SET admin_notes = ? WHERE applicant_ID = ? AND document_id = ?");
$executed = $stmt->execute([$admin_notes, $applicant_id, $document_id]);

}



// Redirect back to verify_documents.php with the applicant_id
header("location: verify_documents.php?applicant_id=" . $applicant_id);
exit;
