<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Database connection
include 'db_connect.php';

// Handle post creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_post'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $userId = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $userId, $title, $content);

    if ($stmt->execute()) {
        echo "Post created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();  // Close the statement after execution
}

// Handle post update after password verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_post'])) {
    $postId = $_POST['post_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $adminPassword = $_POST['admin_password'];

    // Verify the admin password
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($storedPassword);
    $stmt->fetch();

    if (password_verify($adminPassword, $storedPassword)) {
        // Proceed to update the post
        $stmt->close();  // Close the previous statement before executing another

        $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
        $stmt->bind_param("ssi", $title, $content, $postId);

        if ($stmt->execute()) {
            echo "Post updated successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Incorrect password. Post not updated.";
    }
    $stmt->close();  // Close the statement after execution
}

// Handle post deletion after password verification
// Handle post deletion after password verification
if (isset($_POST['delete_post_button']) && isset($_POST['delete_post']) && isset($_POST['delete_post_password'])) {
    $postId = $_POST['delete_post']; // Get post ID from POST
    $adminPassword = $_POST['delete_post_password'];

    // Verify the admin password
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($storedPassword);
    $stmt->fetch();

    if (password_verify($adminPassword, $storedPassword)) {
        // Proceed to delete the post
        $stmt->close();  // Close the previous statement before executing another

        $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->bind_param("i", $postId);

        if ($stmt->execute()) {
            echo "Post deleted successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Incorrect password. Post not deleted.";
    }
    $stmt->close();  // Close the statement after execution
}


// Fetch all posts
$query = "SELECT id, title, content, created_at FROM posts ORDER BY created_at DESC";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Dashboard</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/admin_db.css" rel="stylesheet">
</head>
<body class="bg-page">
    <div class="sidebar">
        <a href="admin_dashboard.php">Home</a>
        <a href="post_dashboard.php" class="active">Post Dashboard</a>
        <a href="comments_dashboard.php">Comment Dashboard</a>
        <a href="account_management.php">Account Management</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main-content">
        <div class = card>
            <h2 class="text">Create a New Post</h2>
        </div>
        <form method="POST" action="post_dashboard.php">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="input-box" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea class="input-box" id="content" name="content" rows="5" required></textarea>
            </div>
            <button type="submit" name="create_post" class="submit-btn">Create Post</button>
        </form>

        <hr>

        <h2 class="text">Existing Posts</h2>
        <?php if ($result->num_rows > 0): ?>
            <div class="list-group">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="list-group-item">
                        <h5><?php echo htmlspecialchars($row['title']); ?></h5>
                        <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                        <p><small>Created on <?php echo $row['created_at']; ?></small></p>

                        <!-- Update Post Form -->
                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#updatePostModal<?php echo $row['id']; ?>">Update</button>

                        <!-- Delete Post Link -->
                        <a href="?delete_post=<?php echo $row['id']; ?>" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deletePostModal<?php echo $row['id']; ?>">Delete</a>

                        <!-- Update Post Modal -->
                        <div class="modal fade" id="updatePostModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="updatePostModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updatePostModalLabel">Update Post</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="post_dashboard.php">
                                            <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
                                            <div class="mb-3">
                                                <label for="title" class="form-label">Title</label>
                                                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($row['title']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="content" class="form-label">Content</label>
                                                <textarea class="form-control" id="content" name="content" rows="5" required><?php echo htmlspecialchars($row['content']); ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="admin_password" class="form-label">Enter Admin Password</label>
                                                <input type="password" class="form-control" id="admin_password" name="admin_password" required>
                                            </div>
                                            <button type="submit" name="update_post" class="btn btn-primary">Update Post</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>

                        <!-- Delete Post Modal -->
                      <!-- Delete Post Modal -->
<div class="modal fade" id="deletePostModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="deletePostModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePostModalLabel">Delete Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="post_dashboard.php">
                    <!-- Add a hidden field for post_id -->
                    <input type="hidden" name="delete_post" value="<?php echo $row['id']; ?>">
                    <div class="mb-3">
                        <label for="delete_post_password" class="form-label">Enter Admin Password</label>
                        <input type="password" class="form-control" id="delete_post_password" name="delete_post_password" required>
                    </div>
                    <button type="submit" class="btn btn-danger" name="delete_post_button">Delete Post</button>
                </form>
            </div>
        </div>
    </div>
</div>

                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No posts found.</p>
        <?php endif; ?>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
