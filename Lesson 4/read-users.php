<html>
    <body><h1>ETI 461 - CRUD</h1>
    
<?php
error_reporting (E_ALL);
ini_set("display_errors", 1);

$dbserver = "mysql.up.ist.psu.edu";

// change USERNAME to be whatever your USERNAME is, should be the same as your database name
$dbuser = "ajd7226";

// change PASSWORD to whatever password IST created for you when you created your MySQL database
$dbpass = "3pPngvMK"; 

// change USERNAME to your USERNAME which is the same as your database name
$dbname = "ajd7226";

$mysqlconn = new mysqli( $dbserver, $dbuser, $dbpass, $dbname);

if ( $mysqlconn->connect_error ) {
  die("Connection failed: " . $mysqlconn->connect_error);
}

// write a SELECT query to return all the columns from the eti_user table
// and assign that value to the variable $query.
// for example, a similar query for the rental table would look like:
// $query = "SELECT * FROM eti_rental";

$query = "SELECT * FROM `eti_user`";

echo "<div>
	<a href=\"create-user.php\">Create User</a>
</div>";

echo "<table border=1 cellpadding=5 >";
echo "<tr><th>ID</th><th>First</th><th>Last</th><th>Email</th><th>Username</th><th>Password</th><th>Registration Date</th><th>Last Updated</th><th>Actions</th></tr>";
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
   
	  echo "<td><a href=\"update-user.php?id={$row['id']}\">Update User</a> | <a href=\"delete-user.php?id={$row['id']}\">Delete User</a></td>";
    echo "</tr>";
  }
}

echo "</table>";
$mysqlconn->close();
?>
</body>
</html>