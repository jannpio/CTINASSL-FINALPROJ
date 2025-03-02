<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Database connection
include 'db_connect.php';

// Fetch posts and associated comments
$query = "SELECT posts.id AS post_id, posts.title, posts.content, posts.created_at, users.username AS post_author,
                 comments.comment AS comment_content, comments.created_at AS comment_date, comment_users.username AS comment_author,
                 comments.id AS comment_id
          FROM posts
          LEFT JOIN users ON posts.user_id = users.id
          LEFT JOIN comments ON comments.post_id = posts.id
          LEFT JOIN users AS comment_users ON comments.user_id = comment_users.id
          ORDER BY posts.created_at DESC, comments.created_at ASC";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$newsfeed = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $postId = $row['post_id'];
        if (!isset($newsfeed[$postId])) {
            $newsfeed[$postId] = [
                'title' => $row['title'],
                'content' => $row['content'],
                'created_at' => $row['created_at'],
                'author' => $row['post_author'],
                'comments' => []
            ];
        }
        if ($row['comment_content']) {
            $newsfeed[$postId]['comments'][] = [
                'content' => $row['comment_content'],
                'created_at' => $row['comment_date'],
                'author' => $row['comment_author'],
                'comment_id' => $row['comment_id']
            ];
        }
    }
}

// Handle new comment submission (admin role)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_comment'])) {
    $postId = $_POST['post_id'];
    $commentContent = $_POST['comment_content'];

    // Check for empty comment content
    if (empty($commentContent)) {
        echo "Error: Comment content cannot be empty.";
        exit;
    }

    // Insert new comment into the database
    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $postId, $_SESSION['user_id'], $commentContent);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php"); // Redirect to the dashboard after comment is added
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Handle comment reply submission (admin role)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_comment'])) {
    $commentId = $_POST['comment_id'];
    $replyContent = $_POST['reply_content'];

    // Check if the reply content is empty
    if (empty($replyContent)) {
        echo "Error: Reply content cannot be empty.";
        exit;
    }

    // Insert reply into the database
    $stmt = $conn->prepare("INSERT INTO comment_replies (comment_id, user_id, reply_content) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $commentId, $_SESSION['user_id'], $replyContent);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php"); // Redirect to the dashboard after reply is added
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/admin_db.css" rel="stylesheet">
</head>
<body class="bg-page">
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="admin_dashboard.php" class="active">Home</a>
        <a href="post_dashboard.php">Post Dashboard</a>
        <a href="comments_dashboard.php">Comment Dashboard</a>
        <a href="account_management.php">Account Management</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="card">
            <h2 class="text">Admin Dashboard</h2>
            <p> Here is an overview for the cat cafe</p>
        </div>

        <!-- Metric Container -->
        <div class="metrics-container">
            <!-- Total Users -->
            <div class="card-metric">
                <h3>Registered Users</h3>
                <p>
                    <?php
                    $user_sql = "SELECT COUNT(*) as total_users FROM users";
                    $user_result = $conn->query($user_sql);
                    $user_row = $user_result->fetch_assoc();
                    echo $user_row['total_users'];
                    ?>
                </p>
            </div>

            <!-- Total Posts -->
            <div class="card-metric">
                <h3>Total Posts</h3>
                <p>
                    <?php
                    $post_sql = "SELECT COUNT(*) as total_posts FROM posts";
                    $post_result = $conn->query($post_sql);
                    $post_row = $post_result->fetch_assoc();
                    echo $post_row['total_posts'];
                    ?>
                </p>
            </div>

            <!-- Total Comments -->
            <div class="card-metric">
                <h3>Total Comments</h3>
                <p>
                    <?php
                    $comment_sql = "SELECT COUNT(*) as total_comments FROM comments";
                    $comment_result = $conn->query($comment_sql);
                    $comment_row = $comment_result->fetch_assoc();
                    echo $comment_row['total_comments'];
                    ?>
                </p>
            </div>
        </div>

        
        <!-- Newsfeed -->
        <div class="newsfeed-container">
        <h2 class="text">Newsfeed</h2>
        <?php if (!empty($newsfeed)): ?>
            <?php foreach ($newsfeed as $postId => $post): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                        <p class="text-muted">Posted by <?php echo htmlspecialchars($post['author']); ?> on <?php echo htmlspecialchars($post['created_at']); ?></p>
                    </div>
                    <div class="card-footer">
                        <h6>Comments</h6>
                        <?php if (!empty($post['comments'])): ?>
                            <?php foreach ($post['comments'] as $comment): ?>
                                <div class="mb-2">
                                    <strong><?php echo htmlspecialchars($comment['author']); ?>:</strong>
                                    <span><?php echo htmlspecialchars($comment['content']); ?></span>
                                    <br>
                                    <small class="text-muted">Posted on <?php echo htmlspecialchars($comment['created_at']); ?></small>

                                <!-- Reply form -->
                                <form method="post">
                                        <input type="hidden" name="comment_id" value="<?php echo $comment['comment_id']; ?>">
                                        <textarea name="reply_content" class="input-box" rows="3" placeholder="Reply to this comment..."></textarea>
                                        <button type="submit" name="reply_comment" class="reply-btn">Reply</button>
                                    </form>

                                    <!-- Display replies if any -->
                                    <?php 
                                    $replyQuery = "SELECT reply_content, created_at, users.username AS reply_author
                                                   FROM comment_replies
                                                   LEFT JOIN users ON comment_replies.user_id = users.id
                                                   WHERE comment_replies.comment_id = ?";
                                    $replyStmt = $conn->prepare($replyQuery);
                                    $replyStmt->bind_param("i", $comment['comment_id']);
                                    $replyStmt->execute();
                                    $replyResult = $replyStmt->get_result();

                                    if ($replyResult->num_rows > 0): ?>
                                        <div class="mt-2">
                                            <?php while ($reply = $replyResult->fetch_assoc()): ?>
                                                <div class="mb-2">
                                                    <strong><?php echo htmlspecialchars($reply['reply_author']); ?>:</strong>
                                                    <span><?php echo htmlspecialchars($reply['reply_content']); ?></span>
                                                    <br>
                                                    <small class="text-muted">Replied on <?php echo htmlspecialchars($reply['created_at']); ?></small>
                                                </div>
                                            <?php endwhile; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No comments yet. Be the first to comment!</p>
                        <?php endif; ?>
                        
                        <!-- New comment form -->
                        <form method="post">
                            <input type="hidden" name="post_id" value="<?php echo $postId; ?>">
                            <textarea name="comment_content" class="input-box" rows="3" placeholder="Add a new comment..."></textarea>
                            <button type="submit" name="new_comment" class="reply-btn">Add Comment</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No posts available.</p>
        <?php endif; ?>
    </div>
</body>
</html>
