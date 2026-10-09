<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require "../includes/db_conn.php";
// if there is not user id then redirect user 
if ( !isset($_GET['user_id']) ) {
    header("location: timeline.php");
}
$user_id = $_GET['user_id'];
$user_sql = "SELECT first_name, last_name FROM `eti_user` WHERE id = {$user_id} ";
$user_result = $mysqlconn->query($user_sql);
$user = $user_result->fetch_assoc();

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    
    // PART A
    // WRITE THE SQL STATEMENT TO CREATE A POST FOR A USER

    $post_sql = "INSERT INTO `eti_user_post` (`poster_id`, `post`) VALUES ({$user_id}, '{$_POST['post']}')";
    $mysqlconn->query($post_sql);
    
    header("location: timeline.php?user_id={$user_id}");    
}
?>

<html>
    <head><title>ETI 461 - Create a Post</title>

<body>
    <h1>ETI 461 - Database Design Lab:Create a Post</h1>
<div>
    <a href="index.php">Home</a> |  
	<a href="create-user.php">Create User</a>
</div>
<br>
<div>
  <a href="timeline.php?user_id=<?php echo $user_id; ?>">Timeline</a> | 
  <a href="create-post.php?user_id=<?php echo $user_id; ?>">Create Post</a> | 
  <a href="create-message.php?user_id=<?php echo $user_id; ?>">Send Message</a> |
  <a href="create-connection.php?user_id=<?php echo $user_id; ?>">Create Connections</a> <br>    
  <a href="connections.php?user_id=<?php echo $user_id; ?>">View My Connections</a> |
  <a href="posts.php?user_id=<?php echo $user_id; ?>">View My Posts</a> | 
  <a href="messages.php?user_id=<?php echo $user_id; ?>">View My Messages</a>
</div>
<br>
    

<h3>Creating a post for: <?php echo "{$user['first_name']} {$user['last_name']}"; ?></h3>
<div>
    <form action="create-post.php?user_id=<?php echo "{$user_id}"; ?>" method="post">
        <label for="post">Post</label><br>
        <textarea name="post" cols="80" rows="5"></textarea><br>
        <input type="submit" value="Post">
    </form>
</div>
</body>
</html>
