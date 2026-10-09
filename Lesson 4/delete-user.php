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

// pretty terrible to delete based on a GET variable
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	
	$id = $_GET['id'];
	// write a sql statement to delete the user from the database that
	// had the user id passed to this page. Do NOT forget to include your
	// WHERE clause or you will delete all the users from the table.
	// set the value of this sql statement to the php variable $sql
	$sql = "DELETE FROM `eti_user` WHERE id = {$id}";
	$mysqlconn->query($sql);

}
?>
<html>
<head>
	<title>ETI 461 Delete User</title>
</head>
    <body><h1>ETI 461</h1>
<a href="read-users.php">User Listing</a>

</body>
</html>