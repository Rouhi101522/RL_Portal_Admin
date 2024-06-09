<?php
session_start();
include_once("website/templates/header.php");
include_once("website/config.php");

if ($_SESSION['authorized'] == false) {
    header("location: index.php");
    exit;
}

// Debugging: Check if applicant_id is set
if (!isset($_GET['applicant_id'])) {
    echo "Applicant ID parameter is not set in the URL.";
    exit;
}

$applicant_ID = $_GET['applicant_id'];

// Debugging: Output the applicant ID value
echo "Applicant ID: " . htmlspecialchars($applicant_ID) . "<br>";

// Check if applicant_id is empty
if (empty($applicant_ID)) {
    echo "Applicant ID is empty.";
    exit;
}

// Fetch applicant details
$stmt = $conn->prepare("SELECT * FROM person_inf WHERE applicant_ID = :applicant_ID");
$stmt->bindParam(":applicant_ID", $applicant_ID, PDO::PARAM_STR);
$stmt->execute();
$applicant = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if applicant data is fetched
if (!$applicant) {
    echo "Applicant not found.";
    exit;
}

// Fetch document status for the applicant
$stmt = $conn->prepare("SELECT d.*, dt.description AS document_description
                        FROM documents d 
                        JOIN document_types dt ON d.document_type_id = dt.document_type_id 
                        WHERE d.applicant_ID = ?");
$stmt->execute([$applicant_ID]);
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch additional details from acc_inf
$stmt = $conn->prepare("SELECT * FROM acc_inf WHERE applicant_ID = ?");
$stmt->execute([$applicant_ID]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "Additional applicant details not found.";
    exit;
}

// Define application status options
$appStatusOptions = array(
    'On Application Process',
    'For Visitation',
    'For Interview',
    'Decline',
    'Approved'
);
?>

<div class="container" style="margin-top: 100px;">
    <h1 class="text-center">Verify Documents for <?php echo htmlspecialchars($applicant['first_name']) . " " . htmlspecialchars($applicant['last_name']); ?></h1>
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <!-- Applicant Details -->
            <h4>Applicant Details</h4>
            <p><strong>Applicant Profile:</strong> 
                <?php if (!empty($applicant['applicant_profile'])) : ?>
                    <img src="../RL_BGC_PortalWebApps/<?php echo htmlspecialchars($applicant['applicant_profile']); ?>" alt="Applicant Profile Image" style="max-width: 100px; max-height: 100px;">
                <?php else : ?>
                    <img src="placeholder.jpg" alt="Profile Placeholder" style="max-width: 100px; max-height: 100px;">
                <?php endif; ?>
            </p>

            <p><strong>Name:</strong> <?php echo htmlspecialchars($applicant['first_name']) . " " . htmlspecialchars($applicant['last_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($data['user']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($applicant['house_num']) . " " . htmlspecialchars($applicant['street']) . ", " . htmlspecialchars($applicant['brgy']) . ", " . htmlspecialchars($applicant['city']); ?></p>

            <!-- Uploaded Documents -->
            <h4>Uploaded Documents</h4>
            <table class="table table-hover table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Document Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documents as $document) : ?>
                        <tr>
                            <td>
                                <a href="document_viewer.php?document_id=<?php echo htmlspecialchars($document['document_id']); ?>" target="_blank">
                                    <?php echo htmlspecialchars($document['document_file_name']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($document['document_description']); ?></td>
                            <td><?php echo htmlspecialchars($document['document_status']); ?></td>
                            <td>
                                <form method="POST" action="update_document_status.php" class="d-inline">
                                    <input type="hidden" name="document_id" value="<?php echo htmlspecialchars($document['document_id']); ?>">
                                    <input type="hidden" name="applicant_id" value="<?php echo htmlspecialchars($applicant_ID); ?>">
                                    <button type="submit" name="status" value="Passed" class="btn btn-success">Pass</button>
                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal" data-document-id="<?php echo htmlspecialchars($document['document_id']); ?>">Fail</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Application Status -->
            <h4>Update Application Status</h4>
            <form method="POST" action="update_application_status.php">
                <input type="hidden" name="applicant_id" value="<?php echo htmlspecialchars($applicant_ID); ?>">
                <div class="form-group">
                    <label for="app_status">Application Status:</label>
                    <select class="form-control" id="app_status" name="app_status">
                        <?php foreach ($appStatusOptions as $option) : ?>
                            <option value="<?php echo htmlspecialchars($option); ?>" <?php if ($option == $data['app_stat']) echo 'selected'; ?>><?php echo htmlspecialchars($option); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update Status</button>
            </form>
            
            <!-- Form for removing applicant -->
            <form method="POST" action="remove_applicant.php" onsubmit="return confirm('Are you sure you want to remove this applicant?');">
                <input type="hidden" name="applicant_id" value="<?php echo htmlspecialchars($applicant_ID); ?>">
                <button type="submit" class="btn btn-danger mt-3">Remove Applicant</button>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Rejection Notes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="update_document_status.php">
                <div class="modal-body">
                    <input type="hidden" name="document_id" id="modal-document-id">
                    <input type="hidden" name="applicant_id" value="<?php echo htmlspecialchars($applicant_ID); ?>">
                    <div class="form-group">
                        <label for="admin_notes">Reason for Rejection</label>
                        <textarea class="form-control" name="admin_notes" id="admin_notes" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="status" value="For Reupload" class="btn btn-danger">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#rejectModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var documentId = button.data('document-id'); // Extract info from data-* attributes
        var modal = $(this);
        modal.find('.modal-body #modal-document-id').val(documentId);
    });
</script>

<?php include_once("website/templates/footer.php"); ?>
