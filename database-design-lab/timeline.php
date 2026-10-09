<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);
require "../includes/db_conn.php";

// set user id to whatever user_id variable is passed in
if ( isset($_GET['user_id']) ) {
    $user_id = $_GET['user_id'];
}
// no user id passed in then go get a user to become -- get the first one
else {
    $default_user = "SELECT * from `eti_user` ORDER BY last_name LIMIT 1";
    $default_user_result = $mysqlconn->query($default_user);
    $default_user = $default_user_result->fetch_assoc();
    $user_id = $default_user['id'];

}

// write a query to get all the other users for a drop down to masq as this user
$all_users = "SELECT * from `eti_user` ORDER BY last_name";
$all_users_result = $mysqlconn->query($all_users);
$masq_user_name = "";

// get user confirmed connections
$connections_query = "SELECT * FROM `eti_user_connection` 
WHERE (requestor_id = {$user_id} OR responder_id = {$user_id}) 
AND confirmation_id = 1";
//echo $query;


$result = $mysqlconn->query($connections_query);
$user_ids_posts = array();
$user_ids_posts[] = $user_id;
while ($row = $result->fetch_assoc()) {
    if ( $row['requestor_id'] == $user_id ) {
        $user_ids_posts[] = $row['responder_id'];
    }
    else {
        $user_ids_posts[] = $row['requestor_id'];
    }
}
$sql_in_user_ids = implode(",", $user_ids_posts);
//echo "Users: $sql_in_user_ids<br>";

$query = "SELECT * 
    FROM `eti_user_post` up, `eti_user` u
    WHERE up.poster_id = u.id
    AND u.id IN ({$sql_in_user_ids})
    ORDER BY post_date DESC";
?>
<html>
    <head>
        <title>ETI 461 - Timeline</title>
    </head>
    <body>
        <h1>ETI 461 - Database Design Lab :Timeline</h1>
<div>
    <a href="index.php">Home</a> |    
	<a href="create-user.php">Create User</a>
</div>
<br>
    <div>
        Displaying User Connection information for: 
          <form action="timeline.php" method="get">
            <select name="user_id">";
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
      
<div>
    <?php
    echo "
        <a href=\"timeline.php?user_id={$user_id}\">Timeline</a> | 
        <a href=\"create-post.php?user_id={$user_id}\">Create Post</a> | 
        <a href=\"create-message.php?user_id={$user_id}\">Send Message</a> |
        <a href=\"create-connection.php?user_id={$user_id}\">Create Connections</a> <br>    
        <a href=\"connections.php?user_id={$user_id}\">View My Connections</a> |
        <a href=\"posts.php?user_id={$user_id}\">View My Posts</a> | 
        <a href=\"messages.php?user_id={$user_id}\">View My Messages</a>";
    ?>
</div>
<?php
$result = $mysqlconn->query($query);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<div>
    <blockquote>{$row['post']}</blockquote>
    {$row['first_name']} {$row['last_name']}, {$row['username']}<br>
    {$row['post_date']}
    </div><p>&nbsp;</p>";
  }
}


$mysqlconn->close();
?>
</body>
</html>