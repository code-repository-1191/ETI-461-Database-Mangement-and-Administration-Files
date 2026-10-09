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

$query = "SELECT u.id as user_id, u.first_name, u.last_name, u.username, u.email, 
            up.id as post_id, poster_id, post, post_date
    FROM `eti_user_post` up, `eti_user` u
    WHERE up.poster_id = u.id
    AND u.id IN ({$sql_in_user_ids})
    ORDER BY post_date DESC";
/* 
this will return multiple posts if there are multiple images in a post
SELECT up.*, 
	u.id, u.first_name, u.last_name, u.username, 
    	upm.upload_path, upm.mime_type 
FROM `eti_user_post` up
LEFT JOIN `eti_user_post_media` upm ON up.id = upm.post_id  
INNER JOIN `eti_user` u ON up.poster_id = u.id;
*/
?>
<html>
    <head>
        <title>ETI 461 - Timeline</title>
    </head>
    <body>
        <h1>ETI 461 - Transaction Management Lab:Timeline</h1>
<div>
    <a href="index.php">Home</a> | 
	<a href="create-user.php">Create User</a> | 
    <a href="search.php">Search Blog Posts</a> 
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
<?php
$result = $mysqlconn->query($query);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $tag_sql = "SELECT u.first_name, u.last_name, u.username
                FROM `eti_user_tag_post` utp, `eti_user` u 
                WHERE post_id = {$row['post_id']}
                AND utp.tagged_user_id = u.id";
    //echo "{$tag_sql}<br>";
    $tag_result = $mysqlconn->query($tag_sql);

    $media_sql = "SELECT upload_path, mime_type
                    FROM `eti_user_post` up, `eti_user_post_media` upm
                    WHERE up.id = upm.post_id
                    AND up.id = {$row['post_id']}";
    
    //echo "{$media_sql}";
    $media_result = $mysqlconn->query($media_sql);

    $reply_sql = "SELECT u.first_name, u.last_name, up.post, up.post_date
                FROM `eti_user` u, `eti_user_post` up
                WHERE up.comment_post_id = {$row['post_id']}
                AND up.poster_id = u.id
                ORDER BY up.post_date";
    $reply_result = $mysqlconn->query($reply_sql);

    echo "<div><blockquote>";
    echo "{$row['post']}<br>";
    if ( $media_result->num_rows) {
        while( $image_row = $media_result->fetch_assoc()) {
            echo "<img src=\"{$image_row['upload_path']}\" width=\"100\" alt=\"Need text here\"><br>";
        }
    }
    echo "</blockquote>
    {$row['first_name']} {$row['last_name']}, {$row['username']}<br>";
    if ($tag_result->num_rows) {
        $tag = $tag_result->fetch_assoc();
        echo " is with {$tag['first_name']} {$tag['last_name']}, {$tag['username']} ";
    }
    echo "{$row['post_date']}
    </div>";
    while ( $reply_row = $reply_result->fetch_assoc()) {
        echo "<em>{$reply_row['post']}</em> <br> {$reply_row['first_name']} {$reply_row['last_name']}";
    }
    echo "<p>&nbsp;</p>";
    /*
    echo "
    <form action=\"reply.php?user_id={$user_id}\" method=\"post\">
        <input type=\"hidden\" name=\"comment_post_id\" value=\"{$row['post_id']}\">
        <textarea name=\"post\" rows=\"3\" cols=\"80\"></textarea>
        <input type=\"submit\" value=\"Reply\">
    </form>";
    */
  }
}


$mysqlconn->close();
?>
</body>
</html>