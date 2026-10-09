<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require "../includes/db_conn.php";
// if there is not user id then redirect user 
if ( !isset($_GET['user_id']) ) {
    header("location: timeline.php");
}
$user_id = $_GET['user_id'];

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    $message = $mysqlconn->real_escape_string($_POST['message']);
    $insert_sql = "INSERT INTO `eti_user_message` (`sender_id`, `receiver_id`, `message`) 
    VALUES ({$user_id}, {$_POST['receiver_id']}, '{$message}')";
    //echo $insert_sql;
    $mysqlconn->query($insert_sql);
    header("location: messages.php?user_id={$user_id}");
}
// if we only allow messages sent to users with a connection
/*
$connections_query = "SELECT * FROM `eti_user_connection` 
WHERE (requestor_id = {$user_id} OR responder_id = {$user_id}) 
AND confirmation_id = 1";
*/
// if we allow users to send a message to anyone no matter a connection
$connections_query = "SELECT * FROM `eti_user` ORDER BY last_name";
$connections_result = $mysqlconn->query($connections_query);

$user_sql = "SELECT first_name, last_name FROM `eti_user` WHERE id = {$user_id} ";
$user_result = $mysqlconn->query($user_sql);
$user = $user_result->fetch_assoc();


?>

<html>
    <head>
        <title>ETI 461 - Send a Message</title>
    </head>
<body>
    <h1>ETI 461 - Transaction Management Lab:Send a Message</h1>

<div>
    <a href="index.php">Home</a> | 
	<a href="create-user.php">Create User</a> | 
    <a href="search.php">Search Blog Posts</a> 
</div>
<br>
<div>
  <a href="timeline.php?user_id=<?php echo $user_id; ?>">Timeline</a> | 
  <a href="create-post.php?user_id=<?php echo $user_id; ?>">Create Post</a> | 
  <a href="create-message.php?user_id=<?php echo $user_id; ?>">Send Message</a> |
  <a href="create-connection.php?user_id=<?php echo $user_id; ?>">Create Connections</a> | 
  <a href="create-blog.php?user_id=<?php echo $user_id; ?>">Create Blog Entry</a><br>    
  <a href="connections.php?user_id=<?php echo $user_id; ?>">View My Connections</a> |
  <a href="posts.php?user_id=<?php echo $user_id; ?>">View My Posts</a> | 
  <a href="messages.php?user_id=<?php echo $user_id; ?>">View My Messages</a> | 
  <a href="blogs.php?user_id=<?php echo $user_id; ?>">View My Blog Entries</a>
</div>
<br><br>
    
<div>
    <form action="create-message.php?user_id=<?php echo $user_id; ?>" method="post">
        <?php echo "Sending Message From: {$user['first_name']} {$user['last_name']}"; ?><br>
        <label for="receiver_id">Sending Message To:</label>
        <select name="receiver_id" id="receiver_id">
            <option value="">Select User:</option>
        <?php
        while ($row = $connections_result->fetch_assoc()) {
            echo "<option value=\"{$row['id']}\">{$row['first_name']} {$row['last_name']}</option>";
        }
        ?>
        </select><br>
        <label for="message">Message</label><br>
        <textarea name="message" cols="80" rows="5"></textarea><br>
        <input type="submit" value="Send Message">
    </form>
</div>

</body>
</html>
