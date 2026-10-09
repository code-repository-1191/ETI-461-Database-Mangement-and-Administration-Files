<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require "../includes/db_conn.php";
$query = "SELECT * FROM `eti_user` ORDER BY last_name";
?>
<html>
    <head>
        <title>ETI 461</title>
    </head>
    <body>
        <h1>ETI 461 - Database Design Lab </h1>
<div>
    <a href="index.php">Home</a> | 
	<a href="create-user.php">Create User</a>
</div>

<table border=1 cellpadding=5 >
    <tr>
        <th>ID</th>
        <!-- THE <a href> element below IS WHERE THE URL VARIABLES AND VALUES TO SORT BY FIRST NAME -->
        <th>First
            <a href="index.php">&uarr;</a>
            <a href="index.php">&darr;</a>
        </th>
        <!-- THE <a href> element below IS WHERE THE URL VARIABLES AND VALUES TO SORT BY LAST NAME -->
        <th>Last
            <a href="index.php">&uarr;</a>
            <a href="index.php">&darr;</a>
        </th>
        <!-- THE <a href> element below IS WHERE THE URL VARIABLES AND VALUES TO SORT BY EMAIL -->
        <th>Email
            <a href="index.php">&uarr;</a>
            <a href="index.php">&darr;</a>
        </th>
        <th>Username</th>
        <th>Password</th>
        <th>Registration Date</th>
        <th>Last Updated</th>
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
      echo "<td>{$row['username']}</td>";
      echo "<td>{$row['password']}</td>";
      
      echo "<td>{$row['registration_date']}</td>";
      echo "<td>{$row['updated_date']}</td>";
   
	  echo "<td>
        <a href=\"timeline.php?user_id={$row['id']}\">User Timeline</a> | 
        <a href=\"update-user.php?id={$row['id']}\">Update User</a> | 
        <a href=\"delete-user.php?id={$row['id']}\">Delete User</a>
      </td>";
    echo "</tr>";
  }
}
$mysqlconn->close();
?>
</table>
</body>
</html>