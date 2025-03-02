<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cat Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/user_db.css" rel="stylesheet">
</head>
<body class="bg-page">
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <!-- Navbar Toggler (for small screens) -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Navbar Links -->
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link active" href="user_dashboard.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="menu.php">Menu</a></li>
                <li class="nav-item"><a class="nav-link" href="cats.php">Cats</a></li>
                <li class="nav-item"><a class="nav-link" href="new_arrivals.php">New Arrivals</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-3">
            <a href="menu.php">
                <div class="image-card">
                    <img src="images/menu.png" alt="Menu" class="img-fluid">
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="cats.php">
                <div class="image-card">
                    <img src="images/cats.png" alt="Cats" class="img-fluid">
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="new_arrivals.php">
                <div class="image-card">
                    <img src="images/newarrivals.png" alt="NewArrival" class="img-fluid">
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="about.php">
                <div class="image-card">
                    <img src="images/about.png" alt="About" class="img-fluid">
                </div>
            </a>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
