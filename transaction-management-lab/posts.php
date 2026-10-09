<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require "../includes/db_conn.php";

if ($mysqlconn->connect_error) {
    die("Connection failed: " . $mysqlconn->connect_error);
}

// if there is not user id then redirect user 
if ( !isset($_GET['user_id']) ) {
    header("location: index.php");
}
$user_id = intval($_GET['user_id']);

// PART B
// B1 - write a query to select the posts for a user that
//  we are currently viewing posts for (as identified by $user_id)
// this query must include post id, poster_id, post, post_date, 
//        user id, first_name, last_name
//      From the user post and user table respectively and the id for
// the post and user table must have aliases (post_id, user_id)
$query = "SELECT p.id post_id, p.poster_id, p.post, p.post_date, u.id user_id, u.first_name, u.last_name 
          FROM eti_user_post p 
          JOIN eti_user u 
          ON p.poster_id = u.id 
          WHERE p.poster_id = $user_id";
    

// write a query to get all the other users for a drop down to masq as this user
$all_users = "SELECT * FROM `eti_user` 
              WHERE id != $user_id ORDER BY last_name";
$all_users_result = $mysqlconn->query($all_users);
if (!$all_users_result) {
    die("All users query failed: " . $mysqlconn->error);
}
$masq_user_name = "";
?>
<html>
    <head>
        <title>ETI 461 - Posts</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <h1>ETI 461 - Transaction Management Lab:Posts</h1>
    
<div>
  <a href="index.php">Home</a> | 
	<a href="create-user.php">Create User</a> | 
  <a href="search.php">Search Blog Posts</a> 
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
  <a href="create-connection.php?user_id=<?php echo $user_id; ?>">Create Connections</a> | 
  <a href="create-blog.php?user_id=<?php echo $user_id; ?>">Create Blog Entry</a><br>    
  <a href="connections.php?user_id=<?php echo $user_id; ?>">View My Connections</a> |
  <a href="posts.php?user_id=<?php echo $user_id; ?>">View My Posts</a> | 
  <a href="messages.php?user_id=<?php echo $user_id; ?>">View My Messages</a> | 
  <a href="blogs.php?user_id=<?php echo $user_id; ?>">View My Blog Entries</a>
</div>
<br><br>

<table border=1 cellpadding=5 >
    <tr>
        <th>ID for Post</th>
        <th>Name</th>
        <th>Post</th>
        <th>Tagged User</th>
        <th>Post Date
            <a href="posts.php?order_by=post_date ASC">&uarr;</a>
            <a href="posts.php?order_by=post_date DESC">&darr;</a>
        </th>
    </tr>
<?php
$result = $mysqlconn->query($query);
if (!$result) {
    die("Query failed: " . $mysqlconn->error);
}
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    // PART B2 
    // write a query to select the user information for the tagged
    // user based on the post_id that we are viewing
    $tag_user = "SELECT u.first_name, u.last_name 
                  FROM `eti_user` u, `eti_user_tag_post` utp
                  WHERE u.id = utp.tagged_user_id 
                  AND utp.post_id = {$row['post_id'] }";
    $tag_user_result = $mysqlconn->query($tag_user);
    if (!$tag_user_result) {
        die("Tag query failed: " . $mysqlconn->error);
    }
    echo "<tr>";
      echo "<td>{$row['post_id']}</td>";
      echo "<td>{$row['first_name']} {$row['last_name']}</td>";
      echo "<td>{$row['post']}</td>";
      if ($tag_user_result->num_rows) {
        $tag_user = $tag_user_result->fetch_assoc();
        echo "<td>{$tag_user['first_name']} {$tag_user['last_name']}</td>";
      }
      else {
        echo "<td>&nbsp;</td>";
      }
      echo "<td>{$row['post_date']}</td>";
	  echo "</tr>";
  }
}

echo "</table>";
$mysqlconn->close();
?>
</body>
</html>