<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cat Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/user_db.css" rel="stylesheet">
</head>
<body class="cat-page">
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <!-- Navbar Toggler (for small screens) -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Navbar Links -->
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="user_dashboard.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="menu.php">Menu</a></li>
                <li class="nav-item"><a class="nav-link active" href="cats.php">Cats</a></li>
                <li class="nav-item"><a class="nav-link" href="new_arrivals.php">New Arrivals</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="body-content">
    <div class="row">
        <!-- Cat Cards -->
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="cat-card">
                <img src="images/cat1.jpg" alt="Jewel">
                <div class="overlay-text">
                    <h4>Jewel</h4>
                    <p>5 years old</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6 mb-4">
            <div class="cat-card">
                <img src="images/cat2.jpg" alt="Nicole">
                <div class="overlay-text">
                    <h4>Nicole</h4>
                    <p>7 years old</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6 mb-4">
            <div class="cat-card">
                <img src="images/cat3.png" alt="Julie">
                <div class="overlay-text">
                    <h4>Julie</h4>
                    <p>2 years old</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6 mb-4">
            <div class="cat-card">
                <img src="images/cat4.jpg" alt="Julie">
                <div class="overlay-text">
                    <h4>Theo</h4>
                    <p>1 year old</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6 mb-4">
            <div class="cat-card">
                <img src="images/cat5.jpg" alt="Julie">
                <div class="overlay-text">
                    <h4>Francine</h4>
                    <p>2 years old</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6 mb-4">
            <div class="cat-card">
                <img src="images/cat6.jpg" alt="Julie">
                <div class="overlay-text">
                    <h4>Mae</h4>
                    <p>1 year old</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>