<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require "../includes/db_conn.php";
// if there is not user id then redirect user 
if ( !isset($_GET['user_id']) ) {
    header("location: index.php");
}
$user_id = $_GET['user_id'];
$user_sql = "SELECT first_name, last_name FROM `eti_user` WHERE id = {$user_id} ";
$user_result = $mysqlconn->query($user_sql);
$user = $user_result->fetch_assoc();

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    
    // PART C
    // WRITE THE SQL STATEMENT TO INSERT A CONNECTION

    $connection_sql = "INSERT INTO `eti_user_connection` (`requestor_id`, `responder_id`, `confirmation_id`) VALUES ({$_POST['requestor_id']}, {$_POST['responder_id']}, 0)";
    $mysqlconn->query($connection_sql);

    header("location: connections.php?user_id={$user_id}");
}

$query = "SELECT * FROM `eti_user` ORDER BY last_name";

?>
<html>
    <head>
        <title>ETI 461 - Create Connection</title>
    </head>
    <body>
        <h1>ETI 461 - Database Design Lab: Create a Connection</h1>
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
<h3>Request a connection for: <?php echo "{$user['first_name']} {$user['last_name']}"; ?></h3>
<table border=1 cellpadding=5 >
<tr>
        <th>ID</th>
        <th>First</th>
        <th>Last</th>
        <th>Email</th>
        <th>Actions</th>
    </tr>
<?php
$result = $mysqlconn->query($query);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
      echo "<td>{$row['id']}</td>";
      echo "<td>{$row['first_name']}</td>";
      echo "<td>{$row['last_name']}</td>";
      echo "<td>{$row['email']}</td>";   
	  echo "<td>
        <form action=\"create-connection.php?user_id={$user_id}\" method=\"post\">
            <input type=\"hidden\" name=\"requestor_id\" value=\"{$user_id}\">
            <input type=\"hidden\" name=\"responder_id\" value=\"{$row['id']}\">

            <input type=\"submit\" value=\"Request Connect\">
        </form>
        </td>";
    echo "</tr>";
  }
}
$mysqlconn->close();
?>
</table>
</body>
</html>