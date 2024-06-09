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

// Fetch list of users
$stmt = $conn->prepare("SELECT DISTINCT sender FROM messages WHERE receiver = 'admin'");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch messages sent to the admin by the selected user
if (isset($_GET['user'])) {
    $selectedUser = $_GET['user'];
    $stmt = $conn->prepare("SELECT sender, message, `timestamp` FROM messages WHERE receiver = 'admin' AND sender = ? ORDER BY `timestamp` DESC");
    $stmt->execute([$selectedUser]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Inside the existing PHP code, before the closing PHP tag
if (isset($_POST['admin-message'])) {
    $sender = 'admin';
    $receiver = $_POST['receiver']; // Change this to the appropriate receiver
    $message = htmlspecialchars($_POST['admin-message']);

    $stmt = $conn->prepare("INSERT INTO messages (sender, receiver, message) VALUES (?, ?, ?)");
    if ($stmt->execute([$sender, $receiver, $message])) {
        echo "Message sent successfully.";
    } else {
        echo "Failed to send message.";
    }
}

?>

<head>
    <title>Messaging Platform</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .message-container {
            margin-top: 20px;
        }
        .message {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .admin-message-form {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container" style="margin-top: 100px;">
        <h1 class="text-center">Messaging Platform</h1>
        <div class="row">
            <div class="col-md-4">
                <h5>Select User:</h5>
                <ul class="list-group">
                    <?php foreach ($users as $user) : ?>
                        <li class="list-group-item"><a href="?user=<?php echo urlencode($user['sender']); ?>"><?php echo htmlspecialchars($user['sender']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-md-8">
                <?php if (isset($messages)) : ?>
                    <h5>Messages from <?php echo htmlspecialchars($selectedUser); ?>:</h5>
                    <div class="message-container">
                        <?php foreach ($messages as $message) : ?>
                            <div class="message">
                                <p><strong>From: <?php echo htmlspecialchars($message['sender']); ?></strong></p>
                                <p><?php echo htmlspecialchars($message['message']); ?></p>
                                <p><small>Sent at: <?php echo htmlspecialchars($message['timestamp']); ?></small></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="admin-message-form">
                        <h5 class="font-weight-bold mb-3 text-center">Reply to <?php echo htmlspecialchars($selectedUser); ?>:</h5>
                        <div class="card">
                            <div class="card-body">
                                <form id="admin-message-form" method="post">
                                    <input type="hidden" name="receiver" value="<?php echo htmlspecialchars($selectedUser); ?>">
                                    <div class="form-group">
                                        <label for="admin-message">Message:</label>
                                        <textarea class="form-control" id="admin-message" rows="3" name="admin-message" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Send</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>

<?php include_once("website/templates/footer.php"); ?>
