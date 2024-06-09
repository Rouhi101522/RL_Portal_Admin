
<?php
include_once("website/config.php");
if (isset($_GET['document_id'])) {
    $document_id = $_GET['document_id'];

    // Fetch the document details
    $stmt = $conn->prepare("SELECT document_file, document_file_name FROM documents WHERE document_id = ?");
    $stmt->execute([$document_id]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($document) {
        $file_content = $document['document_file'];
        $file_name = $document['document_file_name'];
        
        // Set content type header based on file extension
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        switch ($file_extension) {
            case 'pdf':
                header('Content-Type: application/pdf');
                break;
            case 'jpg':
            case 'jpeg':
                header('Content-Type: image/jpeg');
                break;
            // Add more cases for other file types as needed
            default:
                header('Content-Type: application/octet-stream');
        }
        
        // Set content disposition to inline
        header('Content-Disposition: inline; filename="' . $file_name . '"');
        
        // Output the file content
        echo $file_content;
    } else {
        echo "Document not found.";
    }
} else {
    echo "Invalid document ID.";
}
?>
    