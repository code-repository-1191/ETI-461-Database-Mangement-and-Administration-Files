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
// PART D1 - Write a query to get the the blog, tagged subject (if exists), and
// user information for the user that wrote the blog, for the user we are 
// currently viewing based on $user_id value
// You will need the following columns from the following tables:
// user_blog - id, title, text, blog_date
// user - first_name, last_name
// user_tag_blog - tagged_subject
// hint: you will need a LEFT JOIN with user_tag_blog and user_blog table
// and INNER JOIN with user and user_blog table
$query = "SELECT ub.id, ub.title, ub.blog_text, ub.blog_date, u.first_name, u.last_name, utb.tagged_subject
          FROM eti_user_blog ub
          INNER JOIN eti_user u ON ub.blogger_id = u.id
          LEFT JOIN eti_user_tag_blog utb ON ub.id = utb.blog_id
          WHERE ub.blogger_id = $user_id
          ORDER BY ub.blog_date DESC";
// write a query to get all the other users for a drop down to masq as this user
$all_users = "SELECT * FROM `eti_user` 
              WHERE id != $user_id ORDER BY last_name";
$all_users_result = $mysqlconn->query($all_users);
$masq_user_name = "";
?>
<html>
    <head>
        <title>ETI 461 - Blogs</title>
    </head>
    <body>
        <h1>ETI 461 - Transaction Management Lab:Blogs</h1>
    
<div>
    <a href="index.php">Home</a> | 
  	<a href="create-user.php">Create User</a> | 
    <a href="search.php">Search Blog Posts</a> 
</div>
<br>
<div>
  Displaying User Blog Information for: 
  <form action="blogs.php" method="get">
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
  <a href="timeline.php?user_id=<?php echo urlencode($user_id); ?>">Timeline</a> | 
  <a href="create-post.php?user_id=<?php echo urlencode($user_id); ?>">Create Post</a> | 
  <a href="create-message.php?user_id=<?php echo urlencode($user_id); ?>">Send Message</a> |
  <a href="create-connection.php?user_id=<?php echo urlencode($user_id); ?>">Create Connections</a> | 
  <a href="create-blog.php?user_id=<?php echo urlencode($user_id); ?>">Create Blog Entry</a><br>    
  <a href="connections.php?user_id=<?php echo $user_id; ?>">View My Connections</a> |
  <a href="posts.php?user_id=<?php echo $user_id; ?>">View My Posts</a> | 
  <a href="messages.php?user_id=<?php echo $user_id; ?>">View My Messages</a> | 
  <a href="blogs.php?user_id=<?php echo $user_id; ?>">View My Blog Entries</a>
</div>
<br>

<table border=1 cellpadding=5 >
    <tr>
        <th>ID Blog</th>
        <th>Name</th>
        <th>Tagged Keyword</th>
        <th>Blog</th>
        <th>Blog Date</th>
    </tr>
<?php
$result = $mysqlconn->query($query);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
      echo "<td>{$row['id']}</td>";
      echo "<td>{$row['first_name']} {$row['last_name']}</td>";
      echo "<td>{$row['tagged_subject']}";
      echo "<td>{$row['blog_text']}</td>";
      echo "<td>{$row['blog_date']}</td>";
	  echo "</tr>";
  }
}

echo "</table>";
$mysqlconn->close();
?>
</body>
</html>