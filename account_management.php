<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Management</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/admin_db.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .modal-content {
            border-radius: 10px;
        }
    </style>
</head>
<body class="bg-page">

<!-- Navbar -->
<div class="sidebar">
        <a href="admin_dashboard.php">Home</a>
        <a href="post_dashboard.php">Post Dashboard</a>
        <a href="comments_dashboard.php">Comment Dashboard</a>
        <a href="account_management.php" class="active">Account Management</a>
        <a href="logout.php">Logout</a>
    </div>

<div class="main-content">
    <div class="card">
        <h2 class="text">Account Management</h2>
    </div><br>
    <a href="create_account.php" class="reply-btn">Create Account</a>

    <table class="table table-bordered table-striped mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include 'db_connect.php';
            $query = "SELECT id, username, email, role FROM users";
            $result = $conn->query($query);
            if ($result->num_rows > 0) {
                while ($user = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                    echo "<td>
                            <a href='edit_account.php?id=" . $user['id'] . "' class='btn btn-warning btn-sm'>Edit</a>
                            <button class='btn btn-danger btn-sm delete-btn' data-id='" . $user['id'] . "' data-bs-toggle='modal' data-bs-target='#deleteModal'>Delete</button>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='text-center'>No users found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Account Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this account?</p>
                <form id="deleteForm">
                    <input type="hidden" name="user_id" id="user_id">
                    <label for="adminPassword" class="form-label">Enter Admin Password:</label>
                    <input type="password" class="form-control" name="password" id="adminPassword" required>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-danger">Confirm Delete</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $(".delete-btn").click(function() {
        let userId = $(this).data("id");
        $("#user_id").val(userId);
    });

    $("#deleteForm").submit(function(event) {
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: "delete_account.php",
            data: $(this).serialize(),
            success: function(response) {
                alert(response);
                location.reload();
            }
        });
    });
});
</script>

</body>
</html>
