<?php
session_start();
DEFINE("TITLE", "LOGIN | ADMIN");

ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

include_once('website\config.php');

// Check if form is submitted
if(isset($_POST['submit'])) {

    // Retrieve form data
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    // Prepare and execute SQL query to check user credentials
    $stmt = $conn->prepare("SELECT * FROM admin_inf WHERE user=? AND pass=?");
    $stmt->execute([$email, $password]);

    // Check if a row is returned
    if($stmt->rowCount() > 0) {
        // User is authenticated, set session variable
        $_SESSION['authorized'] = true;
        // Redirect to admin dashboard 
        header("Location: dash.php"); 
        exit();
    } else {
        // Authentication failed, set error message
        $_SESSION['status'] = "Invalid email or password";
        // Redirect back to login page
        header("Location: index.php"); 
        exit();
    }
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="assets/rl/Logo/favicon.ico">
    <title><?php echo TITLE; ?></title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/css/bootstrap.min.css"> <!-- local Bootstrap file -->
</head>
<body>

    <nav class="navbar ">
        <div class="container-fluid">
            <img class="navbar-brand" src="assets/rl/Logo/Real LIFE Logo ON black.png" alt="Logo">
        </div>
    </nav>
    <div class="main-content">
        <div class="left-panel">
            <h1>ADMIN DASHBOARD</h1>
            <form method="POST">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <br>
                <button type="submit" name="submit">Sign In</button>
            </form>
            <br>
            <div class="message">
                <?php 
                if(isset($_SESSION['status'])) {
                    ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <div>
                            <?= $_SESSION['status'] ?>
                        </div>
                    </div>
                    <?php
                    unset($_SESSION['status']);
                }
                ?>
            </div>
        </div>
        
        <div class="right-panel">
            <img src="assets/rl/REALLIFE PORTAL GRAPHIC DESIGNS.jpeg" alt="Group Photo">
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-wEmeIV1mKuiNp12B2QajB/uVbsE3SrtyDloEgFBp5yYV3fm5e27e6FA3/UdKDp3" crossorigin="anonymous"></script>
</body>
</html>
