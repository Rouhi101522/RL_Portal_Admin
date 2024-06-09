    <?php
    session_start();
    DEFINE("TITLE", "Admin Dashboard");

    ini_set('display_errors', 0);
    error_reporting(E_ALL & ~E_NOTICE);

    include_once("website/templates/header.php");

    if ($_SESSION['authorized'] == false) {
        header("location: index.php");
            exit; // Add an exit to stop further execution
        }

    // Function to get counts from the database
    function getCounts($conn) {
        $counts = array();

        // Count of applicants
        $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM person_inf");
        $stmt->execute();
        $counts['applicants'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Count of files to be verified
        $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM documents WHERE document_status = 'For Verification'");
        $stmt->execute();
        $counts['files_to_verify'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Count of files to be reuploaded
        $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM documents WHERE document_status = 'For Reupload'");
        $stmt->execute();
        $counts['files_to_resubmit'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        return $counts;
    }

    // Fetch counts
    $counts = getCounts($conn);

    // Function to fetch applicant data from the database
    function getApplicantsData($conn) {
        $stmt = $conn->prepare("SELECT * FROM acc_inf JOIN person_inf ON acc_inf.applicant_ID = person_inf.applicant_ID JOIN acad_inf ON acc_inf.applicant_ID=acad_inf.applicant_ID WHERE is_applicant = 1"); 
        $stmt->execute();
        $applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $applicants;
    }



    // Fetch applicants data
    $applicantsData = getApplicantsData($conn);

    ?>

        <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        .dashboard {
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: inline-block;
        }

        .table-container {
            margin-top: 20px;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        img {
            max-width: 50px;
            max-height: 50px;
            border-radius: 50%;
        }
    </style>
    </head>

    <body>
        <h1><?php echo TITLE; ?></h1>
        <div class="dashboard">
            <h3>Dashboard</h3>
            <canvas id="myChart" width="400" height="200"></canvas>
            <div class="row">
                <div class="col">
                    <h5>Number of Applicants</h5>
                    <p><?php echo $counts['applicants']; ?></p>
                </div>
                <div class="col">
                    <h5>Number of Files to be Verified</h5>
                    <p><?php echo $counts['files_to_verify']; ?></p>
                </div>
                <div class="col">
                    <h5>Number of Files to be Resubmitted</h5>
                    <p><?php echo $counts['files_to_resubmit']; ?></p>
                </div>
            </div>
        </div>
        <div class="table-container">
            <h3>Applicants Table</h3>
            <table>
                <thead>
                    <tr>
                        <th>Profile</th>
                        <th>Last Name</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Current Year Level</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($applicantsData !== null) : ?>
                        <?php foreach ($applicantsData as $applicant) : ?>
                            <tr>
                                <td>
                                    <?php
                                    $profileImage = $applicant['applicant_profile'];
                                    // Assuming applicant_profile contains the relative path to the image file
                                    echo "<img src='../RL_BGC_PortalWebApps/$profileImage' alt='Profile Image'>";
                                    ?>
                                </td>
                                <td><?php echo $applicant['last_name']; ?></td>
                                <td><?php echo $applicant['first_name']; ?></td>
                                <td><?php echo $applicant['middle_name']; ?></td>
                                <td><?php echo $applicant['cur_year']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5">No applicants found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </body>

    </html>

    <?php
    include_once("website/templates/footer.php");
    ?>


<script>
    // Example function to fetch data from a mock API
    async function fetchData(url) {
        const response = await fetch(url);
        const data = await response.json();
        return data;
    }

    // Function to update the dashboard with fetched data and render a chart
    async function updateDashboard() {
        const numApplicants = await fetchData('/api/get_num_applicants');
        const numDocuments = await fetchData('/api/get_num_documents');

        // Update the dashboard elements with the fetched data
        document.getElementById('numApplicants').textContent = numApplicants;
        document.getElementById('numDocuments').textContent = numDocuments;

        // Render a chart using Chart.js
        const ctx = document.getElementById('myChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Applicants', 'Documents'],
                datasets: [{
                    label: 'Count',
                    data: [numApplicants, numDocuments],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Call the updateDashboard function to populate the dashboard initially
    updateDashboard();

    // Set an interval to update the dashboard every minute (or as desired)
    setInterval(updateDashboard, 60000); // Update every 60 seconds
</script>
     