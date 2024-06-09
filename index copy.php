<?php 
session_start();
DEFINE("TITLE", "RL | LOGIN");

include_once("website/config.php");

// if (isset($_GET['verification'])) {

//     $code = $_GET['verification'];
//     $status = 'true';


// $stmt1 = $conn->prepare("SELECT * FROM acc_inf WHERE ver_code = ?");
//     $stmt1->execute([$code]);
//             $user = $stmt1->fetch(PDO::FETCH_ASSOC); # get users data

// $stmt = $conn->prepare("UPDATE acc_inf SET is_verified = ? WHERE ver_code = ?");
//                 $stmt->execute([$status, $code]);
//                 $msg = "<div class='alert alert-success'>Account verification has been successfully completed.</div>";
// }
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="assets\rl\Logo\favicon.ico">
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
            <h1><i>Hello aspiring RealLife Scholar</i></h1>
            <form method="POST">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <br>
                <button type="submit" name="submit">Sign In</button>
            </form>
            <br>
            <p>Don't have an account? <a href="acct_cre.php">Create one!</a></p>
            <div class="message">
                <?php 
                if(isset($_SESSION['status']))
                {
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

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

include_once("website/config.php");

if(isset($_POST['submit'])){
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM acc_inf WHERE user=? AND pass=? ");
    $stmt->execute([$email, $password]);

    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
        #FETCH DATA FROM DATABASE
    			$applicant_ID = $data['applicant_ID'];
    			$user= $data['user'];
    			$pass = $data['pass'];
    			$is_active = $data['is_active'];
    			$is_applicant= $data['is_applicant'];
    			$is_verified = $data['is_verified'];
    		}

             if($pass == $password)
             {
             	if ($is_verified == '0') {
                
    				$_SESSION['status'] = "Verify your account, go to your email.";
                    header("Location: index.php");
                }
    			else  {
    	         	if ($is_applicant == '0') {									
    						$_SESSION['status'] = "Your account is currently suspended. Please reach out to your administrator.";
                            header("Location: index.php");

    					}	
    						else {

                            //get status of profile information. If it is not set, proceeed to acc_inf.php to insert needed information
                            


                            $stmt = $conn->prepare("UPDATE acc_inf SET is_active = 1 WHERE user = ?");
                            $stmt->execute([$email]);
                            $_SESSION['status-code'] = "success";
                            header("Location: home.php");
    			     	    }
            	 	}
             }
    else{
    	$_SESSION['status'] = "Your email and password do not match. Please try again";
    		$_SESSION['type'] = "info";
            header("Location: index.php");
    }

   

    }

?>
