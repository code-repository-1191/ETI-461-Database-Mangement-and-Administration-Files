<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require "../includes/db_conn.php";

// if there is not user id then redirect user 
if ( !isset($_GET['user_id']) ) {
  header("location: index.php");
}
$user_id = $_GET['user_id'];

$user_query = "SELECT * FROM `eti_user` WHERE id = {$user_id}";
$user_result = $mysqlconn->query($user_query);
$user = $user_result->fetch_assoc();

$already_sent = array();

// get confirmed connections
$connection_query = "SELECT uc.*, u.first_name, u.last_name, u.username FROM `eti_user_connection` uc, `eti_user` u 
WHERE (uc.requestor_id = u.id OR uc.responder_id = u.id) 
AND uc.confirmation_id = 1
AND u.id = {$user_id}";


// get unconfirmed requests to for user_id to respond to
// user_id is responder
$unconfirmed_response_query = "SELECT uc.*, u.first_name, u.last_name, u.username FROM `eti_user_connection` uc, `eti_user` u 
WHERE uc.responder_id = u.id 
AND uc.confirmation_id = 0
AND u.id = {$user_id}";


// get unconfirmed requests from user_id that are awaiting response from other user
// user_id is requestor
$unconfirmed_request_query = "SELECT uc.*, u.first_name, u.last_name, u.username FROM `eti_user_connection` uc, `eti_user` u 
WHERE uc.requestor_id = u.id 
AND uc.confirmation_id = 0
AND u.id = {$user_id}";

$declined_connection_query = "SELECT uc.*, u.first_name, u.last_name, u.username FROM `eti_user_connection` uc, `eti_user` u 
WHERE (uc.requestor_id = u.id OR uc.responder_id = u.id) 
AND uc.confirmation_id = -1
AND u.id = {$user_id}";

// write a query to get all the other users to make a connection with:
$all_users = "SELECT * from `eti_user` ORDER BY last_name";
$all_users_result = $mysqlconn->query($all_users);
$masq_user_name = "";
?>
<html>
  <head>
    <title>ETI 461 - Connections</title>
  </head>
    <body>
      <h1>ETI 461 - Transaction Management Lab:Connections</h1>
<div>
    <a href="index.php">Home</a> | 
  	<a href="create-user.php">Create User</a> | 
    <a href="search.php">Search Blog Posts</a> 
</div>
<br>
<div>
  Displaying User Connection information for: 
  <form action="connections.php" method="get">
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

<h3>Connections Made</h3>
<table border=1 cellpadding=5 >
  <tr>
    <th>Name</th>
    <th>Response</th>
    <th>Request Date</th>
    <th>Action</th>
  </tr>
<?php 
$result = $mysqlconn->query($connection_query);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    if ($row['responder_id'] == $user_id) {
      $already_sent[] = $row['requestor_id'];
      $connection_sql = "SELECT * FROM `eti_user` WHERE id = {$row['requestor_id']}";
    }
    else {
      $already_sent[] = $row['responder_id'];
      $connection_sql = "SELECT * FROM `eti_user` WHERE id = {$row['responder_id']}";
    }
    $connection_result = $mysqlconn->query($connection_sql);    
    $connection_user = $connection_result->fetch_assoc();
    if ($row['response_date']) {
        $response = ($row['confirmation_id'] == 1 ? "Yes" : "No");
    }
    echo "<tr>";
      echo "<td>{$connection_user['first_name']} {$connection_user['last_name']} {$connection_user['username']}</td>";
      echo "<td>{$response}</td>";
      echo "<td>{$row['request_date']}</td>";
      echo "<td><a href=\"connections-action.php?user_id={$user_id}&action=remove&id={$row['id']}\">Remove Connection</a></td>";
    echo "</tr>";
  }
}

echo "</table>";

echo "<h3>Connections Declined</h3>";
echo "<table border=1 cellpadding=5 >";
echo "<tr>
        <th>Name</th>
        <th>Response</th>
        <th>Request Date</th>
        <th>Response Date</th>
    </tr>";
$result = $mysqlconn->query($declined_connection_query);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    if ($row['responder_id'] == $user_id) {
      $already_sent[] = $row['requestor_id'];
      $connection_sql = "SELECT * FROM `eti_user` WHERE id = {$row['requestor_id']}";
    }
    else {
      $already_sent[] = $row['responder_id'];
      $connection_sql = "SELECT * FROM `eti_user` WHERE id = {$row['responder_id']}";
    }
    $connection_result = $mysqlconn->query($connection_sql);    
    $connection_user = $connection_result->fetch_assoc();
    if ($row['response_date']) {
        $response = ($row['confirmation_id'] == 1 ? "Yes" : "No");
    }
    echo "<tr>";
      echo "<td>{$connection_user['first_name']} {$connection_user['last_name']} {$connection_user['username']}</td>";
      echo "<td>{$response}</td>";
      echo "<td>{$row['request_date']}</td>";
      echo "<td>{$row['response_date']}</td>";
    echo "</tr>";
  }
}

echo "</table>";

echo "<h3>Awaiting Response From {$masq_user_name}</h3>";
echo "<table border=1 cellpadding=5 style=\"background-color:#eee;\" >";
echo "<tr>
        <th>Name</th>
        <th>Request Date</th>
        <th>Action</th>
    </tr>";
$result = $mysqlconn->query($unconfirmed_response_query);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    // D  Write a query to get all the user information from for the requester of the request and assign it to $requestor_sql variable
    $already_sent[] = $row['requestor_id'];
    $connection_sql = "SELECT * FROM `eti_user` WHERE id = {$row['requestor_id']}";
    $connection_result = $mysqlconn->query($connection_sql);    
    $connection_user = $connection_result->fetch_assoc();
    
    echo "<tr>";
      echo "<td>{$connection_user['first_name']} {$connection_user['last_name']} {$connection_user['username']}</td>";
      echo "<td>{$row['request_date']}</td>";
      echo "<td><a href=\"connections-action.php?user_id={$user_id}&action=confirm&id={$row['id']}\">Confirm</a> or 
      <a href=\"connections-action.php?user_id={$user_id}&action=deny&id={$row['id']}\">Deny</a></td>";
    echo "</tr>";
  }
}

echo "</table>";

echo "<h3>Awaiting Response To {$masq_user_name}</h3>";
echo "<table border=1 cellpadding=5 style=\"background-color:#ccc;\" >";
echo "<tr>
        <th>Name</th>
        <th>Request Date</th>
    </tr>";
$result = $mysqlconn->query($unconfirmed_request_query);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $already_sent[] = $row['responder_id'];
    $connection_sql = "SELECT * FROM `eti_user` WHERE id = {$row['responder_id']}";
    $connection_result = $mysqlconn->query($connection_sql);    
    $connection_user = $connection_result->fetch_assoc();
    
    echo "<tr>";
      echo "<td>{$connection_user['first_name']} {$connection_user['last_name']} {$connection_user['username']}</td>";
      echo "<td>{$row['request_date']}</td>";
      
    echo "</tr>";
  }
}

echo "</table>";


$mysqlconn->close();
?>
</body>
</html>