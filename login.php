<?php
session_start();
include 'db_connect.php';

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Session timeout (30 minutes)
$session_timeout = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $session_timeout) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}
$_SESSION['last_activity'] = time();

// Handle login process
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['username']) || !isset($_POST['password'])) {
        echo "<div class='alert alert-danger'>Please fill in all fields.</div>";
    } else {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        // Prepare SQL statement
        $stmt = $conn->prepare("SELECT id, username, role, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['user_id'] = $user['id'];
                
                // Redirect user based on role
                if ($user['role'] === 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: user_dashboard.php");
                }
                exit();
            } else {
                echo "<div class='alert alert-danger'>Invalid username or password.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Invalid username or password.</div>";
        }
        
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/form.css" rel="stylesheet">
</head>
<body class="login-page d-flex align-items-center justify-content-end">
    <div class="login-box">
        <h2 class="welcome-text">Welcome</h2> 
        <br>
        <form method="POST" action="" class="login-box">
    <div class="mb-3">
        <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
    </div>
    <div class="mb-3">
        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
    </div>
    <button type="submit" class="btn btn-login w-50">Log In</button>
</form>
<br><br>
        <div class="text a">
            <p>Don't have an account? <a href="register.php">Sign Up here</a></p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
