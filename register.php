<?php
session_start();

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Security Headers
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("Content-Security-Policy: default-src 'self'; style-src 'self' https://cdn.jsdelivr.net");
header("Referrer-Policy: no-referrer");

// Database Connection
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failed!");
    }
    
    // Sanitize and validate input
    $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Securely hash password
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }
    
    // Use a prepared statement
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
    $stmt->bind_param("sss", $username, $email, $password);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Registration successful! <a href='login.php'>Login here</a></div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }
    
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/register.css" rel="stylesheet">
</head>
<body class="signup-page d-flex align-items-center justify-content-center">
    <div class="signup-box">
            <h2 class="signup-text">Sign Up</h2>
            <form action="register.php" method="POST" class="signup-box" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required><br>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required><br>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required autocomplete="new-password"><br>
                <button type="submit" class="btn btn-signup">Sign Up</button>
            </form>
            <div class="text">
                <p>Already have an account? <a href="login.php">Sign In here</a></p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>