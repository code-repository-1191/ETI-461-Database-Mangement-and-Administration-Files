<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require "../includes/db_conn.php";

// if there is not user id then redirect user 
if ( !isset($_GET['user_id']) ) {
  header("location: index.php");
}
$user_id = $_GET['user_id'];

$q = "";

// PART E
// WRITE THE SQL TO SELECT ALL THE MESSAGES FOR THE USER ($user_id)
// THAT THIS PAGE IS VIEWING

$query = "SELECT um.id, u.first_name, u.last_name, u.username, um.message, um.send_date, um.receiver_id 
          FROM `eti_user_message` um 
          JOIN `eti_user` u 
          ON um.sender_id = u.id 
          WHERE um.receiver_id = {$user_id}";

if ( isset($_GET['q']) ) {
    $q = $_GET['q'];
    $query .= " AND um.message LIKE '%{$q}%'";    
}

// write a query to get all the other users for a drop down to masq as this user

$all_users = "SELECT * from `eti_user` WHERE id != {$user_id} ORDER BY last_name";
$all_users_result = $mysqlconn->query($all_users);
$masq_user_name = "";
?>
<html>
    <body><h1>ETI 461 - Database Design Lab:Messages</h1>
    <div>
  <a href="index.php">Home</a> |  
	<a href="create-user.php">Create User</a>
</div>
<br>
<div>
  Displaying User Message information for: 
  <form action="messages.php" method="get">
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
    <th>Sender</th>
    <th>Message</th>
    <th>Recipient</th>
    <th>Sent</th>
  </tr>
<?php
$result = $mysqlconn->query($query);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $recipient_sql      = "SELECT first_name, last_name, username FROM `eti_user` WHERE id = {$row['receiver_id']}";
    $recipient_result   = $mysqlconn->query($recipient_sql);
    $recipient = $recipient_result->fetch_array();
    
    echo "<tr>";
      echo "<td>{$row['id']}</td>";
      echo "<td>{$row['first_name']} {$row['last_name']}, {$row['username']}</td>";
      echo "<td>{$row['message']}</td>";
      if ( strlen(trim($recipient['first_name'])) ) {
        echo "<td>{$recipient['first_name']} {$recipient['last_name']} {$recipient['username']}";
      }
      else {
        echo "<td>No recipient</td>";
      }
      
      echo "<td>{$row['send_date']}</td>";
    echo "</tr>";
  }
}
$mysqlconn->close();
?>

</table>
</body>
</html>