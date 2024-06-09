<?php
session_start();
include_once("website/config.php");

if (!isset($_SESSION['auth_user'])) {
    header("HTTP/1.1 403 Forbidden");
    exit("You are not authorized to access this resource.");
}

if (!isset($_GET['document_id'])) {
    header("HTTP/1.1 400 Bad Request");
    exit("Invalid request.");
}

$document_id = $_GET['document_id'];

// Fetch document details from the database
$stmt = $conn->prepare("SELECT * FROM documents WHERE document_id = ?");
$stmt->execute([$document_id]);
$document = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$document) {
    header("HTTP/1.1 404 Not Found");
    exit("Document not found.");
}

// Fetch the document file from the BLOB
$document_content = $document['document_file'];
$document_name = $document['document_file_name'];

// Serve the file
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($document_name) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . strlen($document_content));

echo $document_content;
exit;
?>
