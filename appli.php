<?php
session_start();
include_once("website/templates/header.php");
include_once("website/config.php");

ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE);

if ($_SESSION['authorized'] == false) {
    header("location: index.php");
    exit; // Add an exit to stop further execution
}

$stmt = $conn->prepare("SELECT applicant_ID, first_name, last_name FROM person_inf");
$stmt->execute();
$applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);

$active_applicants = [];
foreach ($applicants as $applicant) {
    $stmt = $conn->prepare("SELECT is_applicant FROM acc_inf WHERE applicant_ID = ?");
    $stmt->execute([$applicant['applicant_ID']]);
    $is_applicant = $stmt->fetchColumn();

    if ($is_applicant == 1) {
        $active_applicants[] = $applicant;
    }
}

// Pagination logic
$per_page = 10;
$total_applicants = count($active_applicants);
$total_pages = ceil($total_applicants / $per_page);

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $per_page;
$active_applicants = array_slice($active_applicants, $start, $per_page);

?>

<div class="container" style="margin-top: 100px;">
    <h1 class="text-center">List of Applicants</h1>
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <input class="form-control mb-4" id="search" type="text" placeholder="Search for names..">
            <table class="table table-hover table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th onclick="sortTable(0)">Applicant Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="applicantTable">
                    <?php foreach ($active_applicants as $applicant) : ?>
                        <tr>
                            <td><?php echo $applicant['first_name'] . " " . $applicant['last_name']; ?></td>
                            <td><a class="btn btn-primary" href="verify_documents.php?applicant_id=<?php echo $applicant['applicant_ID']; ?>">Verify Documents</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                        <li class="page-item <?php if ($i == $page) echo 'active'; ?>"><a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<script>
    // Search functionality
    document.getElementById('search').addEventListener('keyup', function () {
        var value = this.value.toLowerCase();
        var rows = document.getElementById('applicantTable').getElementsByTagName('tr');
        for (var i = 0; i < rows.length; i++) {
            var name = rows[i].getElementsByTagName('td')[0].textContent.toLowerCase();
            if (name.indexOf(value) > -1) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    });

    // Sort functionality
    function sortTable(n) {
        var table = document.querySelector('.table');
        var rows = Array.from(table.rows).slice(1);
        var ascending = table.rows[0].cells[n].getAttribute('data-order') === 'asc';
        rows.sort(function (row1, row2) {
            var cell1 = row1.cells[n].innerText.toLowerCase();
            var cell2 = row2.cells[n].innerText.toLowerCase();
            if (cell1 < cell2) return ascending ? -1 : 1;
            if (cell1 > cell2) return ascending ? 1 : -1;
            return 0;
        });
        table.tBodies[0].append(...rows);
        table.rows[0].cells[n].setAttribute('data-order', ascending ? 'desc' : 'asc');
    }
</script>

<?php include_once("website/templates/footer.php"); ?>
