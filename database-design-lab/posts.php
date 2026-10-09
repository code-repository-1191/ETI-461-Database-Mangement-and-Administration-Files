<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require "../includes/db_conn.php";

// if there is not user id then redirect user 
if ( !isset($_GET['user_id']) ) {
    header("location: index.php");
}
$user_id = $_GET['user_id'];

// PART D 
// WRITE THE SQL TO SELECT ALL THE POSTS FOR THE USER ($user_id)
// THAT THIS PAGE IS VIEWING

$query = "SELECT p.id, u.first_name, u.last_name, u.username, p.post, p.post_date 
          FROM `eti_user_post` p 
          JOIN `eti_user` u 
          ON p.poster_id = u.id 
          WHERE p.poster_id = {$user_id} 
          ORDER BY post_date DESC";

// write a query to get all the other users for a drop down to masq as this user

$all_users = "SELECT * from `eti_user` WHERE id != {$user_id} ORDER BY last_name";
$all_users_result = $mysqlconn->query($all_users);
$masq_user_name = "";

?>
<html>
    <head>
        <title>ETI 461 - Posts</title>
    </head>
    <body>
        <h1>ETI 461 - Database Design Lab:Posts</h1>
    
<div>
    <a href="index.php">Home</a> |  
	<a href="create-user.php">Create User</a>
</div>
<br>
<div>
  Displaying User Post Information for: 
  <form action="posts.php" method="get">
    <select name="user_id">
            <?php
            while ($row = $all_users_result->fetch_assoc()) {
              $selected = "";              
              if ( $row['id'] == $user_id ) {
                $selected = " selected ";
                $masq_user_name = "{$row['first_name']} {$row['last_name']}";
              }
              echo "<option value=\"{$row['id']}\" $selected>{$row['first_name']} {$row['last_name']}</option>";
            }
            $all_users_result->data_seek(0);
            ?>
    </select>
    <input type="submit" value="Pretend I'm this User">
  </form>
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

<table border=1 cellpadding=5 >
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Post</th>
        <th>Post Date
            <a href="posts.php?order_by=post_date ASC">&uarr;</a>
            <a href="posts.php?order_by=post_date DESC">&darr;</a>
        </th>
    </tr>
<?php
$result = $mysqlconn->query($query);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
      echo "<td>{$row['id']}</td>";
      echo "<td>{$row['first_name']} {$row['last_name']}, {$row['username']}</td>";
      echo "<td>{$row['post']}</td>";
      echo "<td>{$row['post_date']}</td>";
	  echo "</tr>";
  }
}

echo "</table>";
$mysqlconn->close();
?>
</body>
</html>