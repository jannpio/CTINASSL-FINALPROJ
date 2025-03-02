<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Database connection
include 'db_connect.php';

// Fetch comments
$query = "SELECT comments.id, comments.comment, comments.created_at, users.username AS comment_author, posts.title AS post_title
          FROM comments
          LEFT JOIN users ON comments.user_id = users.id
          LEFT JOIN posts ON comments.post_id = posts.id
          ORDER BY comments.created_at DESC";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

// Handle comment deletion
if (isset($_POST['delete'])) {
    $comment_id = $_POST['comment_id'];
    $delete_query = "DELETE FROM comments WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param('i', $comment_id);
    $stmt->execute();
    header('Location: comments_dashboard.php');
    exit;
}

// Handle comment update
if (isset($_POST['update'])) {
    $comment_id = $_POST['comment_id'];
    $updated_comment = $_POST['updated_comment'];
    $update_query = "UPDATE comments SET comment = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('si', $updated_comment, $comment_id);
    $stmt->execute();
    header('Location: comments_dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments Dashboard</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/admin_db.css" rel="stylesheet">
</head>
<body class="bg-page">
    <div class="sidebar">
        <a href="admin_dashboard.php">Home</a>
        <a href="post_dashboard.php">Post Dashboard</a>
        <a href="comments_dashboard.php" class="active">Comment Dashboard</a>
        <a href="account_management.php">Account Management</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main-content">
        <div class="card">
            <h2 class="text">Comments Management</h2>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Comment</th>
                        <th>Author</th>
                        <th>Post</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['comment']); ?></td>
                            <td><?php echo htmlspecialchars($row['comment_author']); ?></td>
                            <td><?php echo htmlspecialchars($row['post_title']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="comment_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateCommentModal<?php echo $row['id']; ?>">Edit</button>
                            </td>
                        </tr>

                        <!-- Update Comment Modal -->
                        <div class="modal fade" id="updateCommentModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="updateCommentModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateCommentModalLabel">Update Comment</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="comments_dashboard.php">
                                            <input type="hidden" name="comment_id" value="<?php echo $row['id']; ?>">
                                            <div class="mb-3">
                                                <label for="updated_comment" class="form-label">Updated Comment</label>
                                                <textarea class="form-control" id="updated_comment" name="updated_comment" rows="3" required><?php echo htmlspecialchars($row['comment']); ?></textarea>
                                            </div>
                                            <button type="submit" name="update" class="btn btn-primary">Update Comment</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No comments found.</p>
        <?php endif; ?>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
